<x-public-layout>
    <x-slot name="title">Welcome - {{ config('app.name', 'LaraShop') }}</x-slot>

    <!-- Hero Section -->
    <section class="relative bg-gradient-to-br from-primary-800 via-primary-700 to-primary-600 text-white overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 opacity-10">
            <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                <defs>
                    <pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse">
                        <circle cx="1" cy="1" r="1" fill="currentColor"/>
                    </pattern>
                </defs>
                <rect width="100" height="100" fill="url(#grid)"/>
            </svg>
        </div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 lg:py-32">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <div class="space-y-8">
                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold leading-tight">
                        Fresh & Delicious
                        <span class="block text-primary-200">Every Day</span>
                    </h1>
                    <p class="text-lg text-primary-100 max-w-lg">
                        Discover our carefully crafted menu featuring the finest ingredients. 
                        Order online for pickup or delivery.
                    </p>
                    <div class="flex flex-wrap gap-4">
                        <a href="{{ url('/menu') }}" class="inline-flex items-center px-6 py-3 bg-white text-primary-700 font-semibold rounded-lg hover:bg-primary-50 transition-colors shadow-lg">
                            View Menu
                            <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                            </svg>
                        </a>
                        <a href="{{ url('/about') }}" class="inline-flex items-center px-6 py-3 border-2 border-white/30 text-white font-semibold rounded-lg hover:bg-white/10 transition-colors">
                            Learn More
                        </a>
                    </div>
                </div>

                <!-- Hero Image Placeholder -->
                <div class="hidden lg:block">
                    <div class="relative">
                        <div class="w-80 h-80 mx-auto bg-white/10 rounded-full flex items-center justify-center backdrop-blur-sm">
                            <svg class="w-40 h-40 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M21 15.546c-.523 0-1.046.151-1.5.454a2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0 2.701 2.701 0 00-1.5-.454M9 6v2m3-2v2m3-2v2M9 3h.01M12 3h.01M15 3h.01M21 21v-7a2 2 0 00-2-2H5a2 2 0 00-2 2v7h18zm-3-9v-2a2 2 0 00-2-2H8a2 2 0 00-2 2v2h12z" />
                            </svg>
                        </div>
                        <!-- Floating Elements -->
                        <div class="absolute top-0 right-0 w-20 h-20 bg-accent rounded-full opacity-80 animate-pulse"></div>
                        <div class="absolute bottom-10 left-0 w-16 h-16 bg-primary-300 rounded-full opacity-60"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-16 lg:py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900">Why Choose Us</h2>
                <p class="mt-4 text-gray-600 max-w-2xl mx-auto">
                    We're committed to providing the best experience for our customers.
                </p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="text-center p-6 rounded-2xl bg-surface hover:shadow-lg transition-shadow">
                    <div class="w-16 h-16 mx-auto mb-4 bg-primary-100 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Fast Service</h3>
                    <p class="text-gray-600">Quick preparation and delivery to save your valuable time.</p>
                </div>

                <!-- Feature 2 -->
                <div class="text-center p-6 rounded-2xl bg-surface hover:shadow-lg transition-shadow">
                    <div class="w-16 h-16 mx-auto mb-4 bg-accent-100 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Quality Products</h3>
                    <p class="text-gray-600">Only the finest ingredients make it to your order.</p>
                </div>

                <!-- Feature 3 -->
                <div class="text-center p-6 rounded-2xl bg-surface hover:shadow-lg transition-shadow">
                    <div class="w-16 h-16 mx-auto mb-4 bg-primary-100 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Easy Ordering</h3>
                    <p class="text-gray-600">Simple online ordering with secure payment options.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-16 bg-gray-900 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl font-bold mb-4">Ready to Order?</h2>
            <p class="text-gray-400 mb-8 max-w-xl mx-auto">
                Browse our menu and place your order in just a few clicks.
            </p>
            <a href="{{ url('/menu') }}" class="inline-flex items-center px-8 py-4 bg-primary text-white font-semibold rounded-lg hover:bg-primary-600 transition-colors">
                Order Now
                <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                </svg>
            </a>
        </div>
    </section>
</x-public-layout>

