@extends('admin.layout')

@section('title', 'Portal Manager')

@php
    $pageTitle = 'Portal Manager';
    $headerActions = [
        [
            'url' => route('admin.dashboard'),
            'text' => 'Back to Dashboard',
            'icon' => 'fas fa-arrow-left',
            'class' => 'px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-gray-700 transition-colors'
        ]
    ];
    $hideDefaultActions = false; // Show default notifications/messages/user dropdown
@endphp

@push('styles')
<style>
    .file-upload-area {
        border: 2px dashed #cbd5e0;
        transition: all 0.3s ease;
    }

    .file-upload-area.dragover {
        border-color: #055498;
        background-color: rgba(5, 84, 152, 0.1);
    }

    .file-item {
        transition: all 0.2s ease;
    }

    .file-item:hover {
        background-color: #f7fafc;
    }

    .tab-button {
        transition: all 0.3s ease;
    }

    .tab-button.active {
        border-bottom: 3px solid #055498 !important;
        color: #055498 !important;
        font-weight: 600;
    }

    .tab-content {
        display: none;
    }

    .tab-content.active {
        display: block;
    }
</style>
@endpush

@section('content')
<div class="p-6">
    <div class="max-w-6xl mx-auto">
        <!-- Page Header -->
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-2">CONSEC Portal Manager</h2>
            <p class="text-gray-600">Customize dashboard and send emails to board members, authorized representatives, and CONSEC</p>
        </div>

        <!-- Tabs Navigation -->
        <div class="bg-white rounded-t-lg border-b border-gray-200">
            <div class="flex space-x-1 px-4">
                <button type="button" class="tab-button px-6 py-4 text-sm font-medium text-gray-600 hover:text-gray-800 active" data-tab="dashboard">
                    <i class="fas fa-tachometer-alt mr-2"></i>Dashboard Customization
                </button>
                <button type="button" class="tab-button px-6 py-4 text-sm font-medium text-gray-600 hover:text-gray-800" data-tab="emails">
                    <i class="fas fa-envelope mr-2"></i>Send Emails
                </button>
            </div>
        </div>

        <!-- Portal Manager Form -->
        <form id="portalManagerForm" class="bg-white rounded-b-lg shadow-md">
            <!-- Dashboard Customization Tab -->
            <div id="tab-dashboard" class="tab-content active p-6">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Dashboard Layout</label>
                        <select id="dashboard_layout" name="dashboard_layout" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-transparent" style="focus:ring-color: #055498;">
                            <option value="default">Default Layout</option>
                            <option value="compact">Compact Layout</option>
                            <option value="detailed">Detailed Layout</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Widget Configuration</label>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <label class="flex items-center p-3 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                                <input type="checkbox" name="widgets[]" value="announcements" class="mr-3" checked>
                                <span class="text-sm text-gray-700">Announcements Widget</span>
                            </label>
                            <label class="flex items-center p-3 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                                <input type="checkbox" name="widgets[]" value="calendar" class="mr-3" checked>
                                <span class="text-sm text-gray-700">Calendar Widget</span>
                            </label>
                            <label class="flex items-center p-3 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                                <input type="checkbox" name="widgets[]" value="notifications" class="mr-3" checked>
                                <span class="text-sm text-gray-700">Notifications Widget</span>
                            </label>
                            <label class="flex items-center p-3 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                                <input type="checkbox" name="widgets[]" value="statistics" class="mr-3">
                                <span class="text-sm text-gray-700">Statistics Widget</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Send Emails Tab -->
            <div id="tab-emails" class="tab-content p-6">
                <div class="space-y-4">
                    <!-- Recipients -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Recipients *</label>
                        <div class="space-y-2">
                            <label class="flex items-center p-3 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                                <input type="checkbox" name="recipients[]" value="board_members" class="mr-3" id="recipient_board_members">
                                <div class="flex-1">
                                    <span class="text-sm font-medium text-gray-700">Board Members</span>
                                    <p class="text-xs text-gray-500 mt-1">Send to all registered board members</p>
                                </div>
                            </label>
                            <label class="flex items-center p-3 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                                <input type="checkbox" name="recipients[]" value="authorized_representatives" class="mr-3" id="recipient_representatives">
                                <div class="flex-1">
                                    <span class="text-sm font-medium text-gray-700">Authorized Representatives</span>
                                    <p class="text-xs text-gray-500 mt-1">Send to all authorized representatives</p>
                                </div>
                            </label>
                            <label class="flex items-center p-3 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                                <input type="checkbox" name="recipients[]" value="consec" class="mr-3" id="recipient_consec">
                                <div class="flex-1">
                                    <span class="text-sm font-medium text-gray-700">CONSEC</span>
                                    <p class="text-xs text-gray-500 mt-1">Send to CONSEC members</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Email Subject -->
                    <div>
                        <label for="email_subject" class="block text-sm font-medium text-gray-700 mb-2">Email Subject *</label>
                        <input type="text" id="email_subject" name="email_subject" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-transparent"
                            style="focus:ring-color: #055498;"
                            placeholder="Enter email subject">
                    </div>

                    <!-- Email Content -->
                    <div>
                        <label for="email_content" class="block text-sm font-medium text-gray-700 mb-2">Email Content *</label>
                        <textarea id="email_content" name="email_content" rows="6" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-transparent"
                            style="focus:ring-color: #055498;"
                            placeholder="Enter email content..."></textarea>
                    </div>

                    <!-- File Attachments -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Attachments</label>
                        <div class="file-upload-area rounded-lg p-6 text-center cursor-pointer" id="fileUploadArea">
                            <input type="file" id="fileInput" name="attachments[]" multiple accept=".pdf,.xlsx,.xls,.pptx,.ppt,.docx,.doc,.mov,.mp4" class="hidden">
                            <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-3"></i>
                            <p class="text-sm text-gray-600 mb-1">Click to upload or drag and drop</p>
                            <p class="text-xs text-gray-500">Supported formats: PDF, Excel, PowerPoint, Word, MOV (Max: 25MB per file)</p>
                        </div>
                        <div id="fileList" class="mt-4 space-y-2"></div>
                    </div>

                    <!-- Remarks Section -->
                    <div>
                        <label for="remarks" class="block text-sm font-medium text-gray-700 mb-2">Additional Notes</label>
                        <textarea id="remarks" name="remarks" rows="4"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                            placeholder="Enter any additional remarks or notes..."></textarea>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end space-x-4 p-6 border-t border-gray-200">
                <button type="button" id="cancelBtn" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancel
                </button>
                <button type="submit" id="submitBtn" class="px-6 py-2 text-white rounded-lg transition-colors" style="background-color: #055498;" onmouseover="this.style.backgroundColor='#123a60'" onmouseout="this.style.backgroundColor='#055498'">
                    <i class="fas fa-paper-plane mr-2"></i>Send Email
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Tab Switching
        $('.tab-button').on('click', function() {
            const tabId = $(this).data('tab');
            
            // Remove active class from all tabs and buttons
            $('.tab-button').removeClass('active');
            $('.tab-content').removeClass('active');
            
            // Add active class to clicked tab button and corresponding content
            $(this).addClass('active');
            $('#tab-' + tabId).addClass('active');
        });

        // File Upload Handling
        const fileInput = $('#fileInput');
        const fileUploadArea = $('#fileUploadArea');
        const fileList = $('#fileList');
        let selectedFiles = [];

        // Click to upload
        fileUploadArea.on('click', function() {
            fileInput.click();
        });

        // Drag and drop
        fileUploadArea.on('dragover', function(e) {
            e.preventDefault();
            $(this).addClass('dragover');
        });

        fileUploadArea.on('dragleave', function() {
            $(this).removeClass('dragover');
        });

        fileUploadArea.on('drop', function(e) {
            e.preventDefault();
            $(this).removeClass('dragover');
            const files = e.originalEvent.dataTransfer.files;
            handleFiles(files);
        });

        // File input change
        fileInput.on('change', function() {
            handleFiles(this.files);
        });

        function handleFiles(files) {
            Array.from(files).forEach(file => {
                if (file.size > 25 * 1024 * 1024) {
                    Swal.fire('Error', `File ${file.name} exceeds 25MB limit.`, 'error');
                    return;
                }
                selectedFiles.push(file);
                displayFile(file);
            });
        }

        function displayFile(file) {
            const fileItem = $(`
                <div class="file-item flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-200">
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-file text-blue-500"></i>
                        <div>
                            <p class="text-sm font-medium text-gray-800">${file.name}</p>
                            <p class="text-xs text-gray-500">${(file.size / 1024 / 1024).toFixed(2)} MB</p>
                        </div>
                    </div>
                    <button type="button" class="remove-file text-red-500 hover:text-red-700" data-filename="${file.name}">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `);
            fileList.append(fileItem);
        }

        // Remove file
        $(document).on('click', '.remove-file', function() {
            const filename = $(this).data('filename');
            selectedFiles = selectedFiles.filter(f => f.name !== filename);
            $(this).closest('.file-item').remove();
        });

        // Form submission
        $('#portalManagerForm').on('submit', function(e) {
            e.preventDefault();
            
            const recipients = $('input[name="recipients[]"]:checked').map(function() {
                return $(this).val();
            }).get();

            if (recipients.length === 0) {
                Swal.fire('Error', 'Please select at least one recipient.', 'error');
                return;
            }

            const formData = new FormData(this);
            selectedFiles.forEach(file => {
                formData.append('attachments[]', file);
            });

            Swal.fire({
                title: 'Sending...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: '{{ route("admin.portal-manager.send-email") }}',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message || 'Email sent successfully.',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        $('#portalManagerForm')[0].reset();
                        selectedFiles = [];
                        fileList.empty();
                    });
                },
                error: function(xhr) {
                    const error = xhr.responseJSON?.message || 'An error occurred while sending the email.';
                    Swal.fire('Error', error, 'error');
                }
            });
        });

        // Cancel button
        $('#cancelBtn').on('click', function() {
            Swal.fire({
                title: 'Cancel?',
                text: 'Are you sure you want to cancel? All unsaved changes will be lost.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, Cancel',
                cancelButtonText: 'No, Continue'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '{{ route("admin.dashboard") }}';
                }
            });
        });
    });
</script>
@endpush

