@php
    $business = $currentBusiness ?? null;
    if ($business) {
        $variant = app(\App\Domain\Themes\Services\GetFooterVariantService::class)->getVariant($business);
    }
@endphp

<footer class="footer-simple bg-gray-900 text-white py-8">
    <div class="container mx-auto px-4">
        <div class="text-center">
            <p>&copy; {{ date('Y') }} {{ $business ? $business->name : config('app.name', 'LaraShop') }}. All rights reserved.</p>
        </div>
    </div>
</footer>

