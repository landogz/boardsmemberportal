<!-- Professional Announcement Modal -->
<div id="announcementModal" class="fixed inset-0 z-50 hidden overflow-y-auto" style="background-color: rgba(0, 0, 0, 0.75); backdrop-filter: blur(4px);">
    <div class="flex items-center justify-center min-h-screen px-4 py-8">
        <div class="fixed inset-0 transition-opacity" onclick="closeAnnouncementModal()">
            <div class="absolute inset-0 bg-black opacity-60"></div>
        </div>

        <div class="relative bg-white rounded-2xl shadow-2xl transform transition-all w-full max-w-4xl mx-auto" style="max-height: 90vh; display: flex; flex-direction: column;" id="announcementModalContent">
            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-[#055498] to-[#123a60] px-6 py-4 rounded-t-2xl flex items-center justify-between flex-shrink-0">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center">
                        <i class="fas fa-bullhorn text-white text-lg"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white">Announcement</h3>
                </div>
                <button onclick="closeAnnouncementModal()" class="w-9 h-9 rounded-full hover:bg-white/20 flex items-center justify-center text-white hover:bg-white/30 transition-all duration-200" aria-label="Close">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>

            <!-- Modal Content -->
            <div class="overflow-y-auto flex-1 bg-white" style="max-height: calc(90vh - 80px);">
                <div id="modalLoading" class="text-center py-16">
                    <div class="inline-block animate-spin rounded-full h-12 w-12 border-4 border-[#055498] border-t-transparent mb-4"></div>
                    <p class="text-gray-600 text-lg">Loading announcement...</p>
                </div>
                <div id="modalContent" class="hidden">
                    <!-- Author Info -->
                    <div class="px-6 pt-6 pb-4 border-b border-gray-200">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 rounded-full bg-gradient-to-br from-[#055498] to-[#123a60] flex items-center justify-center text-white font-bold shadow-lg flex-shrink-0 overflow-hidden" id="modalAuthorAvatar" style="font-size: 16px;">
                                <!-- Profile picture or initials will be inserted here -->
                                <img id="modalAuthorAvatarImg" src="" alt="" class="w-full h-full object-cover hidden">
                                <span id="modalAuthorAvatarInitials"></span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="font-bold text-gray-900 text-lg mb-1" id="modalAuthorName"></div>
                                <div class="text-sm text-gray-500 flex items-center space-x-2" id="modalDate">
                                    <i class="far fa-calendar-alt text-xs"></i>
                                    <span id="modalDateText"></span>
                                    <span class="mx-1">·</span>
                                    <i class="fas fa-globe-americas text-xs"></i>
                                    <span>Public</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Title -->
                    <div class="px-6 pt-6 pb-4">
                        <h2 class="text-3xl font-bold text-gray-900 leading-tight mb-2" id="modalAnnouncementTitle" style="color: #055498;"></h2>
                    </div>

                    <!-- Banner Image -->
                    <div id="modalBanner" class="mb-6 hidden">
                        <div class="relative overflow-hidden rounded-lg mx-6 shadow-lg">
                            <img src="" alt="Banner" class="w-full h-auto max-h-[500px] object-contain object-center" id="modalBannerImg" style="display: block;">
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="px-6 pb-8">
                        <div class="text-gray-700 text-base leading-relaxed prose prose-lg max-w-none prose-headings:text-gray-900 prose-p:text-gray-700 prose-strong:text-gray-900 prose-a:text-[#055498] prose-a:no-underline hover:prose-a:underline prose-ul:text-gray-700 prose-ol:text-gray-700 prose-li:text-gray-700" id="modalDescription" style="line-height: 1.8;"></div>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 rounded-b-2xl flex-shrink-0">
                <div class="flex items-center justify-end">
                    <button onclick="closeAnnouncementModal()" class="px-6 py-2.5 bg-gradient-to-r from-[#055498] to-[#123a60] text-white font-semibold rounded-lg hover:from-[#123a60] hover:to-[#055498] transition-all duration-200 shadow-md hover:shadow-lg transform hover:scale-105">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Professional Announcement Modal Styles */
    #announcementModal {
        animation: fadeIn 0.3s ease-out;
    }
    
    #announcementModal > div > div {
        animation: slideUp 0.3s ease-out;
    }
    
    @keyframes fadeIn {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }
    
    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    #announcementModal .prose {
        font-size: 1rem;
    }
    
    #announcementModal .prose p {
        margin-bottom: 1.25rem;
    }
    
    #announcementModal .prose ul,
    #announcementModal .prose ol {
        margin-bottom: 1.25rem;
        padding-left: 1.5rem;
    }
    
    #announcementModal .prose li {
        margin-bottom: 0.5rem;
    }
    
    #announcementModal .prose h1,
    #announcementModal .prose h2,
    #announcementModal .prose h3 {
        margin-top: 1.5rem;
        margin-bottom: 1rem;
    }
    
    #announcementModal .prose img {
        border-radius: 0.5rem;
        margin: 1.5rem 0;
    }
    
    #announcementModal .prose a {
        color: #055498;
        font-weight: 500;
    }
    
    #announcementModal .prose a:hover {
        text-decoration: underline;
    }
    
    /* Mobile Responsive */
    @media (max-width: 640px) {
        #announcementModal > div > div {
            max-width: 95vw;
            margin: 1rem;
        }
        
        #announcementModal .prose {
            font-size: 0.9375rem;
        }
        
        #modalAnnouncementTitle {
            font-size: 1.5rem !important;
        }
    }
