@extends('admin.layout')

@section('title', 'View Notice')

@php
    $pageTitle = 'View Notice';
    $headerActions = [];
    $headerActions[] = [
        'url' => route('admin.notices.index'),
        'text' => 'Back to Notices',
        'icon' => 'fas fa-arrow-left',
        'class' => 'px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-gray-700 transition-colors inline-flex items-center'
    ];
    if (Auth::user()->hasPermission('edit notices')) {
        $headerActions[] = [
            'url' => route('admin.notices.edit', $notice->id),
            'text' => 'Edit',
            'icon' => 'fas fa-edit',
            'class' => 'px-4 py-2 text-white rounded-lg font-semibold transition-all duration-300',
            'style' => 'background: linear-gradient(135deg, #055498 0%, #123a60 100%);'
        ];
    }
    $hideDefaultActions = false;
@endphp

@push('styles')
<style>
    .notice-type-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    .type-meeting {
        background-color: rgba(5, 84, 152, 0.1);
        color: #055498;
    }
    .type-agenda {
        background-color: rgba(206, 32, 40, 0.1);
        color: #CE2028;
    }
    .type-board-issuances {
        background-color: rgba(139, 92, 246, 0.1);
        color: #8B5CF6;
    }
    .type-other {
        background-color: rgba(156, 163, 175, 0.1);
        color: #6B7280;
    }
</style>
@endpush

