@auth
<div id="pendingNoticesContainer" style="position: fixed; top: 20px; left: 20px; z-index: 9999; max-width: 400px;"></div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
    (function() {
        let displayedNotices = new Set();
        let currentModal = null;
        
        // Set up axios defaults
        if (typeof axios !== 'undefined') {
            axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
        }

        function isOnNoticesPage() {
            const currentPath = window.location.pathname;
            // Check if we're on /notices or /notices/{id}
            return currentPath === '/notices' || currentPath.startsWith('/notices/');
        }

        function fetchPendingNotices() {
            // Don't show modal if user is on notices pages
            if (isOnNoticesPage()) {
                return;
            }
            
            axios.get('{{ route("notices.pending") }}')
                .then(response => {
                    if (response.data.success && response.data.notices.length > 0) {
                        const notices = response.data.notices.filter(notice => !displayedNotices.has(notice.id));
                        
                        if (notices.length > 0) {
                            // Show all notices in a single modal as a list
                            showNoticesListModal(notices);
                            notices.forEach(notice => displayedNotices.add(notice.id));
                        }
                    }
                })
                .catch(error => {
                    console.error('Error fetching pending notices:', error);
                });
        }

        function showNoticesListModal(notices) {
            // Close existing modal if any
            if (currentModal) {
                Swal.close();
            }
            
            // Build list HTML
            let listHTML = '<div class="pending-notices-list" style="text-align: left; max-height: 60vh; overflow-y: auto;">';
            
            notices.forEach((notice, index) => {
                const noticeTypeBadge = getNoticeTypeBadge(notice.notice_type);
                const meetingInfo = notice.meeting_date ? 
                    `<p class="text-xs text-gray-600 mb-1"><i class="fas fa-calendar mr-1"></i>${notice.meeting_date}${notice.meeting_time ? ' at ' + notice.meeting_time : ''}</p>` : 
                    '';
                
                listHTML += `
                    <div class="notice-item" data-notice-id="${notice.id}" style="border-bottom: 1px solid #e5e7eb; padding: 0.75rem 0; ${index === notices.length - 1 ? 'border-bottom: none;' : ''}">
                        <div class="mb-2">
                            ${noticeTypeBadge}
                        </div>
                        <h3 class="notice-title" style="font-size: 0.9rem; font-weight: 600; margin-bottom: 0.25rem; color: #1f2937; line-height: 1.3; word-wrap: break-word;">${notice.title}</h3>
                        ${meetingInfo}
                        <div class="notice-actions mt-2 flex flex-wrap gap-2">
                            <button onclick="handleNoticeAction(${notice.id}, 'accept')" class="btn-accept" style="flex: 1; min-width: 80px; padding: 0.5rem 0.75rem; background: #10b981; color: white; border: none; border-radius: 0.375rem; font-size: 0.75rem; cursor: pointer; font-weight: 500; min-height: 36px;">
                                <i class="fas fa-check mr-1"></i><span class="btn-text">Accept</span>
                            </button>
                            <button onclick="handleNoticeAction(${notice.id}, 'view')" class="btn-view" style="flex: 1; min-width: 80px; padding: 0.5rem 0.75rem; background: #055498; color: white; border: none; border-radius: 0.375rem; font-size: 0.75rem; cursor: pointer; font-weight: 500; min-height: 36px;">
                                <i class="fas fa-eye mr-1"></i><span class="btn-text">View</span>
                            </button>
                            <button onclick="handleNoticeAction(${notice.id}, 'decline')" class="btn-decline" style="flex: 1; min-width: 80px; padding: 0.5rem 0.75rem; background: #ef4444; color: white; border: none; border-radius: 0.375rem; font-size: 0.75rem; cursor: pointer; font-weight: 500; min-height: 36px;">
                                <i class="fas fa-times mr-1"></i><span class="btn-text">Decline</span>
                            </button>
                        </div>
                    </div>
                `;
            });
            
            listHTML += '</div>';

            // Determine modal width based on screen size
            const isMobile = window.innerWidth < 640;
            const isTablet = window.innerWidth >= 640 && window.innerWidth < 1024;
            const modalWidth = isMobile ? '90vw' : (isTablet ? '500px' : '450px');
            const modalPosition = isMobile ? 'center' : 'top-left';

            currentModal = Swal.fire({
                title: `Pending Notices (${notices.length})`,
                html: listHTML,
                icon: 'info',
                width: modalWidth,
                padding: isMobile ? '0.75rem' : '1rem',
                showConfirmButton: false,
                showCancelButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                showCloseButton: true,
                customClass: {
                    popup: 'pending-notice-popup-list',
                    container: 'pending-notice-container',
                    closeButton: 'pending-notice-close-btn'
                },
                didOpen: () => {
                    // Position the modal based on screen size
                    setTimeout(() => {
                        const popup = document.querySelector('.swal2-popup.pending-notice-popup-list');
                        if (popup) {
                            if (isMobile) {
                                // Center on mobile
                                popup.style.position = 'fixed';
                                popup.style.top = '50%';
                                popup.style.left = '50%';
                                popup.style.transform = 'translate(-50%, -50%)';
                                popup.style.margin = '0';
                                popup.style.maxHeight = '85vh';
                                popup.style.zIndex = '9999';
                            } else {
                                // Top left on desktop/tablet
                                popup.style.position = 'fixed';
                                popup.style.top = '20px';
                                popup.style.left = '20px';
                                popup.style.margin = '0';
                                popup.style.transform = 'none';
                                popup.style.zIndex = '9999';
                            }
                        }
                        
                        const backdrop = document.querySelector('.swal2-container.pending-notice-container');
                        if (backdrop) {
                            backdrop.style.pointerEvents = 'none';
                        }
                    }, 100);
                }
            });
        }
        
        // Global function to handle notice actions
        window.handleNoticeAction = function(noticeId, action) {
            if (action === 'accept') {
                acceptNotice(noticeId);
            } else if (action === 'view') {
                window.location.href = `/notices/${noticeId}`;
            } else if (action === 'decline') {
                showDeclineReasonModal(noticeId, '', currentModal);
            }
        };

        function getNoticeTypeBadge(type) {
            const badges = {
                'Notice of Meeting': '<span style="display: inline-flex; align-items: center; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; background: rgba(5, 84, 152, 0.1); color: #055498; border: 1px solid rgba(5, 84, 152, 0.2);"><i class="fas fa-bell mr-1"></i>Notice of Meeting</span>',
                'Agenda': '<span style="display: inline-flex; align-items: center; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; background: rgba(206, 32, 40, 0.1); color: #CE2028; border: 1px solid rgba(206, 32, 40, 0.2);"><i class="fas fa-clipboard-list mr-1"></i>Agenda</span>',
                'Board Issuances': '<span style="display: inline-flex; align-items: center; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; background: rgba(251, 191, 36, 0.1); color: #f59e0b; border: 1px solid rgba(251, 191, 36, 0.2);"><i class="fas fa-file-alt mr-1"></i>Board Issuances</span>',
                'Other Matters': '<span style="display: inline-flex; align-items: center; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; background: rgba(100, 116, 139, 0.1); color: #64748b; border: 1px solid rgba(100, 116, 139, 0.2);"><i class="fas fa-file mr-1"></i>Other Matters</span>'
            };
            return badges[type] || '<span style="display: inline-flex; align-items: center; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; background: rgba(100, 116, 139, 0.1); color: #64748b; border: 1px solid rgba(100, 116, 139, 0.2);">' + type + '</span>';
        }

        function acceptNotice(noticeId) {
            axios.post(`/notices/${noticeId}/accept`)
                .then(response => {
                    if (response.data.success) {
                        // Remove from displayed notices
                        displayedNotices.add(noticeId);
                        
                        // Show success message
                        Swal.fire({
                            icon: 'success',
                            title: 'Accepted!',
                            text: 'You have accepted the notice invitation.',
                            timer: 2000,
                            showConfirmButton: false,
                            position: 'top-end',
                            toast: true
                        });
                        
                        // Refresh pending notices
                        setTimeout(() => {
                            fetchPendingNotices();
                        }, 500);
                    }
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: error.response?.data?.message || 'Failed to accept notice. Please try again.'
                    });
                });
        }

        function showDeclineReasonModal(noticeId, noticeTitle, originalSwal) {
            // Determine modal width based on screen size
            const isMobile = window.innerWidth < 640;
            const modalWidth = isMobile ? '90vw' : '450px';
            
            Swal.fire({
                title: 'Decline Reason',
                html: `
                    <p class="text-sm text-gray-600 mb-3">Please provide a reason for declining:</p>
                    <textarea id="declineReason" class="swal2-textarea" placeholder="Enter your reason..." rows="4" style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.5rem; resize: vertical; font-size: 0.875rem; min-height: 100px;"></textarea>
                `,
                icon: 'question',
                width: modalWidth,
                padding: isMobile ? '0.75rem' : '1rem',
                showCancelButton: true,
                confirmButtonText: 'Submit',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#ef4444',
                allowOutsideClick: false,
                allowEscapeKey: false,
                customClass: {
                    popup: 'decline-reason-popup',
                    container: 'decline-reason-container'
                },
                didOpen: () => {
                    const textarea = document.getElementById('declineReason');
                    if (textarea) {
                        textarea.focus();
                        // Make textarea required
                        textarea.setAttribute('required', 'required');
                    }
                },
                preConfirm: () => {
                    const reason = document.getElementById('declineReason').value.trim();
                    if (!reason) {
                        Swal.showValidationMessage('Please provide a reason for declining.');
                        return false;
                    }
                    if (reason.length < 3) {
                        Swal.showValidationMessage('Reason must be at least 3 characters long.');
                        return false;
                    }
                    return reason;
                }
            }).then((result) => {
                if (result.isConfirmed && result.value) {
                    declineNotice(noticeId, result.value);
                }
            });
        }

        function declineNotice(noticeId, reason) {
            axios.post(`/notices/${noticeId}/decline`, {
                reason: reason
            })
                .then(response => {
                    if (response.data.success) {
                        // Remove from displayed notices
                        displayedNotices.add(noticeId);
                        
                        // Show success message
                        Swal.fire({
                            icon: 'info',
                            title: 'Declined',
                            text: 'You have declined the notice invitation.',
                            timer: 2000,
                            showConfirmButton: false,
                            position: 'top-end',
                            toast: true
                        });
                        
                        // Refresh pending notices
                        setTimeout(() => {
                            fetchPendingNotices();
                        }, 500);
                    }
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: error.response?.data?.message || 'Failed to decline notice. Please try again.'
                    });
                });
        }

        // Initialize on page load
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function() {
                // Only fetch if not on notices pages
                if (!isOnNoticesPage()) {
                    setTimeout(fetchPendingNotices, 1000); // Wait 1 second after page load
                }
            });
        } else {
            // Only fetch if not on notices pages
            if (!isOnNoticesPage()) {
                setTimeout(fetchPendingNotices, 1000);
            }
        }

        // Also check periodically for new notices (every 30 seconds) - but only if not on notices pages
        setInterval(function() {
            if (!isOnNoticesPage()) {
                fetchPendingNotices();
            }
        }, 30000);
    })();
