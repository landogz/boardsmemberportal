<!-- Global PDF Viewer Modal - Full Screen -->
<div id="globalPdfModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center p-0 sm:p-4" style="display: none;">
    <div class="bg-white w-full h-full sm:max-h-[95vh] sm:rounded-lg overflow-hidden flex flex-col max-h-[100dvh]">
        <!-- Modal Header -->
        <div class="flex-shrink-0 flex items-center justify-between p-4 lg:p-6 border-b border-gray-200 bg-gray-50">
            <h3 id="globalPdfModalTitle" class="text-xl lg:text-2xl font-semibold text-gray-800 truncate pr-2">PDF Viewer</h3>
            <button onclick="closeGlobalPdfModal()" class="text-gray-500 hover:text-gray-700 p-2 flex-shrink-0">
                <i class="fas fa-times text-xl lg:text-2xl"></i>
            </button>
        </div>
        
        <!-- PDF Viewer - scrollable on tablet/mobile -->
        <div class="flex-1 min-h-0 overflow-y-auto overflow-x-hidden p-4 lg:p-6 relative pdf-modal-scroll-area">
            <div id="pdfViewerContainer" class="w-full border border-gray-300 rounded-lg relative overflow-hidden pdf-viewer-container">
                <!-- Tap overlay: on mobile/tablet iOS/Android often block iframe touch until "activated". One tap removes this so the iframe receives touch. -->
                <div id="pdfTapOverlay" class="pdf-tap-overlay absolute inset-0 z-10 flex items-center justify-center bg-gray-100/90 cursor-pointer select-none" style="touch-action: none;">
                    <div class="text-center px-6 py-4 rounded-lg bg-white/95 shadow-lg border border-gray-200">
                        <p class="text-gray-700 font-medium mb-1">Tap to scroll PDF</p>
                        <p class="text-sm text-gray-500">Touch the viewer below to scroll</p>
                    </div>
                </div>
                <iframe id="globalPdfViewer" src="" class="w-full h-full pdf-iframe" frameborder="0" scrolling="yes"></iframe>
            </div>
        </div>
        
        <style>
            /* Scrollable area for tablet/mobile - smooth touch scrolling */
            .pdf-modal-scroll-area {
                -webkit-overflow-scrolling: touch;
                overscroll-behavior: contain;
            }
            /* Minimum height so iframe gets a real height and PDF can scroll inside on mobile/tablet */
            .pdf-viewer-container {
                position: relative;
                overflow: hidden;
                min-height: 70vh;
                height: 70vh;
            }
            @media (min-width: 1024px) {
                .pdf-viewer-container {
                    min-height: 100%;
                    height: 100%;
                }
            }
            #globalPdfViewer {
                display: block;
                width: 100%;
                height: 100%;
                min-height: 0;
            }
            
            /* Allow touch events to reach iframe for scrolling on iOS/Android */
            .pdf-viewer-container iframe {
                touch-action: pan-y pinch-zoom;
                pointer-events: auto;
            }
            /* Show tap overlay only on touch devices (mobile/tablet) */
            .pdf-tap-overlay {
                display: flex;
            }
            /* Treat up to 1279px as tablet (keep tap overlay for 1024 widths) */
            @media (min-width: 1280px) {
                .pdf-tap-overlay {
                    display: none !important;
                }
            }
        </style>
        
        <!-- Modal Footer -->
        <div class="flex items-center justify-between p-4 lg:p-6 border-t border-gray-200 bg-gray-50">
            <div class="flex items-center space-x-4">
                <a id="globalPdfDownloadLink" href="#" download class="text-[#055498] hover:underline flex items-center cursor-pointer" data-pdf-modal="false">
                    <i class="fas fa-download mr-2"></i>Download PDF
                </a>
                <a id="globalPdfViewNewTabLink" href="#" target="_blank" rel="noopener noreferrer" class="text-[#055498] hover:underline flex items-center cursor-pointer" data-pdf-modal="false">
                    <i class="fas fa-external-link-alt mr-2"></i>Open in New Tab
                </a>
            </div>
            <button onclick="closeGlobalPdfModal()" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-100 transition-colors">
                Close
            </button>
        </div>
    </div>
</div>

