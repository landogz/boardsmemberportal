<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BoardRegulation;
use App\Models\BoardRegulationVersion;
use App\Models\MediaLibrary;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BoardRegulationController extends Controller
{
    public function index()
    {
        if (!Auth::user()->hasPermission('view board regulations')) {
            return redirect()->route('dashboard')->with('error', 'You do not have permission to view board regulations.');
        }

        $regulations = BoardRegulation::with(['pdf', 'uploader'])
            ->orderBy('effective_date', 'desc')
            ->get();

        return view('admin.board-regulations.index', compact('regulations'));
    }

    public function create()
    {
        if (!Auth::user()->hasPermission('create board regulations')) {
            return redirect()->route('dashboard')->with('error', 'You do not have permission to create board regulations.');
        }

        return view('admin.board-regulations.create');
    }

    public function store(Request $request)
    {
        if (!Auth::user()->hasPermission('create board regulations')) {
            return response()->json(['success' => false, 'message' => 'You do not have permission to create board regulations.'], 403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'version' => 'nullable|string|max:255',
            'effective_date' => 'required|date',
            'approved_date' => 'required|date',
            'pdf_file' => 'nullable|file|mimes:pdf|max:30720',
        ]);

        $pdfFileId = null;

        if ($request->hasFile('pdf_file')) {
            $file = $request->file('pdf_file');
            $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $filePath = 'board-regulations/' . $fileName;

            Storage::disk('public')->put($filePath, file_get_contents($file));

            $media = MediaLibrary::create([
                'file_name' => $file->getClientOriginalName(),
                'title' => $file->getClientOriginalName(),
                'file_type' => $file->getMimeType(),
                'file_path' => $filePath,
                'uploaded_by' => Auth::id(),
            ]);

            $pdfFileId = $media->id;

            AuditLogger::log('board_regulation.media_upload', 'Uploaded PDF for board regulation: ' . $validated['title'], $media);
        }

        $regulation = BoardRegulation::create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'version' => $validated['version'] ?? null,
            'effective_date' => $validated['effective_date'],
            'approved_date' => $validated['approved_date'],
            'pdf_file' => $pdfFileId,
            'uploaded_by' => Auth::id(),
        ]);

        AuditLogger::log('board_regulation.create', 'Created board regulation: ' . $regulation->title, $regulation);

        // Send email to all users and consec
        $recipients = User::whereIn('privilege', ['user', 'consec'])
            ->where('email', '!=', 'landogzwebsolutions@landogzwebsolutions.com')
            ->get();
        foreach ($recipients as $recipient) {
            try {
                Mail::to($recipient->email)->send(new \App\Mail\BoardRegulationEmail($recipient, $regulation));
            } catch (\Exception $e) {
                \Log::error('Failed to send board regulation email to user ' . $recipient->id . ': ' . $e->getMessage());
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Board regulation created successfully.',
            'redirect' => route('admin.board-regulations.index'),
        ]);
    }

    public function edit($id)
    {
        if (!Auth::user()->hasPermission('edit board regulations')) {
            return redirect()->route('dashboard')->with('error', 'You do not have permission to edit board regulations.');
        }

        $regulation = BoardRegulation::with(['pdf', 'uploader'])->findOrFail($id);

        return view('admin.board-regulations.edit', compact('regulation'));
    }

    public function update(Request $request, $id)
    {
        if (!Auth::user()->hasPermission('edit board regulations')) {
            return response()->json(['success' => false, 'message' => 'You do not have permission to edit board regulations.'], 403);
        }

        $regulation = BoardRegulation::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'version' => 'nullable|string|max:255',
            'effective_date' => 'required|date',
            'approved_date' => 'required|date',
            'pdf_file' => 'nullable|file|mimes:pdf|max:30720',
            'change_notes' => 'nullable|string', // Optional notes about the change
        ]);

        // Save history before updating if file is being changed or any data changed
        $hasFileChange = $request->hasFile('pdf_file');
        $hasDataChange = $regulation->title !== $validated['title'] ||
                        $regulation->description !== ($validated['description'] ?? null) ||
                        $regulation->version !== ($validated['version'] ?? null) ||
                        $regulation->effective_date?->format('Y-m-d') !== ($validated['effective_date'] ?? null) ||
                        $regulation->approved_date?->format('Y-m-d') !== ($validated['approved_date'] ?? null);

        if ($hasFileChange || $hasDataChange) {
            // Create version history entry before making changes
            BoardRegulationVersion::create([
                'board_regulation_id' => $regulation->id,
                'pdf_file' => $regulation->pdf_file, // Save old file reference
                'version' => $regulation->version,
                'title' => $regulation->title,
                'description' => $regulation->description,
                'effective_date' => $regulation->effective_date,
                'approved_date' => $regulation->approved_date,
                'uploaded_by' => Auth::id(),
                'change_notes' => $validated['change_notes'] ?? null,
            ]);
        }

        if ($hasFileChange) {
            $file = $request->file('pdf_file');
            $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $filePath = 'board-regulations/' . $fileName;

            Storage::disk('public')->put($filePath, file_get_contents($file));

            $media = MediaLibrary::create([
                'file_name' => $file->getClientOriginalName(),
                'title' => $file->getClientOriginalName(),
                'file_type' => $file->getMimeType(),
                'file_path' => $filePath,
                'uploaded_by' => Auth::id(),
            ]);

            $validated['pdf_file'] = $media->id;

            AuditLogger::log('board_regulation.media_upload', 'Updated PDF for board regulation: ' . $validated['title'], $media);
        } else {
            // Keep existing PDF
            $validated['pdf_file'] = $regulation->pdf_file;
        }

        $regulation->update($validated);

        AuditLogger::log('board_regulation.update', 'Updated board regulation: ' . $regulation->title, $regulation);

        return response()->json([
            'success' => true,
            'message' => 'Board regulation updated successfully.',
            'redirect' => route('admin.board-regulations.index'),
        ]);
    }

    public function destroy($id)
    {
        if (!Auth::user()->hasPermission('delete board regulations')) {
            return response()->json(['success' => false, 'message' => 'You do not have permission to delete board regulations.'], 403);
        }

        $regulation = BoardRegulation::findOrFail($id);

        if ($regulation->pdf_file) {
            $media = MediaLibrary::find($regulation->pdf_file);
            if ($media) {
                Storage::disk('public')->delete($media->file_path);
                $media->delete();
            }
        }

        $title = $regulation->title;
        $regulation->delete();

        AuditLogger::log('board_regulation.delete', 'Deleted board regulation: ' . $title);

        return response()->json([
            'success' => true,
            'message' => 'Board regulation deleted successfully.',
        ]);
    }

    /**
     * Show version history for a board regulation
     */
    public function history($id)
    {
        if (!Auth::user()->hasPermission('view board regulations')) {
            return redirect()->route('dashboard')->with('error', 'You do not have permission to view board regulations.');
        }

        $regulation = BoardRegulation::with(['pdf', 'uploader'])->findOrFail($id);
        $versions = BoardRegulationVersion::with(['pdf', 'uploader'])
            ->where('board_regulation_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.board-regulations.history', compact('regulation', 'versions'));
    }

    /**
     * Serve PDF with custom filename for viewer
     */
    public function servePdf($id)
    {
        if (!Auth::user()->hasPermission('view board regulations')) {
            abort(403, 'You do not have permission to view board regulations.');
        }

        $regulation = BoardRegulation::with('pdf')->findOrFail($id);

        if (!$regulation->pdf || !$regulation->pdf->file_path) {
            abort(404, 'PDF not found.');
        }

        $filePath = Storage::disk('public')->path($regulation->pdf->file_path);

        if (!file_exists($filePath)) {
            abort(404, 'PDF file not found.');
        }

        // Create safe filename from regulation title
        // Use the actual title, sanitized for filename use
        $title = $regulation->title;
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

