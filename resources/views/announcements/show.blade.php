@extends('layout')

@section('title', $announcement->title)

@section('content')
<div class="container mx-auto px-4 py-8 max-w-4xl">
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <!-- Banner Image -->
        @if($announcement->bannerImage)
            <div class="w-full">
                <img src="{{ asset('storage/' . $announcement->bannerImage->file_path) }}" alt="{{ $announcement->title }}" class="w-full h-64 md:h-96 object-cover">
            </div>
        @endif

        <div class="p-6 md:p-8">
            <!-- Title -->
            <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">{{ $announcement->title }}</h1>

            <!-- Meta Information -->
            <div class="flex flex-wrap items-center gap-4 mb-6 text-sm text-gray-600 border-b pb-4">
                <div class="flex items-center">
                    <i class="fas fa-user mr-2"></i>
                    <span>{{ $announcement->creator->first_name }} {{ $announcement->creator->last_name }}</span>
                </div>
                <div class="flex items-center">
                    <i class="fas fa-calendar mr-2"></i>
                    <span>{{ $announcement->created_at->format('F d, Y') }}</span>
                </div>
                <div class="flex items-center">
                    <i class="fas fa-tag mr-2"></i>
                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">
                        {{ $announcement->category_label ?? 'Public' }}
                    </span>
                </div>
            </div>

            <!-- Description (with auto-linked URLs) -->
            <div class="prose max-w-none text-gray-700 mb-6">
                {!! $announcement->description_with_links !!}
            </div>

            <!-- Back Button -->
            <div class="mt-8 pt-6 border-t">
                <a href="{{ route('announcements.index') }}" class="inline-flex items-center text-[#055498] hover:text-[#123a60]">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Announcements
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

