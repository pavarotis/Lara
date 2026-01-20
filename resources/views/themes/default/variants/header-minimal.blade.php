@php
    $business = $currentBusiness ?? null;
    if ($business) {
        $variant = app(\App\Domain\Themes\Services\GetHeaderVariantService::class)->getVariant($business);
        $showPhone = $variant['show_phone'] ?? false;
        $showHours = $variant['show_hours'] ?? false;
        $showSocial = $variant['show_social'] ?? false;
        $sticky = $variant['sticky'] ?? false;
    } else {
        $showPhone = false;
        $showHours = false;
        $showSocial = false;
        $sticky = false;
    }
@endphp

<header class="header-minimal {{ $sticky ? 'sticky top-0 z-50 bg-white shadow-sm' : '' }}">
    <div class="container mx-auto px-4">
        <div class="flex items-center justify-between py-4">
            <div class="logo">
                @if($business && $business->logo)
                    <img src="{{ asset($business->logo) }}" alt="{{ $business->name }}" class="h-12">
                @elseif($business)
                    <span class="text-xl font-bold" style="color: var(--color-primary);">{{ $business->name }}</span>
                @else
                    <span class="text-xl font-bold" style="color: var(--color-primary);">{{ config('app.name', 'LaraShop') }}</span>
                @endif
            </div>
            <nav class="menu">
                {{-- Menu items will be rendered here --}}
                <ul class="flex gap-6">
                    <li><a href="/" class="hover:opacity-75">Home</a></li>
                    <li><a href="/menu" class="hover:opacity-75">Menu</a></li>
                </ul>
            </nav>
        </div>
    </div>
</header>

