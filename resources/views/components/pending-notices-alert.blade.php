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
            let listHTML = '<div style="text-align: left; max-height: 400px; overflow-y: auto;">';
            
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
                        <h3 style="font-size: 0.9rem; font-weight: 600; margin-bottom: 0.25rem; color: #1f2937; line-height: 1.3;">${notice.title}</h3>
                        ${meetingInfo}
                        <div class="mt-2 flex gap-2">
                            <button onclick="handleNoticeAction(${notice.id}, 'accept')" class="btn-accept" style="flex: 1; padding: 0.4rem 0.75rem; background: #10b981; color: white; border: none; border-radius: 0.375rem; font-size: 0.75rem; cursor: pointer; font-weight: 500;">
                                <i class="fas fa-check mr-1"></i>Accept
                            </button>
                            <button onclick="handleNoticeAction(${notice.id}, 'view')" class="btn-view" style="flex: 1; padding: 0.4rem 0.75rem; background: #055498; color: white; border: none; border-radius: 0.375rem; font-size: 0.75rem; cursor: pointer; font-weight: 500;">
                                <i class="fas fa-eye mr-1"></i>View
                            </button>
                            <button onclick="handleNoticeAction(${notice.id}, 'decline')" class="btn-decline" style="flex: 1; padding: 0.4rem 0.75rem; background: #ef4444; color: white; border: none; border-radius: 0.375rem; font-size: 0.75rem; cursor: pointer; font-weight: 500;">
                                <i class="fas fa-times mr-1"></i>Decline
                            </button>
                        </div>
                    </div>
                `;
            });
            
            listHTML += '</div>';

            currentModal = Swal.fire({
                title: `Pending Notices (${notices.length})`,
                html: listHTML,
                icon: 'info',
                width: '380px',
                padding: '1rem',
                showConfirmButton: false,
                showCancelButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                showCloseButton: false,
                customClass: {
                    popup: 'pending-notice-popup-list',
                    container: 'pending-notice-container'
                },
                didOpen: () => {
                    // Position the modal at top left
                    setTimeout(() => {
                        const popup = document.querySelector('.swal2-popup.pending-notice-popup-list');
                        if (popup) {
                            popup.style.position = 'fixed';
                            popup.style.top = '20px';
                            popup.style.left = '20px';
                            popup.style.margin = '0';
                            popup.style.transform = 'none';
                            popup.style.zIndex = '9999';
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
            Swal.fire({
                title: 'Decline Reason',
                html: `
                    <p class="text-sm text-gray-600 mb-3">Please provide a reason for declining:</p>
                    <textarea id="declineReason" class="swal2-textarea" placeholder="Enter your reason..." rows="3" style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.5rem; resize: vertical; font-size: 0.875rem;"></textarea>
                `,
                icon: 'question',
                width: '380px',
                padding: '1rem',
                showCancelButton: true,
                confirmButtonText: 'Submit',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#ef4444',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    const textarea = document.getElementById('declineReason');
                    if (textarea) {
                        textarea.focus();
                    }
                },
                preConfirm: () => {
                    const reason = document.getElementById('declineReason').value.trim();
                    if (!reason) {
                        Swal.showValidationMessage('Please provide a reason for declining.');
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
                declined_reason: reason
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
        max-width: 380px;
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
    
    .notice-item {
        transition: background-color 0.2s;
    }
    
    .notice-item:hover {
        background-color: #f9fafb;
        margin: 0 -0.5rem;
        padding-left: 0.5rem !important;
        padding-right: 0.5rem !important;
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
</style>
@endauth

