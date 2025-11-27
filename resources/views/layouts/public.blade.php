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

        @stack('styles')
    </head>
    <body class="font-sans antialiased bg-surface text-content">
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
                        <button type="button" class="md:hidden p-2 text-gray-600" id="mobile-menu-btn">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Mobile Menu -->
                <div class="hidden md:hidden pb-4" id="mobile-menu">
                    <a href="{{ url('/') }}" class="block py-2 text-gray-600 hover:text-primary">Home</a>
                    <a href="{{ url('/menu') }}" class="block py-2 text-gray-600 hover:text-primary">Menu</a>
                    <a href="{{ url('/cart') }}" class="block py-2 text-gray-600 hover:text-primary">Cart</a>
                    <a href="{{ url('/about') }}" class="block py-2 text-gray-600 hover:text-primary">About</a>
                    <a href="{{ url('/contact') }}" class="block py-2 text-gray-600 hover:text-primary">Contact</a>
                </div>
            </nav>
        </header>

        <!-- Main Content -->
        <main>
            {{ $slot }}
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
            // Mobile menu toggle
            document.getElementById('mobile-menu-btn')?.addEventListener('click', function() {
                document.getElementById('mobile-menu')?.classList.toggle('hidden');
            });
        </script>
    </body>
</html>

