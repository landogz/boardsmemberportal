<?php

namespace App\Http\Controllers;

use App\Models\MediaLibrary;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaLibraryController extends Controller
{
    /**
     * Display the media library
     */
    public function index(Request $request)
    {
        if (!Auth::user()->hasPermission('view media library')) {
            return redirect()->route('dashboard')->with('error', 'You do not have permission to view media library.');
        }

        $query = MediaLibrary::with('uploader')->orderBy('created_at', 'desc');

        // Search functionality
        if ($request->has('search') && $request->search) {
            $query->where('file_name', 'like', '%' . $request->search . '%');
        }

        // Filter by file type
        if ($request->has('type') && $request->type) {
            if ($request->type === 'image') {
                $query->whereIn('file_type', ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/svg+xml', 'image/webp']);
            } elseif ($request->type === 'document') {
                $query->whereIn('file_type', ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']);
            } elseif ($request->type === 'video') {
                $query->where('file_type', 'like', 'video/%');
            } elseif ($request->type === 'audio') {
                $query->where('file_type', 'like', 'audio/%');
            }
        }

        $mediaFiles = $query->paginate(24);

        return view('admin.media-library.index', compact('mediaFiles'));
    }

    /**
     * Upload media files
     */
    public function store(Request $request)
    {
        if (!Auth::user()->hasPermission('upload media')) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to upload media.'
            ], 403);
        }

        // Check if POST data is too large (before validation)
        // This happens when PHP rejects the upload before Laravel sees it
        if (!$request->hasFile('files') || count($request->file('files')) === 0) {
            $uploadMaxFilesize = ini_get('upload_max_filesize');
            $postMaxSize = ini_get('post_max_size');
            
            return response()->json([
                'success' => false,
                'message' => 'File upload failed: The file is too large for current PHP settings. Current limits: upload_max_filesize=' . $uploadMaxFilesize . ', post_max_size=' . $postMaxSize . '. Please update php.ini (check phpinfo for the correct file location) and restart your PHP server.',
                'current_limits' => [
                    'upload_max_filesize' => $uploadMaxFilesize,
                    'post_max_size' => $postMaxSize
                ]
            ], 413);
        }

        // Check file sizes FIRST and provide detailed error messages
        $maxSize = 30 * 1024 * 1024; // 30MB in bytes (increased temporarily to debug)
        $maxSizeKB = 30 * 1024; // 30MB in KB
        $validationErrors = [];
        
        foreach ($request->file('files') as $index => $file) {
            // Get file size using multiple methods for debugging
            $fileSize = $file->getSize();
            $fileSizeFromPath = @filesize($file->getRealPath()) ?: $fileSize;
            $fileSizeMB = round($fileSize / (1024 * 1024), 2);
            $fileSizeKB = round($fileSize / 1024, 2);
            
            // Log for debugging
            \Log::info('File upload size check', [
                'original_name' => $file->getClientOriginalName(),
                'size_getSize' => $fileSize,
                'size_getSize_bytes' => $fileSize,
                'size_getSize_MB' => $fileSizeMB,
                'size_getSize_KB' => $fileSizeKB,
                'size_filesize' => $fileSizeFromPath,
                'size_filesize_MB' => round($fileSizeFromPath / (1024 * 1024), 2),
                'mime_type' => $file->getMimeType(),
                'temp_path' => $file->getRealPath(),
            ]);
            
            // Use the actual file size
            $actualSize = $fileSize;
            $actualSizeMB = $fileSizeMB;
            $actualSizeKB = $fileSizeKB;
            
            if ($actualSize > $maxSize) {
                $validationErrors["files.{$index}"] = [
                    "The file '{$file->getClientOriginalName()}' is {$actualSizeMB}MB ({$actualSizeKB}KB / {$actualSize} bytes), which exceeds the maximum allowed size of 30MB ({$maxSizeKB}KB). Note: If your file is actually smaller, this may indicate an upload issue."
                ];
            }
        }
        
        // If there are size errors, return them before standard validation
        if (!empty($validationErrors)) {
            return response()->json([
                'success' => false,
                'message' => 'File validation failed: One or more files exceed the size limit.',
                'errors' => $validationErrors
            ], 422);
        }

        // Validate files (this will catch other validation errors)
        $request->validate([
            'files.*' => [
                'required',
                'file',
            ],
        ]);

        $uploadedFiles = [];
        $errors = [];

        foreach ($request->file('files') as $file) {
            try {
                $fileSize = $file->getSize();
                $fileSizeMB = round($fileSize / (1024 * 1024), 2);
                $maxSize = 30 * 1024 * 1024; // 30MB in bytes (increased to accommodate larger files)
                
                // Validate file size (30MB = 30720 KB)
                if ($fileSize > $maxSize) {
                    $errors[] = [
                        'file' => $file->getClientOriginalName(),
                        'error' => "File size ({$fileSizeMB}MB) exceeds 30MB limit. Maximum allowed: 30MB."
                    ];
                    continue;
                }

                // Determine file category and path
                $fileType = $file->getMimeType();
                $category = $this->getFileCategory($fileType);
                $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
                $filePath = $category . '/' . $fileName;

                // Create directory if it doesn't exist
                if (!Storage::disk('public')->exists($category)) {
                    Storage::disk('public')->makeDirectory($category);
                }

                // Upload file
                $uploaded = Storage::disk('public')->put($filePath, file_get_contents($file));

                if (!$uploaded) {
                    $errors[] = [
                        'file' => $file->getClientOriginalName(),
                        'error' => 'Failed to save file to storage.'
                    ];
                    continue;
                }

                // Create media library entry
                $media = MediaLibrary::create([
                    'file_name' => $file->getClientOriginalName(),
                    'file_type' => $fileType,
                    'file_path' => $filePath,
                    'uploaded_by' => Auth::id(),
                ]);

                if ($media) {
                    $uploadedFiles[] = [
                        'id' => $media->id,
                        'name' => $media->file_name,
                        'url' => asset('storage/' . $media->file_path),
                        'type' => $fileType,
                        'size' => $file->getSize(),
                    ];
                    // Audit log per uploaded file
                    AuditLogger::log(
                        'media.uploaded',
                        'Uploaded media file: ' . $media->file_name,
                        $media,
                        [
                            'file_type' => $fileType,
                            'file_size' => $file->getSize(),
                        ]
                    );
                } else {
                    // Clean up uploaded file if media creation fails
                    Storage::disk('public')->delete($filePath);
                    $errors[] = [
                        'file' => $file->getClientOriginalName(),
                        'error' => 'Failed to create media record.'
                    ];
                }
            } catch (\Exception $e) {
                $errors[] = [
                    'file' => $file->getClientOriginalName(),
                    'error' => $e->getMessage()
                ];
            }
        }

        return response()->json([
            'success' => count($uploadedFiles) > 0,
            'message' => count($uploadedFiles) . ' file(s) uploaded successfully' . (count($errors) > 0 ? '. ' . count($errors) . ' file(s) failed.' : ''),
            'files' => $uploadedFiles,
            'errors' => $errors
        ]);
    }

    /**
     * Delete media file
     */
    public function destroy($id)
    {
        if (!Auth::user()->hasPermission('delete media')) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to delete media.'
            ], 403);
        }

        $media = MediaLibrary::findOrFail($id);

        // Delete file from storage
        if (Storage::disk('public')->exists($media->file_path)) {
            Storage::disk('public')->delete($media->file_path);
        }

        // Delete media record
        $media->delete();

        AuditLogger::log(
            'media.deleted',
            'Deleted media file: ' . $media->file_name,
            $media
        );

        return response()->json([
            'success' => true,
            'message' => 'Media file deleted successfully'
        ]);
    }

    /**
     * Download media file with a friendly filename (using title if available)
     */
    public function download($id)
    {
        if (!Auth::user()->hasPermission('view media library')) {
            abort(403, 'You do not have permission to download media.');
        }

        $media = MediaLibrary::findOrFail($id);

        if (!Storage::disk('public')->exists($media->file_path)) {
            abort(404);
        }

        $fullPath = Storage::disk('public')->path($media->file_path);

        // Determine original extension
        $extension = pathinfo($fullPath, PATHINFO_EXTENSION);

        // Use title if set, otherwise fall back to original file_name (without path)
        $baseTitle = $media->title ?: pathinfo($media->file_name, PATHINFO_FILENAME);

        // Slugify to make it safe for downloads (no spaces/special chars)
        $safeBase = Str::slug($baseTitle, '-');
        if (!$safeBase) {
            $safeBase = 'file';
        }

        $downloadName = $extension ? ($safeBase . '.' . $extension) : $safeBase;

        return response()->download($fullPath, $downloadName);
    }

    /**
     * Get media file details
     */
    public function show($id)
    {
        if (!Auth::user()->hasPermission('view media library')) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to view media.'
            ], 403);
        }

        $media = MediaLibrary::with('uploader')->findOrFail($id);
        $fileSize = Storage::disk('public')->exists($media->file_path) 
            ? Storage::disk('public')->size($media->file_path) 
            : 0;

        // Get image dimensions if it's an image
        $dimensions = null;
        if (strpos($media->file_type, 'image/') === 0 && Storage::disk('public')->exists($media->file_path)) {
            try {
                $imagePath = Storage::disk('public')->path($media->file_path);
                $imageInfo = getimagesize($imagePath);
                if ($imageInfo) {
                    $dimensions = $imageInfo[0] . ' × ' . $imageInfo[1] . ' pixels';
                }
            } catch (\Exception $e) {
                // Ignore errors
            }
        }

        return response()->json([
            'success' => true,
            'media' => [
                'id' => $media->id,
                'file_name' => $media->file_name,
                'title' => $media->title ?? $media->file_name,
                'alt_text' => $media->alt_text ?? '',
                'caption' => $media->caption ?? '',
                'description' => $media->description ?? '',
                'file_type' => $media->file_type,
                'file_path' => $media->file_path,
                'url' => asset('storage/' . $media->file_path),
                'size' => $fileSize,
                'size_formatted' => $this->formatBytes($fileSize),
                'dimensions' => $dimensions,
                'uploaded_by' => $media->uploader ? $media->uploader->first_name . ' ' . $media->uploader->last_name : 'Unknown',
                'uploaded_by_id' => $media->uploader ? $media->uploader->id : null,
                'uploaded_at' => $media->created_at->format('F d, Y g:i A'),
                'uploaded_at_short' => $media->created_at->format('M d, Y'),
                'is_image' => strpos($media->file_type, 'image/') === 0,
            ]
        ]);
    }

    /**
     * Update media file metadata
     */
    public function update(Request $request, $id)
    {
        if (!Auth::user()->hasPermission('edit media')) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to edit media.'
            ], 403);
        }

        $media = MediaLibrary::findOrFail($id);

        $request->validate([
            'title' => 'nullable|string|max:255',
            'alt_text' => 'nullable|string|max:255',
            'caption' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $media->update([
            'title' => $request->title,
            'alt_text' => $request->alt_text,
            'caption' => $request->caption,
            'description' => $request->description,
        ]);

        AuditLogger::log(
            'media.updated',
            'Updated media details: ' . $media->file_name,
            $media,
            [
                'title' => $media->title,
                'alt_text' => $media->alt_text,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Media details updated successfully',
            'media' => $media
        ]);
    }

    /**
     * Bulk delete media files
     */
    public function bulkDelete(Request $request)
    {
        if (!Auth::user()->hasPermission('delete media')) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to delete media.'
            ], 403);
        }

        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:media_library,id',
        ]);

        $deletedCount = 0;
        foreach ($request->ids as $id) {
            $media = MediaLibrary::find($id);
            if ($media) {
                // Delete file from storage
                if (Storage::disk('public')->exists($media->file_path)) {
                    Storage::disk('public')->delete($media->file_path);
                }
                $media->delete();
                $deletedCount++;

                AuditLogger::log(
                    'media.deleted',
                    'Deleted media file via bulk delete: ' . $media->file_name,
                    $media
                );
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Successfully deleted {$deletedCount} file(s)"
        ]);
    }

    /**
     * Get file category based on MIME type
     */
    private function getFileCategory($mimeType)
    {
        if (strpos($mimeType, 'image/') === 0) {
            return 'images';
        } elseif (strpos($mimeType, 'video/') === 0) {
            return 'videos';
        } elseif (strpos($mimeType, 'audio/') === 0) {
            return 'audio';
        } elseif (strpos($mimeType, 'application/pdf') === 0 || 
                  strpos($mimeType, 'application/msword') === 0 ||
                  strpos($mimeType, 'application/vnd.openxmlformats') === 0) {
            return 'documents';
        } else {
            return 'other';
        }
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }

    /**
     * Browse media library for CKEditor file browser
     */
    public function browse(Request $request)
    {
        if (!Auth::user()->hasPermission('view media library')) {
            abort(403, 'You do not have permission to view media library.');
        }

        $query = MediaLibrary::with('uploader')->orderBy('created_at', 'desc');

        // Filter by type if specified (for Images)
        if ($request->has('type') && $request->type === 'Images') {
            $query->whereIn('file_type', ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/svg+xml', 'image/webp']);
        }

        // Search functionality
        if ($request->has('search') && $request->search) {
            $query->where('file_name', 'like', '%' . $request->search . '%');
        }

        $mediaFiles = $query->paginate(24);

        // Get CKEditor parameters
        $CKEditor = $request->get('CKEditor');
        $CKEditorFuncNum = $request->get('CKEditorFuncNum');
        $langCode = $request->get('langCode', 'en');

        return view('admin.media-library.browse', compact('mediaFiles', 'CKEditor', 'CKEditorFuncNum', 'langCode'));
    }
}

