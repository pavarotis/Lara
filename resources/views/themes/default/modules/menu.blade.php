@php
    // Module views receive $module and $settings
    $title = $settings['title'] ?? 'Menu';
    $categoryId = $settings['category_id'] ?? null;
    $limit = $settings['limit'] ?? 12;
    $columns = $settings['columns'] ?? 3;
    
    // Get business from module
    $businessId = $module->business_id ?? null;
    
    // Load products from category (scoped to business)
    $products = collect();
    if ($categoryId && $businessId) {
        $category = \App\Domain\Catalog\Models\Category::where('business_id', $businessId)
            ->find($categoryId);
        if ($category) {
            $products = $category->products()
                ->where('business_id', $businessId)
                ->where('is_available', true)
                ->limit($limit)
                ->get();
        }
    }
    
    $gridClasses = [
        1 => 'grid-cols-1',
        2 => 'grid-cols-1 md:grid-cols-2',
        3 => 'grid-cols-1 md:grid-cols-2 lg:grid-cols-3',
        4 => 'grid-cols-1 md:grid-cols-2 lg:grid-cols-4',
    ];
    $gridClass = $gridClasses[$columns] ?? 'grid-cols-1 md:grid-cols-2 lg:grid-cols-3';
@endphp

<div class="menu-module py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @if($title)
            <h2 class="text-3xl font-bold mb-8 text-center">{{ $title }}</h2>
        @endif
        
        @if($products->count() > 0)
            <div class="grid {{ $gridClass }} gap-6">
                @foreach($products as $product)
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                        @if($product->image_url)
                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-48 object-cover">
                        @endif
                        <div class="p-4">
                            <h3 class="text-xl font-semibold mb-2">{{ $product->name }}</h3>
                            @if($product->description)
                                <p class="text-gray-600 mb-4">{{ \Illuminate\Support\Str::limit($product->description, 100) }}</p>
                            @endif
                            <div class="flex justify-between items-center">
                                <span class="text-2xl font-bold text-blue-600">{{ number_format($product->price, 2) }} €</span>
                                <button class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors">
                                    Add to Cart
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-center text-gray-500">No products found.</p>
        @endif
    </div>
</div>

