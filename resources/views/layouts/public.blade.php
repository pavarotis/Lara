<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? config('app.name', 'LaraShop') }}</title>

        <!-- Fonts from CMS Variables -->
        @php
            $business = $currentBusiness ?? null;
            $locale = app()->getLocale();
            
            if ($business) {
                $typographyService = app(\App\Domain\Variables\Services\GetTypographyFontsService::class);
                $typography = $typographyService->getFonts($business, $locale);
                $googleFontsLink = $typography['google_fonts_link'] ?? null;
                $fonts = $typography['fonts'] ?? [];
            } else {
                $googleFontsLink = null;
                $fonts = [];
            }
        @endphp
        
        @if($googleFontsLink)
            <link rel="preconnect" href="https://fonts.googleapis.com">
            <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
            <link href="{{ $googleFontsLink }}" rel="stylesheet" />
        @else
            <!-- Fallback: Default fonts -->
            <link rel="preconnect" href="https://fonts.bunny.net">
            <link href="https://fonts.bunny.net/css?family=outfit:300,400,500,600,700&display=swap" rel="stylesheet" />
        @endif
        
        @if(!empty($fonts))
            <style>
                :root {
                    @if(isset($fonts['primary']))
                        --font-primary: '{{ $fonts['primary'] }}', sans-serif;
                    @endif
                    @if(isset($fonts['secondary']))
                        --font-secondary: '{{ $fonts['secondary'] }}', sans-serif;
                    @endif
                    @if(isset($fonts['heading']))
                        --font-heading: '{{ $fonts['heading'] }}', sans-serif;
                    @endif
                    @if(isset($fonts['body']))
                        --font-body: '{{ $fonts['body'] }}', sans-serif;
                    @endif
                }
            </style>
        @endif

        <!-- Base CSS (always loaded) -->
        @vite(['resources/css/app.css'])

        <!-- Conditional JS (only if needed) -->
        @php
            // Check if page needs JavaScript (Alpine.js, widgets, etc.)
            $needsAlpine = isset($needsAlpine) ? $needsAlpine : false;
            $widgetAssets = $widgetAssets ?? [];
        @endphp

        @if($needsAlpine || !empty($widgetAssets['js'] ?? []))
            @vite(['resources/js/app.js'])
        @endif

        <!-- Widget-specific JS -->
        @stack('widget-scripts')

        <!-- Theme CSS Injection (Sprint 5) -->
        @if(isset($themeCss))
            <style>
                {!! $themeCss !!}
            </style>
        @else
            <!-- Fallback: Dynamic Theme Colors (Legacy) -->
            @php
                $business = $currentBusiness ?? null;
                $themeColors = $business ? app(\App\Domain\Businesses\Services\GetBusinessSettingsService::class)->getThemeColors($business) : null;
            @endphp
            @if($themeColors)
            <style>
                :root {
                    --color-primary: {{ $themeColors['primary'] }};
                    --color-accent: {{ $themeColors['accent'] }};
                }
            </style>
            @endif
        @endif

        <!-- UX Polish: Smooth scroll & animations -->
        <style>
            html { scroll-behavior: smooth; }
            @keyframes fadeInUp {
                from { opacity: 0; transform: translateY(20px); }
                to { opacity: 1; transform: translateY(0); }
            }
            .animate-fade-in-up { animation: fadeInUp 0.5s ease-out forwards; }
            .animate-delay-100 { animation-delay: 100ms; }
            .animate-delay-200 { animation-delay: 200ms; }
            .animate-delay-300 { animation-delay: 300ms; }
        </style>

        @stack('styles')
        @stack('meta')
    </head>
    <body class="font-sans antialiased bg-surface text-content" style="{{ isset($themeTokens) && isset($themeTokens['colors']['background']) ? 'background-color: ' . $themeTokens['colors']['background'] . ';' : '' }}">
        <!-- Header Variant (Sprint 5) -->
        @php
            $business = $currentBusiness ?? null;
            if ($business) {
                $headerVariant = app(\App\Domain\Themes\Services\GetHeaderVariantService::class)->getVariant($business);
            } else {
                $headerVariant = config('header_variants.minimal');
            }
        @endphp
        @include($headerVariant['view'], ['variant' => $headerVariant])

        <!-- Mobile Menu Drawer -->
        @php
            // Calculate cart count from session
            $cart = session('cart', []);
            $cartCount = isset($cartCount) ? $cartCount : array_sum(array_column($cart, 'quantity'));
        @endphp
        <div id="mobile-menu-overlay" class="fixed inset-0 z-50 hidden">
            <!-- Backdrop -->
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm transition-opacity" id="mobile-menu-backdrop"></div>
            
            <!-- Drawer -->
            <div class="absolute top-0 right-0 h-full w-72 bg-white shadow-2xl transform translate-x-full transition-transform duration-300 ease-out" id="mobile-menu-drawer">
                <!-- Drawer Header -->
                <div class="flex items-center justify-between p-4 border-b">
                    <span class="font-semibold text-lg text-gray-900">Menu</span>
                    <button type="button" class="p-2 text-gray-500 hover:text-gray-700 transition-colors" id="mobile-menu-close">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Drawer Content -->
                <nav class="p-4 space-y-1">
                    <a href="{{ url('/') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-primary-50 hover:text-primary transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        Home
                    </a>
                    <a href="{{ url('/menu') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-primary-50 hover:text-primary transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                        </svg>
                        Menu
                    </a>
                    <a href="{{ url('/cart') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-primary-50 hover:text-primary transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        Cart
                        @if($cartCount > 0)
                            <span class="ml-auto bg-accent text-white text-xs px-2 py-0.5 rounded-full">{{ $cartCount }}</span>
                        @endif
                    </a>
                    <a href="{{ url('/about') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-primary-50 hover:text-primary transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        About
                    </a>
                    <a href="{{ url('/contact') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-primary-50 hover:text-primary transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        Contact
                    </a>
                </nav>

                <!-- Drawer Footer -->
                <div class="absolute bottom-0 left-0 right-0 p-4 border-t bg-gray-50">
                    <p class="text-xs text-gray-500 text-center">{{ config('app.name', 'LaraShop') }}</p>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <main>
            @yield('content')
        </main>

        <!-- Footer Variant (Sprint 5) -->
        @php
            $business = $currentBusiness ?? null;
            if ($business) {
                $footerVariant = app(\App\Domain\Themes\Services\GetFooterVariantService::class)->getVariant($business);
            } else {
                $footerVariant = config('footer_variants.simple');
            }
        @endphp
        @include($footerVariant['view'], ['variant' => $footerVariant])

        @stack('scripts')

        <script>
            // Mobile menu drawer
            const overlay = document.getElementById('mobile-menu-overlay');
            const drawer = document.getElementById('mobile-menu-drawer');
            const backdrop = document.getElementById('mobile-menu-backdrop');

            function openMobileMenu() {
                overlay.classList.remove('hidden');
                setTimeout(() => {
                    drawer.classList.remove('translate-x-full');
                }, 10);
                document.body.style.overflow = 'hidden';
            }

            function closeMobileMenu() {
                drawer.classList.add('translate-x-full');
                setTimeout(() => {
                    overlay.classList.add('hidden');
                }, 300);
                document.body.style.overflow = '';
            }

            document.getElementById('mobile-menu-open')?.addEventListener('click', openMobileMenu);
            document.getElementById('mobile-menu-close')?.addEventListener('click', closeMobileMenu);
            backdrop?.addEventListener('click', closeMobileMenu);
        </script>
    </body>
</html>

