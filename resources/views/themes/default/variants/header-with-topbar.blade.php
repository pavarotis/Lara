@php
    $variant = app(\App\Domain\Themes\Services\GetHeaderVariantService::class)->getVariant($currentBusiness);
    $showPhone = $variant['show_phone'] ?? false;
    $showHours = $variant['show_hours'] ?? false;
    $showSocial = $variant['show_social'] ?? false;
    $sticky = $variant['sticky'] ?? false;
@endphp

<header class="header-with-topbar {{ $sticky ? 'sticky top-0 z-50 bg-white shadow-sm' : '' }}">
    @if($showPhone || $showHours || $showSocial)
        <div class="topbar bg-gray-100 py-2">
            <div class="container mx-auto px-4">
                <div class="flex items-center justify-between text-sm">
                    <div class="flex items-center gap-4">
                        @if($showPhone && isset($currentBusiness->settings['phone']))
                            <a href="tel:{{ $currentBusiness->settings['phone'] }}" class="hover:opacity-75">
                                📞 {{ $currentBusiness->settings['phone'] }}
                            </a>
                        @endif
                        @if($showHours && isset($currentBusiness->settings['opening_hours']))
                            <span>🕐 Open Now</span>
                        @endif
                    </div>
                    @if($showSocial)
                        <div class="social-links">
                            {{-- Social links --}}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
    <div class="container mx-auto px-4">
        <div class="flex items-center justify-between py-4">
            <div class="logo">
                @if($currentBusiness->logo)
                    <img src="{{ asset($currentBusiness->logo) }}" alt="{{ $currentBusiness->name }}" class="h-12">
                @else
                    <span class="text-xl font-bold" style="color: var(--color-primary);">{{ $currentBusiness->name }}</span>
                @endif
            </div>
            <nav class="menu">
                <ul class="flex gap-6">
                    <li><a href="/" class="hover:opacity-75">Home</a></li>
                    <li><a href="/menu" class="hover:opacity-75">Menu</a></li>
                </ul>
            </nav>
        </div>
    </div>
</header>

