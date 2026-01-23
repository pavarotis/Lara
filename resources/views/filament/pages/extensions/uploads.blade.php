<x-filament-panels::page>
    @php
        // Get recent media from Page class
        $recentMedia = $this->getRecentMedia();

        // SVG Icons for Info Section
        $folderIcon = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />';
        
        $imageIcon = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />';
        
        $variantIcon = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />';

        // File icon SVG (for non-image files)
        $fileIcon = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />';
    @endphp

    <div class="space-y-6">
        <!-- Quick Upload Section -->
        <x-filament::section>
            <x-slot name="heading">
                Quick Upload
            </x-slot>
            <x-slot name="description">
                Upload files directly to the media library. Files will be organized and available for use in content, products, and other areas.
            </x-slot>

            <form wire:submit="upload">
                {{ $this->form }}

                <div class="mt-4 flex items-center gap-3">
                    <x-filament::button type="submit" color="primary">
                        Upload Files
                    </x-filament::button>
                    <x-filament::button 
                        type="button" 
                        tag="a" 
                        href="{{ route('admin.media.index') }}"
                        color="gray"
                        outlined
                    >
                        Open Media Library
                    </x-filament::button>
                </div>
            </form>
        </x-filament::section>

        <!-- Recent Uploads -->
        @if($recentMedia && $recentMedia->count() > 0)
            <x-filament::section>
                <x-slot name="heading">
                    Recent Uploads
                </x-slot>
                <x-slot name="description">
                    Recently uploaded files in the media library
                </x-slot>

                <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-5 gap-4">
                    @foreach($recentMedia as $media)
                        <div class="group relative">
                            @if($media->type === 'image')
                                <img 
                                    src="{{ $media->url }}" 
                                    alt="{{ $media->name }}"
                                    class="w-full h-20 object-cover rounded-lg border border-gray-200 dark:border-gray-700"
                                >
                            @else
                                <div class="w-full h-20 bg-gray-100 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        {!! $fileIcon !!}
                                    </svg>
                                </div>
                            @endif
                            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition-opacity rounded-lg flex items-center justify-center opacity-0 group-hover:opacity-100">
                                <a 
                                    href="{{ route('admin.media.index') }}?search={{ urlencode($media->name) }}"
                                    class="text-white text-xs font-medium px-2 py-1 bg-white bg-opacity-20 rounded"
                                >
                                    View
                                </a>
                            </div>
                            <p class="mt-1 text-xs text-gray-600 dark:text-gray-400 truncate" title="{{ $media->name }}">
                                {{ $media->name }}
                            </p>
                        </div>
                    @endforeach
                </div>

                <div class="mt-4">
                    <x-filament::button 
                        tag="a" 
                        href="{{ route('admin.media.index') }}"
                        color="gray"
                        outlined
                    >
                        View All Media
                    </x-filament::button>
                </div>
            </x-filament::section>
        @endif

        <!-- Info Section -->
        <x-filament::section>
            <x-slot name="heading">
                About Media Library
            </x-slot>
            <x-slot name="description">
                Learn how to use the media library effectively
            </x-slot>

            <div class="my-info-list">
                <div class="my-info-row">
                    <span class="my-info-icon">
                        <svg
                            width="16"
                            height="16"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                        >
                            {!! $folderIcon !!}
                        </svg>
                    </span>
                    <div class="my-info-text">
                        <strong>Organize with Folders:</strong>
                        Create folders to organize your files by category, project, or date.
                    </div>
                </div>
                <div class="my-info-row">
                    <span class="my-info-icon">
                        <svg
                            width="16"
                            height="16"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                        >
                            {!! $imageIcon !!}
                        </svg>
                    </span>
                    <div class="my-info-text">
                        <strong>Use in Content:</strong>
                        Uploaded images can be used in content blocks, products, categories, and more.
                    </div>
                </div>
                <div class="my-info-row">
                    <span class="my-info-icon">
                        <svg
                            width="16"
                            height="16"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                        >
                            {!! $variantIcon !!}
                        </svg>
                    </span>
                    <div class="my-info-text">
                        <strong>Automatic Variants:</strong>
                        Images are automatically resized into thumbnails and different sizes for optimal performance.
                    </div>
                </div>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
