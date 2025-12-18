@php
    // Module views receive $module and $settings
    $imageId = $settings['image_id'] ?? null;
    $caption = $settings['caption'] ?? null;
    $alignment = $settings['alignment'] ?? 'center';
    
    // Load media if image ID provided
    $imageUrl = null;
    $imageSmall = null;
    $imageMedium = null;
    $imageLarge = null;
    if ($imageId) {
        $media = \App\Domain\Media\Models\Media::find($imageId);
        if ($media) {
            $imageUrl = $media->url;
            $imageSmall = $media->small_url ?? $media->url;
            $imageMedium = $media->medium_url ?? $media->url;
            $imageLarge = $media->large_url ?? $media->url;
        }
    }
    
    // Alignment classes
    $alignmentClasses = [
        'left' => 'text-left',
        'center' => 'text-center',
        'right' => 'text-right',
    ];
    $alignClass = $alignmentClasses[$alignment] ?? 'text-center';
    
    // Build srcset for responsive images
    $srcset = [];
    if ($imageSmall) $srcset[] = $imageSmall . ' 300w';
    if ($imageMedium) $srcset[] = $imageMedium . ' 600w';
    if ($imageLarge) $srcset[] = $imageLarge . ' 1200w';
    $srcsetString = !empty($srcset) ? implode(', ', $srcset) : null;
@endphp

@if($imageUrl)
    <div class="image-module py-8 {{ $alignClass }}">
        <div class="max-w-4xl mx-auto">
            <picture>
                @if($srcsetString)
                    <source srcset="{{ $srcsetString }}" sizes="(max-width: 768px) 100vw, 80vw">
                @endif
                <img 
                    src="{{ $imageUrl }}" 
                    alt="{{ $caption ?? '' }}"
                    class="w-full h-auto rounded-lg shadow-md"
                    loading="lazy"
                    @if($srcsetString)
                        srcset="{{ $srcsetString }}"
                        sizes="(max-width: 768px) 100vw, 80vw"
                    @endif
                >
            </picture>
            @if($caption)
                <p class="mt-4 text-sm text-gray-600 italic">{{ $caption }}</p>
            @endif
        </div>
    </div>
@endif

