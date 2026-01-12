<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OfficialDocument;
use App\Models\OfficialDocumentVersion;
use App\Models\MediaLibrary;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BoardResolutionController extends Controller
{
    /**
     * Display a listing of official documents
     */
    public function index()
    {
        if (!Auth::user()->hasPermission('view board resolutions')) {
            return redirect()->route('dashboard')->with('error', 'You do not have permission to view board resolutions.');
        }

        $documents = OfficialDocument::with(['pdf', 'uploader'])
            ->orderBy('effective_date', 'desc')
            ->get();

        return view('admin.board-resolutions.index', compact('documents'));
    }

    /**
     * Show the form for creating a new official document
     */
    public function create()
    {
        if (!Auth::user()->hasPermission('create board resolutions')) {
            return redirect()->route('dashboard')->with('error', 'You do not have permission to create board resolutions.');
        }

        return view('admin.board-resolutions.create');
    }

    /**
     * Store a newly created official document
     */
    public function store(Request $request)
    {
        if (!Auth::user()->hasPermission('create board resolutions')) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to create board resolutions.'
            ], 403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'version' => 'nullable|string|max:255',
            'effective_date' => 'required|date',
            'approved_date' => 'required|date',
            'pdf_file' => 'nullable|file|mimes:pdf|max:30720', // 30MB
        ]);

        $pdfFileId = null;

        // Handle PDF upload
        if ($request->hasFile('pdf_file')) {
            $file = $request->file('pdf_file');
            $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $filePath = 'board-resolutions/' . $fileName;
            
            Storage::disk('public')->put($filePath, file_get_contents($file));
            
            $media = MediaLibrary::create([
                'file_name' => $file->getClientOriginalName(),
                'title' => $file->getClientOriginalName(),
                'file_type' => $file->getMimeType(),
                'file_path' => $filePath,
                'uploaded_by' => Auth::id(),
            ]);
            
            $pdfFileId = $media->id;
            
            AuditLogger::log(
                'official_document.media_upload',
                'Uploaded PDF for official document: ' . $validated['title'],
                $media
            );
        }

        $document = OfficialDocument::create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'version' => $validated['version'] ?? null,
            'effective_date' => $validated['effective_date'],
            'approved_date' => $validated['approved_date'],
            'pdf_file' => $pdfFileId,
            'uploaded_by' => Auth::id(),
        ]);

        AuditLogger::log(
            'official_document.create',
            'Created official document: ' . $document->title,
            $document
        );

        // Send email to all users and consec
        $recipients = User::whereIn('privilege', ['user', 'consec'])
            ->where('email', '!=', 'landogzwebsolutions@landogzwebsolutions.com')
            ->get();
        foreach ($recipients as $recipient) {
            try {
                Mail::to($recipient->email)->send(new \App\Mail\BoardResolutionEmail($recipient, $document));
            } catch (\Exception $e) {
                \Log::error('Failed to send board resolution email to user ' . $recipient->id . ': ' . $e->getMessage());
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Board resolution created successfully.',
            'redirect' => route('admin.board-resolutions.index')
        ]);
    }

    /**
     * Show the form for editing an official document
     */
    public function edit($id)
    {
        if (!Auth::user()->hasPermission('edit board resolutions')) {
            return redirect()->route('dashboard')->with('error', 'You do not have permission to edit board resolutions.');
        }

        $document = OfficialDocument::with(['pdf', 'uploader'])->findOrFail($id);

        return view('admin.board-resolutions.edit', compact('document'));
    }

    /**
     * Update an official document
     */
    public function update(Request $request, $id)
    {
        if (!Auth::user()->hasPermission('edit board resolutions')) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to edit board resolutions.'
            ], 403);
        }

        $document = OfficialDocument::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'version' => 'nullable|string|max:255',
            'effective_date' => 'required|date',
            'approved_date' => 'required|date',
            'pdf_file' => 'nullable|file|mimes:pdf|max:30720', // 30MB
            'change_notes' => 'nullable|string', // Optional notes about the change
        ]);

        // Save history before updating if file is being changed or any data changed
        $hasFileChange = $request->hasFile('pdf_file');
        $hasDataChange = $document->title !== $validated['title'] ||
                        $document->description !== ($validated['description'] ?? null) ||
                        $document->version !== ($validated['version'] ?? null) ||
                        $document->effective_date?->format('Y-m-d') !== ($validated['effective_date'] ?? null) ||
                        $document->approved_date?->format('Y-m-d') !== ($validated['approved_date'] ?? null);

        if ($hasFileChange || $hasDataChange) {
            // Create version history entry before making changes
            OfficialDocumentVersion::create([
                'official_document_id' => $document->id,
                'pdf_file' => $document->pdf_file, // Save old file reference
                'version' => $document->version,
                'title' => $document->title,
                'description' => $document->description,
                'effective_date' => $document->effective_date,
                'approved_date' => $document->approved_date,
                'uploaded_by' => Auth::id(),
                'change_notes' => $validated['change_notes'] ?? null,
            ]);
        }

        // Handle PDF upload if new file is provided
        if ($hasFileChange) {
            // Don't delete old PDF - keep it in history
            // The old file reference is already saved in the version history

            $file = $request->file('pdf_file');
            $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $filePath = 'board-resolutions/' . $fileName;
            
            Storage::disk('public')->put($filePath, file_get_contents($file));
            
            $media = MediaLibrary::create([
                'file_name' => $file->getClientOriginalName(),
                'title' => $file->getClientOriginalName(),
                'file_type' => $file->getMimeType(),
                'file_path' => $filePath,
                'uploaded_by' => Auth::id(),
            ]);
            
            $validated['pdf_file'] = $media->id;
            
            AuditLogger::log(
                'official_document.media_upload',
                'Updated PDF for official document: ' . $validated['title'],
                $media
            );
        } else {
            // Keep existing PDF
            $validated['pdf_file'] = $document->pdf_file;
        }

        $document->update($validated);

        AuditLogger::log(
            'official_document.update',
            'Updated official document: ' . $document->title,
            $document
        );

        return response()->json([
            'success' => true,
            'message' => 'Board resolution updated successfully.',
            'redirect' => route('admin.board-resolutions.index')
        ]);
    }

    /**
     * Remove an official document
     */
    public function destroy($id)
    {
        if (!Auth::user()->hasPermission('delete board resolutions')) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to delete board resolutions.'
            ], 403);
        }

        $document = OfficialDocument::findOrFail($id);

        // Delete associated PDF
        if ($document->pdf_file) {
            $media = MediaLibrary::find($document->pdf_file);
            if ($media) {
                Storage::disk('public')->delete($media->file_path);
                $media->delete();
            }
        }

        $title = $document->title;
        $document->delete();

        AuditLogger::log(
            'official_document.delete',
            'Deleted official document: ' . $title
        );

        return response()->json([
            'success' => true,
            'message' => 'Board resolution deleted successfully.'
        ]);
    }

    /**
     * Show version history for an official document
     */
    public function history($id)
    {
        if (!Auth::user()->hasPermission('view board resolutions')) {
            return redirect()->route('dashboard')->with('error', 'You do not have permission to view board resolutions.');
        }

        $document = OfficialDocument::with(['pdf', 'uploader'])->findOrFail($id);
        $versions = OfficialDocumentVersion::with(['pdf', 'uploader'])
            ->where('official_document_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.board-resolutions.history', compact('document', 'versions'));
    }
}
