<x-public-layout>
    <x-slot name="title">Menu - {{ $business->name }}</x-slot>

    <!-- Hero Banner -->
    <section class="bg-gradient-to-r from-primary-700 to-primary-600 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-3xl sm:text-4xl font-bold mb-2">{{ $business->name }}</h1>
            <p class="text-primary-100 text-lg">{{ $business->description ?? 'Discover our delicious menu' }}</p>
        </div>
    </section>

    <!-- Categories Grid -->
    <section class="py-12 bg-surface">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if($categories->isEmpty())
                <div class="text-center py-16">
                    <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    <p class="text-gray-500 text-lg">No menu items available yet.</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($categories as $category)
                        <a href="{{ url('/menu/' . $category->slug) }}" 
                           class="group bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden">
                            <!-- Category Image -->
                            <div class="aspect-video bg-gradient-to-br from-primary-100 to-primary-50 relative overflow-hidden">
                                @if($category->image)
                                    <img src="{{ asset('storage/' . $category->image) }}" 
                                         alt="{{ $category->name }}"
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                @else
                                    <div class="absolute inset-0 flex items-center justify-center">
                                        <svg class="w-16 h-16 text-primary-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                @endif
                                
                                <!-- Product Count Badge -->
                                <div class="absolute top-3 right-3 bg-white/90 backdrop-blur-sm px-3 py-1 rounded-full text-sm font-medium text-gray-700">
                                    {{ $category->products_count ?? $category->products->count() }} items
                                </div>
                            </div>

                            <!-- Category Info -->
                            <div class="p-5">
                                <h3 class="text-xl font-semibold text-gray-900 group-hover:text-primary transition-colors">
                                    {{ $category->name }}
                                </h3>
                                @if($category->description)
                                    <p class="mt-2 text-gray-600 text-sm line-clamp-2">{{ $category->description }}</p>
                                @endif
                                
                                <div class="mt-4 flex items-center text-primary font-medium text-sm">
                                    View Products
                                    <svg class="ml-2 w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                    </svg>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    <!-- Featured Products Section -->
    @php
        $featuredProducts = $categories->flatMap->products->where('is_featured', true)->take(4);
    @endphp
    
    @if($featuredProducts->isNotEmpty())
        <section class="py-12 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between mb-8">
                    <h2 class="text-2xl font-bold text-gray-900">Featured Items</h2>
                    <span class="text-accent text-sm font-medium">Popular choices</span>
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($featuredProducts as $product)
                        <x-product-card :product="$product" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif
</x-public-layout>

