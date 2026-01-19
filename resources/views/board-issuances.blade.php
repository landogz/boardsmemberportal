<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Board Issuances - Board Member Portal</title>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <!-- Montserrat Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
    <!-- Gotham Font -->
    <link href="https://cdn.jsdelivr.net/npm/gotham-fonts@1.0.3/css/gotham-rounded.min.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x/dist/cdn.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @include('components.header-footer-styles')
    <style>
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .issuance-card:hover {
            transform: translateY(-2px);
        }
        
        /* Fix Safari select height mismatch with inputs */
        select {
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            box-sizing: border-box;
            height: auto;
        }
        
        /* Ensure inputs and selects have same height calculation */
        input[type="text"],
        input[type="email"],
        input[type="tel"],
        input[type="date"],
        input[type="password"],
        textarea,
        select {
            box-sizing: border-box;
            line-height: 1.5;
        }
        
        /* Safari specific fix for select dropdown arrow */
        select::-webkit-inner-spin-button,
        select::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        
        /* Custom dropdown arrow for Safari */
        select {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23374151' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 12px;
            padding-right: 36px !important;
        }
        
        .dark select {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%9ca3af' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
        }
    </style>
</head>
<body class="bg-[#F9FAFB] dark:bg-[#0F172A] text-[#0A0A0A] dark:text-[#F1F5F9] transition-colors duration-300">
    @include('components.header')
    @include('components.theme-toggle-script')

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">
        <div class="mb-6 sm:mb-8 text-center">
            <h1 class="text-3xl sm:text-4xl font-bold mb-2" style="color: #055498;">Board Issuances</h1>
            <p class="text-gray-600 dark:text-gray-400 text-sm sm:text-base">
                Access board resolutions and regulations relevant to your role.
            </p>
        </div>

        <!-- Search & Filter -->
        <div class="mb-6 sm:mb-8 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 sm:p-5">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                <div>
                    <label for="filterType" class="block text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1">Type</label>
                    <select id="filterType" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-sm text-gray-800 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-[#055498] focus:border-[#055498]">
                        <option value="">All Types</option>
                        <option value="resolution">Board Resolutions</option>
                    </select>
                </div>
                <div>
                    <label for="filterYear" class="block text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1">Select Year</label>
                    <select id="filterYear" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-sm text-gray-800 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-[#055498] focus:border-[#055498]">
                        <option value="">All Years</option>
                        @foreach($years as $year)
                            <option value="{{ $year }}">{{ $year }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label for="filterKeyword" class="block text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1">Enter Keyword</label>
                    <div class="flex">
                        <input 
                            type="text" 
                            id="filterKeyword" 
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-l-lg bg-white dark:bg-gray-800 text-sm text-gray-800 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-[#055498] focus:border-[#055498]" 
                            placeholder="Keyword"
                        >
                        <button type="button" id="clearFilters" class="px-3 sm:px-4 py-2 text-xs sm:text-sm font-semibold rounded-r-lg text-white" style="background: linear-gradient(135deg, #055498 0%, #123a60 100%);">
                            Clear
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Board Resolutions -->
        <div id="issuancesContainer" class="grid grid-cols-1 gap-6">
            <!-- Board Resolutions Section -->
            <div id="resolutionsSection" class="bg-white dark:bg-gray-800 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700 p-5 sm:p-6">
            <div class="flex items-center mb-4">
                <div class="w-10 h-10 rounded-full flex items-center justify-center mr-3" style="background-color: rgba(5, 84, 152, 0.1);">
                    <i class="fas fa-gavel text-lg" style="color: #055498;"></i>
                </div>
                <div>
                    <h2 class="text-xl font-semibold" style="color: #055498;">Board Resolutions</h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Effective resolutions and related issuances</p>
                </div>
            </div>
                <div class="border-t border-gray-200 dark:border-gray-700 pt-4 mt-2 space-y-4" id="resolutionsList">
                @forelse($documents as $document)
                        <div id="resolution-{{ $document->id }}" class="issuance-item issuance-card bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 hover:shadow-md transition-all duration-200 scroll-mt-20" 
                         data-type="resolution" 
                         data-year="{{ $document->year }}" 
                         data-keywords="{{ strtolower($document->title . ' ' . ($document->description ?? '') . ' ' . ($document->version ?? '')) }}">
                            <div class="flex items-start justify-between">
                                <div class="flex-1 pr-4">
                                    <div class="flex items-center mb-2">
                                        <div class="w-8 h-8 rounded-lg flex items-center justify-center mr-2" style="background-color: rgba(5, 84, 152, 0.1);">
                                            <i class="fas fa-gavel text-sm" style="color: #055498;"></i>
                                        </div>
                                        <span class="text-xs font-semibold px-2 py-1 rounded-full" style="background-color: rgba(5, 84, 152, 0.1); color: #055498;">Resolution</span>
                                    </div>
                                    <h3 class="text-sm font-bold text-gray-800 dark:text-gray-100 mb-2 line-clamp-2">{{ $document->title }}</h3>
                            @if($document->description)
                                        <p class="text-xs text-gray-600 dark:text-gray-400 mb-3 whitespace-pre-wrap">{{ $document->description }}</p>
                            @endif
                                    <div class="flex flex-wrap gap-2 text-xs text-gray-500 dark:text-gray-400">
                            @if($document->version)
                                            <span class="flex items-center">
                                                <i class="fas fa-tag mr-1"></i>
                                                {{ $document->version }}
                                            </span>
                            @endif
                            @if($document->effective_date)
                                            <span class="flex items-center">
                                                <i class="fas fa-calendar-alt mr-1"></i>
                                                {{ $document->effective_date->format('M d, Y') }}
                                            </span>
                            @endif
                                    </div>
                            @if($document->uploader)
                                        <div class="flex items-center gap-2 mt-2">
                                            @php
                                                $uploaderProfilePic = 'https://ui-avatars.com/api/?name=' . urlencode($document->uploader->first_name . ' ' . $document->uploader->last_name) . '&size=32&background=055498&color=fff&bold=true';
                                                if ($document->uploader->profile_picture) {
                                                    $media = \App\Models\MediaLibrary::find($document->uploader->profile_picture);
                                                    if ($media) {
                                                        $uploaderProfilePic = asset('storage/' . $media->file_path);
                                                    }
                                                }
                                            @endphp
                                            <img src="{{ $uploaderProfilePic }}" alt="{{ $document->uploader->first_name }} {{ $document->uploader->last_name }}" class="w-6 h-6 rounded-full object-cover border border-gray-300 dark:border-gray-600">
                                            <p class="text-xs text-gray-400 dark:text-gray-500">
                                                {{ $document->uploader->first_name }} {{ $document->uploader->last_name }}
                                            </p>
                                        </div>
                            @endif
                        </div>
                                <div class="flex-shrink-0">
                        @if($document->pdf)
                            <button 
                                onclick="viewPDF('{{ asset('storage/' . $document->pdf->file_path) }}', '{{ $document->title }}', '{{ addslashes($document->title) }}', 'document')"
                                            class="px-4 py-2 text-xs font-semibold rounded-lg text-white hover:opacity-90 transition-all duration-200 shadow-sm hover:shadow-md" 
                                style="background: linear-gradient(135deg, #055498 0%, #123a60 100%);"
                            >
                                            <i class="fas fa-eye mr-1"></i>VIEW
                            </button>
                        @else
                                        <button type="button" class="px-4 py-2 text-xs font-semibold rounded-lg text-gray-400 bg-gray-100 dark:bg-gray-700 cursor-not-allowed" disabled>
                                            <i class="fas fa-file-times mr-1"></i>NO FILE
                            </button>
                        @endif
                                </div>
                            </div>
                    </div>
                @empty
                        <div class="text-center py-8">
                            <i class="fas fa-gavel text-4xl text-gray-300 dark:text-gray-600 mb-3"></i>
                            <p class="text-sm text-gray-500 dark:text-gray-400">No board resolutions available at this time.</p>
                        </div>
                @endforelse
                </div>
            </div>
        </div>
    </div>

    @include('components.footer')

    <!-- PDF Viewer Modal -->
    <div id="pdfModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center">
        <div class="bg-white dark:bg-gray-800 w-full h-full overflow-hidden flex flex-col">
            <!-- Modal Header -->
            <div class="flex justify-between items-center p-4 lg:p-6 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                <h3 id="pdfModalTitle" class="text-xl lg:text-2xl font-semibold text-gray-800 dark:text-gray-100">PDF Viewer</h3>
                <button onclick="closePDFModal()" class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 p-2">
                    <i class="fas fa-times text-xl lg:text-2xl"></i>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="flex-1 overflow-hidden p-4 lg:p-6">
                <iframe id="pdfViewer" src="" class="w-full h-full border border-gray-300 dark:border-gray-600 rounded-lg" frameborder="0"></iframe>
            </div>

            <!-- Modal Footer -->
            <div class="border-t border-gray-200 dark:border-gray-700 p-4 bg-gray-50 dark:bg-gray-900 flex justify-between items-center">
                <div class="flex items-center gap-4">
                    <a id="pdfDownloadLink" href="#" download class="text-[#055498] dark:text-[#4A9EFF] hover:underline flex items-center">
                        <i class="fas fa-download mr-2"></i>Download PDF
                    </a>
                    <a id="pdfViewNewTabLink" href="#" target="_blank" class="text-[#055498] dark:text-[#4A9EFF] hover:underline flex items-center">
                        <i class="fas fa-external-link-alt mr-2"></i>View in New Tab
                    </a>
                </div>
                <button onclick="closePDFModal()" class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    Close
                </button>
            </div>
        </div>
    </div>

    <script>
        // Prevent automatic scroll to hash anchor and keep page at top
        (function() {
            // Scroll to top immediately
            window.scrollTo(0, 0);
            
            // Remove hash from URL without scrolling
            if (window.location.hash) {
                history.replaceState(null, null, window.location.pathname + window.location.search);
            }
            
            // Prevent scroll on hash change
            window.addEventListener('hashchange', function(e) {
                e.preventDefault();
                window.scrollTo(0, 0);
                history.replaceState(null, null, window.location.pathname + window.location.search);
            }, false);
        })();
        
        // Simple client-side filtering for Board Issuances (using jQuery, no reload)
        $(document).ready(function() {
            // Ensure page stays at top after filters are applied
            window.scrollTo(0, 0);
            let filterTimeout = null;

            function applyIssuanceFilters() {
                const type = $('#filterType').val();
                const year = $('#filterYear').val();
                const keyword = $('#filterKeyword').val().toLowerCase().trim();

                $('.issuance-item').each(function() {
                    const $item = $(this);
                    const itemType = $item.data('type') || '';
                    const itemYear = $item.data('year') ? String($item.data('year')) : '';
                    const itemKeywords = ($item.data('keywords') || '').toLowerCase();
                    const text = $item.text().toLowerCase();

                    let matchesType = true;
                    let matchesYear = true;
                    let matchesKeyword = true;

                    if (type) {
                        matchesType = (itemType === type);
                    }

                    if (year) {
                        matchesYear = (itemYear === year);
                    }

                    if (keyword) {
                        matchesKeyword = itemKeywords.includes(keyword) || text.includes(keyword);
                    }

                    if (matchesType && matchesYear && matchesKeyword) {
                        $item.show();
                    } else {
                        $item.hide();
                    }
                });

                // Always show resolutions section (regulations removed)
                const $resolutionsSection = $('#resolutionsSection');
                $resolutionsSection.show();
            }
            
            // Populate filters from URL parameters
            function populateFiltersFromURL() {
                const urlParams = new URLSearchParams(window.location.search);
                const type = urlParams.get('type');
                const year = urlParams.get('year');
                let keyword = urlParams.get('keyword');
                
                if (type) {
                    $('#filterType').val(type);
                }
                
                if (year) {
                    $('#filterYear').val(year);
                }
                
                if (keyword) {
                    // Properly decode URL-encoded keyword (handle both + and %20 for spaces)
                    keyword = decodeURIComponent(keyword.replace(/\+/g, ' '));
                    $('#filterKeyword').val(keyword);
                }
                
                // Apply filters after populating
                if (type || year || keyword) {
                    applyIssuanceFilters();
                }
            }

            // Populate filters from URL parameters on page load
            populateFiltersFromURL();
            
            $('#filterType').on('change', function() {
                applyIssuanceFilters();
            });

            $('#filterYear').on('change', function() {
                applyIssuanceFilters();
            });

            $('#filterKeyword').on('input', function() {
                clearTimeout(filterTimeout);
                filterTimeout = setTimeout(applyIssuanceFilters, 300);
            });

            $('#clearFilters').on('click', function() {
                $('#filterType').val('');
                $('#filterYear').val('');
                $('#filterKeyword').val('');
                applyIssuanceFilters();
            });
        });

        // View PDF in modal
        function viewPDF(pdfUrl, identifier, title, type) {
            const modal = document.getElementById('pdfModal');
            const iframe = document.getElementById('pdfViewer');
            const modalTitle = document.getElementById('pdfModalTitle');
            const downloadLink = document.getElementById('pdfDownloadLink');
            const viewNewTabLink = document.getElementById('pdfViewNewTabLink');

            // Set title based on type
            if (type === 'resolution') {
                modalTitle.textContent = 'Resolution Number: ' + (identifier || 'N/A');
            } else {
                modalTitle.textContent = 'Document Title: ' + (identifier || 'N/A');
            }

            iframe.src = pdfUrl;
            downloadLink.href = pdfUrl;
            // Use title for download filename, fallback to identifier if title not provided
            const downloadFilename = title || identifier || 'document';
            downloadLink.download = downloadFilename.replace(/[^a-z0-9]/gi, '_') + '.pdf';
            viewNewTabLink.href = pdfUrl;
            
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';
        }

        // Close PDF modal
        function closePDFModal() {
            const modal = document.getElementById('pdfModal');
            const iframe = document.getElementById('pdfViewer');

            // Stop video/audio if playing
            iframe.src = '';
            
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = 'auto';
        }

        // Close modal on outside click
        document.getElementById('pdfModal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closePDFModal();
            }
        });

        // Close modal on ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closePDFModal();
            }
        });
    </script>
</body>
</html>