</style>

<script>
    // Initialize immediately to ensure functions are available when notification handlers run
    (function() {
        // Only initialize if functions don't already exist (to avoid conflicts)
        if (typeof window.openAnnouncementModal === 'undefined') {
            // Open announcement modal
            window.openAnnouncementModal = function(announcementId) {
            const modal = document.getElementById('announcementModal');
            const modalLoading = document.getElementById('modalLoading');
            const modalContent = document.getElementById('modalContent');
            
            if (!modal) {
                console.error('Announcement modal not found');
                return;
            }

            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            
            // Force light mode on admin pages (remove dark mode classes)
            const modalContentEl = document.getElementById('announcementModalContent');
            if (modalContentEl) {
                modalContentEl.classList.remove('dark:bg-[#1e293b]');
                modalContentEl.classList.add('bg-white');
            }
            
            // Scroll to top of modal content
            const modalScrollContainer = modal.querySelector('.overflow-y-auto');
            if (modalScrollContainer) {
                modalScrollContainer.scrollTop = 0;
            }
            
            modalLoading.classList.remove('hidden');
            modalContent.classList.add('hidden');

            // Ensure axios is available
            if (typeof axios === 'undefined') {
                console.error('Axios is not loaded');
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to load announcement. Please refresh the page.',
                });
                closeAnnouncementModal();
                return;
            }

            axios.get(`/announcements/api/${announcementId}/modal`)
                .then(response => {
                    const announcement = response.data.announcement;
                    
                    // Set author info
                    const authorName = announcement.author;
                    const authorInitials = authorName.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase();
                    const authorAvatar = document.getElementById('modalAuthorAvatar');
                    const authorAvatarImg = document.getElementById('modalAuthorAvatarImg');
                    const authorAvatarInitials = document.getElementById('modalAuthorAvatarInitials');
                    
                    // Display profile picture if available, otherwise show initials
                    if (announcement.author_profile_url) {
                        authorAvatarImg.src = announcement.author_profile_url;
                        authorAvatarImg.alt = authorName;
                        authorAvatarImg.classList.remove('hidden');
                        authorAvatarInitials.textContent = '';
                        authorAvatarInitials.classList.add('hidden');
                    } else {
                        authorAvatarImg.classList.add('hidden');
                        authorAvatarInitials.textContent = authorInitials;
                        authorAvatarInitials.classList.remove('hidden');
                    }
                    
                    document.getElementById('modalAuthorName').textContent = authorName;
                    document.getElementById('modalDateText').textContent = announcement.created_at;

                    // Set banner
                    const bannerEl = document.getElementById('modalBanner');
                    if (announcement.banner_url) {
                        document.getElementById('modalBannerImg').src = announcement.banner_url;
                        bannerEl.classList.remove('hidden');
                    } else {
                        bannerEl.classList.add('hidden');
                    }

                    // Set title and description
                    document.getElementById('modalAnnouncementTitle').textContent = announcement.title;
                    // Render HTML description directly (from CKEditor, already sanitized)
                    const description = announcement.description || '';
                    document.getElementById('modalDescription').innerHTML = description;

                    modalLoading.classList.add('hidden');
                    modalContent.classList.remove('hidden');
                    
                    // Ensure scroll is at top after content loads
                    setTimeout(() => {
                        if (modalScrollContainer) {
                            modalScrollContainer.scrollTop = 0;
                        }
                    }, 100);
                })
                .catch(error => {
                    console.error('Error loading announcement:', error);
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to load announcement details.',
                        });
                    }
                    closeAnnouncementModal();
                });
            };
        }
        
        if (typeof window.closeAnnouncementModal === 'undefined') {
            // Close announcement modal
            window.closeAnnouncementModal = function() {
                const modal = document.getElementById('announcementModal');
                if (modal) {
                    modal.classList.add('hidden');
                    document.body.style.overflow = '';
                }
            };
        }
        
        // Close modal on ESC key (only add listener once)
        if (!document.hasAnnouncementModalEscListener) {
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    const modal = document.getElementById('announcementModal');
                    if (modal && !modal.classList.contains('hidden')) {
                        closeAnnouncementModal();
                    }
                }
            });
            document.hasAnnouncementModalEscListener = true;
        }
    })(); // End IIFE
</script>

