@php
    // Module views receive $module and $settings
    $title = $settings['title'] ?? 'Categories';
    $parentCategoryId = $settings['parent_category_id'] ?? null;
    $limit = $settings['limit'] ?? 12;
    
    // Get business from module
    $businessId = $module->business_id ?? null;
    
    // Load categories (scoped to business)
    $query = \App\Domain\Catalog\Models\Category::query();
    if ($businessId) {
        $query->where('business_id', $businessId);
    }
    if ($parentCategoryId) {
        $query->where('parent_id', $parentCategoryId);
    } else {
        $query->whereNull('parent_id');
    }
    $categories = $query->limit($limit)->get();
@endphp

<div class="categories-list-module py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @if($title)
            <h2 class="text-3xl font-bold mb-8 text-center">{{ $title }}</h2>
        @endif
        
        @if($categories->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($categories as $category)
                    <a href="{{ route('catalog.category', $category->slug) }}" class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow block">
                        @if($category->image_url)
                            <img src="{{ $category->image_url }}" alt="{{ $category->name }}" class="w-full h-48 object-cover">
                        @endif
                        <div class="p-4">
                            <h3 class="text-xl font-semibold">{{ $category->name }}</h3>
                            @if($category->description)
                                <p class="text-gray-600 mt-2">{{ \Illuminate\Support\Str::limit($category->description, 100) }}</p>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <p class="text-center text-gray-500">No categories found.</p>
        @endif
    </div>
</div>

