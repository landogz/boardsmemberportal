<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notice;
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
     * Normalize empty strings to null for optional fields before validation/persistence.
     */
    private function normalizeRequest(Request $request): void
    {
        $request->merge([
            'effective_date' => $request->filled('effective_date') ? $request->input('effective_date') : null,
            'notice_id' => $request->filled('notice_id') ? $request->input('notice_id') : null,
            'description' => $request->filled('description') ? $request->input('description') : null,
            'version' => $request->filled('version') ? $request->input('version') : null,
        ]);
    }

    /**
     * Display a listing of official documents
     */
    public function index()
    {
        if (!Auth::user()->hasPermission('view board resolutions')) {
            return redirect()->route('dashboard')->with('error', 'You do not have permission to view board resolutions.');
        }

        $documents = OfficialDocument::with(['pdf', 'uploader'])
            ->orderBy('approved_date', 'desc')
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

        $noticeOfMeetingNotices = Notice::where('notice_type', 'Notice of Meeting')
            ->orderByDesc('meeting_date')
            ->orderByDesc('created_at')
            ->get(['id', 'title', 'meeting_date']);

        return view('admin.board-resolutions.create', compact('noticeOfMeetingNotices'));
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

        try {
            $this->normalizeRequest($request);

            $validated = $request->validate([
                'title' => 'required|string',
                'description' => 'nullable|string',
                'version' => 'nullable|string|max:255',
                'effective_date' => 'nullable|date',
                'approved_date' => 'required|date',
                'pdf_file' => 'required|file|mimes:pdf|max:102400', // 100MB
                'notice_id' => 'nullable|exists:notices,id',
            ]);

            if (!$request->hasFile('pdf_file') && (int) $request->server('CONTENT_LENGTH') > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'The PDF file could not be uploaded. It may exceed the server upload limit (' . ini_get('upload_max_filesize') . ').',
                ], 422);
            }

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
            'effective_date' => $validated['approved_date'],
            'approved_date' => $validated['approved_date'],
            'pdf_file' => $pdfFileId,
            'uploaded_by' => Auth::id(),
            'notice_id' => !empty($validated['notice_id']) ? $validated['notice_id'] : null,
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
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            \Log::error('Board resolution store failed: ' . $e->getMessage(), ['exception' => $e]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to save board resolution. Please check all required fields and try again.',
            ], 500);
        }
    }

    /**
     * Show the form for editing an official document
     */
    public function edit($id)
    {
        if (!Auth::user()->hasPermission('edit board resolutions')) {
            return redirect()->route('dashboard')->with('error', 'You do not have permission to edit board resolutions.');
        }

        $document = OfficialDocument::with(['pdf', 'uploader', 'notice'])->findOrFail($id);
        $noticeOfMeetingNotices = Notice::where('notice_type', 'Notice of Meeting')
            ->orderByDesc('meeting_date')
            ->orderByDesc('created_at')
            ->get(['id', 'title', 'meeting_date']);

        return view('admin.board-resolutions.edit', compact('document', 'noticeOfMeetingNotices'));
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

        try {
            $this->normalizeRequest($request);

            $validated = $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'version' => 'nullable|string|max:255',
            'effective_date' => 'nullable|date',
            'approved_date' => 'required|date',
            'pdf_file' => 'nullable|file|mimes:pdf|max:102400', // 100MB
            'change_notes' => 'nullable|string', // Optional notes about the change
            'notice_id' => 'nullable|exists:notices,id',
        ]);

        // Save history before updating if file is being changed or any data changed
        $hasFileChange = $request->hasFile('pdf_file');
        $hasDataChange = $document->title !== $validated['title'] ||
                        $document->description !== ($validated['description'] ?? null) ||
                        $document->version !== ($validated['version'] ?? null) ||
                        $document->approved_date?->format('Y-m-d') !== ($validated['approved_date'] ?? null) ||
                        $document->notice_id != ($validated['notice_id'] ?? null);

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

        $updateData = $validated;
        $updateData['notice_id'] = !empty($validated['notice_id']) ? $validated['notice_id'] : null;
        $updateData['effective_date'] = $validated['approved_date'];
        $document->update($updateData);

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
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            \Log::error('Board resolution update failed: ' . $e->getMessage(), ['exception' => $e]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update board resolution. Please check all required fields and try again.',
            ], 500);
        }
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

    /**
     * Serve PDF with custom filename for viewer
     */
    public function servePdf($id)
    {
        if (!Auth::user()->hasPermission('view board resolutions')) {
            abort(403, 'You do not have permission to view board resolutions.');
        }

        $document = OfficialDocument::with('pdf')->findOrFail($id);

        if (!$document->pdf || !$document->pdf->file_path) {
            abort(404, 'PDF not found.');
        }

        $filePath = Storage::disk('public')->path($document->pdf->file_path);

        if (!file_exists($filePath)) {
            abort(404, 'PDF file not found.');
        }

        // Create safe filename from document title
        // Use the actual title, sanitized for filename use
        $title = $document->title;
        // Remove or replace characters that are problematic in filenames
        $filename = preg_replace('#[<>:"/\\\\|?*]#', '_', $title);
        // Limit length to avoid issues
        if (strlen($filename) > 200) {
            $filename = substr($filename, 0, 197);
        }
        $filename .= '.pdf';

        // Serve file with custom filename in Content-Disposition header
        // Use both filename and filename* for better browser compatibility
        return response()->file($filePath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . addslashes($filename) . '"; filename*=UTF-8\'\'' . rawurlencode($filename),
        ]);
    }
}
