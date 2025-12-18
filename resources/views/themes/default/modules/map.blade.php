@php
    // Module views receive $module and $settings
    $title = $settings['title'] ?? 'Location';
    $address = $settings['address'] ?? '';
    $latitude = $settings['latitude'] ?? null;
    $longitude = $settings['longitude'] ?? null;
    $zoom = $settings['zoom'] ?? 15;
    $height = $settings['height'] ?? '400px';
    
    // Google Maps embed URL
    $mapUrl = null;
    if ($latitude && $longitude) {
        $mapUrl = "https://www.google.com/maps/embed/v1/place?key=" . config('services.google.maps_api_key', '') . "&q={$latitude},{$longitude}&zoom={$zoom}";
    } elseif ($address) {
        $mapUrl = "https://www.google.com/maps/embed/v1/place?key=" . config('services.google.maps_api_key', '') . "&q=" . urlencode($address) . "&zoom={$zoom}";
    }
@endphp

<div class="map-module py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @if($title)
            <h2 class="text-3xl font-bold mb-6 text-center">{{ $title }}</h2>
        @endif
        
        @if($mapUrl)
            <div class="rounded-lg overflow-hidden shadow-md" style="height: {{ $height }};">
                <iframe
                    width="100%"
                    height="100%"
                    style="border:0"
                    loading="lazy"
                    allowfullscreen
                    referrerpolicy="no-referrer-when-downgrade"
                    src="{{ $mapUrl }}">
                </iframe>
            </div>
        @elseif($address)
            <div class="text-center text-gray-500">
                <p>Map configuration required. Please set latitude/longitude or Google Maps API key.</p>
                <p class="mt-2">{{ $address }}</p>
            </div>
        @else
            <p class="text-center text-gray-500">No location information provided.</p>
        @endif
    </div>
</div>

