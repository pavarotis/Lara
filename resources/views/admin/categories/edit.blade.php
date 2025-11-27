<x-admin-layout>
    <x-slot name="title">Edit Category</x-slot>

    <!-- Header -->
    <div class="mb-6">
        <nav class="flex items-center gap-2 text-sm text-gray-500 mb-2">
            <a href="{{ route('admin.categories.index') }}" class="hover:text-primary">Categories</a>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
            <span class="text-gray-900">Edit: {{ $category->name }}</span>
        </nav>
        <h2 class="text-2xl font-bold text-gray-900">Edit Category</h2>
    </div>

    <!-- Form -->
    <form action="{{ route('admin.categories.update', $category) }}" method="POST" class="max-w-2xl">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-xl shadow-sm p-6 space-y-6">
            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
                <input type="text" name="name" id="name" value="{{ old('name', $category->name) }}" required
                       class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Slug -->
            <div>
                <label for="slug" class="block text-sm font-medium text-gray-700 mb-1">Slug *</label>
                <input type="text" name="slug" id="slug" value="{{ old('slug', $category->slug) }}" required
                       class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary">
                @error('slug')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea name="description" id="description" rows="3"
                          class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary">{{ old('description', $category->description) }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Image URL -->
            <div>
                <label for="image" class="block text-sm font-medium text-gray-700 mb-1">Image Path</label>
                <input type="text" name="image" id="image" value="{{ old('image', $category->image) }}"
                       class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary">
                <p class="mt-1 text-xs text-gray-500">Path relative to storage/app/public</p>
                @error('image')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Sort Order -->
            <div>
                <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-1">Sort Order</label>
                <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', $category->sort_order) }}"
                       min="0"
                       class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary">
                @error('sort_order')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Toggle -->
            <div>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $category->is_active) ? 'checked' : '' }}
                           class="rounded border-gray-300 text-primary focus:ring-primary">
                    <span class="text-sm text-gray-700">Active</span>
                </label>
            </div>
        </div>

        <!-- Actions -->
        <div class="mt-6 flex items-center gap-4">
            <button type="submit" 
                    class="px-6 py-2 bg-primary text-white font-medium rounded-lg hover:bg-primary-600 transition-colors">
                Update Category
            </button>
            <a href="{{ route('admin.categories.index') }}" 
               class="px-6 py-2 text-gray-700 hover:text-gray-900 transition-colors">
                Cancel
            </a>
        </div>
    </form>
</x-admin-layout>

