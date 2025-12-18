@php
    // Module views receive $module and $settings
    $title = $settings['title'] ?? '';
    $text = $settings['text'] ?? '';
    $link = $settings['link'] ?? null;
    $linkText = $settings['link_text'] ?? 'Learn More';
    $backgroundImageId = $settings['background_image_id'] ?? null;
    $backgroundColor = $settings['background_color'] ?? '#3b82f6';
@endphp

<div class="banner-module py-16 px-4 sm:px-6 lg:px-8" style="background-color: {{ $backgroundColor }};">
    @if($backgroundImageId)
        @php
            $bgMedia = \App\Domain\Media\Models\Media::find($backgroundImageId);
            $bgImageUrl = $bgMedia ? ($bgMedia->large_url ?? $bgMedia->url) : null;
        @endphp
        @if($bgImageUrl)
            <div class="absolute inset-0 opacity-20">
                <img src="{{ $bgImageUrl }}" alt="" class="w-full h-full object-cover">
            </div>
        @endif
    @endif
    
    <div class="relative z-10 max-w-4xl mx-auto text-center text-white">
        @if($title)
            <h2 class="text-3xl md:text-4xl font-bold mb-4">{{ $title }}</h2>
        @endif
        
        @if($text)
            <p class="text-lg md:text-xl mb-6">{{ $text }}</p>
        @endif
        
        @if($link)
            <a href="{{ $link }}" class="inline-block px-6 py-3 bg-white text-gray-900 font-semibold rounded-lg hover:bg-gray-100 transition-colors">
                {{ $linkText }}
            </a>
        @endif
    </div>
</div>

<style>
    .banner-module {
        @apply relative;
    }
</style>

