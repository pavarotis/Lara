@php
    // Module views receive $module and $settings
    $title = $settings['title'] ?? 'Products';
    $categoryIds = $settings['category_ids'] ?? [];
    $limit = $settings['limit'] ?? 12;
    $columns = $settings['columns'] ?? 4;
    
    // Get business from module
    $businessId = $module->business_id ?? null;
    
    // Load products (scoped to business)
    $query = \App\Domain\Catalog\Models\Product::query();
    if ($businessId) {
        $query->where('business_id', $businessId);
    }
    if (!empty($categoryIds)) {
        $query->whereIn('category_id', $categoryIds);
    }
    $products = $query->where('is_available', true)
        ->limit($limit)
        ->get();
    
    $gridClasses = [
        2 => 'grid-cols-1 md:grid-cols-2',
        3 => 'grid-cols-1 md:grid-cols-2 lg:grid-cols-3',
        4 => 'grid-cols-1 md:grid-cols-2 lg:grid-cols-4',
    ];
    $gridClass = $gridClasses[$columns] ?? 'grid-cols-1 md:grid-cols-2 lg:grid-cols-4';
@endphp

<div class="products-grid-module py-12">
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
                            <h3 class="text-lg font-semibold mb-2">{{ $product->name }}</h3>
                            <p class="text-2xl font-bold text-blue-600">{{ number_format($product->price, 2) }} €</p>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-center text-gray-500">No products found.</p>
        @endif
    </div>
</div>

