@php
    $business = $currentBusiness ?? null;
    if ($business) {
        $variant = app(\App\Domain\Themes\Services\GetFooterVariantService::class)->getVariant($business);
        $showOpeningHours = $variant['show_opening_hours'] ?? false;
        $showSocial = $variant['show_social'] ?? false;
        $showNewsletter = $variant['show_newsletter'] ?? false;
    } else {
        $showOpeningHours = false;
        $showSocial = false;
        $showNewsletter = false;
    }
@endphp

<footer class="footer-business-info bg-gray-900 text-white py-12">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <div>
                <h3 class="text-lg font-semibold mb-4">{{ $business ? $business->name : config('app.name', 'LaraShop') }}</h3>
                @if($business && isset($business->settings['address']))
                    <p class="text-gray-400 mb-2">{{ $business->settings['address'] }}</p>
                @endif
                @if($business && isset($business->settings['phone']))
                    <p class="text-gray-400">
                        <a href="tel:{{ $business->settings['phone'] }}" class="hover:text-white">
                            {{ $business->settings['phone'] }}
                        </a>
                    </p>
                @endif
            </div>
            <div>
                <h3 class="text-lg font-semibold mb-4">Quick Links</h3>
                <ul class="space-y-2 text-gray-400">
                    <li><a href="/" class="hover:text-white">Home</a></li>
                    <li><a href="/menu" class="hover:text-white">Menu</a></li>
                </ul>
            </div>
            @if($business && $showOpeningHours)
                <div>
                    <h3 class="text-lg font-semibold mb-4">Opening Hours</h3>
                    @if(isset($business->settings['opening_hours']))
                        <p class="text-gray-400">Mon-Sun: 9:00 - 17:00</p>
                    @endif
                </div>
            @endif
            @if($showNewsletter)
                <div>
                    <h3 class="text-lg font-semibold mb-4">Newsletter</h3>
                    <form class="flex gap-2">
                        <input type="email" placeholder="Your email" class="px-3 py-2 rounded text-gray-900 flex-1">
                        <button type="submit" class="px-4 py-2 bg-primary text-white rounded hover:opacity-75">Subscribe</button>
                    </form>
                </div>
            @endif
        </div>
        @if($showSocial)
            <div class="mt-8 pt-8 border-t border-gray-800 flex justify-center gap-4">
                {{-- Social links --}}
            </div>
        @endif
        <div class="mt-8 pt-8 border-t border-gray-800 text-center text-gray-400">
            <p>&copy; {{ date('Y') }} {{ $business ? $business->name : config('app.name', 'LaraShop') }}. All rights reserved.</p>
        </div>
    </div>
</footer>

