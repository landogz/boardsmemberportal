<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReferenceMaterial;
use App\Models\Notice;
use App\Models\AgendaInclusionRequest;
use App\Models\BoardRegulation;
use App\Models\OfficialDocument;
use App\Models\Notification;
use App\Models\MediaLibrary;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ReferenceMaterialController extends Controller
{
    /**
     * Display folder view (notices of meeting) or listing of files for a notice
     */
    public function index(Request $request)
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        if (!Auth::user()->hasPermission('view reference materials')) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to view reference materials.');
        }

        $noticeId = $request->query('notice');

        if (!$noticeId) {
            // Folder view: show Notice of Meeting folders with accurate item count (files)
            $query = Notice::noticeOfMeeting()
                ->with(['referenceMaterials']);
            $q = $request->query('q');
            if ($q && trim($q) !== '') {
                $query->where(function ($qry) use ($q) {
                    $qry->where('title', 'like', '%' . trim($q) . '%')
                        ->orWhere('meeting_date', 'like', '%' . trim($q) . '%');
                });
            }
            $sort = $request->query('sort', 'date');
            $dir = $request->query('dir', 'desc');

            // Load notices then compute total attachment items per notice (reference materials + notice + related agenda notices + approved agenda items + board regulations/resolutions PDFs)
            $notices = $query->get();
            foreach ($notices as $notice) {
                $regPdfIds = BoardRegulation::where('notice_id', $notice->id)
                    ->orWhereIn('id', $notice->board_regulations ?? [])
                    ->pluck('pdf_file')
                    ->filter();
                $resPdfIds = OfficialDocument::where('notice_id', $notice->id)
                    ->orWhereIn('id', $notice->board_resolutions ?? [])
                    ->pluck('pdf_file')
                    ->filter();
                $itemIds = collect($notice->referenceMaterials)
                    ->flatMap(function ($material) {
                        return $material->attachments ?? [];
                    })
                    ->merge($notice->attachments ?? [])
                    ->merge(
                        Notice::where('related_notice_id', $notice->id)->get()->flatMap(function ($agenda) {
                            return $agenda->attachments ?? [];
                        })
                    )
                    ->merge(
                        AgendaInclusionRequest::where('notice_id', $notice->id)->where('status', 'approved')->get()->flatMap(function ($req) {
                            return $req->attachments ?? [];
                        })
                    )
                    ->merge($regPdfIds)
                    ->merge($resPdfIds)
                    ->filter()
                    ->unique()
                    ->values();
                $notice->reference_material_items_count = $itemIds->count();
            }

            // Sort in memory based on requested sort
            if ($sort === 'name') {
                $notices = ($dir === 'asc')
                    ? $notices->sortBy('title', SORT_FLAG_CASE | SORT_NATURAL)
                    : $notices->sortByDesc('title', SORT_FLAG_CASE | SORT_NATURAL);
            } elseif ($sort === 'items') {
                $notices = ($dir === 'asc')
                    ? $notices->sortBy('reference_material_items_count')
                    : $notices->sortByDesc('reference_material_items_count');
            } else {
                $notices = ($dir === 'asc')
                    ? $notices->sortBy('meeting_date')
                    : $notices->sortByDesc('meeting_date');
            }
            $pageDescription = 'Browse and manage board meeting reference documents.';
            return view('admin.reference-materials.folders', compact('notices', 'q', 'sort', 'dir', 'pageDescription'));
        }

        $materialsQuery = ReferenceMaterial::with(['notice', 'user'])
            ->where('notice_id', $noticeId)
            ->whereHas('user', function ($query) {
                $query->where('email', '!=', 'landogzwebsolutions@landogzwebsolutions.com');
            });
        $q = $request->query('q');
        if ($q && trim($q) !== '') {
            $materialsQuery->where(function ($qry) use ($q) {
                $qry->where('description', 'like', '%' . trim($q) . '%')
                    ->orWhereHas('user', function ($u) use ($q) {
                        $u->where('first_name', 'like', '%' . trim($q) . '%')
                            ->orWhere('last_name', 'like', '%' . trim($q) . '%')
                            ->orWhere('email', 'like', '%' . trim($q) . '%');
                    });
            });
        }
        $materials = $materialsQuery->orderBy('created_at', 'desc')->get();

        $notice = Notice::with('creator')->find($noticeId);
        if (!$notice) {
            return redirect()->route('admin.reference-materials.index')->with('error', 'Notice not found.');
        }

        // Build a flat list of files (one row per media), de‑duplicated by media_id.
        // Sources: reference materials, notice's own attachments, and related Agenda notices' attachments.
        $files = [];

        $addFileFromMedia = function ($media, $modifiedAt, $ownerName, $ownerAvatar, $materialId, $sourceLabel = null) use (&$files) {
            if (!$media || isset($files[$media->id])) {
                return;
            }
            $fileSize = 0;
            if (Storage::disk('public')->exists($media->file_path)) {
                $fileSize = Storage::disk('public')->size($media->file_path);
            }
            $fileName = $media->file_name;
            $displayName = trim((string) ($media->title ?? $fileName));
            if ($displayName === '') {
                $displayName = $fileName;
            }
            $files[$media->id] = (object)[
                'material_id' => $materialId,
                'media_id' => $media->id,
                'file_name' => $fileName,
                'display_name' => $displayName,
                'file_path' => $media->file_path,
                'file_type' => $media->file_type,
                'file_extension' => strtolower(pathinfo($fileName, PATHINFO_EXTENSION)),
                'file_size' => $fileSize,
                'owner_name' => $ownerName,
                'owner_avatar' => $ownerAvatar,
                'modified_at' => $modifiedAt,
                'source_label' => $sourceLabel,
            ];
        };

        foreach ($materials as $material) {
            $attachmentIds = $material->attachments ?? [];
            $mediaItems = MediaLibrary::whereIn('id', $attachmentIds)->get()->keyBy('id');
            foreach ($attachmentIds as $mediaId) {
                $media = $mediaItems->get($mediaId);
                $addFileFromMedia(
                    $media,
                    $material->created_at,
                    $material->user->first_name . ' ' . $material->user->last_name,
                    $material->user->profile_picture ? optional(MediaLibrary::find($material->user->profile_picture))->file_path : null,
                    $material->id,
                    null
                );
            }
        }

        // Notice's own attachments
        $noticeAttachmentIds = $notice->attachments ?? [];
        if (!empty($noticeAttachmentIds)) {
            $creator = $notice->creator;
            $ownerName = $creator ? ($creator->first_name . ' ' . $creator->last_name) : 'Notice';
            $ownerAvatar = $creator && $creator->profile_picture ? optional(MediaLibrary::find($creator->profile_picture))->file_path : null;
            foreach (MediaLibrary::whereIn('id', $noticeAttachmentIds)->get() as $media) {
                $addFileFromMedia($media, $notice->updated_at ?? $notice->created_at, $ownerName, $ownerAvatar, null, 'Notice');
            }
        }

        // Related Agenda notices' attachments — show uploader (agenda creator) avatar and name on hover
        $agendaNotices = Notice::with('creator')->where('related_notice_id', $noticeId)->get();
        foreach ($agendaNotices as $agenda) {
            $agendaAttachmentIds = $agenda->attachments ?? [];
            if (empty($agendaAttachmentIds)) {
                continue;
            }
            $agendaCreator = $agenda->creator;
            $ownerName = $agendaCreator ? ($agendaCreator->first_name . ' ' . $agendaCreator->last_name) : 'Agenda';
            $ownerAvatar = $agendaCreator && $agendaCreator->profile_picture ? optional(MediaLibrary::find($agendaCreator->profile_picture))->file_path : null;
            foreach (MediaLibrary::whereIn('id', $agendaAttachmentIds)->get() as $media) {
                $addFileFromMedia($media, $agenda->updated_at ?? $agenda->created_at, $ownerName, $ownerAvatar, null, null);
            }
        }

        // Approved Agenda Inclusion Request attachments (approved agenda items) — show uploader avatar and name on hover
        $approvedAgendaRequests = AgendaInclusionRequest::with('user')->where('notice_id', $noticeId)->where('status', 'approved')->get();
        foreach ($approvedAgendaRequests as $agendaRequest) {
            $attachmentIds = $agendaRequest->attachments ?? [];
            if (empty($attachmentIds)) {
                continue;
            }
            $user = $agendaRequest->user;
            $ownerName = $user ? ($user->first_name . ' ' . $user->last_name) : 'Agenda item';
            $ownerAvatar = $user && $user->profile_picture ? optional(MediaLibrary::find($user->profile_picture))->file_path : null;
            foreach (MediaLibrary::whereIn('id', $attachmentIds)->get() as $media) {
                $addFileFromMedia($media, $agendaRequest->updated_at ?? $agendaRequest->created_at, $ownerName, $ownerAvatar, null, null);
            }
        }

        // Board Regulation PDFs linked to this notice (notice_id or in notice's board_regulations)
        $regulations = BoardRegulation::with('pdf')->where('notice_id', $noticeId)
            ->orWhereIn('id', $notice->board_regulations ?? [])
            ->get();
        $creator = $notice->creator;
        $noticeOwnerName = $creator ? ($creator->first_name . ' ' . $creator->last_name) : 'Notice';
        $noticeOwnerAvatar = $creator && $creator->profile_picture ? optional(MediaLibrary::find($creator->profile_picture))->file_path : null;
        foreach ($regulations as $regulation) {
            if ($regulation->pdf_file && ($media = MediaLibrary::find($regulation->pdf_file))) {
                $addFileFromMedia($media, $regulation->updated_at ?? $regulation->created_at, $noticeOwnerName, $noticeOwnerAvatar, null, 'Notice');
            }
        }

        // Board Resolution (OfficialDocument) PDFs linked to this notice (notice_id or in notice's board_resolutions)
        $resolutions = OfficialDocument::with('pdf')->where('notice_id', $noticeId)
            ->orWhereIn('id', $notice->board_resolutions ?? [])
            ->get();
        foreach ($resolutions as $resolution) {
            if ($resolution->pdf_file && ($media = MediaLibrary::find($resolution->pdf_file))) {
                $addFileFromMedia($media, $resolution->updated_at ?? $resolution->created_at, $noticeOwnerName, $noticeOwnerAvatar, null, 'Notice');
            }
        }

        $sort = $request->query('sort', 'modified');
        $dir = $request->query('dir', 'desc');
        $coll = collect(array_values($files));
        if ($sort === 'name') {
            $coll = $dir === 'asc'
                ? $coll->sortBy('file_name', SORT_FLAG_CASE | SORT_NATURAL)
                : $coll->sortByDesc(fn ($f) => strtolower($f->file_name));
        } else {
            $coll = $dir === 'asc'
                ? $coll->sortBy('modified_at')
                : $coll->sortByDesc('modified_at');
        }
        $perPage = 15;
        $currentPage = \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPage();
        $filesPaginated = new \Illuminate\Pagination\LengthAwarePaginator(
            $coll->forPage($currentPage, $perPage)->values(),
            $coll->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('admin.reference-materials.index', compact('filesPaginated', 'noticeId', 'notice', 'q', 'sort', 'dir'));
    }

    /**
     * Normalize a value to a scalar media ID (for attachments that may be stored as id or ['id' => x]).
     */
    private function normalizeMediaId($value): ?int
    {
        if (is_numeric($value)) {
            return (int) $value;
        }
        if (is_array($value) && isset($value['id'])) {
            return is_numeric($value['id']) ? (int) $value['id'] : null;
        }
        return null;
    }

    /**
     * Collect all media IDs that should appear for a notice (same sources as index file list).
     * Used by downloadAll so the zip always matches what the user sees.
     * Includes all reference materials for the notice (no uploader filter) so zip is never empty when files exist.
     */
    private function getMediaIdsForNotice(Notice $notice): array
    {
        $noticeId = $notice->id;
        $mediaById = [];

        // All reference materials for this notice (no user filter so download includes every file in the folder)
        $materials = ReferenceMaterial::where('notice_id', $noticeId)->get();
        foreach ($materials as $material) {
            foreach ($material->attachments ?? [] as $rawId) {
                $id = $this->normalizeMediaId($rawId);
                if ($id !== null) {
                    $mediaById[$id] = true;
                }
            }
        }

        $noticeAttachments = $notice->attachments ?? [];
        if (is_string($noticeAttachments)) {
            $decoded = json_decode($noticeAttachments, true);
            $noticeAttachments = is_array($decoded) ? $decoded : [];
        }
        foreach ($noticeAttachments as $rawId) {
            $id = $this->normalizeMediaId($rawId);
            if ($id !== null) {
                $mediaById[$id] = true;
            }
        }

        foreach (Notice::where('related_notice_id', $noticeId)->get() as $agenda) {
            foreach ($agenda->attachments ?? [] as $rawId) {
                $id = $this->normalizeMediaId($rawId);
                if ($id !== null) {
                    $mediaById[$id] = true;
                }
            }
        }
        foreach (AgendaInclusionRequest::where('notice_id', $noticeId)->where('status', 'approved')->get() as $agendaRequest) {
            foreach ($agendaRequest->attachments ?? [] as $rawId) {
                $id = $this->normalizeMediaId($rawId);
                if ($id !== null) {
                    $mediaById[$id] = true;
                }
            }
        }

        $regIds = $notice->board_regulations ?? [];
        $resIds = $notice->board_resolutions ?? [];
        if (is_string($regIds)) {
            $decoded = json_decode($regIds, true);
            $regIds = is_array($decoded) ? $decoded : [];
        }
        if (is_string($resIds)) {
            $decoded = json_decode($resIds, true);
            $resIds = is_array($decoded) ? $decoded : [];
        }

        $regQuery = BoardRegulation::where('notice_id', $noticeId);
        if (!empty($regIds)) {
            $regQuery->orWhereIn('id', $regIds);
        }
        foreach ($regQuery->pluck('pdf_file') as $id) {
            if ($id !== null && $id !== '') {
                $mediaById[(int) $id] = true;
            }
        }

        $resQuery = OfficialDocument::where('notice_id', $noticeId);
        if (!empty($resIds)) {
            $resQuery->orWhereIn('id', $resIds);
        }
        foreach ($resQuery->pluck('pdf_file') as $id) {
            if ($id !== null && $id !== '') {
                $mediaById[(int) $id] = true;
            }
        }

        return array_keys($mediaById);
    }

    /**
     * Download all reference material files for a notice as a zip named after the meeting
     */
    public function downloadAll(Request $request)
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        if (!Auth::user()->hasPermission('view reference materials')) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to view reference materials.');
        }

        $noticeId = $request->query('notice');
        if (!$noticeId) {
            return redirect()->route('admin.reference-materials.index')->with('error', 'No notice specified.');
        }

        $notice = Notice::findOrFail($noticeId);
        $mediaIds = $this->getMediaIdsForNotice($notice);

        if (empty($mediaIds)) {
            return redirect()->route('admin.reference-materials.index', ['notice' => $noticeId])->with('info', 'No files to download.');
        }

        $mediaFiles = MediaLibrary::whereIn('id', $mediaIds)->get();
        $disk = Storage::disk('public');
        $usedNames = [];
        $tempDir = storage_path('app/temp');
        if (!is_dir($tempDir)) {
            if (!@mkdir($tempDir, 0755, true)) {
                return redirect()->route('admin.reference-materials.index', ['notice' => $noticeId])->with('error', 'Could not create temp directory. Check storage/app is writable.');
            }
        }
        if (!is_writable($tempDir)) {
            return redirect()->route('admin.reference-materials.index', ['notice' => $noticeId])->with('error', 'Temp directory is not writable. Check storage/app/temp permissions.');
        }
        $zipPath = $tempDir . '/ref-mat-' . uniqid('', true) . '.zip';

        if (!class_exists(\ZipArchive::class)) {
            return redirect()->route('admin.reference-materials.index', ['notice' => $noticeId])->with('error', 'Zip support is not available. Install php-zip on the server.');
        }

        $zip = new \ZipArchive();
        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            return redirect()->route('admin.reference-materials.index', ['notice' => $noticeId])->with('error', 'Could not create zip file. Check storage/app/temp is writable.');
        }

        foreach ($mediaFiles as $media) {
            if (!$disk->exists($media->file_path)) {
                continue;
            }
            $baseName = $media->file_name;
            $name = $baseName;
            $n = 0;
            while (isset($usedNames[$name])) {
                $n++;
                $ext = pathinfo($baseName, PATHINFO_EXTENSION);
                $base = pathinfo($baseName, PATHINFO_FILENAME);
                $name = $base . '-' . $n . ($ext ? '.' . $ext : '');
            }
            $usedNames[$name] = true;
            $fullPath = $disk->path($media->file_path);
            $zip->addFile($fullPath, $name);
        }
        $zip->close();

        $zipFileName = preg_replace('/[\\\\\/:*?"<>|]/', '-', $notice->title);
        $zipFileName = trim($zipFileName);
        $zipFileName = \Illuminate\Support\Str::limit($zipFileName, 100, '');
        if ($zipFileName === '') {
            $zipFileName = 'reference-materials';
        }
        $zipFileName .= '.zip';

        return response()->download($zipPath, $zipFileName, ['Content-Type' => 'application/zip'])->deleteFileAfterSend(true);
    }

    /**
     * Store new reference material (admin upload to a notice folder)
     */
    public function store(Request $request)
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        if (!Auth::user()->hasPermission('view reference materials')) {
            return response()->json(['success' => false, 'message' => 'Permission denied.'], 403);
        }

        $request->validate([
            'notice_id' => 'required|exists:notices,id',
            'description' => 'nullable|string|max:5000',
            'files' => 'required|array',
            'files.*' => 'file|max:30720',
        ]);

        $notice = Notice::findOrFail($request->notice_id);
        if ($notice->notice_type !== 'Notice of Meeting') {
            return response()->json(['success' => false, 'message' => 'Reference materials can only be uploaded to Notice of Meeting.'], 422);
        }

        $category = 'reference-materials';
        if (!Storage::disk('public')->exists($category)) {
            Storage::disk('public')->makeDirectory($category);
        }

        $files = $request->file('files');
        if (!is_array($files)) {
            $files = $files ? [$files] : [];
        }
        $attachmentIds = [];
        foreach ($files as $file) {
            if (!$file->isValid()) {
                continue;
            }
            $fileType = $file->getMimeType();
            $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $filePath = $category . '/' . $fileName;
            if (!Storage::disk('public')->put($filePath, file_get_contents($file))) {
                continue;
            }
            $media = MediaLibrary::create([
                'file_name' => $file->getClientOriginalName(),
                'file_type' => $fileType,
                'file_path' => $filePath,
                'uploaded_by' => Auth::id(),
            ]);
            if ($media) {
                $attachmentIds[] = $media->id;
            } else {
                Storage::disk('public')->delete($filePath);
            }
        }

        if (empty($attachmentIds)) {
            return response()->json(['success' => false, 'message' => 'No files were uploaded.'], 422);
        }

        DB::beginTransaction();
        try {
            ReferenceMaterial::create([
                'notice_id' => $notice->id,
                'user_id' => Auth::id(),
                'attendance_confirmation_id' => null,
                'description' => $request->description ?? 'Uploaded from Board Library',
                'attachments' => $attachmentIds,
                'status' => 'approved',
                'reviewed_by' => Auth::id(),
                'reviewed_at' => now(),
            ]);
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => count($attachmentIds) . ' file(s) uploaded successfully.',
                'redirect' => route('admin.reference-materials.index', ['notice' => $notice->id]),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Admin reference material upload: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to save. Please try again.'], 500);
        }
    }

    /**
     * Remove a single file (attachment) from a reference material
     */
    public function removeAttachment(Request $request)
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        if (!Auth::user()->hasPermission('view reference materials')) {
            return response()->json(['success' => false, 'message' => 'Permission denied.'], 403);
        }

        $request->validate([
            'material_id' => 'required|exists:reference_materials,id',
            'media_id' => 'required|exists:media_library,id',
        ]);

        $material = ReferenceMaterial::where('id', $request->material_id)->firstOrFail();
        $attachments = $material->attachments ?? [];
        if (!in_array((int) $request->media_id, $attachments)) {
            return response()->json(['success' => false, 'message' => 'File not found in this material.'], 404);
        }

        DB::beginTransaction();
        try {
            $material->attachments = array_values(array_diff($attachments, [(int) $request->media_id]));
            $material->save();

            if (count($material->attachments) === 0) {
                $material->delete();
            }

            $media = MediaLibrary::findOrFail($request->media_id);
            $path = $media->file_path;
            $media->delete();
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'File deleted.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Remove reference material attachment: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to delete file.'], 500);
        }
    }

    /**
     * Rename a file (attachment) display name
     */
    public function renameFile(Request $request)
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        if (!Auth::user()->hasPermission('view reference materials')) {
            return response()->json(['success' => false, 'message' => 'Permission denied.'], 403);
        }

        $request->validate([
            'material_id' => 'required|exists:reference_materials,id',
            'media_id' => 'required|exists:media_library,id',
            'file_name' => 'required|string|max:255',
        ]);

        $material = ReferenceMaterial::where('id', $request->material_id)->firstOrFail();
        $attachments = $material->attachments ?? [];
        if (!in_array((int) $request->media_id, $attachments)) {
            return response()->json(['success' => false, 'message' => 'File not found in this material.'], 404);
        }

        $fileName = trim($request->file_name);
        if ($fileName === '') {
            return response()->json(['success' => false, 'message' => 'File name is required.'], 422);
        }

        $media = MediaLibrary::findOrFail($request->media_id);
        $ext = pathinfo($media->file_name, PATHINFO_EXTENSION);
        if ($ext && !Str::endsWith(strtolower($fileName), '.' . strtolower($ext))) {
            $fileName = $fileName . '.' . $ext;
        }
        $media->file_name = $fileName;
        $media->save();

        return response()->json([
            'success' => true,
            'message' => 'File renamed.',
            'file_name' => $media->file_name,
        ]);
    }

    /**
     * Display the specified reference material
     */
    public function show($id)
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        if (!Auth::user()->hasPermission('view reference materials')) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to view reference materials.');
        }

        $material = ReferenceMaterial::with(['notice', 'user.governmentAgency', 'reviewer', 'attendanceConfirmation'])
            ->findOrFail($id);

        return view('admin.reference-materials.show', compact('material'));
    }

    /**
     * Approve reference material
     */
    public function approve(Request $request, $id)
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        if (
            !Auth::user()->hasPermission('manage reference materials') &&
            Auth::user()->privilege !== 'consec'
        ) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to manage reference materials.'
            ], 403);
        }

        $material = ReferenceMaterial::with(['notice', 'user'])->findOrFail($id);

        if (
            $material->notice->created_by !== Auth::id() &&
            Auth::user()->privilege !== 'consec'
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Only the meeting creator or CONSEC can approve reference materials.'
            ], 403);
        }

        DB::beginTransaction();
        try {
            $material->update([
                'status' => 'approved',
                'reviewed_by' => Auth::id(),
                'reviewed_at' => now(),
            ]);

            Notification::create([
                'user_id' => $material->user_id,
                'type' => 'reference_material_approved',
                'title' => 'Reference Materials Approved',
                'message' => 'Your reference materials for "' . $material->notice->title . '" have been approved.',
                'url' => route('notices.show', $material->notice_id),
                'data' => [
                    'reference_material_id' => $material->id,
                    'notice_id' => $material->notice_id,
                ],
            ]);

            AuditLogger::log(
                'reference_material.approved',
                'Reference material approved for notice: ' . $material->notice->title,
                $material
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Reference materials approved successfully.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error approving reference material: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to approve reference materials. Please try again.',
            ], 500);
        }
    }

    /**
     * Reject reference material
     */
    public function reject(Request $request, $id)
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        if (
            !Auth::user()->hasPermission('manage reference materials') &&
            Auth::user()->privilege !== 'consec'
        ) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to manage reference materials.'
            ], 403);
        }

        $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        $material = ReferenceMaterial::with(['notice', 'user'])->findOrFail($id);

        if (
            $material->notice->created_by !== Auth::id() &&
            Auth::user()->privilege !== 'consec'
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Only the meeting creator or CONSEC can reject reference materials.'
            ], 403);
        }

        DB::beginTransaction();
        try {
            $material->update([
                'status' => 'rejected',
                'rejection_reason' => $request->rejection_reason,
                'reviewed_by' => Auth::id(),
                'reviewed_at' => now(),
            ]);

            Notification::create([
                'user_id' => $material->user_id,
                'type' => 'reference_material_rejected',
                'title' => 'Reference Materials Rejected',
                'message' => 'Your reference materials for "' . $material->notice->title . '" have been rejected.',
                'url' => route('notices.show', $material->notice_id),
                'data' => [
                    'reference_material_id' => $material->id,
                    'notice_id' => $material->notice_id,
                    'rejection_reason' => $request->rejection_reason,
                ],
            ]);

            AuditLogger::log(
                'reference_material.rejected',
                'Reference material rejected for notice: ' . $material->notice->title,
                $material
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Reference materials rejected successfully.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error rejecting reference material: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to reject reference materials. Please try again.',
            ], 500);
        }
    }
}
