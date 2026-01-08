@props([
    'src',
    'alt' => '',
    'lazy' => true,
    'variants' => null,
    'class' => '',
])

@php
    use Illuminate\Support\Facades\Storage;
    
    // Get variants from media model or passed directly
    $webpSrcset = '';
    $avifSrcset = '';
    $srcset = '';

    if ($variants) {
        $webpPath = $variants['webp'] ?? null;
        $avifPath = $variants['avif'] ?? null;
        $sizes = $variants['sizes'] ?? [];

        // Generate WebP srcset
        if ($webpPath && $sizes) {
            $webpSrcset = collect($sizes)->map(function ($path, $width) {
                return Storage::url($path) . ' ' . $width . 'w';
            })->implode(', ');
        }

        // Generate AVIF srcset
        if ($avifPath && $sizes) {
            $avifSrcset = collect($sizes)->map(function ($path, $width) {
                return Storage::url($path) . ' ' . $width . 'w';
            })->implode(', ');
        }

        // Generate original format srcset
        if ($sizes) {
            $srcset = collect($sizes)->map(function ($path, $width) {
                return Storage::url($path) . ' ' . $width . 'w';
            })->implode(', ');
        }
    }
@endphp

<picture>
    @if($avifSrcset)
        <source srcset="{{ $avifSrcset }}" type="image/avif">
    @endif
    @if($webpSrcset)
        <source srcset="{{ $webpSrcset }}" type="image/webp">
    @endif
    <img 
        src="{{ Storage::url($src) }}" 
        @if($srcset) srcset="{{ $srcset }}" @endif
        loading="{{ $lazy ? 'lazy' : 'eager' }}"
        alt="{{ $alt }}"
        class="{{ $class }}"
    >
</picture>
