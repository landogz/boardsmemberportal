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
        .board-issuances-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem 1rem;
        }

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
        /* Accordion by year */
        .issuance-accordion-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            text-align: left;
            padding: 0.75rem 1rem;
            background: #f3f4f6;
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            font-weight: 600;
            color: #374151;
            cursor: pointer;
            transition: background 0.2s;
        }
        .dark .issuance-accordion-header {
            background: #374151;
            border-color: #4b5563;
            color: #e5e7eb;
        }
        .issuance-accordion-header:hover {
            background: #e5e7eb;
        }
        .dark .issuance-accordion-header:hover {
            background: #4b5563;
        }
        .issuance-accordion-icon {
            width: 1.5rem;
            height: 1.5rem;
            border-radius: 9999px;
            border: 1px solid #9ca3af;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            transition: transform 0.2s;
        }
        .issuance-accordion-panel[aria-expanded="true"] .issuance-accordion-icon {
            transform: rotate(45deg);
        }
        .issuance-accordion-panel-content {
            overflow: hidden;
            transition: all 0.2s ease;
        }
    </style>
</head>
<body class="bg-[#F9FAFB] dark:bg-[#0F172A] text-[#0A0A0A] dark:text-[#F1F5F9] transition-colors duration-300">
    @include('components.header')
    @include('components.theme-toggle-script')

    <div class="board-issuances-container">
        <div class="mb-6 sm:mb-8 text-center">
            <h1 class="text-3xl sm:text-4xl font-bold mb-2" style="color: #055498;">Board Issuances</h1>
            <p class="text-gray-600 dark:text-gray-400 text-sm sm:text-base">
                Access board resolutions and regulations relevant to your role.
            </p>
        </div>

        <!-- Search & Filter (GET form for server-side filtering and pagination) -->
        <form method="get" action="{{ route('board-issuances') }}" id="issuanceFilterForm" class="mb-6 sm:mb-8 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 sm:p-5">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                <div>
                    <label for="filterType" class="block text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1">Type</label>
                    <select name="type" id="filterType" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-sm text-gray-800 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-[#055498] focus:border-[#055498]">
                        <option value="">All Types</option>
                        <option value="regulation" {{ request('type') === 'regulation' ? 'selected' : '' }}>Board Regulations</option>
                        <option value="resolution" {{ request('type') === 'resolution' ? 'selected' : '' }}>Board Resolutions</option>
                    </select>
                </div>
                <div>
                    <label for="filterYear" class="block text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1">Select Year</label>
                    <select name="year" id="filterYear" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-sm text-gray-800 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-[#055498] focus:border-[#055498]">
                        <option value="">All Years</option>
                        @foreach($years as $y)
                            <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label for="filterKeyword" class="block text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1">Enter Keyword</label>
                    <div class="flex">
                        <input 
                            type="text" 
                            name="keyword" 
                            id="filterKeyword" 
                            value="{{ request('keyword') }}"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-l-lg bg-white dark:bg-gray-800 text-sm text-gray-800 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-[#055498] focus:border-[#055498]" 
                            placeholder="Keyword"
                        >
                        <button type="submit" class="px-3 sm:px-4 py-2 text-xs sm:text-sm font-semibold rounded-r-lg text-white" style="background: linear-gradient(135deg, #055498 0%, #123a60 100%);">
                            Search
                        </button>
                    </div>
                </div>
            </div>
            <div class="mt-2 flex justify-end">
                <a href="{{ route('board-issuances') }}" id="clearFilters" class="text-sm text-gray-500 dark:text-gray-400 hover:underline">Clear filters</a>
            </div>
        </form>

        <!-- Board Regulations and Resolutions -->
        <div id="issuancesContainer" class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Board Regulations Section -->
            <div id="regulationsSection" class="bg-white dark:bg-gray-800 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700 p-5 sm:p-6">
                <div class="flex items-center mb-4">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center mr-3" style="background-color: rgba(5, 84, 152, 0.1);">
                        <i class="fas fa-balance-scale text-lg" style="color: #055498;"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-semibold" style="color: #055498;">Board Regulations</h2>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Effective regulations and policy guidelines</p>
                        <a href="https://ddb.gov.ph/board-regulations/" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 mt-1.5 text-xs font-medium transition hover:underline" style="color: #055498;">
                            <i class="fas fa-external-link-alt text-[10px]"></i>
                            View all on DDB Website
                        </a>
                    </div>
                </div>
                <div class="border-t border-gray-200 dark:border-gray-700 pt-4 mt-2" id="regulationsList">
                    @if($regulationYears->isEmpty())
                        <div class="text-center py-8">
                            <i class="fas fa-balance-scale text-4xl text-gray-300 dark:text-gray-600 mb-3"></i>
                            <p class="text-sm text-gray-500 dark:text-gray-400">No board regulations available at this time.</p>
                        </div>
                    @else
                        <div class="space-y-3" x-data="{ openYear: '{{ $regulationYears->first() }}' }" x-init="$nextTick(() => loadSeries('regulation', '{{ $regulationYears->first() }}'))">
                            @foreach($regulationYears as $yr)
                                <div class="issuance-accordion-panel rounded-lg border border-gray-200 dark:border-gray-700" :aria-expanded="openYear === '{{ $yr }}'">
                                    <button type="button" class="issuance-accordion-header w-full flex items-center justify-between rounded-lg" @click="const wasOpen = openYear === '{{ $yr }}'; openYear = wasOpen ? null : '{{ $yr }}'; if (!wasOpen) $nextTick(() => loadSeries('regulation', '{{ $yr }}'));">
                                        <span>Series {{ $yr }}</span>
                                        <span class="issuance-accordion-icon" :class="{ 'rotate-45': openYear === '{{ $yr }}' }">+</span>
                                    </button>
                                    <div class="issuance-accordion-panel-content border-t border-gray-200 dark:border-gray-700" x-show="openYear === '{{ $yr }}'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                                        <div class="series-content p-4" data-type="regulation" data-year="{{ $yr }}" id="series-regulation-{{ $yr }}">
                                            <div class="series-placeholder text-center py-4 text-gray-500 dark:text-gray-400 text-sm">Open to load</div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
            <!-- Board Resolutions Section -->
            <div id="resolutionsSection" class="bg-white dark:bg-gray-800 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700 p-5 sm:p-6">
            <div class="flex items-center mb-4">
                <div class="w-10 h-10 rounded-full flex items-center justify-center mr-3" style="background-color: rgba(5, 84, 152, 0.1);">
                    <i class="fas fa-gavel text-lg" style="color: #055498;"></i>
                </div>
                <div>
                    <h2 class="text-xl font-semibold" style="color: #055498;">Board Resolutions</h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Effective resolutions and related issuances</p>
                    <a href="https://ddb.gov.ph/board-resolutions-2/" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 mt-1.5 text-xs font-medium transition hover:underline" style="color: #055498;">
                        <i class="fas fa-external-link-alt text-[10px]"></i>
                        View all on DDB Website
                    </a>
                </div>
            </div>
                <div class="border-t border-gray-200 dark:border-gray-700 pt-4 mt-2" id="resolutionsList">
                    @if($documentYears->isEmpty())
                        <div class="text-center py-8">
                            <i class="fas fa-gavel text-4xl text-gray-300 dark:text-gray-600 mb-3"></i>
                            <p class="text-sm text-gray-500 dark:text-gray-400">No board resolutions available at this time.</p>
                        </div>
                    @else
                        <div class="space-y-3" x-data="{ openYear: '{{ $documentYears->first() }}' }" x-init="$nextTick(() => loadSeries('resolution', '{{ $documentYears->first() }}'))">
                            @foreach($documentYears as $yr)
                                <div class="issuance-accordion-panel rounded-lg border border-gray-200 dark:border-gray-700" :aria-expanded="openYear === '{{ $yr }}'">
                                    <button type="button" class="issuance-accordion-header w-full flex items-center justify-between rounded-lg" @click="const wasOpen = openYear === '{{ $yr }}'; openYear = wasOpen ? null : '{{ $yr }}'; if (!wasOpen) $nextTick(() => loadSeries('resolution', '{{ $yr }}'));">
                                        <span>Series {{ $yr }}</span>
                                        <span class="issuance-accordion-icon" :class="{ 'rotate-45': openYear === '{{ $yr }}' }">+</span>
                                    </button>
                                    <div class="issuance-accordion-panel-content border-t border-gray-200 dark:border-gray-700" x-show="openYear === '{{ $yr }}'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                                        <div class="series-content p-4" data-type="resolution" data-year="{{ $yr }}" id="series-resolution-{{ $yr }}">
                                            <div class="series-placeholder text-center py-4 text-gray-500 dark:text-gray-400 text-sm">Open to load</div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
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

            <!-- Modal metadata: description, date, creator (with image) -->
            <div id="pdfModalMeta" class="px-4 lg:px-6 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50 text-sm space-y-2 hidden">
                <p id="pdfModalDescription" class="text-gray-600 dark:text-gray-300 whitespace-pre-wrap"></p>
                <div class="flex flex-wrap items-center gap-4 text-gray-500 dark:text-gray-400">
                    <span id="pdfModalDateWrap"><i class="fas fa-calendar-alt mr-1"></i><span id="pdfModalDate"></span></span>
                    <span id="pdfModalCreatorWrap" class="flex items-center gap-2">
                        <img id="pdfModalCreatorImage" src="" alt="" class="w-8 h-8 rounded-full object-cover border border-gray-300 dark:border-gray-600 hidden">
                        <span id="pdfModalCreator"></span>
                    </span>
                </div>
            </div>

            <!-- Modal Body -->
            <div class="flex-1 overflow-hidden p-4 lg:p-6 relative">
                <div id="pdfViewerContainerUser" class="w-full h-full min-h-0 border border-gray-300 dark:border-gray-600 rounded-lg relative overflow-hidden">
                    <iframe 
                        id="pdfViewer" 
                        src="" 
                        class="w-full h-full min-h-0 block" 
                        frameborder="0"
                    ></iframe>
                </div>
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
        var BOARD_ISSUANCES_DATA_URL = @json(route('board-issuances.data'));

        function escapeAttr(s) {
            if (s == null) return '';
            return String(s)
                .replace(/&/g, '&amp;')
                .replace(/"/g, '&quot;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;');
        }

        function buildCardHtml(item) {
            var id = item.type === 'regulation' ? 'regulation-' + item.id : 'resolution-' + item.id;
            var cursor = item.has_pdf ? 'cursor-pointer' : 'cursor-default';
            var attrs = 'id="' + escapeAttr(id) + '" class="issuance-item issuance-card bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 hover:shadow-md transition-all duration-200 scroll-mt-20 ' + cursor + '" data-type="' + escapeAttr(item.type) + '" data-year="' + escapeAttr(item.year) + '"';
            if (item.has_pdf && item.pdf_url) {
                attrs += ' data-pdf-url="' + escapeAttr(item.pdf_url) + '" data-title="' + escapeAttr(item.title) + '" data-description="' + escapeAttr(item.description || '') + '" data-date="' + escapeAttr(item.date || '') + '" data-creator="' + escapeAttr(item.creator || '') + '" data-creator-image="' + escapeAttr(item.creator_image || '') + '" onclick="viewPDFWithMeta(this)"';
            }
            return '<div ' + attrs + '><h3 class="text-sm font-bold text-gray-800 dark:text-gray-100 line-clamp-2">' + escapeAttr(item.title) + '</h3></div>';
        }

        function buildPaginationHtml(type, year, pagination) {
            var cur = pagination.current_page;
            var last = pagination.last_page;
            var total = pagination.total;
            if (total === 0) return '';
            var prevDisabled = cur <= 1 ? ' disabled' : '';
            var nextDisabled = cur >= last ? ' disabled' : '';
            var prevPage = Math.max(1, cur - 1);
            var nextPage = Math.min(last, cur + 1);
            return '<div class="series-pagination mt-3 flex flex-wrap items-center justify-between gap-2 text-sm">' +
                '<button type="button" class="series-pagination-btn px-3 py-1.5 rounded border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed" data-type="' + escapeAttr(type) + '" data-year="' + escapeAttr(year) + '" data-page="' + prevPage + '"' + prevDisabled + '>Prev</button>' +
                '<span class="text-gray-600 dark:text-gray-400">Page ' + cur + ' of ' + last + '</span>' +
                '<button type="button" class="series-pagination-btn px-3 py-1.5 rounded border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed" data-type="' + escapeAttr(type) + '" data-year="' + escapeAttr(year) + '" data-page="' + nextPage + '"' + nextDisabled + '>Next</button>' +
                '</div>';
        }

        function loadSeries(type, year, page) {
            page = page || 1;
            var container = document.getElementById('series-' + type + '-' + year);
            if (!container) return;
            var keyword = (document.getElementById('filterKeyword') && document.getElementById('filterKeyword').value) || '';
            container.innerHTML = '<div class="text-center py-6 text-gray-500 dark:text-gray-400"><i class="fas fa-spinner fa-spin mr-2"></i>Loading...</div>';
            var url = BOARD_ISSUANCES_DATA_URL + '?type=' + encodeURIComponent(type) + '&year=' + encodeURIComponent(year) + '&page=' + page + (keyword ? '&keyword=' + encodeURIComponent(keyword) : '');
            fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    var html = '<div class="space-y-3">';
                    (data.items || []).forEach(function(item) {
                        html += buildCardHtml(item);
                    });
                    html += '</div>';
                    html += buildPaginationHtml(type, year, data.pagination || { current_page: 1, last_page: 1, total: 0 });
                    container.innerHTML = html;
                    container.querySelectorAll('.series-pagination-btn').forEach(function(btn) {
                        btn.addEventListener('click', function() {
                            var t = btn.getAttribute('data-type');
                            var y = btn.getAttribute('data-year');
                            var p = parseInt(btn.getAttribute('data-page'), 10);
                            if (btn.disabled) return;
                            loadSeries(t, y, p);
                        });
                    });
                })
                .catch(function() {
                    container.innerHTML = '<div class="text-center py-4 text-red-500 dark:text-red-400 text-sm">Failed to load. Try again.</div>';
                });
        }

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
        
        // Layout and section visibility based on Type filter (server already filtered data)
        $(document).ready(function() {
            window.scrollTo(0, 0);

            function applyIssuanceLayout() {
                const type = $('#filterType').val();
                const $container = $('#issuancesContainer');
                const $resolutionsSection = $('#resolutionsSection');
                const $regulationsSection = $('#regulationsSection');

                if (type === 'regulation') {
                    $container.removeClass('md:grid-cols-2').addClass('md:grid-cols-1');
                    $resolutionsSection.hide();
                    $regulationsSection.show();
                } else if (type === 'resolution') {
                    $container.removeClass('md:grid-cols-2').addClass('md:grid-cols-1');
                    $regulationsSection.hide();
                    $resolutionsSection.show();
                } else {
                    $container.removeClass('md:grid-cols-1').addClass('md:grid-cols-2');
                    $regulationsSection.show();
                    $resolutionsSection.show();
                }
            }

            applyIssuanceLayout();

            // Type or Year change: submit form for server-side filter + pagination
            $('#filterType, #filterYear').on('change', function() {
                $('#issuanceFilterForm').submit();
            });
        });

        // Call viewPDF from card click with data from data attributes
        function viewPDFWithMeta(el) {
            const pdfUrl = el.getAttribute('data-pdf-url');
            const title = el.getAttribute('data-title') || '';
            const type = (el.getAttribute('data-type') === 'resolution') ? 'resolution' : 'document';
            const description = el.getAttribute('data-description') || '';
            const date = el.getAttribute('data-date') || '';
            const creator = el.getAttribute('data-creator') || '';
            const creatorImage = el.getAttribute('data-creator-image') || '';
            viewPDF(pdfUrl, title, title, type, description, date, creator, creatorImage);
        }

        // View PDF in modal (user side); optional meta: description, date, creator, creatorImage
        function viewPDF(pdfUrl, identifier, title, type, description, date, creator, creatorImage) {
            const modal = document.getElementById('pdfModal');
            const iframe = document.getElementById('pdfViewer');
            const modalTitle = document.getElementById('pdfModalTitle');
            const downloadLink = document.getElementById('pdfDownloadLink');
            const viewNewTabLink = document.getElementById('pdfViewNewTabLink');
            const metaSection = document.getElementById('pdfModalMeta');
            const descEl = document.getElementById('pdfModalDescription');
            const dateEl = document.getElementById('pdfModalDate');
            const dateWrap = document.getElementById('pdfModalDateWrap');
            const creatorEl = document.getElementById('pdfModalCreator');
            const creatorWrap = document.getElementById('pdfModalCreatorWrap');
            const creatorImgEl = document.getElementById('pdfModalCreatorImage');

            // Set title based on type
            if (type === 'resolution') {
                modalTitle.textContent = 'Resolution: ' + (identifier || 'N/A');
            } else {
                modalTitle.textContent = (identifier || 'Document');
            }

            // Show description, date, creator (with image)
            descEl.textContent = description || '';
            dateEl.textContent = date || '';
            creatorEl.textContent = creator || '';
            dateWrap.style.display = date ? '' : 'none';
            creatorWrap.style.display = creator ? '' : 'none';
            if (creatorImage) {
                creatorImgEl.src = creatorImage;
                creatorImgEl.alt = creator || 'Creator';
                creatorImgEl.classList.remove('hidden');
            } else {
                creatorImgEl.classList.add('hidden');
                creatorImgEl.removeAttribute('src');
            }
            metaSection.classList.toggle('hidden', !description && !date && !creator);

            // Build absolute URL and hide built-in PDF toolbar/header
            const absoluteUrl = pdfUrl.startsWith('http') 
                ? pdfUrl 
                : (window.location.origin + (pdfUrl.startsWith('/') ? '' : '/') + pdfUrl);

            let pdfUrlWithParams = absoluteUrl;
            if (!pdfUrlWithParams.includes('#')) {
                pdfUrlWithParams += '#toolbar=0&navpanes=0';
            } else if (!pdfUrlWithParams.includes('toolbar=')) {
                pdfUrlWithParams += '&toolbar=0&navpanes=0';
            }

            iframe.src = pdfUrlWithParams;
            downloadLink.href = absoluteUrl;
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


