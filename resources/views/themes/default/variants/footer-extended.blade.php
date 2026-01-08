@php
    $variant = app(\App\Domain\Themes\Services\GetFooterVariantService::class)->getVariant($currentBusiness);
    $showOpeningHours = $variant['show_opening_hours'] ?? false;
    $showSocial = $variant['show_social'] ?? false;
@endphp

<footer class="footer-extended bg-gray-900 text-white py-12">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div>
                <h3 class="text-lg font-semibold mb-4">{{ $currentBusiness->name }}</h3>
                @if(isset($currentBusiness->settings['address']))
                    <p class="text-gray-400">{{ $currentBusiness->settings['address'] }}</p>
                @endif
            </div>
            @if($showOpeningHours)
                <div>
                    <h3 class="text-lg font-semibold mb-4">Opening Hours</h3>
                    @if(isset($currentBusiness->settings['opening_hours']))
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
            <p>&copy; {{ date('Y') }} {{ $currentBusiness->name }}. All rights reserved.</p>
        </div>
    </div>
</footer>

