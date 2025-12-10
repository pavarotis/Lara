<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? config('app.name', 'LaraShop') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=outfit:300,400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Dynamic Theme Colors -->
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
    <body class="font-sans antialiased bg-surface text-content" style="{{ $themeColors ? '--tw-primary: ' . $themeColors['primary'] . ';' : '' }}">
        <!-- Header -->
        <header class="sticky top-0 z-50 bg-white/80 backdrop-blur-md border-b border-gray-100">
            <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <!-- Logo -->
                    <a href="{{ url('/') }}" class="flex items-center gap-2">
                        <x-application-logo class="h-8 w-auto text-primary" />
                        <span class="font-semibold text-xl text-gray-900">{{ config('app.name', 'LaraShop') }}</span>
                    </a>

                    <!-- Navigation -->
                    <div class="hidden md:flex items-center gap-8">
                        <a href="{{ url('/') }}" class="text-gray-600 hover:text-primary transition-colors">Home</a>
                        <a href="{{ url('/menu') }}" class="text-gray-600 hover:text-primary transition-colors">Menu</a>
                        <a href="{{ url('/about') }}" class="text-gray-600 hover:text-primary transition-colors">About</a>
                        <a href="{{ url('/contact') }}" class="text-gray-600 hover:text-primary transition-colors">Contact</a>
                    </div>

                    <!-- Cart & Auth -->
                    <div class="flex items-center gap-4">
                        <!-- Cart Link -->
                        <a href="{{ url('/cart') }}" class="relative p-2 text-gray-600 hover:text-primary transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            @php $cartCount = count(session('cart', [])); @endphp
                            <span class="absolute -top-1 -right-1 bg-accent text-white text-xs w-5 h-5 rounded-full flex items-center justify-center" id="cart-count">{{ $cartCount }}</span>
                        </a>

                        <!-- Mobile Menu Button -->
                        <button type="button" class="md:hidden p-2 text-gray-600 hover:text-primary transition-colors" id="mobile-menu-open">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                    </div>
                </div>
            </nav>
        </header>

        <!-- Mobile Menu Drawer -->
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

        <!-- Footer -->
        <footer class="bg-gray-900 text-gray-300 mt-auto">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                    <!-- Brand -->
                    <div class="col-span-1 md:col-span-2">
                        <h3 class="text-white font-semibold text-lg mb-4">{{ config('app.name', 'LaraShop') }}</h3>
                        <p class="text-gray-400 text-sm max-w-md">
                            A modern, modular e-commerce platform built with Laravel. 
                            Perfect for cafes, restaurants, and retail businesses.
                        </p>
                    </div>

                    <!-- Quick Links -->
                    <div>
                        <h4 class="text-white font-medium mb-4">Quick Links</h4>
                        <ul class="space-y-2 text-sm">
                            <li><a href="{{ url('/menu') }}" class="hover:text-white transition-colors">Menu</a></li>
                            <li><a href="{{ url('/about') }}" class="hover:text-white transition-colors">About Us</a></li>
                            <li><a href="{{ url('/contact') }}" class="hover:text-white transition-colors">Contact</a></li>
                        </ul>
                    </div>

                    <!-- Contact -->
                    <div>
                        <h4 class="text-white font-medium mb-4">Contact</h4>
                        <ul class="space-y-2 text-sm">
                            <li>info@larashop.test</li>
                            <li>+30 210 1234567</li>
                        </ul>
                    </div>
                </div>

                <div class="border-t border-gray-800 mt-8 pt-8 text-sm text-center text-gray-500">
                    &copy; {{ date('Y') }} {{ config('app.name', 'LaraShop') }}. All rights reserved.
                </div>
            </div>
        </footer>

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

