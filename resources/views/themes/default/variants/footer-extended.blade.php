@php
    $business = $currentBusiness ?? null;
    if ($business) {
        $variant = app(\App\Domain\Themes\Services\GetFooterVariantService::class)->getVariant($business);
        $showOpeningHours = $variant['show_opening_hours'] ?? false;
        $showSocial = $variant['show_social'] ?? false;
    } else {
        $showOpeningHours = false;
        $showSocial = false;
    }
@endphp

<footer class="footer-extended bg-gray-900 text-white py-12">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div>
                <h3 class="text-lg font-semibold mb-4">{{ $business ? $business->name : config('app.name', 'LaraShop') }}</h3>
                @if($business && isset($business->settings['address']))
                    <p class="text-gray-400">{{ $business->settings['address'] }}</p>
                @endif
            </div>
            @if($business && $showOpeningHours)
                <div>
                    <h3 class="text-lg font-semibold mb-4">Opening Hours</h3>
                    @if(isset($business->settings['opening_hours']))
                        <p class="text-gray-400">Mon-Sun: 9:00 - 17:00</p>
                    @endif
                </div>
            @endif
            @if($showSocial)
                <div>
                    <h3 class="text-lg font-semibold mb-4">Follow Us</h3>
                    <div class="flex gap-4">
                        {{-- Social links --}}
                    </div>
                </div>
            @endif
        </div>
        <div class="mt-8 pt-8 border-t border-gray-800 text-center text-gray-400">
            <p>&copy; {{ date('Y') }} {{ $business ? $business->name : config('app.name', 'LaraShop') }}. All rights reserved.</p>
        </div>
    </div>
</footer>

