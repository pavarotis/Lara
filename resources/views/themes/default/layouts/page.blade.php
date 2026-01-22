@php
    $meta = $content->meta ?? [];
    $title = $content->title ?? config('app.name', 'LaraShop');
    $description = $meta['description'] ?? '';
    $keywords = $meta['keywords'] ?? '';
    $ogImage = $meta['og_image'] ?? null;
    
    // Load OG image media if provided
    $ogImageUrl = null;
    if ($ogImage) {
        $media = \App\Domain\Media\Models\Media::find($ogImage);
        if ($media) {
            $ogImageUrl = $media->url;
        }
    }
@endphp

@extends('layouts.public')

@section('title', $title)

@push('styles')
    <style>
        .prose {
            color: #2c2c2c;
        }
        .prose h1, .prose h2, .prose h3, .prose h4, .prose h5, .prose h6 {
            font-weight: 700;
            margin-top: 2em;
            margin-bottom: 1em;
        }
        .prose h1 { font-size: 2.25em; }
        .prose h2 { font-size: 1.875em; }
        .prose h3 { font-size: 1.5em; }
        .prose p {
            margin-bottom: 1.25em;
            line-height: 1.75;
        }
        .prose a {
            color: var(--color-primary, #3b82f6);
            text-decoration: underline;
        }
        .prose a:hover {
            text-decoration: none;
        }
        .prose img {
            max-width: 100%;
            height: auto;
            border-radius: 0.5rem;
        }
        .prose ul, .prose ol {
            margin-bottom: 1.25em;
            padding-left: 1.625em;
        }
        .prose li {
            margin-bottom: 0.5em;
        }
    </style>
@endpush

@push('meta')
    @php
        $currentUrl = url()->current();
        $siteName = config('app.name', 'LaraShop');
    @endphp
    
    <!-- Basic SEO -->
    @if($description)
        <meta name="description" content="{{ $description }}">
    @endif
    
    @if($keywords)
        <meta name="keywords" content="{{ $keywords }}">
    @endif
    
    <!-- Canonical URL -->
    <link rel="canonical" href="{{ $currentUrl }}">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $title }}">
    <meta property="og:site_name" content="{{ $siteName }}">
    <meta property="og:url" content="{{ $currentUrl }}">
    @if($description)
        <meta property="og:description" content="{{ $description }}">
    @endif
    @if($ogImageUrl)
        <meta property="og:image" content="{{ $ogImageUrl }}">
        <meta property="og:image:width" content="1200">
        <meta property="og:image:height" content="630">
    @endif
    
    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $title }}">
    @if($description)
        <meta name="twitter:description" content="{{ $description }}">
    @endif
    @if($ogImageUrl)
        <meta name="twitter:image" content="{{ $ogImageUrl }}">
    @endif
@endpush

@section('content')
    @if($isPreview ?? false)
        <!-- Preview Mode Banner -->
        <div class="bg-yellow-500 text-yellow-900 px-4 py-3 border-b border-yellow-600 sticky top-0 z-50">
            <div class="max-w-7xl mx-auto flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <span class="font-semibold">Preview Mode</span>
                    <span class="text-sm">You are viewing draft content. This page is not visible to public visitors.</span>
                </div>
                <a href="{{ route('admin.content.edit', $content->id ?? 0) }}" class="text-sm bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded transition-colors">
                    Edit Content
                </a>
            </div>
        </div>
    @endif
    
    <div class="min-h-screen">
        {!! $renderedContent !!}
    </div>
@endsection