</script>

<style>
    /* Stack multiple modals */
    .swal2-container.pending-notice-container {
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        width: 100% !important;
        height: 100% !important;
        pointer-events: none !important;
        overflow: visible !important;
    }
    
    .swal2-container.pending-notice-container .swal2-popup {
        pointer-events: all !important;
        max-width: 450px;
        text-align: left !important;
        font-size: 0.875rem;
    }
    
    .swal2-container.pending-notice-container .swal2-title {
        font-size: 0.95rem !important;
        margin-bottom: 0.75rem !important;
        padding-bottom: 0.5rem !important;
        border-bottom: 1px solid #e5e7eb !important;
    }
    
    .swal2-container.pending-notice-container .swal2-html-container {
        font-size: 0.875rem !important;
        padding: 0 !important;
        margin: 0 !important;
    }
    
    .pending-notices-list {
        -webkit-overflow-scrolling: touch;
        scrollbar-width: thin;
    }
    
    .pending-notices-list::-webkit-scrollbar {
        width: 6px;
    }
    
    .pending-notices-list::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    
    .pending-notices-list::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 10px;
    }
    
    .pending-notices-list::-webkit-scrollbar-thumb:hover {
        background: #555;
    }
    
    .notice-item {
        /* No hover effects - clean design */
    }
    
    .notice-title {
        word-wrap: break-word;
        overflow-wrap: break-word;
    }
    
    .notice-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }
    
    .btn-accept:hover {
        background: #059669 !important;
    }
    
    .btn-view:hover {
        background: #034e7a !important;
    }
    
    .btn-decline:hover {
        background: #dc2626 !important;
    }
    
    .swal2-container.pending-notice-container .swal2-backdrop-show {
        background: transparent !important;
    }
    
    /* Hide backdrop for pending notice modals */
    .swal2-container.pending-notice-container .swal2-backdrop {
        display: none !important;
    }
    
    .pending-notice-close-btn {
        color: #6b7280 !important;
        font-size: 1.25rem !important;
    }
    
    .pending-notice-close-btn:hover {
        color: #374151 !important;
    }
    
    /* Decline reason modal styles */
    .swal2-container.decline-reason-container .swal2-popup {
        max-width: 450px;
    }
    
    .swal2-container.decline-reason-container .swal2-textarea {
        font-family: inherit;
    }
    
    /* Mobile Responsive Styles */
    @media (max-width: 640px) {
        .swal2-container.pending-notice-container .swal2-popup {
            max-width: 90vw !important;
            max-height: 85vh !important;
            margin: 0 !important;
        }
        
        .swal2-container.pending-notice-container .swal2-title {
            font-size: 0.875rem !important;
            padding: 0.5rem 0 !important;
        }
        
        .swal2-container.pending-notice-container .swal2-html-container {
            font-size: 0.8125rem !important;
            max-height: calc(85vh - 120px) !important;
        }
        
        .pending-notices-list {
            max-height: calc(85vh - 120px) !important;
        }
        
        .notice-item {
            padding: 0.625rem 0 !important;
        }
        
        .notice-title {
            font-size: 0.8125rem !important;
        }
        
        .notice-actions {
            flex-direction: column;
            gap: 0.5rem;
        }
        
        .notice-actions button {
            flex: 1 !important;
            width: 100% !important;
            min-width: 100% !important;
            padding: 0.625rem 0.75rem !important;
            font-size: 0.8125rem !important;
            min-height: 44px !important;
        }
        
        .btn-text {
            display: inline;
        }
        
        .swal2-container.decline-reason-container .swal2-popup {
            max-width: 90vw !important;
            padding: 0.75rem !important;
        }
        
        .swal2-container.decline-reason-container .swal2-textarea {
            font-size: 0.875rem !important;
            padding: 0.625rem !important;
        }
        
        .swal2-container.decline-reason-container .swal2-actions {
            flex-direction: column-reverse;
            gap: 0.5rem;
        }
        
        .swal2-container.decline-reason-container .swal2-actions button {
            width: 100% !important;
            margin: 0 !important;
            min-height: 44px !important;
        }
    }
    
    /* Tablet Responsive Styles */
    @media (min-width: 641px) and (max-width: 1024px) {
        .swal2-container.pending-notice-container .swal2-popup {
            max-width: 500px !important;
        }
        
        .notice-actions {
            gap: 0.5rem;
        }
        
        .notice-actions button {
            min-width: 100px !important;
            padding: 0.5rem 0.75rem !important;
        }
        
        .swal2-container.decline-reason-container .swal2-popup {
            max-width: 500px !important;
        }
    }
    
    /* Touch-friendly targets for mobile */
    @media (hover: none) and (pointer: coarse) {
        .notice-actions button {
            min-height: 44px !important;
            min-width: 44px !important;
        }
        
        .swal2-container.decline-reason-container .swal2-actions button {
            min-height: 44px !important;
        }
    }
    
    /* Dark mode support */
    @media (prefers-color-scheme: dark) {
        .pending-notices-list::-webkit-scrollbar-track {
            background: #374151;
        }
        
        .pending-notices-list::-webkit-scrollbar-thumb {
            background: #6b7280;
        }
        
        .pending-notices-list::-webkit-scrollbar-thumb:hover {
            background: #9ca3af;
        }
    }
</style>
@endauth

