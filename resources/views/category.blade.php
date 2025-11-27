<x-public-layout>
    <x-slot name="title">{{ $category->name }} - {{ $business->name }}</x-slot>

    <!-- Breadcrumb & Header -->
    <section class="bg-gradient-to-r from-primary-700 to-primary-600 text-white py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Breadcrumb -->
            <nav class="flex items-center gap-2 text-sm text-primary-200 mb-4">
                <a href="{{ url('/') }}" class="hover:text-white transition-colors">Home</a>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <a href="{{ url('/menu') }}" class="hover:text-white transition-colors">Menu</a>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <span class="text-white">{{ $category->name }}</span>
            </nav>

            <h1 class="text-3xl sm:text-4xl font-bold">{{ $category->name }}</h1>
            @if($category->description)
                <p class="mt-2 text-primary-100 max-w-2xl">{{ $category->description }}</p>
            @endif
        </div>
    </section>

    <!-- Products Grid -->
    <section class="py-12 bg-surface">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Results Count -->
            <div class="flex items-center justify-between mb-8">
                <p class="text-gray-600">
                    <span class="font-semibold text-gray-900">{{ $products->count() }}</span> products found
                </p>
                
                <a href="{{ url('/menu') }}" class="text-primary hover:text-primary-700 font-medium text-sm flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18" />
                    </svg>
                    Back to Menu
                </a>
            </div>

            @if($products->isEmpty())
                <div class="text-center py-16 bg-white rounded-2xl">
                    <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    <p class="text-gray-500 text-lg">No products in this category yet.</p>
                    <a href="{{ url('/menu') }}" class="mt-4 inline-block text-primary hover:underline">Browse other categories</a>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach($products as $product)
                        <x-product-card :product="$product" />
                    @endforeach
                </div>
            @endif
        </div>
    </section>
</x-public-layout>

