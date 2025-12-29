<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Announcements - Board Member Portal</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="https://upload.wikimedia.org/wikipedia/commons/thumb/f/f7/Dangerous_Drugs_Board_%28DDB%29.svg/1209px-Dangerous_Drugs_Board_%28DDB%29.svg.png">
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
    <script>
        // Initialize theme immediately before page renders to prevent flash
        (function() {
            const theme = localStorage.getItem('theme') || 
                (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
            if (theme === 'dark') {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>
    <style>
        .blog-post-card {
            transition: all 0.3s ease;
        }
        .blog-post-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px rgba(5, 84, 152, 0.15);
        }
        .gradient-text {
            background: linear-gradient(135deg, #055498, #123a60, #055498);
            background-size: 200% 200%;
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: gradient-shift 3s ease infinite;
        }
        @keyframes gradient-shift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        .line-clamp-3 {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        /* Pagination Styling */
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 0.5rem;
            margin-top: 2rem;
        }
        .pagination a,
        .pagination span {
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            text-decoration: none;
            transition: all 0.2s;
        }
        .pagination a {
            color: #055498;
            border: 1px solid #e5e7eb;
            background: white;
        }
        .pagination a:hover {
            background: #055498;
            color: white;
            border-color: #055498;
        }
        .pagination .active span {
            background: linear-gradient(135deg, #055498 0%, #123a60 100%);
            color: white;
            border: 1px solid #055498;
        }
        .dark .pagination a {
            background: #1e293b;
            border-color: #374151;
            color: #f1f5f9;
        }
        .dark .pagination a:hover {
            background: #055498;
            color: white;
        }
        .dark .pagination .active span {
            background: linear-gradient(135deg, #055498 0%, #123a60 100%);
        }
    </style>
</head>
<body class="bg-[#F9FAFB] dark:bg-[#0F172A] text-[#0A0A0A] dark:text-[#F1F5F9] transition-colors duration-300">
    @include('components.header')
    @include('components.theme-toggle-script')
    
    <!-- Announcements Page Header -->
    <div class="bg-gradient-to-r from-[#055498] to-[#123a60] py-12 sm:py-16">
        <div class="container mx-auto px-4 sm:px-6">
            <div class="max-w-4xl mx-auto text-center">
                <h1 class="text-3xl sm:text-4xl md:text-5xl font-bold text-white mb-4">
                    Announcements
                </h1>
                <p class="text-white/90 text-base sm:text-lg">
                    Stay informed with the latest updates and important information
                </p>
            </div>
        </div>
    </div>

    <!-- Search and Filter Section -->
    <div class="bg-white dark:bg-[#1e293b] border-b border-gray-200 dark:border-gray-700 sticky top-[109px] z-30 shadow-sm">
        <div class="container mx-auto px-4 sm:px-6 py-4">
            <div class="max-w-4xl mx-auto">
                <form method="GET" action="{{ route('announcements.index') }}" class="flex flex-col sm:flex-row gap-3">
                    <div class="flex-1 relative">
                        <input 
                            type="text" 
                            name="search" 
                            value="{{ $search }}" 
                            placeholder="Search announcements..." 
                            class="w-full px-4 py-3 pl-12 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none bg-white dark:bg-[#0F172A] text-gray-900 dark:text-white"
                        >
                        <i class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    </div>
                    <button 
                        type="submit" 
                        class="px-6 py-3 bg-gradient-to-r from-[#055498] to-[#123a60] text-white font-semibold rounded-lg hover:from-[#123a60] hover:to-[#055498] transition-all duration-200 shadow-md hover:shadow-lg"
                    >
                        <i class="fas fa-search mr-2"></i>Search
                    </button>
                    @if($search)
                    <a 
                        href="{{ route('announcements.index') }}" 
                        class="px-6 py-3 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-semibold rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors"
                    >
                        <i class="fas fa-times mr-2"></i>Clear
                    </a>
                    @endif
                </form>
            </div>
        </div>
    </div>

    <!-- Announcements Content -->
    <div class="container mx-auto px-4 sm:px-6 py-8 sm:py-12">
        <div class="max-w-4xl mx-auto">
            @if($announcements->count() > 0)
                <!-- Blog-style Posts -->
                <div class="space-y-8">
                    @foreach($announcements as $announcement)
                        <article class="blog-post-card bg-white dark:bg-[#1e293b] rounded-xl shadow-md overflow-hidden border border-gray-200 dark:border-gray-700">
                            <!-- Banner Image -->
                            @if($announcement->bannerImage)
                                <div class="w-full h-64 sm:h-80 overflow-hidden">
                                    <img 
                                        src="{{ asset('storage/' . $announcement->bannerImage->file_path) }}" 
                                        alt="{{ $announcement->title }}" 
                                        class="w-full h-full object-cover hover:scale-105 transition-transform duration-500 cursor-pointer"
                                        onclick="openAnnouncementModal({{ $announcement->id }})"
                                    >
                                </div>
                            @else
                                <div class="w-full h-64 sm:h-80 bg-gradient-to-br from-[#055498] to-[#123a60] flex items-center justify-center cursor-pointer" onclick="openAnnouncementModal({{ $announcement->id }})">
                                    <i class="fas fa-bullhorn text-6xl text-white opacity-50"></i>
                                </div>
                            @endif
                            
                            <!-- Post Content -->
                            <div class="p-6 sm:p-8">
                                <!-- Meta Information -->
                                <div class="flex flex-wrap items-center gap-4 mb-4 text-sm text-gray-500 dark:text-gray-400">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-[#055498] to-[#123a60] flex items-center justify-center text-white font-semibold text-xs mr-2">
                                            {{ strtoupper(substr($announcement->creator->first_name, 0, 1) . substr($announcement->creator->last_name, 0, 1)) }}
                                        </div>
                                        <span>{{ $announcement->creator->first_name }} {{ $announcement->creator->last_name }}</span>
                                    </div>
                                    <div class="flex items-center">
                                        <i class="far fa-calendar-alt mr-2"></i>
                                        <span>{{ $announcement->created_at->format('F d, Y') }}</span>
                                    </div>
                                    <div class="flex items-center">
                                        <i class="far fa-clock mr-2"></i>
                                        <span>{{ $announcement->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>

                                <!-- Title -->
                                <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white mb-4 cursor-pointer hover:text-[#055498] dark:hover:text-[#055498] transition-colors" onclick="openAnnouncementModal({{ $announcement->id }})" style="color: #055498;">
                                    {{ $announcement->title }}
                                </h2>

                                <!-- Excerpt -->
                                <div class="text-gray-700 dark:text-gray-300 mb-6 prose prose-sm max-w-none line-clamp-3">
                                    {!! Str::limit(strip_tags($announcement->description), 200) !!}
                                </div>

                                <!-- Read More Button -->
                                <button 
                                    onclick="openAnnouncementModal({{ $announcement->id }})"
                                    class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-[#055498] to-[#123a60] text-white font-semibold rounded-lg hover:from-[#123a60] hover:to-[#055498] transition-all duration-200 shadow-md hover:shadow-lg transform hover:scale-105"
                                >
                                    Read More
                                    <i class="fas fa-arrow-right ml-2"></i>
                                </button>
                            </div>
                        </article>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-8">
                    {{ $announcements->links() }}
                </div>
            @else
                <!-- Empty State -->
                <div class="bg-white dark:bg-[#1e293b] rounded-xl shadow-md border border-gray-200 dark:border-gray-700 p-12 text-center">
                    <i class="fas fa-bullhorn text-6xl text-gray-400 dark:text-gray-500 mb-4"></i>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                        @if($search)
                            No announcements found
                        @else
                            No Announcements Available
                        @endif
                    </h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-6">
                        @if($search)
                            Try adjusting your search terms or <a href="{{ route('announcements.index') }}" class="text-[#055498] hover:underline">view all announcements</a>.
                        @else
                            There are no announcements available at this time.
                        @endif
                    </p>
                    @if($search)
                        <a 
                            href="{{ route('announcements.index') }}" 
                            class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-[#055498] to-[#123a60] text-white font-semibold rounded-lg hover:from-[#123a60] hover:to-[#055498] transition-all duration-200"
                        >
                            <i class="fas fa-arrow-left mr-2"></i>
                            View All Announcements
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <!-- Announcement Modal -->
    @include('components.announcement-modal')

    @include('components.footer')

    <script>
        // Set up axios defaults
        axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
    </script>
    
    <!-- Global PDF Modal -->
    @include('components.pdf-modal')
</body>
</html>