<script>
    // Global PDF Modal Functions
    function openGlobalPdfModal(pdfUrl, title) {
        const modal = document.getElementById('globalPdfModal');
        const iframe = document.getElementById('globalPdfViewer');
        const modalTitle = document.getElementById('globalPdfModalTitle');
        const downloadLink = document.getElementById('globalPdfDownloadLink');
        const viewNewTabLink = document.getElementById('globalPdfViewNewTabLink');
        
        // Set modal title
        modalTitle.textContent = title || 'PDF Viewer';
        
        // Create download filename from title
        // Clean title: remove special characters, replace spaces with underscores, ensure it ends with .pdf
        let downloadFilename = (title || 'document').trim();
        // Remove common prefixes like "Resolution Number:", "Document Title:", etc.
        downloadFilename = downloadFilename.replace(/^(Resolution Number|Document Title|Title):\s*/i, '');
        // Replace special characters with underscores and ensure it's a valid filename
        downloadFilename = downloadFilename.replace(/[^a-z0-9\s-]/gi, '_').replace(/\s+/g, '_');
        // Remove multiple consecutive underscores
        downloadFilename = downloadFilename.replace(/_+/g, '_');
        // Remove leading/trailing underscores
        downloadFilename = downloadFilename.replace(/^_+|_+$/g, '');
        // Ensure it ends with .pdf
        if (!downloadFilename.toLowerCase().endsWith('.pdf')) {
            downloadFilename = downloadFilename + '.pdf';
        }
        // Limit filename length (max 200 chars to avoid filesystem issues)
        if (downloadFilename.length > 200) {
            downloadFilename = downloadFilename.substring(0, 197) + '.pdf';
        }
        
        // Get absolute URL
        const absoluteUrl = pdfUrl.startsWith('http') ? pdfUrl : (window.location.origin + (pdfUrl.startsWith('/') ? '' : '/') + pdfUrl);
        
        // Set iframe source - if using the new PDF routes, they will serve with custom filename in Content-Disposition header
        // Add URL parameters to minimize toolbar (Chrome PDF viewer)
        let pdfUrlWithParams = absoluteUrl;
        if (!pdfUrlWithParams.includes('#')) {
            pdfUrlWithParams += '#toolbar=0&navpanes=0';
        } else if (!pdfUrlWithParams.includes('toolbar=')) {
            pdfUrlWithParams += '&toolbar=0&navpanes=0';
        }
        
        iframe.src = pdfUrlWithParams;
        iframe.setAttribute('title', title || 'PDF Document');
        
        // Try to hide title span in PDF viewer after iframe loads (if accessible)
        iframe.onload = function() {
            function hideTitleSpan() {
                try {
                    const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
                    if (!iframeDoc) return;
                    
                    // Hide title span
                    const titleSpan = iframeDoc.getElementById('title');
                    if (titleSpan) {
                        titleSpan.style.display = 'none';
                        titleSpan.style.visibility = 'hidden';
                        titleSpan.style.opacity = '0';
                        titleSpan.style.height = '0';
                        titleSpan.style.width = '0';
                        titleSpan.style.overflow = 'hidden';
                    }
                    
                    // Also try to hide using querySelector
                    const titleElements = iframeDoc.querySelectorAll('#title, span#title');
                    titleElements.forEach(function(el) {
                        el.style.display = 'none';
                        el.style.visibility = 'hidden';
                        el.style.opacity = '0';
                        el.style.height = '0';
                        el.style.width = '0';
                        el.style.overflow = 'hidden';
                    });
                    
                    // Try to inject CSS to hide it permanently
                    if (iframeDoc.head) {
                        let style = iframeDoc.getElementById('hide-title-style');
                        if (!style) {
                            style = iframeDoc.createElement('style');
                            style.id = 'hide-title-style';
                            style.textContent = '#title, span#title { display: none !important; visibility: hidden !important; opacity: 0 !important; height: 0 !important; width: 0 !important; overflow: hidden !important; }';
                            iframeDoc.head.appendChild(style);
                        }
                    }
                } catch (e) {
                    // Cross-origin or other error - overlay will cover it
                }
            }
            
            // Hide immediately
            hideTitleSpan();
            
            // Use MutationObserver to watch for dynamically added elements
            try {
                const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
                if (iframeDoc) {
                    const observer = new MutationObserver(function(mutations) {
                        hideTitleSpan();
                    });
                    
                    observer.observe(iframeDoc.body || iframeDoc.documentElement, {
                        childList: true,
                        subtree: true
                    });
                }
            } catch (e) {
                // Cross-origin error - overlay will cover it
            }
        };
        
        // Set download link
        downloadLink.href = absoluteUrl;
        downloadLink.download = downloadFilename;
        downloadLink.setAttribute('data-pdf-modal', 'false'); // Prevent interception
        
        // Set new tab link
        viewNewTabLink.href = absoluteUrl;
        viewNewTabLink.setAttribute('data-pdf-modal', 'false'); // Prevent interception
        
        // Show tap overlay on mobile/tablet so first tap "activates" the iframe for touch scrolling
        const tapOverlay = document.getElementById('pdfTapOverlay');
        if (tapOverlay) {
            tapOverlay.style.display = '';
            tapOverlay.onclick = null;
            tapOverlay.ontouchend = null;
            tapOverlay.onclick = function() { tapOverlay.style.display = 'none'; tapOverlay.onclick = null; tapOverlay.ontouchend = null; };
            tapOverlay.ontouchend = function(e) { e.preventDefault(); tapOverlay.style.display = 'none'; tapOverlay.onclick = null; tapOverlay.ontouchend = null; };
        }
        
        // Show modal
        modal.classList.remove('hidden');
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
    
    function closeGlobalPdfModal() {
        const modal = document.getElementById('globalPdfModal');
        const iframe = document.getElementById('globalPdfViewer');
        
        // Clear iframe source
        iframe.src = '';
        
        // Hide modal
        modal.classList.add('hidden');
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }
    
    // Intercept all PDF link clicks
    $(document).ready(function() {
        // Function to check if a URL is a PDF
        function isPdfUrl(url) {
            if (!url) return false;
            const lowerUrl = url.toLowerCase();
            // Check if URL ends with .pdf or contains .pdf in the path
            return lowerUrl.endsWith('.pdf') || 
                   (lowerUrl.includes('/storage/') && lowerUrl.includes('.pdf'));
        }
        
        // Intercept clicks on links
        $(document).on('click', 'a[href]', function(e) {
            const href = $(this).attr('href');
            
            if (!href || href === '#' || href === '') return true;
            
            // Skip links inside the modal footer (download and new tab links)
            if ($(this).closest('#globalPdfModal').length > 0) {
                return true; // Allow normal link behavior for modal footer links
            }
            
            // Check if the link has data-pdf-modal="false" or class "no-pdf-modal" to skip modal
            if ($(this).data('pdf-modal') === false || $(this).hasClass('no-pdf-modal')) {
                return true; // Allow normal link behavior
            }
            
            // Check if it's a PDF file
            if (isPdfUrl(href)) {
                e.preventDefault();
                e.stopPropagation();
                
                // Get title from link text, data attribute, or filename
                let title = $(this).data('pdf-title') || $(this).text().trim() || '';
                
                // Extract filename from URL if title is empty
                if (!title || title === '') {
                    const urlParts = href.split('/');
                    let filename = urlParts[urlParts.length - 1];
                    // Remove query parameters if any
                    filename = filename.split('?')[0];
                    // Clean up filename for display - remove .pdf extension and replace dashes/underscores with spaces
                    title = filename.replace(/\.pdf$/i, '').replace(/[-_]/g, ' ').trim();
                    if (!title) title = 'PDF Document';
                }
                
                // Open PDF in modal
                openGlobalPdfModal(href, title);
                return false;
            }
        });
        
        // Intercept buttons with onclick that call viewPDF - prevent default and use global modal
        $(document).on('click', 'button[onclick*="viewPDF"]', function(e) {
            const onclick = $(this).attr('onclick');
            if (onclick && onclick.includes('viewPDF(') && !onclick.includes('openGlobalPdfModal')) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                
                // Extract parameters from viewPDF call - handle different parameter formats
                // Format 1: viewPDF('url', 'title')
                // Format 2: viewPDF('url', 'param1', 'param2')
                let match = onclick.match(/viewPDF\(['"]([^'"]+)['"],\s*['"]([^'"]*)['"]/);
                if (match) {
                    const pdfUrl = match[1];
                    // If there are 3 parameters, use the last one as title (for resolution number format)
                    const params = onclick.match(/viewPDF\(([^)]+)\)/);
                    if (params) {
                        const paramList = params[1].split(',').map(p => p.trim().replace(/^['"]|['"]$/g, ''));
                        const title = paramList.length >= 3 ? paramList[2] : (paramList[1] || 'PDF Document');
                        openGlobalPdfModal(pdfUrl, title);
                    } else {
                        openGlobalPdfModal(pdfUrl, match[2] || 'PDF Document');
                    }
                }
                return false;
            }
        });
        
        // Close modal on outside click
        $('#globalPdfModal').on('click', function(e) {
            if ($(e.target).attr('id') === 'globalPdfModal') {
                closeGlobalPdfModal();
            }
        });
        
        // Close modal on ESC key
        $(document).on('keydown', function(e) {
            if (e.key === 'Escape' && !$('#globalPdfModal').hasClass('hidden')) {
                closeGlobalPdfModal();
            }
        });
        
        // Handle download link click - ensure it works properly
        $(document).on('click', '#globalPdfDownloadLink', function(e) {
            const href = $(this).attr('href');
            const download = $(this).attr('download');
            
            if (!href || href === '#') {
                e.preventDefault();
                e.stopPropagation();
                return false;
            }
            
            // Stop propagation to prevent PDF link interceptor from catching this
            e.stopPropagation();
            
            // For same-origin files, the download attribute should work
            // Allow default download behavior
            return true;
        });
        
        // Handle new tab link click - ensure it opens properly
        $(document).on('click', '#globalPdfViewNewTabLink', function(e) {
            const href = $(this).attr('href');
            
            if (!href || href === '#') {
                e.preventDefault();
                e.stopPropagation();
                return false;
            }
            
            // Stop propagation to prevent PDF link interceptor from catching this
            e.stopPropagation();
            
            // Prevent default and open manually to ensure it works
            e.preventDefault();
            window.open(href, '_blank', 'noopener,noreferrer');
            return false;
        });
    });
</script>

