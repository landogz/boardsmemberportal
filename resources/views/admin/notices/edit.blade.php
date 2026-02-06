@extends('admin.layout')

@section('title', 'Edit Notice')

@php
    $pageTitle = 'Edit Notice';
    $headerActions = [];
    $headerActions[] = [
        'url' => route('admin.notices.index'),
        'text' => 'Back to Notices',
        'icon' => 'fas fa-arrow-left',
        'class' => 'px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-gray-700 transition-colors inline-flex items-center'
    ];
    $hideDefaultActions = false;
@endphp

@push('styles')
<style>
    .user-select-item {
        padding: 0.5rem;
        border: 1px solid #e5e7eb;
        border-radius: 0.375rem;
        margin: 0.25rem;
        display: inline-block;
        background: white;
    }
    .user-select-item.selected {
        background-color: #055498;
        color: white;
        border-color: #055498;
    }
    #attachmentPreview {
        max-height: 300px;
        object-fit: cover;
    }
</style>
@endpush

@section('content')
<div class="p-4 lg:p-6">
    <!-- Page Title -->
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Edit Notice</h2>
        <p class="text-gray-600 mt-1">Update notice information</p>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <form id="editNoticeForm" class="space-y-6">
            @csrf
            @method('PUT')
            
            <!-- Two Column Layout -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left Column: Main Form Fields -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Notice Type Selection -->
                    <div>
                        <label for="notice_type" class="block text-sm font-medium text-gray-700 mb-2">Notice Type *</label>
                        <select 
                            id="notice_type" 
                            name="notice_type" 
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none transition"
                        >
                            <option value="">Select Notice Type</option>
                            <option value="Notice of Meeting" {{ $notice->notice_type === 'Notice of Meeting' ? 'selected' : '' }}>Notice of Meeting</option>
                            <option value="Agenda" {{ $notice->notice_type === 'Agenda' ? 'selected' : '' }}>Agenda</option>
                            <option value="Board Issuances" {{ $notice->notice_type === 'Board Issuances' ? 'selected' : '' }}>Board Issuances</option>
                            <option value="Other Matters" {{ $notice->notice_type === 'Other Matters' ? 'selected' : '' }}>Other Matters</option>
                        </select>
                        <span class="text-red-500 text-sm hidden" id="notice_type-error"></span>
                    </div>

                    <!-- Title Field (Text input for Notice of Meeting/Other Matters, Dropdown for Agenda) -->
                    <div id="titleTextContainer">
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Title *</label>
                        <input 
                            type="text" 
                            id="title" 
                            name="title" 
                            required
                            value="{{ old('title', $notice->title) }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none transition"
                            placeholder="Enter notice title"
                        >
                        <span class="text-red-500 text-sm hidden" id="title-error"></span>
                    </div>

                    <!-- Title Dropdown (Only for Agenda) -->
                    <div id="titleDropdownContainer" class="hidden">
                        <label for="title_dropdown" class="block text-sm font-medium text-gray-700 mb-2">Title (Select Notice of Meeting) *</label>
                        <select 
                            id="title_dropdown" 
                            name="title_dropdown" 
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none transition"
                        >
                            <option value="">Select a Notice of Meeting</option>
                            @foreach($noticeOfMeetingNotices as $noticeOption)
                                <option value="{{ $noticeOption->id }}" data-title="{{ $noticeOption->title }}" {{ $notice->related_notice_id == $noticeOption->id ? 'selected' : '' }}>{{ $noticeOption->title }}</option>
                            @endforeach
                        </select>
                        <input type="hidden" id="related_notice_id" name="related_notice_id" value="{{ $notice->related_notice_id }}">
                        <span class="text-red-500 text-sm hidden" id="title_dropdown-error"></span>
                    </div>

                    <!-- Meeting Type Selection -->
                    <div>
                        <label for="meeting_type" class="block text-sm font-medium text-gray-700 mb-2">Meeting Type *</label>
                        <select 
                            id="meeting_type" 
                            name="meeting_type" 
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none transition"
                        >
                            <option value="onsite" {{ $notice->meeting_type === 'onsite' ? 'selected' : '' }}>Onsite</option>
                            <option value="online" {{ $notice->meeting_type === 'online' ? 'selected' : '' }}>Online</option>
                            <option value="hybrid" {{ $notice->meeting_type === 'hybrid' ? 'selected' : '' }}>Hybrid</option>
                        </select>
                        <span class="text-red-500 text-sm hidden" id="meeting_type-error"></span>
                    </div>

                    <!-- Meeting Link (Only for Online/Hybrid) -->
                    <div id="meetingLinkContainer" class="hidden">
                        <label for="meeting_link" class="block text-sm font-medium text-gray-700 mb-2">Meeting Link *</label>
                        <input 
                            type="url" 
                            id="meeting_link" 
                            name="meeting_link" 
                            value="{{ old('meeting_link', $notice->meeting_link) }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none transition"
                            placeholder="Enter meeting link (Zoom, Google Meet, etc.)"
                        >
                        <span class="text-red-500 text-sm hidden" id="meeting_link-error"></span>
                    </div>

                    <!-- Meeting Date -->
                    <div>
                        <label for="meeting_date" class="block text-sm font-medium text-gray-700 mb-2">Meeting Date</label>
                        <input 
                            type="date" 
                            id="meeting_date" 
                            name="meeting_date" 
                            value="{{ old('meeting_date', $notice->meeting_date ? \Carbon\Carbon::parse($notice->meeting_date)->format('Y-m-d') : '') }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none transition"
                        >
                        <span class="text-red-500 text-sm hidden" id="meeting_date-error"></span>
                    </div>

                    <!-- Meeting Time -->
                    <div>
                        <label for="meeting_time" class="block text-sm font-medium text-gray-700 mb-2">Meeting Time</label>
                        <input 
                            type="time" 
                            id="meeting_time" 
                            name="meeting_time" 
                            value="{{ old('meeting_time', $notice->meeting_time ? \Carbon\Carbon::parse($notice->meeting_time)->format('H:i') : '') }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none transition"
                        >
                        <span class="text-red-500 text-sm hidden" id="meeting_time-error"></span>
                    </div>

                    <!-- Board Regulations Selection (Only for Board Issuances) -->
                    <div id="boardRegulationsContainer" class="{{ $notice->notice_type === 'Board Issuances' ? '' : 'hidden' }}">
                        <label for="board_regulations" class="block text-sm font-medium text-gray-700 mb-2">Board Regulations</label>
                        <p class="text-xs text-gray-500 mb-3">Select published Board Regulations</p>
                        <!-- Search Input -->
                        <input 
                            type="text" 
                            id="boardRegulationsSearch" 
                            placeholder="Search Board Regulations..." 
                            class="w-full px-3 py-2 mb-3 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none"
                        >
                        <div class="border border-gray-300 rounded-lg p-4 max-h-64 overflow-y-auto" id="boardRegulationsList">
                            @php
                                $selectedRegulations = $notice->board_regulations ?? [];
                                if (is_string($selectedRegulations)) {
                                    $selectedRegulations = json_decode($selectedRegulations, true) ?? [];
                                }
                            @endphp
                            @foreach($boardRegulations ?? [] as $regulation)
                                <label class="regulation-item flex items-center space-x-3 p-2 hover:bg-gray-50 rounded cursor-pointer" data-title="{{ strtolower($regulation->title) }}" data-date="{{ $regulation->effective_date ? strtolower($regulation->effective_date->format('F d, Y')) : '' }}">
                                    <input 
                                        type="checkbox" 
                                        name="board_regulations[]" 
                                        value="{{ $regulation->id }}"
                                        {{ in_array($regulation->id, $selectedRegulations) ? 'checked' : '' }}
                                        class="board-regulation-checkbox h-4 w-4 text-[#055498] border-gray-300 rounded focus:ring-[#055498]"
                                    >
                                    <div class="flex-1 min-w-0">
                                        <span class="text-sm font-medium text-gray-700 block truncate">
                                            {{ $regulation->title }}
                                        </span>
                                        @if($regulation->effective_date)
                                            <span class="text-xs text-gray-500">
                                                Effective: {{ $regulation->effective_date->format('F d, Y') }}
                                            </span>
                                        @endif
                                    </div>
                                </label>
                            @endforeach
                            @if(empty($boardRegulations ?? []))
                                <p class="text-sm text-gray-500 text-center py-4">No Board Regulations available</p>
                            @endif
                        </div>
                        <span class="text-red-500 text-sm hidden" id="board_regulations-error"></span>
                    </div>

                    <!-- Board Resolutions Selection (Only for Board Issuances) -->
                    <div id="boardResolutionsContainer" class="{{ $notice->notice_type === 'Board Issuances' ? '' : 'hidden' }}">
                        <label for="board_resolutions" class="block text-sm font-medium text-gray-700 mb-2">Board Resolutions</label>
                        <p class="text-xs text-gray-500 mb-3">Select published Board Resolutions</p>
                        <!-- Search Input -->
                        <input 
                            type="text" 
                            id="boardResolutionsSearch" 
                            placeholder="Search Board Resolutions..." 
                            class="w-full px-3 py-2 mb-3 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none"
                        >
                        <div class="border border-gray-300 rounded-lg p-4 max-h-64 overflow-y-auto" id="boardResolutionsList">
                            @php
                                $selectedResolutions = $notice->board_resolutions ?? [];
                                if (is_string($selectedResolutions)) {
                                    $selectedResolutions = json_decode($selectedResolutions, true) ?? [];
                                }
                            @endphp
                            @foreach($boardResolutions ?? [] as $resolution)
                                <label class="resolution-item flex items-center space-x-3 p-2 hover:bg-gray-50 rounded cursor-pointer" data-title="{{ strtolower($resolution->title) }}" data-date="{{ $resolution->effective_date ? strtolower($resolution->effective_date->format('F d, Y')) : '' }}">
                                    <input 
                                        type="checkbox" 
                                        name="board_resolutions[]" 
                                        value="{{ $resolution->id }}"
                                        {{ in_array($resolution->id, $selectedResolutions) ? 'checked' : '' }}
                                        class="board-resolution-checkbox h-4 w-4 text-[#055498] border-gray-300 rounded focus:ring-[#055498]"
                                    >
                                    <div class="flex-1 min-w-0">
                                        <span class="text-sm font-medium text-gray-700 block truncate">
                                            {{ $resolution->title }}
                                        </span>
                                        @if($resolution->effective_date)
                                            <span class="text-xs text-gray-500">
                                                Effective: {{ $resolution->effective_date->format('F d, Y') }}
                                            </span>
                                        @endif
                                    </div>
                                </label>
                            @endforeach
                            @if(empty($boardResolutions ?? []))
                                <p class="text-sm text-gray-500 text-center py-4">No Board Resolutions available</p>
                            @endif
                        </div>
                        <span class="text-red-500 text-sm hidden" id="board_resolutions-error"></span>
                    </div>

                    <!-- No. of Attendees (Only for Board Issuances) -->
                    <div id="noOfAttendeesContainer" class="{{ $notice->notice_type === 'Board Issuances' ? '' : 'hidden' }}">
                        <label for="no_of_attendees" class="block text-sm font-medium text-gray-700 mb-2">No. of Attendees</label>
                        <input 
                            type="number" 
                            id="no_of_attendees" 
                            name="no_of_attendees" 
                            min="1"
                            value="{{ old('no_of_attendees', $notice->no_of_attendees) }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none transition"
                            placeholder="Enter number of attendees"
                        >
                        <span class="text-red-500 text-sm hidden" id="no_of_attendees-error"></span>
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                        <textarea 
                            id="description" 
                            name="description" 
                            rows="6"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none transition"
                            placeholder="Enter notice description"
                        >{{ old('description', $notice->description) }}</textarea>
                        <span class="text-red-500 text-sm hidden" id="description-error"></span>
                    </div>

                    <!-- File Attachments (Drag and Drop - Multiple) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Attachments</label>
                        <div 
                            id="attachmentsDropZone" 
                            class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center transition-colors hover:border-[#055498] hover:bg-blue-50/50"
                        >
                            <input 
                                type="file" 
                                id="attachments" 
                                name="attachments[]" 
                                multiple
                                accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png,.gif"
                                class="hidden"
                            >
                            <div id="dropZoneContent" class="cursor-pointer">
                                <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-3"></i>
                                <p class="text-sm text-gray-600 mb-1">
                                    <span class="text-[#055498] font-semibold">Click to upload</span> or drag and drop
                                </p>
                                <p class="text-xs text-gray-500">PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, JPG, PNG, GIF (Max: 30MB per file)</p>
                            </div>
                            <div id="dropZoneActive" class="hidden">
                                <i class="fas fa-file-upload text-4xl text-[#055498] mb-3 animate-bounce"></i>
                                <p class="text-sm text-[#055498] font-semibold">Drop files here</p>
                            </div>
                            <div id="attachmentsPreview" class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-4">
                                @if($notice->attachments && count($notice->attachments) > 0)
                                    @foreach($notice->attachments as $attachmentId)
                                        @php
                                            $media = \App\Models\MediaLibrary::find($attachmentId);
                                        @endphp
                                        @if($media)
                                            @php
                                                $isImage = str_starts_with($media->file_type, 'image/');
                                                $isPdf = str_ends_with(strtolower($media->file_name), '.pdf') || $media->file_type === 'application/pdf';
                                            @endphp
                                            <div class="relative border rounded-lg p-2 attachment-item" data-file-id="{{ $media->id }}">
                                                <button type="button" class="absolute top-1 right-1 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center hover:bg-red-600 transition-colors delete-attachment-btn" data-file-id="{{ $media->id }}" data-file-name="{{ $media->file_name }}" title="Remove attachment">
                                                    <i class="fas fa-times text-xs"></i>
                                                </button>
                                                @if($isImage)
                                                    <img src="{{ asset('storage/' . $media->file_path) }}" alt="{{ $media->file_name }}" class="w-full h-24 object-cover rounded">
                                                @elseif($isPdf)
                                                    <div class="w-full h-24 flex flex-col items-center justify-center bg-gray-100 rounded">
                                                        <i class="fas fa-file-pdf text-3xl text-red-500 mb-1"></i>
                                                    </div>
                                                @else
                                                    <div class="w-full h-24 flex items-center justify-center bg-gray-100 rounded">
                                                        <i class="fas fa-file text-3xl text-gray-400"></i>
                                                    </div>
                                                @endif
                                                <p class="text-xs text-gray-600 mt-1 truncate" title="{{ $media->file_name }}">{{ $media->file_name }}</p>
                                            </div>
                                        @endif
                                    @endforeach
                                @endif
                            </div>
                        </div>
                        <span class="text-red-500 text-sm hidden" id="attachments-error"></span>
                    </div>
                </div>

                <!-- Right Column: Allowed Users, CC Emails, and Submit Button -->
                <div class="lg:col-span-1">
                    <div class="sticky top-6 space-y-6">
                        <!-- Allowed Users Selection -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Allowed Users *</label>
                            <p class="text-xs text-gray-500 mb-3">Select users who can view this notice</p>
                            <div class="border border-gray-300 rounded-lg p-4 max-h-[calc(100vh-500px)] overflow-y-auto">
                                <!-- Search -->
                                <input 
                                    type="text" 
                                    id="userSearch" 
                                    placeholder="Search users..." 
                                    class="w-full px-3 py-2 mb-3 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none"
                                >
                                
                                <!-- Select All -->
                                <div class="mb-3 pb-3 border-b border-gray-200">
                                    <label class="flex items-center p-2 hover:bg-gray-50 rounded cursor-pointer font-medium">
                                        <input 
                                            type="checkbox" 
                                            id="selectAllUsers"
                                            class="h-4 w-4 text-[#055498] border-gray-300 rounded focus:ring-[#055498]"
                                        >
                                        <span class="ml-3 text-sm text-gray-700">Select All</span>
                                    </label>
                                </div>
                                
                                <!-- User List -->
                                <div id="usersList" class="space-y-2">
                                    @php
                                        $currentPrivilege = null;
                                        $currentRepresentativeType = null;
                                    @endphp
                                    @foreach($users as $user)
                                        @if($currentPrivilege !== $user->privilege)
                                            @if($currentPrivilege !== null)
                                                </div>
                                            @endif
                                            <div class="user-list-header mb-2 mt-3 first:mt-0">
                                                <h5 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                                                    @if($user->privilege === 'user')
                                                        Board Members
                                                    @elseif($user->privilege === 'consec')
                                                        CONSEC Accounts
                                                    @else
                                                        {{ ucfirst($user->privilege ?? 'Other') }}
                                                    @endif
                                                </h5>
                                            </div>
                                            <div class="space-y-2">
                                            @php
                                                $currentPrivilege = $user->privilege;
                                                $currentRepresentativeType = null;
                                            @endphp
                                        @endif
                                        
                                        @if($user->privilege === 'user' && $currentRepresentativeType !== $user->representative_type)
                                            @if($currentRepresentativeType !== null)
                                                </div>
                                            @endif
                                            <div class="user-list-header ml-4 mb-1 mt-2">
                                                <h6 class="text-xs font-medium text-gray-600 uppercase tracking-wide">
                                                    @if($user->representative_type === 'Board Member')
                                                        Board Members
                                                    @elseif($user->representative_type === 'Authorized Representative')
                                                        Authorized Representatives
                                                    @else
                                                        {{ $user->representative_type ?? 'Other' }}
                                                    @endif
                                                </h6>
                                            </div>
                                            <div class="ml-4 space-y-2">
                                            @php
                                                $currentRepresentativeType = $user->representative_type;
                                            @endphp
                                        @endif
                                        
                                        @php
                                            $profilePic = 'https://ui-avatars.com/api/?name=' . urlencode($user->first_name . ' ' . $user->last_name) . '&size=64&background=055498&color=fff';
                                            if ($user->profile_picture) {
                                                $media = \App\Models\MediaLibrary::find($user->profile_picture);
                                                if ($media) {
                                                    $profilePic = asset('storage/' . $media->file_path);
                                                }
                                            }
                                        @endphp
                                        <label class="flex items-center space-x-3 p-2 hover:bg-gray-50 rounded cursor-pointer user-item {{ $user->privilege === 'user' ? 'ml-4' : '' }}">
                                            <input 
                                                type="checkbox" 
                                                name="allowed_users[]" 
                                                value="{{ $user->id }}"
                                                {{ $notice->allowedUsers->contains($user->id) ? 'checked' : '' }}
                                                class="user-checkbox h-4 w-4 text-[#055498] border-gray-300 rounded focus:ring-[#055498] flex-shrink-0"
                                            >
                                            <img src="{{ $profilePic }}" alt="{{ $user->first_name }} {{ $user->last_name }}" class="w-10 h-10 rounded-full object-cover border-2 flex-shrink-0" style="border-color: #055498;">
                                            <div class="flex-1 min-w-0">
                                                <span class="text-sm font-medium text-gray-700 block truncate">
                                                    {{ $user->first_name }} {{ $user->last_name }}
                                                </span>
                                                <span class="text-xs text-gray-500 block truncate">{{ $user->email }}</span>
                                                @if($user->governmentAgency)
                                                    <span class="text-xs text-gray-400 block truncate">{{ $user->governmentAgency->name }}</span>
                                                @endif
                                            </div>
                                            @if($user->privilege === 'consec')
                                                <span class="ml-auto px-2 py-0.5 text-xs rounded font-medium flex-shrink-0" style="background-color: #055498; color: #ffffff;">CONSEC</span>
                                            @endif
                                        </label>
                                    @endforeach
                                    @if($currentPrivilege === 'user' && $currentRepresentativeType !== null)
                                        </div>
                                    @endif
                                    @if($currentPrivilege !== null)
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <span class="text-red-500 text-sm hidden" id="allowed_users-error"></span>
                        </div>

                        <!-- CC Emails -->
                        <div class="pt-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">CC Emails</label>
                            <p class="text-xs text-gray-500 mb-3">Add non-registered users who should receive this notice</p>
                            
                            <div id="ccEmailsContainer" class="space-y-3 mb-3 max-h-[400px] overflow-y-auto">
                                <!-- CC emails will be added here dynamically by JavaScript -->
                            </div>
                            
                            <button 
                                type="button" 
                                id="addCcEmailBtn"
                                class="w-full px-4 py-2 text-sm text-[#055498] border border-[#055498] rounded-lg hover:bg-[#055498] hover:text-white transition-colors"
                            >
                                <i class="fas fa-plus mr-2"></i>Add CC Email
                            </button>
                            
                            <span class="text-red-500 text-sm hidden" id="cc_emails-error"></span>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex flex-col space-y-3 pt-4 border-t">
                            <button 
                                type="submit" 
                                id="submitBtn"
                                class="w-full px-6 py-3 text-white rounded-lg font-semibold transition-all duration-300 transform hover:scale-105 shadow-lg"
                                style="background: linear-gradient(135deg, #055498 0%, #123a60 100%);"
                            >
                                <span id="submitBtnText">Update Notice</span>
                            </button>
                            <a 
                                href="{{ route('admin.notices.index') }}" 
                                class="w-full px-6 py-3 text-center border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition"
                            >
                                Cancel
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

    // Configure SweetAlert Toast for top right notifications
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
    });
    $(document).ready(function() {
        // Initialize form based on current notice type
        const currentNoticeType = $('#notice_type').val();
        if (currentNoticeType === 'Agenda') {
            $('#titleTextContainer').hide();
            $('#title').prop('required', false);
            $('#titleDropdownContainer').show();
            $('#title_dropdown').prop('required', true);
            $('#noOfAttendeesContainer').hide();
            $('#boardRegulationsContainer').hide();
            $('#boardResolutionsContainer').hide();
        } else if (currentNoticeType === 'Board Issuances') {
            $('#titleTextContainer').show();
            $('#title').prop('required', true);
            $('#titleDropdownContainer').hide();
            $('#title_dropdown').prop('required', false);
            $('#noOfAttendeesContainer').show();
            $('#boardRegulationsContainer').show();
            $('#boardResolutionsContainer').show();
        } else {
            $('#titleTextContainer').show();
            $('#title').prop('required', true);
            $('#titleDropdownContainer').hide();
            $('#title_dropdown').prop('required', false);
            $('#noOfAttendeesContainer').hide();
            $('#boardRegulationsContainer').hide();
            $('#boardResolutionsContainer').hide();
        }

        // Initialize meeting link visibility
        const currentMeetingType = $('#meeting_type').val();
        if (currentMeetingType === 'online' || currentMeetingType === 'hybrid') {
            $('#meetingLinkContainer').show();
            $('#meeting_link').prop('required', true);
        } else {
            $('#meetingLinkContainer').hide();
            $('#meeting_link').prop('required', false);
        }

        // Handle notice type change
        $('#notice_type').on('change', function() {
            const noticeType = $(this).val();
            const titleTextContainer = $('#titleTextContainer');
            const titleDropdownContainer = $('#titleDropdownContainer');
            const titleInput = $('#title');
            const titleDropdown = $('#title_dropdown');
            const relatedNoticeId = $('#related_notice_id');

            const noOfAttendeesContainer = $('#noOfAttendeesContainer');
            const noOfAttendeesInput = $('#no_of_attendees');
            const boardRegulationsContainer = $('#boardRegulationsContainer');
            const boardResolutionsContainer = $('#boardResolutionsContainer');
            
            if (noticeType === 'Agenda') {
                titleTextContainer.hide();
                titleInput.prop('required', false);
                titleDropdownContainer.show();
                titleDropdown.prop('required', true);
                noOfAttendeesContainer.hide();
                noOfAttendeesInput.val('');
                boardRegulationsContainer.hide();
                boardResolutionsContainer.hide();
            } else if (noticeType === 'Board Issuances') {
                titleTextContainer.show();
                titleInput.prop('required', true);
                titleDropdownContainer.hide();
                titleDropdown.prop('required', false);
                titleDropdown.val('');
                relatedNoticeId.val('');
                noOfAttendeesContainer.show();
                boardRegulationsContainer.show();
                boardResolutionsContainer.show();
            } else {
                titleTextContainer.show();
                titleInput.prop('required', true);
                titleDropdownContainer.hide();
                titleDropdown.prop('required', false);
                titleDropdown.val('');
                relatedNoticeId.val('');
                noOfAttendeesContainer.hide();
                noOfAttendeesInput.val('');
                boardRegulationsContainer.hide();
                boardResolutionsContainer.hide();
            }
        });

        // Handle title dropdown change (for Agenda)
        $('#title_dropdown').on('change', function() {
            const selectedOption = $(this).find('option:selected');
            const noticeId = selectedOption.val();
            const noticeTitle = selectedOption.data('title');
            $('#related_notice_id').val(noticeId);
            // Also set the title input value for form submission
            $('#title').val(noticeTitle);
        });

        // Handle meeting type change
        $('#meeting_type').on('change', function() {
            const meetingType = $(this).val();
            const meetingLinkContainer = $('#meetingLinkContainer');
            const meetingLinkInput = $('#meeting_link');

            if (meetingType === 'online' || meetingType === 'hybrid') {
                meetingLinkContainer.show();
                meetingLinkInput.prop('required', true);
            } else {
                meetingLinkContainer.hide();
                meetingLinkInput.prop('required', false);
                meetingLinkInput.val('');
            }
        });

        // Search functionality for Board Regulations
        $('#boardRegulationsSearch').on('keyup', function() {
            const searchTerm = $(this).val().toLowerCase();
            $('.regulation-item').each(function() {
                const title = $(this).data('title') || '';
                const date = $(this).data('date') || '';
                if (title.includes(searchTerm) || date.includes(searchTerm)) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });

        // Search functionality for Board Resolutions
        $('#boardResolutionsSearch').on('keyup', function() {
            const searchTerm = $(this).val().toLowerCase();
            $('.resolution-item').each(function() {
                const title = $(this).data('title') || '';
                const date = $(this).data('date') || '';
                if (title.includes(searchTerm) || date.includes(searchTerm)) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });

        // User search filter
        $('#userSearch').on('input', function() {
            const searchTerm = $(this).val().toLowerCase().trim();
            $('.user-item').each(function() {
                const text = $(this).text().toLowerCase();
                if (text.includes(searchTerm)) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
            // Hide section headers (Board Members / Authorized Representatives) when searching so they don't repeat
            if (searchTerm.length > 0) {
                $('#usersList .user-list-header').hide();
            } else {
                $('#usersList .user-list-header').show();
            }
            updateSelectAllState();
        });

        // Select All functionality
        function updateSelectAllState() {
            const visibleCheckboxes = $('.user-item:visible .user-checkbox');
            const checkedCheckboxes = $('.user-item:visible .user-checkbox:checked');
            const selectAllCheckbox = $('#selectAllUsers');

            if (visibleCheckboxes.length === 0) {
                selectAllCheckbox.prop('indeterminate', false).prop('checked', false);
                return;
            }

            if (checkedCheckboxes.length === 0) {
                selectAllCheckbox.prop('indeterminate', false).prop('checked', false);
            } else if (checkedCheckboxes.length === visibleCheckboxes.length) {
                selectAllCheckbox.prop('indeterminate', false).prop('checked', true);
            } else {
                selectAllCheckbox.prop('indeterminate', true).prop('checked', false);
            }
        }

        $('#selectAllUsers').on('change', function() {
            const isChecked = $(this).prop('checked');
            $('.user-item:visible .user-checkbox').prop('checked', isChecked);
        });

        $(document).on('change', '.user-checkbox', function() {
            updateSelectAllState();
        });

        updateSelectAllState();
    });

    // CC Emails Management
    $(document).ready(function() {
        let ccEmailIndex = 0;
        let agenciesData = [];
        
        function addCcEmailEntry(data = {}) {
            const index = ccEmailIndex++;
            const entryHtml = `
                <div class="cc-email-entry border border-gray-300 rounded-lg p-3 bg-gray-50" data-index="${index}">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-medium text-gray-600">CC Email #${index + 1}</span>
                        <button type="button" class="remove-cc-email text-red-500 hover:text-red-700 text-sm" data-index="${index}">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="space-y-2">
                        <input 
                            type="text" 
                            name="cc_emails[${index}][name]" 
                            placeholder="Full Name *" 
                            value="${(data.name || '').replace(/"/g, '&quot;')}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none text-sm"
                            required
                        >
                        <input 
                            type="email" 
                            name="cc_emails[${index}][email]" 
                            placeholder="Email Address *" 
                            value="${(data.email || '').replace(/"/g, '&quot;')}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none text-sm"
                            required
                        >
                        <input 
                            type="text" 
                            name="cc_emails[${index}][position]" 
                            placeholder="Position" 
                            value="${(data.position || '').replace(/"/g, '&quot;')}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none text-sm"
                        >
                        <select 
                            name="cc_emails[${index}][agency]" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none text-sm cc-agency-select"
                            data-index="${index}"
                            data-agency-value="${(data.agency || '').replace(/"/g, '&quot;')}"
                        >
                            <option value="">Select Agency</option>
                        </select>
                    </div>
                </div>
            `;
            $('#ccEmailsContainer').append(entryHtml);
            
            // Populate agency select if agencies are already loaded
            const newSelect = $(`#ccEmailsContainer .cc-agency-select[data-index="${index}"]`);
            if (agenciesData.length > 0) {
                populateAgencySelect(newSelect, data.agency || '');
            }
        }
        
        // Function to populate agency select
        function populateAgencySelect(selectElement, selectedValue = '') {
            const select = $(selectElement);
            select.html('<option value="">Select Agency</option>');
            agenciesData.forEach(agency => {
                // Match by ID (preferred) or by name (for legacy data)
                const selected = (selectedValue == agency.id || selectedValue == agency.name || selectedValue.toString() == agency.id.toString()) ? 'selected' : '';
                select.append(`<option value="${agency.id}" ${selected}>${agency.name}${agency.code ? ' (' + agency.code + ')' : ''}</option>`);
            });
        }
        
        // Initialize existing CC emails first (don't wait for agencies)
        @php
            $ccEmails = $notice->cc_emails;
            // Debug: log the CC emails data
            // \Log::info('CC Emails in edit form:', ['cc_emails' => $ccEmails, 'type' => gettype($ccEmails)]);
        @endphp
        @if($ccEmails && is_array($ccEmails) && count($ccEmails) > 0)
            @foreach($ccEmails as $cc)
                @php
                    $agencyValue = $cc['agency'] ?? '';
                    // If it's numeric, it's an ID, otherwise it's a legacy name
                    if (is_numeric($agencyValue)) {
                        $agencyValue = (int)$agencyValue;
                    }
                @endphp
                addCcEmailEntry({
                    name: '{{ addslashes($cc['name'] ?? '') }}',
                    email: '{{ addslashes($cc['email'] ?? '') }}',
                    position: '{{ addslashes($cc['position'] ?? '') }}',
                    agency: '{{ $agencyValue }}'
                });
            @endforeach
        @endif
        
        // Load agencies and populate selects
        $.ajax({
            url: '/api/government-agencies',
            method: 'GET',
            success: function(agencies) {
                agenciesData = agencies;
                // Populate all existing agency selects
                $('.cc-agency-select').each(function() {
                    const select = $(this);
                    // Get agency value from data attribute (set when entry was created)
                    const agencyValue = select.attr('data-agency-value') || select.data('agency-value') || '';
                    populateAgencySelect(select, agencyValue);
                });
            },
            error: function() {
                console.error('Failed to load government agencies');
            }
        });
        
        $('#addCcEmailBtn').on('click', function() {
            addCcEmailEntry();
            // Populate the newly added agency select
            const newSelect = $('#ccEmailsContainer .cc-agency-select').last();
            if (agenciesData.length > 0) {
                populateAgencySelect(newSelect);
            }
        });
        
        $(document).on('click', '.remove-cc-email', function() {
            const index = $(this).data('index');
            $(`.cc-email-entry[data-index="${index}"]`).fadeOut(300, function() {
                $(this).remove();
            });
        });
    });

    // Store uploaded attachment IDs (include existing attachments)
    let uploadedAttachmentIds = @json($notice->attachments ?? []);

    // Handle file preview and upload
    const attachmentsInput = document.getElementById('attachments');
    const attachmentsPreview = document.getElementById('attachmentsPreview');
    const attachmentsDropZone = document.getElementById('attachmentsDropZone');
    const dropZoneContent = document.getElementById('dropZoneContent');
    const dropZoneActive = document.getElementById('dropZoneActive');

    // Click to upload
    dropZoneContent.addEventListener('click', () => {
        attachmentsInput.click();
    });

    // Drag and drop handlers
    attachmentsDropZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        e.stopPropagation();
        attachmentsDropZone.classList.add('border-[#055498]', 'bg-blue-50/50');
        dropZoneContent.classList.add('hidden');
        dropZoneActive.classList.remove('hidden');
    });

    attachmentsDropZone.addEventListener('dragleave', (e) => {
        e.preventDefault();
        e.stopPropagation();
        attachmentsDropZone.classList.remove('border-[#055498]', 'bg-blue-50/50');
        dropZoneContent.classList.remove('hidden');
        dropZoneActive.classList.add('hidden');
    });

    attachmentsDropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        e.stopPropagation();
        attachmentsDropZone.classList.remove('border-[#055498]', 'bg-blue-50/50');
        dropZoneContent.classList.remove('hidden');
        dropZoneActive.classList.add('hidden');

        const files = Array.from(e.dataTransfer.files);
        if (files.length > 0) {
            handleFilesUpload(files);
        }
    });

    // Handle file upload function
    async function handleFilesUpload(files) {
        if (files.length === 0) return;

        // Validate file sizes
        for (const file of files) {
            if (file.size > 30 * 1024 * 1024) {
                Swal.fire({
                    icon: 'error',
                    title: 'File Too Large',
                    text: `File "${file.name}" exceeds 30MB limit.`,
                });
                return;
            }
        }

        // Show loading state
        if (uploadedAttachmentIds.length === 0) {
            attachmentsPreview.innerHTML = '<div class="col-span-full text-center py-4"><i class="fas fa-spinner fa-spin"></i> Uploading files...</div>';
        } else {
            attachmentsPreview.insertAdjacentHTML('beforeend', '<div class="col-span-full text-center py-4"><i class="fas fa-spinner fa-spin"></i> Uploading additional files...</div>');
        }

        // Upload files to media library
        const uploadFormData = new FormData();
        files.forEach(file => {
            uploadFormData.append('files[]', file);
        });

        try {
            const uploadResponse = await axios.post('{{ route("admin.media-library.store") }}', uploadFormData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            });

            if (uploadResponse.data.success && uploadResponse.data.files) {
                // Add new IDs to existing ones
                const newIds = uploadResponse.data.files.map(file => file.id);
                uploadedAttachmentIds = [...uploadedAttachmentIds, ...newIds];
                
                // Remove loading message
                const loadingMsg = attachmentsPreview.querySelector('.col-span-full.text-center');
                if (loadingMsg) {
                    loadingMsg.remove();
                }
                
                // Clear error state when files are uploaded
                $('#attachments-error').addClass('hidden');
                attachmentsDropZone.classList.remove('border-red-500');
                
                // Display previews for new files
                uploadResponse.data.files.forEach(file => {
                    const isImage = file.type.startsWith('image/');
                    const isPdf = file.type === 'application/pdf' || file.name.toLowerCase().endsWith('.pdf');
                    const previewHtml = `
                        <div class="relative border rounded-lg p-2 attachment-item" data-file-id="${file.id}">
                            <button type="button" class="absolute top-1 right-1 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center hover:bg-red-600 transition-colors delete-attachment-btn" data-file-id="${file.id}" data-file-name="${file.name}" title="Remove attachment">
                                <i class="fas fa-times text-xs"></i>
                            </button>
                            ${isImage ? 
                                `<img src="${file.url}" alt="${file.name}" class="w-full h-24 object-cover rounded">` :
                                isPdf ?
                                `<div class="w-full h-24 flex flex-col items-center justify-center bg-gray-100 rounded">
                                    <i class="fas fa-file-pdf text-3xl text-red-500 mb-1"></i>
                                </div>` :
                                `<div class="w-full h-24 flex items-center justify-center bg-gray-100 rounded">
                                    <i class="fas fa-file text-3xl text-gray-400"></i>
                                </div>`
                            }
                            <p class="text-xs text-gray-600 mt-1 truncate" title="${file.name}">${file.name}</p>
                        </div>
                    `;
                    attachmentsPreview.insertAdjacentHTML('beforeend', previewHtml);
                });
            } else {
                const errorMsg = uploadResponse.data?.message || 'Failed to upload files. Please try again.';
                attachmentsPreview.innerHTML = '<div class="col-span-full text-center py-4 text-red-500">' + errorMsg + '</div>';
            }
        } catch (error) {
            const errorMsg = error.response?.data?.message || error.message || 'Error uploading files';
            attachmentsPreview.innerHTML = '<div class="col-span-full text-center py-4 text-red-500">' + errorMsg + '</div>';
            console.error('Upload error:', error);
        }
    }

    // Handle file input change
    attachmentsInput.addEventListener('change', async function(e) {
        const files = Array.from(e.target.files);
        if (files.length > 0) {
            await handleFilesUpload(files);
            // Reset input to allow selecting same files again
            attachmentsInput.value = '';
        }
    });

    // Delete attachment handler with SweetAlert
    $(document).on('click', '.delete-attachment-btn', async function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const fileId = $(this).data('file-id');
        const attachmentItem = $(this).closest('.attachment-item');
        const fileName = $(this).data('file-name') || attachmentItem.find('p').text().trim();

        const result = await Swal.fire({
            title: 'Remove Attachment?',
            text: `Are you sure you want to remove "${fileName}"?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#EF4444',
            cancelButtonColor: '#6B7280',
            confirmButtonText: 'Yes, remove it',
            cancelButtonText: 'Cancel'
        });

        if (result.isConfirmed) {
            // Remove from uploadedAttachmentIds array
            uploadedAttachmentIds = uploadedAttachmentIds.filter(id => id !== fileId);
            
            // Remove from preview with animation
            attachmentItem.fadeOut(300, function() {
                $(this).remove();
            });

            Toast.fire({
                icon: 'success',
                title: 'Attachment removed successfully'
            });
        }
        
        return false;
    });

    // Form submission
    $('#editNoticeForm').on('submit', function(e) {
        e.preventDefault();

        // Clear previous errors
        $('.text-red-500').addClass('hidden');

        // Validate notice type
        const noticeType = $('#notice_type').val();
        if (!noticeType) {
            $('#notice_type-error').text('Please select a notice type.').removeClass('hidden');
            return;
        }

        // Validate title or related notice
        if (noticeType === 'Agenda') {
            const titleDropdown = $('#title_dropdown').val();
            if (!titleDropdown) {
                $('#title_dropdown-error').text('Please select a notice from the dropdown.').removeClass('hidden');
                return;
            }
            // Ensure title is set from dropdown
            const selectedOption = $('#title_dropdown').find('option:selected');
            $('#title').val(selectedOption.data('title'));
        } else {
            const title = $('#title').val().trim();
            if (!title) {
                $('#title-error').text('Please enter a title.').removeClass('hidden');
                return;
            }
        }

        // Validate meeting type and link
        const meetingType = $('#meeting_type').val();
        if (meetingType === 'online' || meetingType === 'hybrid') {
            const meetingLink = $('#meeting_link').val().trim();
            if (!meetingLink) {
                $('#meeting_link-error').text('Please enter a meeting link.').removeClass('hidden');
                return;
            }
        }

        // Validate allowed users
        const allowedUsers = $('input[name="allowed_users[]"]:checked').length;
        if (allowedUsers === 0) {
            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                text: 'Please select at least one allowed user.',
            });
            return;
        }

        // Show loading
        const submitBtn = $('#submitBtn');
        const submitBtnText = $('#submitBtnText');
        submitBtn.prop('disabled', true);
        submitBtnText.text('Updating...');

        // Create FormData
        const formData = new FormData(this);

        // Add uploaded attachment IDs
        if (uploadedAttachmentIds.length > 0) {
            uploadedAttachmentIds.forEach(id => {
                formData.append('attachments[]', id);
            });
        }

        // Submit via AJAX
        $.ajax({
            url: '{{ route("admin.notices.update", $notice->id) }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message,
                    }).then(() => {
                        window.location.href = response.redirect;
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: response.message || 'Failed to update notice.',
                    });
                    submitBtn.prop('disabled', false);
                    submitBtnText.text('Update Notice');
                }
            },
            error: function(xhr) {
                const errors = xhr.responseJSON?.errors || {};
                Object.keys(errors).forEach(function(key) {
                    $('#' + key.replace('.', '_') + '-error').text(errors[key][0]).removeClass('hidden');
                });
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: xhr.responseJSON?.message || 'Failed to update notice.',
                });
                submitBtn.prop('disabled', false);
                submitBtnText.text('Update Notice');
            }
        });
    });
</script>
@endpush