@section('content')
<div class="p-4 lg:p-6 space-y-6">
    <!-- Header Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-5 border-b border-gray-100">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-3">
                        @php
                            $typeClass = 'type-other';
                            if ($notice->notice_type === 'Notice of Meeting') {
                                $typeClass = 'type-meeting';
                            } elseif ($notice->notice_type === 'Agenda') {
                                $typeClass = 'type-agenda';
                            } elseif ($notice->notice_type === 'Board Issuances') {
                                $typeClass = 'type-board-issuances';
                            }
                        @endphp
                        <span class="notice-type-badge {{ $typeClass }}">
                            {{ $notice->notice_type }}
                        </span>
                        <span class="px-3 py-1.5 rounded-lg text-xs font-medium bg-white text-gray-600 border border-gray-200 capitalize">
                            <i class="fas fa-video mr-1.5"></i>{{ $notice->meeting_type }}
                        </span>
                    </div>
                    <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 leading-tight mb-2">{{ $notice->title }}</h1>
                    <div class="flex flex-wrap items-center gap-4 text-sm text-gray-600">
                        <div class="flex items-center gap-2">
                            @php
                                $profilePic = 'https://ui-avatars.com/api/?name=' . urlencode($notice->creator->first_name . ' ' . $notice->creator->last_name) . '&size=32&background=055498&color=fff&bold=true';
                                if ($notice->creator->profile_picture) {
                                    $media = \App\Models\MediaLibrary::find($notice->creator->profile_picture);
                                    if ($media) {
                                        $profilePic = asset('storage/' . $media->file_path);
                                    }
                                }
                            @endphp
                            <img src="{{ $profilePic }}" alt="{{ $notice->creator->first_name }} {{ $notice->creator->last_name }}" class="w-8 h-8 rounded-full object-cover border-2 border-blue-200 shadow-sm">
                            <span class="font-medium">{{ $notice->creator->first_name }} {{ $notice->creator->last_name }}</span>
                        </div>
                        <div class="flex items-center gap-2 text-gray-500">
                            <i class="fas fa-calendar text-xs"></i>
                            <span>{{ $notice->created_at->format('M d, Y') }}</span>
                        </div>
                        @if($notice->meeting_date)
                            <div class="flex items-center gap-2 text-gray-500">
                                <i class="fas fa-calendar-alt text-xs"></i>
                                <span>{{ \Carbon\Carbon::parse($notice->meeting_date)->format('M d, Y') }}</span>
                            </div>
                        @endif
                        @if($notice->meeting_time)
                            <div class="flex items-center gap-2 text-gray-500">
                                <i class="fas fa-clock text-xs"></i>
                                <span>{{ \Carbon\Carbon::parse($notice->meeting_time)->format('g:i A') }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Meeting Details -->
        <div class="p-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <div>
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1 block">Meeting Type</label>
                        <div class="flex items-center gap-2 mt-1">
                            <i class="fas fa-video text-gray-400"></i>
                            <span class="text-sm font-medium text-gray-900 capitalize">{{ $notice->meeting_type }}</span>
                        </div>
                    </div>
                    @if(in_array($notice->meeting_type, ['online', 'hybrid']) && $notice->meeting_link)
                        <div>
                            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1 block">Meeting Link</label>
                            <a href="{{ $notice->meeting_link }}" target="_blank" class="inline-flex items-center gap-2 mt-1 text-sm font-medium text-blue-600 hover:text-blue-700 transition-colors">
                                <i class="fas fa-external-link-alt text-xs"></i>
                                <span class="truncate max-w-xs">{{ $notice->meeting_link }}</span>
                            </a>
                        </div>
                    @endif
                    @if($notice->notice_type === 'Board Issuances' && $notice->no_of_attendees)
                        <div>
                            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1 block">No. of Attendees</label>
                            <div class="flex items-center gap-2 mt-1">
                                <i class="fas fa-users text-gray-400"></i>
                                <span class="text-sm font-medium text-gray-900">{{ $notice->no_of_attendees }}</span>
                            </div>
                        </div>
                    @endif
                </div>
                @if($notice->notice_type === 'Agenda' && $notice->relatedNotice)
                    <div>
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1 block">Related Notice</label>
                        <a href="{{ route('admin.notices.show', $notice->relatedNotice->id) }}" class="inline-flex items-center gap-2 mt-1 text-sm font-medium text-blue-600 hover:text-blue-700 transition-colors">
                            <i class="fas fa-link text-xs"></i>
                            <span class="line-clamp-2">{{ $notice->relatedNotice->title }}</span>
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Board Regulations and Resolutions (Only for Board Issuances) -->
    @if($notice->notice_type === 'Board Issuances')
        @php
            $selectedRegulations = $notice->board_regulations ?? [];
            if (is_string($selectedRegulations)) {
                $selectedRegulations = json_decode($selectedRegulations, true) ?? [];
            }
            $selectedResolutions = $notice->board_resolutions ?? [];
            if (is_string($selectedResolutions)) {
                $selectedResolutions = json_decode($selectedResolutions, true) ?? [];
            }
            $regulations = !empty($selectedRegulations) ? \App\Models\BoardRegulation::whereIn('id', $selectedRegulations)->get() : collect([]);
            $resolutions = !empty($selectedResolutions) ? \App\Models\OfficialDocument::whereIn('id', $selectedResolutions)->get() : collect([]);
        @endphp
        
        @if($regulations->count() > 0 || $resolutions->count() > 0)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <div class="w-1 h-6 bg-gradient-to-b from-gray-500 to-gray-600 rounded-full"></div>
                    <span>Selected Board Issuances</span>
                </h3>
                
                @if($regulations->count() > 0)
                    <div class="mb-6">
                        <h4 class="text-sm font-semibold text-gray-700 mb-3">Board Regulations</h4>
                        <div class="space-y-2">
                            @foreach($regulations as $regulation)
                                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <h5 class="text-sm font-semibold text-gray-900 mb-1">{{ $regulation->title }}</h5>
                                            @if($regulation->effective_date)
                                                <p class="text-xs text-gray-500">Effective: {{ $regulation->effective_date->format('F d, Y') }}</p>
                                            @endif
                                        </div>
                                        @if($regulation->pdf)
                                            <a href="{{ route('admin.media-library.download', $regulation->pdf->id) }}" target="_blank" class="ml-4 text-blue-600 hover:text-blue-700">
                                                <i class="fas fa-file-pdf text-lg"></i>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
                
                @if($resolutions->count() > 0)
                    <div>
                        <h4 class="text-sm font-semibold text-gray-700 mb-3">Board Resolutions</h4>
                        <div class="space-y-2">
                            @foreach($resolutions as $resolution)
                                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <h5 class="text-sm font-semibold text-gray-900 mb-1">{{ $resolution->title }}</h5>
                                            @if($resolution->effective_date)
                                                <p class="text-xs text-gray-500">Effective: {{ $resolution->effective_date->format('F d, Y') }}</p>
                                            @endif
                                        </div>
                                        @if($resolution->pdf)
                                            <a href="{{ route('admin.media-library.download', $resolution->pdf->id) }}" target="_blank" class="ml-4 text-blue-600 hover:text-blue-700">
                                                <i class="fas fa-file-pdf text-lg"></i>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        @endif
    @endif

    <!-- Statistics Cards -->
    @php
        $attendanceConfirmations = \App\Models\AttendanceConfirmation::where('notice_id', $notice->id)->get();
        $accepted = $attendanceConfirmations->where('status', 'accepted')->count();
        $declined = $attendanceConfirmations->where('status', 'declined')->count();
        $pending = $notice->allowedUsers->count() - $accepted - $declined;
        $agendaRequests = \App\Models\AgendaInclusionRequest::where('notice_id', $notice->id)->count();
        $referenceMaterials = \App\Models\ReferenceMaterial::where('notice_id', $notice->id)->count();
    @endphp
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl p-5 border border-green-100 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <div class="w-12 h-12 rounded-xl bg-green-500 flex items-center justify-center shadow-lg">
                    <i class="fas fa-check-circle text-white text-xl"></i>
                </div>
                <div class="text-right">
                    <div class="text-3xl font-bold text-green-700">{{ $accepted }}</div>
                    <div class="text-xs font-medium text-green-600 uppercase tracking-wide">Accepted</div>
                </div>
            </div>
        </div>
        <div class="bg-gradient-to-br from-red-50 to-rose-50 rounded-xl p-5 border border-red-100 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <div class="w-12 h-12 rounded-xl bg-red-500 flex items-center justify-center shadow-lg">
                    <i class="fas fa-times-circle text-white text-xl"></i>
                </div>
                <div class="text-right">
                    <div class="text-3xl font-bold text-red-700">{{ $declined }}</div>
                    <div class="text-xs font-medium text-red-600 uppercase tracking-wide">Declined</div>
                </div>
            </div>
        </div>
        <div class="bg-gradient-to-br from-yellow-50 to-amber-50 rounded-xl p-5 border border-yellow-100 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <div class="w-12 h-12 rounded-xl bg-yellow-500 flex items-center justify-center shadow-lg">
                    <i class="fas fa-clock text-white text-xl"></i>
                </div>
                <div class="text-right">
                    <div class="text-3xl font-bold text-yellow-700">{{ $pending }}</div>
                    <div class="text-xs font-medium text-yellow-600 uppercase tracking-wide">Pending</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('admin.attendance-confirmations.index') }}?notice={{ $notice->id }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-lg font-medium text-sm transition-all duration-200 border border-blue-200">
                <i class="fas fa-check-circle"></i>
                <span>View Attendance Confirmations</span>
            </a>
            @if($agendaRequests > 0)
                <a href="{{ route('admin.agenda-inclusion-requests.index') }}?notice={{ $notice->id }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-purple-50 hover:bg-purple-100 text-purple-700 rounded-lg font-medium text-sm transition-all duration-200 border border-purple-200">
                    <i class="fas fa-clipboard-list"></i>
                    <span>View {{ $agendaRequests }} Agenda Request(s)</span>
                </a>
            @endif
            @if($referenceMaterials > 0)
                <a href="{{ route('admin.reference-materials.index') }}?notice={{ $notice->id }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-lg font-medium text-sm transition-all duration-200 border border-blue-200">
                    <i class="fas fa-book"></i>
                    <span>View {{ $referenceMaterials }} Reference Material(s)</span>
                </a>
            @endif
        </div>
    </div>

    <!-- Description -->
    @if($notice->description)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                <div class="w-1 h-6 bg-gradient-to-b from-blue-500 to-indigo-600 rounded-full"></div>
                <span>Description</span>
            </h3>
            <div class="prose max-w-none text-gray-700 leading-relaxed">
                {!! $notice->description !!}
            </div>
        </div>
    @endif

    <!-- Attachments -->
    @if($notice->attachments && count($notice->attachments) > 0)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                <div class="w-1 h-6 bg-gradient-to-b from-blue-500 to-indigo-600 rounded-full"></div>
                <span>Attachments</span>
                <span class="ml-2 px-2.5 py-0.5 bg-gray-100 text-gray-600 text-xs font-semibold rounded-full">{{ count($notice->attachments) }}</span>
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($notice->attachment_media as $attachment)
                    @php
                        $isImage = in_array(strtolower(pathinfo($attachment->file_name, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                        $isPdf = strtolower(pathinfo($attachment->file_name, PATHINFO_EXTENSION)) === 'pdf';
                        $ext = strtolower(pathinfo($attachment->file_name, PATHINFO_EXTENSION));
                    @endphp
                    <div class="group border border-gray-200 rounded-xl p-4 hover:shadow-lg hover:border-blue-300 transition-all duration-200 bg-gray-50">
                        @if($isImage)
                            <div class="w-full h-40 rounded-lg overflow-hidden mb-3 bg-gray-100">
                                <img src="{{ asset('storage/' . $attachment->file_path) }}" alt="{{ $attachment->file_name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200">
                            </div>
                        @elseif($isPdf)
                            <a href="javascript:void(0)" onclick="openGlobalPdfModal('{{ asset('storage/' . $attachment->file_path) }}', '{{ addslashes($attachment->file_name) }}')" class="w-full h-40 rounded-lg bg-gradient-to-br from-red-50 to-red-100 flex items-center justify-center mb-3 border border-red-200 cursor-pointer hover:bg-red-100 transition-colors">
                                <i class="fas fa-file-pdf text-5xl text-red-500"></i>
                            </a>
                        @else
                            <div class="w-full h-40 rounded-lg bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center mb-3 border border-gray-300">
                                <i class="fas fa-file text-5xl text-gray-400"></i>
                            </div>
                        @endif
                        <div class="space-y-1">
                            <p class="text-sm font-semibold text-gray-900 truncate" title="{{ $attachment->file_name }}">
                                {{ $attachment->file_name }}
                            </p>
                            <div class="flex items-center justify-between">
                                <p class="text-xs text-gray-500">
                                    {{ number_format($attachment->file_size / 1024, 2) }} KB
                                </p>
                                @if($isPdf)
                                    <a href="javascript:void(0)" onclick="openGlobalPdfModal('{{ asset('storage/' . $attachment->file_path) }}', '{{ addslashes($attachment->file_name) }}')" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-medium rounded-lg transition-colors cursor-pointer">
                                        <i class="fas fa-file-pdf text-xs"></i>
                                        <span>View PDF</span>
                                    </a>
                                @else
                                    <a href="{{ route('admin.media-library.download', $attachment->id) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-lg transition-colors">
                                        <i class="fas fa-download text-xs"></i>
                                        <span>Download</span>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Approved Agenda Requests -->
    @if($approvedAgendaRequests->count() > 0)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                <div class="w-1 h-6 bg-gradient-to-b from-green-500 to-emerald-600 rounded-full"></div>
                <span>Approved Agenda Items</span>
                <span class="ml-2 px-2.5 py-0.5 bg-green-100 text-green-700 text-xs font-semibold rounded-full">{{ $approvedAgendaRequests->count() }}</span>
            </h3>
            <div class="space-y-4">
                @foreach($approvedAgendaRequests as $agendaRequest)
                    <div class="border border-gray-200 rounded-xl p-5 hover:shadow-md transition-shadow bg-gradient-to-r from-white to-green-50/30">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center gap-3">
                                @php
                                    $profilePic = 'https://ui-avatars.com/api/?name=' . urlencode($agendaRequest->user->first_name . ' ' . $agendaRequest->user->last_name) . '&size=48&background=10B981&color=fff&bold=true';
                                    if ($agendaRequest->user->profile_picture) {
                                        $media = \App\Models\MediaLibrary::find($agendaRequest->user->profile_picture);
                                        if ($media) {
                                            $profilePic = asset('storage/' . $media->file_path);
                                        }
                                    }
                                @endphp
                                <img src="{{ $profilePic }}" alt="{{ $agendaRequest->user->first_name }} {{ $agendaRequest->user->last_name }}" class="w-12 h-12 rounded-xl object-cover border-2 border-green-200 shadow-sm">
                                <div>
                                    <p class="text-sm font-bold text-gray-900">{{ $agendaRequest->user->first_name }} {{ $agendaRequest->user->last_name }}</p>
                                    @if($agendaRequest->user->governmentAgency)
                                        <p class="text-xs text-gray-500 mt-0.5">{{ $agendaRequest->user->governmentAgency->name }}</p>
                                    @endif
                                </div>
                            </div>
                            <span class="px-3 py-1.5 bg-green-100 text-green-700 text-xs font-bold rounded-lg border border-green-200">
                                <i class="fas fa-check-circle mr-1.5"></i>Approved
                            </span>
                        </div>
                        <div class="mb-4 pl-1">
                            <p class="text-sm text-gray-700 whitespace-pre-wrap leading-relaxed">{{ $agendaRequest->description }}</p>
                        </div>
                        @if($agendaRequest->attachments && count($agendaRequest->attachments) > 0)
                            <div class="pt-4 border-t border-gray-200">
                                <p class="text-xs font-semibold text-gray-600 mb-2 uppercase tracking-wide">Attachments ({{ count($agendaRequest->attachments) }})</p>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($agendaRequest->attachment_media as $attachment)
                                        @php
                                            $isPdf = strtolower(pathinfo($attachment->file_name, PATHINFO_EXTENSION)) === 'pdf';
                                        @endphp
                                        @if($isPdf)
                                            <a href="javascript:void(0)" onclick="openGlobalPdfModal('{{ asset('storage/' . $attachment->file_path) }}', '{{ addslashes($attachment->file_name) }}')" class="inline-flex items-center gap-2 px-3 py-2 bg-red-100 hover:bg-red-200 rounded-lg text-xs font-medium text-red-700 transition-colors border border-red-200 cursor-pointer">
                                                <i class="fas fa-file-pdf text-xs"></i>
                                                <span class="truncate max-w-[150px]">{{ $attachment->file_name }}</span>
                                            </a>
                                        @else
                                            <a href="{{ route('admin.media-library.download', $attachment->id) }}" target="_blank" class="inline-flex items-center gap-2 px-3 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-xs font-medium text-gray-700 transition-colors border border-gray-200">
                                                <i class="fas fa-file text-xs"></i>
                                                <span class="truncate max-w-[150px]">{{ $attachment->file_name }}</span>
                                            </a>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Approved Reference Materials -->
    @if(isset($approvedReferenceMaterials) && $approvedReferenceMaterials->count() > 0)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                <div class="w-1 h-6 bg-gradient-to-b from-blue-500 to-indigo-600 rounded-full"></div>
                <span>Approved Reference Materials</span>
                <span class="ml-2 px-2.5 py-0.5 bg-blue-100 text-blue-700 text-xs font-semibold rounded-full">{{ $approvedReferenceMaterials->count() }}</span>
            </h3>
            <div class="space-y-4">
                @foreach($approvedReferenceMaterials as $referenceMaterial)
                    <div class="border border-gray-200 rounded-xl p-5 hover:shadow-md transition-shadow bg-gradient-to-r from-white to-blue-50/30">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center gap-3">
                                @php
                                    $profilePic = 'https://ui-avatars.com/api/?name=' . urlencode($referenceMaterial->user->first_name . ' ' . $referenceMaterial->user->last_name) . '&size=48&background=055498&color=fff&bold=true';
                                    if ($referenceMaterial->user->profile_picture) {
                                        $media = \App\Models\MediaLibrary::find($referenceMaterial->user->profile_picture);
                                        if ($media) {
                                            $profilePic = asset('storage/' . $media->file_path);
                                        }
                                    }
                                @endphp
                                <img src="{{ $profilePic }}" alt="{{ $referenceMaterial->user->first_name }} {{ $referenceMaterial->user->last_name }}" class="w-12 h-12 rounded-xl object-cover border-2 border-blue-200 shadow-sm">
                                <div>
                                    <p class="text-sm font-bold text-gray-900">{{ $referenceMaterial->user->first_name }} {{ $referenceMaterial->user->last_name }}</p>
                                    @if($referenceMaterial->user->governmentAgency)
                                        <p class="text-xs text-gray-500 mt-0.5">{{ $referenceMaterial->user->governmentAgency->name }}</p>
                                    @endif
                                </div>
                            </div>
                            <span class="px-3 py-1.5 bg-blue-100 text-blue-700 text-xs font-bold rounded-lg border border-blue-200">
                                <i class="fas fa-check-circle mr-1.5"></i>Approved
                            </span>
                        </div>
                        <div class="mb-4 pl-1">
                            <p class="text-sm text-gray-700 whitespace-pre-wrap leading-relaxed">{{ $referenceMaterial->description }}</p>
                        </div>
                        @if($referenceMaterial->attachments && count($referenceMaterial->attachments) > 0)
                            <div class="pt-4 border-t border-gray-200">
                                <p class="text-xs font-semibold text-gray-600 mb-2 uppercase tracking-wide">Attachments ({{ count($referenceMaterial->attachments) }})</p>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($referenceMaterial->attachment_media as $attachment)
                                        @php
                                            $isPdf = strtolower(pathinfo($attachment->file_name, PATHINFO_EXTENSION)) === 'pdf';
                                        @endphp
                                        @if($isPdf)
                                            <a href="javascript:void(0)" onclick="openGlobalPdfModal('{{ asset('storage/' . $attachment->file_path) }}', '{{ addslashes($attachment->file_name) }}')" class="inline-flex items-center gap-2 px-3 py-2 bg-red-100 hover:bg-red-200 rounded-lg text-xs font-medium text-red-700 transition-colors border border-red-200 cursor-pointer">
                                                <i class="fas fa-file-pdf text-xs"></i>
                                                <span class="truncate max-w-[150px]">{{ $attachment->file_name }}</span>
                                            </a>
                                        @else
                                            <a href="{{ route('admin.media-library.download', $attachment->id) }}" target="_blank" class="inline-flex items-center gap-2 px-3 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-xs font-medium text-gray-700 transition-colors border border-gray-200">
                                                <i class="fas fa-file text-xs"></i>
                                                <span class="truncate max-w-[150px]">{{ $attachment->file_name }}</span>
                                            </a>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Allowed Users -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
            <div class="w-1 h-6 bg-gradient-to-b from-blue-500 to-indigo-600 rounded-full"></div>
            <span>Allowed Users</span>
            <span class="ml-2 px-2.5 py-0.5 bg-gray-100 text-gray-600 text-xs font-semibold rounded-full">{{ $notice->allowedUsers->count() }}</span>
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
            @foreach($notice->allowedUsers as $user)
                @php
                    $profilePic = 'https://ui-avatars.com/api/?name=' . urlencode($user->first_name . ' ' . $user->last_name) . '&size=48&background=055498&color=fff&bold=true';
                    if ($user->profile_picture) {
                        $media = \App\Models\MediaLibrary::find($user->profile_picture);
                        if ($media) {
                            $profilePic = asset('storage/' . $media->file_path);
                        }
                    }
                @endphp
                <div class="flex items-center gap-3 p-3 bg-gray-50 hover:bg-gray-100 rounded-lg border border-gray-200 transition-colors">
                    <img src="{{ $profilePic }}" alt="{{ $user->first_name }} {{ $user->last_name }}" class="w-10 h-10 rounded-lg object-cover border-2 border-blue-200 shadow-sm">
                    <div class="flex-1 min-w-0">
                        <div class="text-sm font-semibold text-gray-900 truncate">{{ $user->first_name }} {{ $user->last_name }}</div>
                        <div class="text-xs text-gray-500 truncate">{{ $user->email }}</div>
                        @if($user->governmentAgency)
                            <div class="text-xs text-gray-400 truncate mt-0.5">{{ $user->governmentAgency->name }}</div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- CC Emails -->
    @if($notice->cc_emails && is_array($notice->cc_emails) && count($notice->cc_emails) > 0)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                <div class="w-1 h-6 bg-gradient-to-b from-gray-500 to-gray-600 rounded-full"></div>
                <span>CC Emails</span>
                <span class="ml-2 px-2.5 py-0.5 bg-gray-100 text-gray-600 text-xs font-semibold rounded-full">{{ count($notice->cc_emails) }}</span>
            </h3>
            <div class="space-y-3">
                @foreach($notice->cc_emails as $cc)
                    @php
                        $agencyName = 'N/A';
                        if (!empty($cc['agency'])) {
                            // Check if it's an ID (numeric) or name (string)
                            if (is_numeric($cc['agency'])) {
                                $agency = \App\Models\GovernmentAgency::find($cc['agency']);
                                $agencyName = $agency ? $agency->name : 'N/A';
                            } else {
                                // Legacy: it's stored as name string
                                $agencyName = $cc['agency'];
                            }
                        }
                    @endphp
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div>
                                <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1 block">Name</label>
                                <p class="text-sm font-semibold text-gray-900">{{ $cc['name'] ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1 block">Email</label>
                                <p class="text-sm text-gray-700">{{ $cc['email'] ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1 block">Position</label>
                                <p class="text-sm text-gray-700">{{ $cc['position'] ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1 block">Agency</label>
                                <p class="text-sm text-gray-700">{{ $agencyName }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @elseif($notice->cc_emails && is_string($notice->cc_emails))
        <!-- Legacy format: comma-separated emails -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                <div class="w-1 h-6 bg-gradient-to-b from-gray-500 to-gray-600 rounded-full"></div>
                <span>CC Emails</span>
            </h3>
            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                <p class="text-sm text-gray-700 font-mono">{{ $notice->cc_emails }}</p>
            </div>
        </div>
    @endif
</div>
@endsection
