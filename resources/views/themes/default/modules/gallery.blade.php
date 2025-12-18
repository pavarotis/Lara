@php
    // Module views receive $module and $settings
    $images = $settings['images'] ?? [];
    $columns = $settings['columns'] ?? 3;
    
    // Load media objects
    // Support both formats:
    // 1. Array of objects: [{id: 1, url: '...'}, ...] (from media picker)
    // 2. Array of IDs: [1, 2, 3] (legacy format)
    $mediaItems = [];
    foreach ($images as $item) {
        $imageId = null;
        
        // Check if item is an object/array with 'id' key
        if (is_array($item) && isset($item['id'])) {
            $imageId = $item['id'];
        } elseif (is_object($item) && isset($item->id)) {
            $imageId = $item->id;
        } elseif (is_numeric($item)) {
            // Legacy format: direct ID
            $imageId = $item;
        }
        
        if ($imageId) {
            $media = \App\Domain\Media\Models\Media::find($imageId);
            if ($media && $media->type === 'image') {
                $mediaItems[] = $media;
            }
        }
    }
    
    // Grid columns classes
    $gridClasses = [
        1 => 'grid-cols-1',
        2 => 'grid-cols-1 md:grid-cols-2',
        3 => 'grid-cols-1 md:grid-cols-2 lg:grid-cols-3',
        4 => 'grid-cols-1 md:grid-cols-2 lg:grid-cols-4',
    ];
    $gridClass = $gridClasses[$columns] ?? 'grid-cols-1 md:grid-cols-2 lg:grid-cols-3';
@endphp

@if(count($mediaItems) > 0)
    <div class="gallery-module py-12">
        <div class="max-w-7xl mx-auto">
            <div class="grid {{ $gridClass }} gap-4 md:gap-6">
                @foreach($mediaItems as $media)
                    <div class="relative group overflow-hidden rounded-lg shadow-md hover:shadow-xl transition-all duration-300 aspect-square">
                        <a href="{{ $media->url }}" class="block h-full" data-lightbox="gallery-{{ $loop->index }}">
                            @php
                                $thumbnail = $media->thumbnail_url ?? $media->url;
                                $small = $media->small_url ?? $media->url;
                                $medium = $media->medium_url ?? $media->url;
                                $large = $media->large_url ?? $media->url;
                                $srcset = [];
                                if ($small) $srcset[] = $small . ' 300w';
                                if ($medium) $srcset[] = $medium . ' 600w';
                                if ($large) $srcset[] = $large . ' 1200w';
                                $srcsetString = !empty($srcset) ? implode(', ', $srcset) : null;
                            @endphp
                            <picture>
                                @if($srcsetString)
                                    <source srcset="{{ $srcsetString }}" sizes="(max-width: 768px) 100vw, (max-width: 1024px) 50vw, 33vw">
                                @endif
                                <img 
                                    src="{{ $thumbnail }}" 
                                    alt="{{ $media->name }}"
                                    class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
                                    loading="lazy"
                                    @if($srcsetString)
                                        srcset="{{ $srcsetString }}"
                                        sizes="(max-width: 768px) 100vw, (max-width: 1024px) 50vw, 33vw"
                                    @endif
                                >
                            </picture>
                            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/30 transition-colors duration-300 flex items-center justify-center">
                                <svg class="w-12 h-12 text-white opacity-0 group-hover:opacity-100 transition-opacity duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" />
                                </svg>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endif

