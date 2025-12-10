@props([
    'name' => 'media',
    'mode' => 'single', // 'single' or 'multiple'
    'type' => null, // Filter by type: 'image', 'video', etc.
    'selected' => [], // Array of selected media IDs
    'folders' => [], // Array of folders (optional)
])

<div x-data="mediaPicker('{{ $name }}', '{{ $mode }}', @js($selected), '{{ $type }}')" 
     x-init="init()">
    <!-- Trigger Button -->
    <button type="button" @click="openPicker()" 
            class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200 transition-colors">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
        </svg>
        <span x-text="selectedMedia.length > 0 ? selectedMedia.length + ' selected' : 'Select Media'"></span>
    </button>

    <!-- Selected Media Preview -->
    <div x-show="selectedMedia.length > 0" class="mt-3 flex flex-wrap gap-2">
        <template x-for="item in selectedMedia" :key="item.id">
            <div class="relative group">
                <div class="w-20 h-20 rounded-lg overflow-hidden border-2 border-gray-300">
                    <img :src="item.thumbnail_url || item.url" :alt="item.name" class="w-full h-full object-cover">
                </div>
                <button type="button" @click="removeMedia(item.id)" 
                        class="absolute -top-2 -right-2 w-6 h-6 bg-red-600 text-white rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                <!-- Hidden inputs for form submission -->
                <template x-if="mode === 'single'">
                    <input type="hidden" :name="getFieldName('_id')" :value="item.id">
                    <input type="hidden" :name="getFieldName('_url')" :value="item.url">
                    <input type="hidden" :name="getFieldName('_thumbnail_url')" :value="item.thumbnail_url || item.url">
                </template>
                <template x-if="mode === 'multiple'">
                    <template x-for="(mediaItem, idx) in selectedMedia" :key="mediaItem.id">
                        <div>
                            <input type="hidden" :name="name + '[' + idx + '][id]'" :value="mediaItem.id">
                            <input type="hidden" :name="name + '[' + idx + '][url]'" :value="mediaItem.url">
                            <input type="hidden" :name="name + '[' + idx + '][thumbnail_url]'" :value="mediaItem.thumbnail_url || mediaItem.url">
                        </div>
                    </template>
                </template>
            </div>
        </template>
    </div>

    <!-- Media Picker Modal -->
    <div x-show="isOpen" 
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50"
         style="display: none;"
         @click.self="closePicker()">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-gray-500 opacity-75"></div>

        <!-- Modal Content -->
        <div class="mb-6 bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:w-full sm:max-w-4xl sm:mx-auto">
            <!-- Header -->
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">
                    <span x-text="mode === 'single' ? 'Select Media' : 'Select Media (Multiple)'"></span>
                </h3>
                <button @click="closePicker()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Search & Filters -->
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex flex-col sm:flex-row gap-4">
                    <!-- Search -->
                    <div class="flex-1">
                        <input type="text" 
                               x-model="searchQuery"
                               @input.debounce.300ms="loadMedia()"
                               placeholder="Search media..."
                               class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary">
                    </div>
                    <!-- Type Filter -->
                    <select x-model="typeFilter" @change="loadMedia()" class="rounded-lg border-gray-300 focus:border-primary focus:ring-primary">
                        <option value="">All Types</option>
                        <option value="image">Images</option>
                        <option value="video">Videos</option>
                        <option value="document">Documents</option>
                    </select>
                    <!-- Folder Selector -->
                    <select x-model="selectedFolder" @change="loadMedia()" class="rounded-lg border-gray-300 focus:border-primary focus:ring-primary">
                        <option value="">All Folders</option>
                        <template x-for="folder in folders" :key="folder.id">
                            <option :value="folder.id" x-text="folder.name"></option>
                        </template>
                    </select>
                </div>
            </div>

            <!-- Breadcrumb Navigation -->
            <div class="px-6 py-2 bg-gray-50 border-b border-gray-200">
                <nav class="flex items-center gap-2 text-sm">
                    <button @click="selectedFolder = null; loadMedia()" 
                            :class="selectedFolder === null ? 'text-primary font-medium' : 'text-gray-600 hover:text-gray-900'"
                            class="transition-colors">
                        All Files
                    </button>
                    <template x-for="(folder, index) in folderBreadcrumb" :key="folder.id">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                            <button @click="selectedFolder = folder.id; loadMedia()" 
                                    :class="index === folderBreadcrumb.length - 1 ? 'text-primary font-medium' : 'text-gray-600 hover:text-gray-900'"
                                    class="transition-colors"
                                    x-text="folder.name">
                            </button>
                        </div>
                    </template>
                </nav>
            </div>

            <!-- Media Grid -->
            <div class="p-6 max-h-96 overflow-y-auto">
                <!-- Loading State -->
                <div x-show="loading" class="flex items-center justify-center h-64">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary"></div>
                </div>

                <!-- Empty State -->
                <div x-show="!loading && media.length === 0" class="flex flex-col items-center justify-center h-64 text-gray-500">
                    <svg class="w-16 h-16 mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <p class="text-lg font-medium mb-2">No media files found</p>
                    <p class="text-sm mb-4">Upload files in the Media Library first</p>
                    <!-- Quick Upload Button -->
                    <label class="inline-flex items-center px-4 py-2 bg-primary text-white font-medium rounded-lg hover:bg-primary-600 transition-colors cursor-pointer">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                        </svg>
                        Upload Files
                        <input type="file" multiple class="hidden" @change="handleQuickUpload($event)" accept="image/*,video/*,application/pdf">
                    </label>
                </div>

                <!-- Media Grid -->
                <div x-show="!loading && media.length > 0" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                    <template x-for="item in media" :key="item.id">
                        <div @click="toggleMedia(item)"
                             :class="isSelected(item.id) ? 'ring-2 ring-primary border-primary' : 'border-transparent hover:border-gray-300'"
                             class="relative group cursor-pointer bg-gray-50 rounded-lg overflow-hidden border-2 transition-all">
                            <!-- Selected Indicator -->
                            <div x-show="isSelected(item.id)" class="absolute top-2 right-2 z-10">
                                <div class="w-6 h-6 bg-primary rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                            </div>
                            <!-- Image Preview -->
                            <div class="aspect-square flex items-center justify-center bg-gray-100">
                                <template x-if="item.type === 'image'">
                                    <img :src="item.thumbnail_url || item.url" :alt="item.name" class="w-full h-full object-cover">
                                </template>
                                <template x-if="item.type !== 'image'">
                                    <div class="text-center p-4">
                                        <svg class="w-12 h-12 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                        </svg>
                                        <p class="text-xs text-gray-600" x-text="item.type"></p>
                                    </div>
                                </template>
                            </div>
                            <!-- File Name -->
                            <div class="p-2 bg-white">
                                <p class="text-xs font-medium text-gray-900 truncate" x-text="item.name"></p>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Footer -->
            <div class="px-6 py-4 border-t border-gray-200 flex items-center justify-between">
                <div class="text-sm text-gray-600">
                    <span x-text="`${selectedMedia.length} selected`"></span>
                    <span x-show="mode === 'single' && selectedMedia.length > 0" class="ml-2 text-xs text-red-600">
                        (Only one selection allowed)
                    </span>
                </div>
                <div class="flex items-center gap-2">
                    <button @click="closePicker()" class="px-4 py-2 text-gray-700 hover:text-gray-900 transition-colors">
                        Cancel
                    </button>
                    <button @click="confirmSelection()" 
                            :disabled="selectedMedia.length === 0"
                            :class="selectedMedia.length === 0 ? 'bg-gray-300 cursor-not-allowed' : 'bg-primary hover:bg-primary-600'"
                            class="px-4 py-2 text-white rounded-lg transition-colors">
                        Select
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function mediaPicker(name, mode, initialSelected, typeFilter) {
        return {
            name: name,
            mode: mode, // 'single' or 'multiple'
            typeFilter: typeFilter || '',
            isOpen: false,
            media: [],
            folders: [],
            selectedMedia: [],
            tempSelectedMedia: [], // Temporary selection in modal
            selectedFolder: null,
            folderBreadcrumb: [],
            searchQuery: '',
            loading: false,

            init() {
                // Initialize with existing selected media
                if (initialSelected && initialSelected.length > 0) {
                    this.selectedMedia = Array.isArray(initialSelected) ? initialSelected : [initialSelected];
                }
                // Load folders on init
                this.loadFolders();
            },

            openPicker() {
                this.isOpen = true;
                // Copy current selection to temp
                this.tempSelectedMedia = [...this.selectedMedia];
                this.loadMedia();
            },

            closePicker() {
                this.isOpen = false;
                // Reset temp selection
                this.tempSelectedMedia = [];
            },

            async loadFolders() {
                try {
                    // Get business ID from view context
                    @php
                        $businessId = isset($business) ? $business->id : (isset($currentBusiness) ? $currentBusiness->id : \App\Domain\Businesses\Models\Business::active()->first()?->id);
                    @endphp
                    const businessId = @js($businessId ?? null);
                    
                    if (!businessId) {
                        console.error('No active business found');
                        return;
                    }

                    // Load folders from prop (passed from parent view)
                    // If folders prop is provided, use it; otherwise try to load from context
                    const foldersFromProp = @js($folders ?? []);
                    if (foldersFromProp && foldersFromProp.length > 0) {
                        this.folders = foldersFromProp.map(f => ({
                            id: f.id,
                            name: f.name,
                            path: f.path || ''
                        }));
                    } else {
                        // Fallback: Try to get from PHP context (for backward compatibility)
                        const foldersFromContext = @js(isset($folders) ? $folders->map(fn($f) => ['id' => $f->id, 'name' => $f->name, 'path' => $f->path])->toArray() : []);
                        this.folders = foldersFromContext || [];
                    }
                } catch (error) {
                    console.error('Error loading folders:', error);
                    this.folders = [];
                }
            },

            async loadMedia() {
                this.loading = true;
                try {
                    // Get business ID from view context or session
                    @php
                        $businessId = isset($business) ? $business->id : (isset($currentBusiness) ? $currentBusiness->id : \App\Domain\Businesses\Models\Business::active()->first()?->id);
                    @endphp
                    const businessId = @js($businessId ?? null);
                    
                    if (!businessId) {
                        console.error('No active business found');
                        this.loading = false;
                        return;
                    }

                    const params = new URLSearchParams();
                    if (this.selectedFolder) params.append('folder_id', this.selectedFolder);
                    if (this.searchQuery) params.append('search', this.searchQuery);
                    if (this.typeFilter) {
                        params.append('type', this.typeFilter);
                    }

                    const response = await axios.get(`/api/v1/businesses/${businessId}/media?${params.toString()}`);
                    
                    if (response.data && response.data.data) {
                        this.media = response.data.data.map(item => ({
                            id: item.id,
                            name: item.name,
                            url: item.url,
                            thumbnail_url: item.thumbnail_url || item.url,
                            type: item.type,
                            size: item.size || 0
                        }));
                    }
                } catch (error) {
                    console.error('Error loading media:', error);
                } finally {
                    this.loading = false;
                }
            },

            toggleMedia(item) {
                if (this.mode === 'single') {
                    // Single mode: replace selection
                    if (this.isSelected(item.id)) {
                        this.tempSelectedMedia = [];
                    } else {
                        this.tempSelectedMedia = [item];
                    }
                } else {
                    // Multiple mode: toggle selection
                    const index = this.tempSelectedMedia.findIndex(m => m.id === item.id);
                    if (index > -1) {
                        this.tempSelectedMedia.splice(index, 1);
                    } else {
                        this.tempSelectedMedia.push(item);
                    }
                }
            },

            isSelected(mediaId) {
                return this.tempSelectedMedia.some(m => m.id === mediaId);
            },

            confirmSelection() {
                // Update selected media
                this.selectedMedia = [...this.tempSelectedMedia];
                // Emit event for parent component
                window.dispatchEvent(new CustomEvent('media-selected', {
                    detail: {
                        name: this.name,
                        media: this.selectedMedia.map(item => ({
                            id: item.id,
                            url: item.url,
                            thumbnail_url: item.thumbnail_url || item.url,
                            name: item.name
                        }))
                    }
                }));
                this.closePicker();
            },

            removeMedia(mediaId) {
                this.selectedMedia = this.selectedMedia.filter(m => m.id !== mediaId);
                // Emit event for parent component
                window.dispatchEvent(new CustomEvent('media-removed', {
                    detail: {
                        name: this.name,
                        mediaId: mediaId
                    }
                }));
            },

            async handleQuickUpload(event) {
                const files = event.target.files;
                if (files.length === 0) return;

                try {
                    @php
                        $businessId = isset($business) ? $business->id : (isset($currentBusiness) ? $currentBusiness->id : \App\Domain\Businesses\Models\Business::active()->first()?->id);
                    @endphp
                    const businessId = @js($businessId ?? null);
                    
                    if (!businessId) {
                        alert('No active business found');
                        return;
                    }

                    const formData = new FormData();
                    Array.from(files).forEach(file => {
                        formData.append('files[]', file);
                    });
                    if (this.selectedFolder) {
                        formData.append('folder_id', this.selectedFolder);
                    }

                    this.loading = true;
                    await axios.post('{{ route('admin.media.store') }}', formData, {
                        headers: {
                            'Content-Type': 'multipart/form-data',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });
                    
                    // Reload media after upload
                    await this.loadMedia();
                    this.loading = false;
                    alert('Files uploaded successfully!');
                } catch (error) {
                    console.error('Error uploading files:', error);
                    this.loading = false;
                    alert('Error uploading files. Please try again.');
                }
            },

            getFieldName(suffix) {
                // Convert blocks[X][props][image] to blocks[X][props][image_id]
                // or blocks[X][props][image_url], etc.
                if (this.name.includes('[image]')) {
                    return this.name.replace('[image]', '[image' + suffix + ']');
                } else if (this.name.includes('[media]')) {
                    return this.name.replace('[media]', '[media' + suffix + ']');
                } else {
                    // Fallback: append suffix
                    return this.name + suffix;
                }
            }
        }
    }
</script>
@endpush

