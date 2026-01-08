@php
    $variant = app(\App\Domain\Themes\Services\GetHeaderVariantService::class)->getVariant($currentBusiness);
    $showPhone = $variant['show_phone'] ?? false;
    $showHours = $variant['show_hours'] ?? false;
    $showSocial = $variant['show_social'] ?? false;
    $sticky = $variant['sticky'] ?? false;
@endphp

<header class="header-centered {{ $sticky ? 'sticky top-0 z-50 bg-white shadow-sm' : '' }}">
    <div class="container mx-auto px-4">
        <div class="flex flex-col items-center py-4">
            <div class="logo mb-4">
                @if($currentBusiness->logo)
                    <img src="{{ asset($currentBusiness->logo) }}" alt="{{ $currentBusiness->name }}" class="h-16">
                @else
                    <span class="text-2xl font-bold" style="color: var(--color-primary);">{{ $currentBusiness->name }}</span>
                @endif
            </div>
            @if($showPhone || $showSocial)
                <div class="flex items-center gap-4 mb-4 text-sm">
                    @if($showPhone && isset($currentBusiness->settings['phone']))
                        <a href="tel:{{ $currentBusiness->settings['phone'] }}" class="hover:opacity-75">
                            {{ $currentBusiness->settings['phone'] }}
                        </a>
                    @endif
                    @if($showSocial)
                        {{-- Social links --}}
                    @endif
                </div>
            @endif
            <nav class="menu">
                <ul class="flex gap-6">
                    <li><a href="/" class="hover:opacity-75">Home</a></li>
                    <li><a href="/menu" class="hover:opacity-75">Menu</a></li>
                </ul>
            </nav>
        </div>
    </div>
</header>

