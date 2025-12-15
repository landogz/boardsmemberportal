<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Portal Manager - Admin Dashboard</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('images/favicon.ico') }}" type="image/png" />
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    
    <style>
        #sidebar {
            transition: transform 0.3s ease-in-out;
        }
        
        #sidebar.hidden {
            transform: translateX(-100%);
        }
        
        @media (min-width: 1024px) {
            #sidebar {
                transform: translateX(0);
            }
        }
        
        .dropdown-menu {
            display: none;
        }
        
        .dropdown.show .dropdown-menu {
            display: block;
        }
        
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 3px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
        
        .online_animation {
            display: inline-block;
            width: 8px;
            height: 8px;
            background-color: #10b981;
            border-radius: 50%;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.5;
            }
        }

        .file-upload-area {
            border: 2px dashed #cbd5e0;
            transition: all 0.3s ease;
        }

        .file-upload-area.dragover {
            border-color: #3b82f6;
            background-color: #eff6ff;
        }

        .file-item {
            transition: all 0.2s ease;
        }

        .file-item:hover {
            background-color: #f7fafc;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="flex h-screen overflow-hidden">
        @include('admin.partials.sidebar')
        
        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden lg:ml-0">
        <!-- Topbar -->
        <header class="bg-white shadow-sm border-b border-gray-200">
            <div class="flex items-center justify-between px-4 py-3">
                <div class="flex items-center space-x-4">
                    <button id="sidebarCollapse" class="lg:hidden p-2 rounded-lg text-gray-600 hover:bg-gray-100">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    <h1 class="text-xl font-semibold text-gray-800">Portal Manager</h1>
                </div>
                
                <div class="flex items-center space-x-4">
                    <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-gray-700 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
                    </a>
                </div>
            </div>
        </header>

        <!-- Main Content Area -->
        <main class="flex-1 overflow-y-auto custom-scrollbar bg-gray-50 p-6">
            <div class="max-w-6xl mx-auto">
                <!-- Page Header -->
                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-gray-800 mb-2">CONSEC Portal Manager</h2>
                    <p class="text-gray-600">Customize dashboard and send emails to board members, authorized representatives, and CONSEC</p>
                </div>

                <!-- Portal Manager Form -->
                <form id="portalManagerForm" class="space-y-6">
                    <!-- Dashboard Customization Section -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex items-center mb-4">
                            <i class="fas fa-tachometer-alt text-blue-500 text-xl mr-3"></i>
                            <h3 class="text-lg font-semibold text-gray-800">Dashboard Customization</h3>
                        </div>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Dashboard Layout</label>
                                <select id="dashboard_layout" name="dashboard_layout" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
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

                    <!-- Send Emails Section -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex items-center mb-4">
                            <i class="fas fa-envelope text-green-500 text-xl mr-3"></i>
                            <h3 class="text-lg font-semibold text-gray-800">Send Emails</h3>
                        </div>
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
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                    placeholder="Enter email subject">
                            </div>

                            <!-- Email Content -->
                            <div>
                                <label for="email_content" class="block text-sm font-medium text-gray-700 mb-2">Email Content *</label>
                                <textarea id="email_content" name="email_content" rows="6" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
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
                        </div>
                    </div>

                    <!-- Remarks Section -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex items-center mb-4">
                            <i class="fas fa-comment-alt text-purple-500 text-xl mr-3"></i>
                            <h3 class="text-lg font-semibold text-gray-800">Remarks</h3>
                        </div>
                        <div>
                            <label for="remarks" class="block text-sm font-medium text-gray-700 mb-2">Additional Notes</label>
                            <textarea id="remarks" name="remarks" rows="4"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                placeholder="Enter any additional remarks or notes..."></textarea>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex justify-end space-x-4">
                        <button type="button" id="cancelBtn" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                            Cancel
                        </button>
                        <button type="submit" id="submitBtn" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-paper-plane mr-2"></i>Send Email
                        </button>
                    </div>
                </form>
            </div>
        </main>
        </div>
    </div>
    
    <!-- Overlay for mobile sidebar -->
    <div id="sidebarOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden hidden"></div>
    
    <script>
        $(document).ready(function() {
            // Sidebar Toggle
            $('#sidebarCollapse').on('click', function() {
                $('#sidebar').toggleClass('-translate-x-full');
                $('#sidebarOverlay').toggleClass('hidden');
            });
            
            $('#sidebarOverlay').on('click', function() {
                $('#sidebar').addClass('-translate-x-full');
                $('#sidebarOverlay').addClass('hidden');
            });

            // Menu Toggle
            $('.menu-toggle').on('click', function(e) {
                e.preventDefault();
                const $menuItem = $(this).parent('li');
                const $submenu = $menuItem.find('> ul');
                $submenu.slideToggle();
                $(this).find('.fa-chevron-down').toggleClass('rotate-180');
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

            fileInput.on('change', function() {
                handleFiles(this.files);
            });

            function handleFiles(files) {
                const maxSize = 25 * 1024 * 1024; // 25MB
                const allowedTypes = ['application/pdf', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 
                                     'application/vnd.ms-powerpoint', 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                                     'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                     'video/quicktime', 'video/mp4'];

                Array.from(files).forEach(file => {
                    // Check file size
                    if (file.size > maxSize) {
                        Swal.fire({
                            icon: 'error',
                            title: 'File Too Large',
                            text: `${file.name} exceeds the 25MB limit.`,
                        });
                        return;
                    }

                    // Check file type
                    if (!allowedTypes.includes(file.type)) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Invalid File Type',
                            text: `${file.name} is not a supported file type.`,
                        });
                        return;
                    }

                    // Add to selected files
                    if (!selectedFiles.find(f => f.name === file.name && f.size === file.size)) {
                        selectedFiles.push(file);
                        displayFile(file);
                    }
                });
            }

            function displayFile(file) {
                const fileSize = (file.size / (1024 * 1024)).toFixed(2);
                const fileIcon = getFileIcon(file.type);
                
                const fileItem = $(`
                    <div class="file-item flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-200">
                        <div class="flex items-center flex-1">
                            <i class="${fileIcon} text-blue-500 text-xl mr-3"></i>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-700 truncate">${file.name}</p>
                                <p class="text-xs text-gray-500">${fileSize} MB</p>
                            </div>
                        </div>
                        <button type="button" class="remove-file ml-3 text-red-500 hover:text-red-700" data-filename="${file.name}">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                `);
                
                fileList.append(fileItem);
            }

            function getFileIcon(type) {
                if (type.includes('pdf')) return 'fas fa-file-pdf';
                if (type.includes('excel') || type.includes('spreadsheet')) return 'fas fa-file-excel';
                if (type.includes('powerpoint') || type.includes('presentation')) return 'fas fa-file-powerpoint';
                if (type.includes('word') || type.includes('document')) return 'fas fa-file-word';
                if (type.includes('video')) return 'fas fa-file-video';
                return 'fas fa-file';
            }

            // Remove file
            $(document).on('click', '.remove-file', function() {
                const fileName = $(this).data('filename');
                selectedFiles = selectedFiles.filter(f => f.name !== fileName);
                $(this).closest('.file-item').fadeOut(300, function() {
                    $(this).remove();
                });
            });

            // Form Submission
            $('#portalManagerForm').on('submit', function(e) {
                e.preventDefault();

                // Validate recipients
                const recipients = $('input[name="recipients[]"]:checked');
                if (recipients.length === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'No Recipients Selected',
                        text: 'Please select at least one recipient.',
                    });
                    return;
                }

                // Show confirmation
                Swal.fire({
                    title: 'Confirm Send Email',
                    text: 'Are you sure you want to send this email?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3b82f6',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Yes, Send Email',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Simulate form submission (static design)
                        const submitBtn = $('#submitBtn');
                        const originalText = submitBtn.html();
                        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i>Sending...');

                        // Simulate API call
                        setTimeout(() => {
                            Swal.fire({
                                icon: 'success',
                                title: 'Email Sent Successfully!',
                                text: 'The email has been sent to all selected recipients.',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                // Reset form
                                $('#portalManagerForm')[0].reset();
                                selectedFiles = [];
                                fileList.empty();
                                submitBtn.prop('disabled', false).html(originalText);
                            });
                        }, 1500);
                    }
                });
            });

            // Cancel button
            $('#cancelBtn').on('click', function() {
                Swal.fire({
                    title: 'Cancel Changes?',
                    text: 'Are you sure you want to cancel? All unsaved changes will be lost.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Yes, Cancel',
                    cancelButtonText: 'No, Keep Editing'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = '{{ route("admin.dashboard") }}';
                    }
                });
            });
        });
    </script>
</body>
</html>

