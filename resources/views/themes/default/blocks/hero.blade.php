@php
    $title = $title ?? '';
    $subtitle = $subtitle ?? '';
    // Support both image_id (from media picker) and legacy image (URL)
    $imageId = $image_id ?? $image ?? null;
    $ctaText = $cta_text ?? null;
    $ctaLink = $cta_link ?? null;
    
    // Load media if image ID provided
    $imageUrl = null;
    $imageThumbnail = null;
    $imageSmall = null;
    $imageMedium = null;
    $imageLarge = null;
    if ($imageId) {
        // If imageId is numeric, it's a media ID
        if (is_numeric($imageId)) {
            $media = \App\Domain\Media\Models\Media::find($imageId);
            if ($media) {
                // Use large variant for hero images (better quality for large displays)
                $imageUrl = $media->large_url ?? $media->url;
                $imageSmall = $media->small_url ?? $media->url;
                $imageMedium = $media->medium_url ?? $media->url;
                $imageLarge = $media->large_url ?? $media->url;
                $imageThumbnail = $media->thumbnail_url ?? $media->url;
            }
        } else {
            // Legacy support: imageId is a URL
            $imageUrl = $imageId;
            $imageSmall = $imageId;
            $imageMedium = $imageId;
            $imageLarge = $imageId;
            $imageThumbnail = $imageId;
        }
    }
    // Also check if image_url is provided directly (from media picker)
    if (! $imageUrl && isset($image_url)) {
        $imageUrl = $image_url;
        $imageSmall = $image_url;
        $imageMedium = $image_url;
        $imageLarge = $image_url;
        $imageThumbnail = $image_thumbnail_url ?? $image_url;
    }
    
    // Build srcset for responsive images
    $srcset = [];
    if ($imageSmall) $srcset[] = $imageSmall . ' 300w';
    if ($imageMedium) $srcset[] = $imageMedium . ' 600w';
    if ($imageLarge) $srcset[] = $imageLarge . ' 1200w';
    $srcsetString = !empty($srcset) ? implode(', ', $srcset) : null;
@endphp

<section class="relative w-full h-[60vh] min-h-[400px] max-h-[800px] flex items-center justify-center overflow-hidden bg-gray-900">
    @if($imageUrl)
        <div class="absolute inset-0 z-0">
            <picture>
                @if($srcsetString)
                    <source srcset="{{ $srcsetString }}" sizes="100vw">
                @endif
                <img 
                    src="{{ $imageUrl }}" 
                    alt="{{ $title }}"
                    class="w-full h-full object-cover"
                    loading="eager"
                    @if($srcsetString)
                        srcset="{{ $srcsetString }}"
                        sizes="100vw"
                    @endif
                >
            </picture>
            <!-- Overlay gradient for better text readability -->
            <div class="absolute inset-0 bg-gradient-to-b from-black/50 via-black/40 to-black/60"></div>
        </div>
    @endif
    
    <div class="relative z-10 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-white drop-shadow-lg">
        @if($title)
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-4 animate-fade-in-up text-shadow-lg">
                {{ $title }}
            </h1>
        @endif
        
        @if($subtitle)
            <p class="text-xl md:text-2xl mb-8 animate-fade-in-up animate-delay-100 text-gray-100 text-shadow">
                {{ $subtitle }}
            </p>
        @endif
        
        @if($ctaText && $ctaLink)
            <a 
                href="{{ $ctaLink }}" 
                class="inline-block px-8 py-4 bg-primary text-white font-semibold rounded-lg hover:bg-primary/90 transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl animate-fade-in-up animate-delay-200"
            >
                {{ $ctaText }}
            </a>
        @endif
    </div>
</section>

<style>
    .text-shadow-lg {
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5), 0 4px 8px rgba(0, 0, 0, 0.3);
    }
    .text-shadow {
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.5);
    }
</style>

