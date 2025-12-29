<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Select Image - Media Library</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            margin: 0;
            padding: 20px;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f5f5f5;
        }
        .browse-header {
            background: white;
            padding: 15px 20px;
            border-bottom: 1px solid #ddd;
            margin: -20px -20px 20px -20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .browse-header h2 {
            margin: 0;
            font-size: 18px;
            color: #333;
        }
        .search-box {
            padding: 15px 20px;
            background: white;
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .search-box input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        .media-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 15px;
        }
        .media-item {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            cursor: pointer;
            transition: all 0.2s;
            border: 2px solid transparent;
        }
        .media-item:hover {
            border-color: #055498;
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .media-item img {
            width: 100%;
            height: 150px;
            object-fit: cover;
            display: block;
        }
        .media-item .file-name {
            padding: 8px;
            font-size: 12px;
            color: #666;
            text-overflow: ellipsis;
            overflow: hidden;
            white-space: nowrap;
        }
        .pagination {
            margin-top: 20px;
            text-align: center;
        }
        .pagination a {
            display: inline-block;
            padding: 8px 12px;
            margin: 0 4px;
            background: white;
            border: 1px solid #ddd;
            border-radius: 4px;
            text-decoration: none;
            color: #333;
        }
        .pagination a:hover {
            background: #055498;
            color: white;
            border-color: #055498;
        }
        .pagination .active {
            background: #055498;
            color: white;
            border-color: #055498;
        }
    </style>
</head>
<body>
    <div class="browse-header">
        <h2>Select Image</h2>
        <button onclick="window.close()" style="padding: 8px 16px; background: #055498; color: white; border: none; border-radius: 4px; cursor: pointer;">
            Close
        </button>
    </div>

    <div class="search-box">
        <input type="text" id="searchInput" placeholder="Search images..." onkeyup="searchImages()">
    </div>

    <div class="media-grid" id="mediaGrid">
        @forelse($mediaFiles as $media)
            @php
                $isImage = strpos($media->file_type, 'image/') === 0;
            @endphp
            @if($isImage)
                <div class="media-item" onclick="selectImage('{{ asset('storage/' . $media->file_path) }}')">
                    <img src="{{ asset('storage/' . $media->file_path) }}" alt="{{ $media->file_name }}">
                    <div class="file-name">{{ $media->file_name }}</div>
                </div>
            @endif
        @empty
            <div style="grid-column: 1 / -1; text-align: center; padding: 40px; color: #999;">
                <i class="fas fa-image" style="font-size: 48px; margin-bottom: 10px; display: block;"></i>
                <p>No images found</p>
            </div>
        @endforelse
    </div>

    @if($mediaFiles->hasPages())
    <div class="pagination">
        @php
            $queryParams = http_build_query([
                'CKEditor' => $CKEditor,
                'CKEditorFuncNum' => $CKEditorFuncNum,
                'langCode' => $langCode,
                'type' => request('type')
            ]);
        @endphp
        @if($mediaFiles->onFirstPage())
            <span style="display: inline-block; padding: 8px 12px; margin: 0 4px; color: #999;">Previous</span>
        @else
            <a href="{{ $mediaFiles->previousPageUrl() }}&{{ $queryParams }}">Previous</a>
        @endif

        @foreach($mediaFiles->getUrlRange(1, $mediaFiles->lastPage()) as $page => $url)
            @if($page == $mediaFiles->currentPage())
                <span class="active">{{ $page }}</span>
            @else
                <a href="{{ $url }}&{{ $queryParams }}">{{ $page }}</a>
            @endif
        @endforeach

        @if($mediaFiles->hasMorePages())
            <a href="{{ $mediaFiles->nextPageUrl() }}&{{ $queryParams }}">Next</a>
        @else
            <span style="display: inline-block; padding: 8px 12px; margin: 0 4px; color: #999;">Next</span>
        @endif
    </div>
    @endif

    <script>
        function selectImage(imageUrl) {
            // Call CKEditor callback function if available
            @if($CKEditorFuncNum)
            if (window.opener && window.opener.CKEDITOR) {
                window.opener.CKEDITOR.tools.callFunction({{ $CKEditorFuncNum }}, imageUrl);
                window.close();
            } else {
                alert('Selected: ' + imageUrl);
            }
            @else
            alert('Selected: ' + imageUrl);
            @endif
        }

        function searchImages() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const items = document.querySelectorAll('.media-item');
            
            items.forEach(item => {
                const fileName = item.querySelector('.file-name').textContent.toLowerCase();
                if (fileName.includes(searchTerm)) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });
        }
    </script>
</body>
</html>

