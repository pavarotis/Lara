<x-admin-layout>
    <x-slot name="title">Media Library</x-slot>

    <div x-data="mediaLibrary(@js($media->items()), @js($folders), @js($media->currentPage()), @js($media->lastPage()), @js(request()->get('folder_id')), @js(request()->get('type')), @js(request()->get('search')))" 
         x-init="init()" 
         class="h-[calc(100vh-4rem)] flex flex-col">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Media Library</h2>
                <p class="text-gray-600 text-sm mt-1">Manage your media files and folders</p>
            </div>
            <div class="flex items-center gap-2">
                <!-- Upload Button -->
                <label class="inline-flex items-center px-4 py-2 bg-primary text-white font-medium rounded-lg hover:bg-primary-600 transition-colors cursor-pointer">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                    </svg>
                    Upload Files
                    <input type="file" multiple class="hidden" @change="handleFileSelect($event)" accept="image/*,video/*,application/pdf">
                </label>
                <!-- Create Folder Button -->
                <button @click="showCreateFolderModal = true" class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    New Folder
                </button>
            </div>
        </div>

        <!-- Flash Messages -->
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                {{ session('error') }}
            </div>
        @endif

        <!-- Main Content Area -->
        <div class="flex-1 flex gap-6 overflow-hidden">
            <!-- Sidebar: Folder Tree -->
            <aside class="w-64 bg-white rounded-xl shadow-sm p-4 overflow-y-auto flex-shrink-0">
                <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-3">Folders</h3>
                <div class="space-y-1">
                    <!-- Root Folder -->
                    <button @click="selectedFolder = null; loadMedia()" 
                            :class="selectedFolder === null ? 'bg-primary-50 text-primary-700' : 'text-gray-700 hover:bg-gray-100'"
                            class="w-full text-left px-3 py-2 rounded-lg transition-colors flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                        </svg>
                        All Files
                    </button>
                    <!-- Folder Tree (will be populated from backend) -->
                    <template x-for="folder in folders" :key="folder.id">
                        <div>
                            <button @click="selectedFolder = folder.id; loadMedia()"
                                    :class="selectedFolder === folder.id ? 'bg-primary-50 text-primary-700' : 'text-gray-700 hover:bg-gray-100'"
                                    class="w-full text-left px-3 py-2 rounded-lg transition-colors flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                                </svg>
                                <span x-text="folder.name"></span>
                            </button>
                        </div>
                    </template>
                </div>
            </aside>

            <!-- Main: Media Grid -->
            <div class="flex-1 flex flex-col bg-white rounded-xl shadow-sm overflow-hidden">
                <!-- Top Bar: Search & Filters -->
                <div class="p-4 border-b border-gray-200">
                    <div class="flex flex-col sm:flex-row gap-4">
                        <!-- Search -->
                        <div class="flex-1">
                            <input type="text" 
                                   x-model="searchQuery"
                                   @input.debounce.300ms="loadMedia()"
                                   placeholder="Search files..."
                                   class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary">
                        </div>
                        <!-- Type Filter -->
                        <select x-model="typeFilter" @change="loadMedia()" class="rounded-lg border-gray-300 focus:border-primary focus:ring-primary">
                            <option value="">All Types</option>
                            <option value="image">Images</option>
                            <option value="video">Videos</option>
                            <option value="document">Documents</option>
                        </select>
                        <!-- View Toggle -->
                        <div class="flex items-center gap-2 border border-gray-300 rounded-lg p-1">
                            <button @click="viewMode = 'grid'" :class="viewMode === 'grid' ? 'bg-primary text-white' : 'text-gray-700'" class="p-2 rounded transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                                </svg>
                            </button>
                            <button @click="viewMode = 'list'" :class="viewMode === 'list' ? 'bg-primary text-white' : 'text-gray-700'" class="p-2 rounded transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Bulk Actions Bar -->
                <div x-show="selectedMedia.length > 0" 
                     x-transition
                     class="px-4 py-3 bg-primary-50 border-b border-primary-200 flex items-center justify-between">
                    <span class="text-sm text-primary-700" x-text="`${selectedMedia.length} file(s) selected`"></span>
                    <div class="flex items-center gap-2">
                        <button @click="showMoveModal = true" class="px-3 py-1.5 text-sm bg-white text-gray-700 rounded-lg hover:bg-gray-100 transition-colors">
                            Move
                        </button>
                        <button @click="confirmDelete()" class="px-3 py-1.5 text-sm bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                            Delete
                        </button>
                        <button @click="selectedMedia = []" class="px-3 py-1.5 text-sm text-gray-600 hover:text-gray-900 transition-colors">
                            Clear
                        </button>
                    </div>
                </div>

                <!-- Media Grid/List -->
                <div class="flex-1 overflow-y-auto p-4">
                    <!-- Loading State -->
                    <div x-show="loading" class="flex items-center justify-center h-64">
                        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary"></div>
                    </div>

                    <!-- Empty State -->
                    <div x-show="!loading && media.length === 0" class="flex flex-col items-center justify-center h-64 text-gray-500">
                        <svg class="w-16 h-16 mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <p class="text-lg font-medium mb-2">No media files</p>
                        <p class="text-sm">Upload your first file to get started</p>
                    </div>

                    <!-- Grid View -->
                    <div x-show="!loading && viewMode === 'grid' && media.length > 0" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                        <template x-for="item in media" :key="item.id">
                            <div @click="toggleSelect(item.id)"
                                 :class="selectedMedia.includes(item.id) ? 'ring-2 ring-primary border-primary' : ''"
                                 class="relative group cursor-pointer bg-gray-50 rounded-lg overflow-hidden border-2 border-transparent hover:border-gray-300 transition-all">
                                <!-- Checkbox -->
                                <div class="absolute top-2 left-2 z-10">
                                    <input type="checkbox" 
                                           :checked="selectedMedia.includes(item.id)"
                                           @click.stop="toggleSelect(item.id)"
                                           class="w-5 h-5 text-primary focus:ring-primary rounded border-gray-300">
                                </div>
                                <!-- Image/File Preview -->
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
                                    <p class="text-xs text-gray-500" x-text="formatFileSize(item.size)"></p>
                                </div>
                                <!-- Actions Overlay -->
                                <div class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2">
                                    <button @click.stop="viewFile(item)" class="p-2 bg-white rounded-lg hover:bg-gray-100">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>
                                    <button @click.stop="deleteFile(item)" class="p-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- List View -->
                    <div x-show="!loading && viewMode === 'list' && media.length > 0" class="space-y-2">
                        <template x-for="item in media" :key="item.id">
                            <div @click="toggleSelect(item.id)"
                                 :class="selectedMedia.includes(item.id) ? 'bg-primary-50 border-primary' : ''"
                                 class="flex items-center gap-4 p-3 rounded-lg border-2 border-transparent hover:border-gray-300 cursor-pointer transition-all">
                                <input type="checkbox" 
                                       :checked="selectedMedia.includes(item.id)"
                                       @click.stop="toggleSelect(item.id)"
                                       class="w-5 h-5 text-primary focus:ring-primary rounded border-gray-300">
                                <div class="w-12 h-12 rounded-lg bg-gray-100 flex items-center justify-center flex-shrink-0">
                                    <template x-if="item.type === 'image'">
                                        <img :src="item.thumbnail_url || item.url" :alt="item.name" class="w-full h-full object-cover rounded-lg">
                                    </template>
                                    <template x-if="item.type !== 'image'">
                                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                        </svg>
                                    </template>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-medium text-gray-900 truncate" x-text="item.name"></p>
                                    <p class="text-sm text-gray-500" x-text="item.type + ' · ' + formatFileSize(item.size)"></p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <button @click.stop="viewFile(item)" class="p-2 text-gray-600 hover:text-primary hover:bg-gray-100 rounded-lg transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>
                                    <button @click.stop="deleteFile(item)" class="p-2 text-red-600 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <!-- Create Folder Modal -->
        <div x-show="showCreateFolderModal" 
             x-transition
             class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50"
             style="display: none;">
            <div class="fixed inset-0 transform transition-all" @click="showCreateFolderModal = false">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>
            <div class="mb-6 bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:w-full sm:max-w-md sm:mx-auto">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Create New Folder</h3>
                <form @submit.prevent="createFolder()">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Folder Name</label>
                        <input type="text" x-model="newFolderName" required
                               class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary"
                               placeholder="My Folder">
                    </div>
                    <div class="flex justify-end gap-2">
                        <button type="button" @click="showCreateFolderModal = false; newFolderName = ''" class="px-4 py-2 text-gray-700 hover:text-gray-900 transition-colors">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-600 transition-colors">
                            Create
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Move Files Modal -->
        <div x-show="showMoveModal" 
             x-transition
             class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50"
             style="display: none;">
            <div class="fixed inset-0 transform transition-all" @click="showMoveModal = false">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>
            <div class="mb-6 bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:w-full sm:max-w-md sm:mx-auto">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Move Files</h3>
                    <p class="text-sm text-gray-600 mb-4" x-text="`Move ${selectedMedia.length} file(s) to:`"></p>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Select Folder</label>
                        <select x-model="moveToFolderId" class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary">
                            <option :value="null">Root (No Folder)</option>
                            <template x-for="folder in folders" :key="folder.id">
                                <option :value="folder.id" x-text="folder.name"></option>
                            </template>
                        </select>
                    </div>
                    <div class="flex justify-end gap-2">
                        <button type="button" @click="showMoveModal = false; moveToFolderId = null" class="px-4 py-2 text-gray-700 hover:text-gray-900 transition-colors">
                            Cancel
                        </button>
                        <button @click="moveFiles()" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-600 transition-colors">
                            Move
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- File Details Modal -->
        <div x-show="showFileDetailsModal" 
             x-transition
             class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50"
             style="display: none;"
             @click.self="showFileDetailsModal = false">
            <div class="fixed inset-0 bg-gray-500 opacity-75"></div>
            <div class="mb-6 bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:w-full sm:max-w-2xl sm:mx-auto">
                <div class="p-6" x-show="selectedFileDetails">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">File Details</h3>
                        <button @click="showFileDetailsModal = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <template x-if="selectedFileDetails">
                        <div class="space-y-4">
                            <!-- Image Preview -->
                            <div x-show="selectedFileDetails.type === 'image'" class="flex justify-center">
                                <img :src="selectedFileDetails.thumbnail_url || selectedFileDetails.url" 
                                     :alt="selectedFileDetails.name"
                                     class="max-w-full h-64 object-contain rounded-lg border border-gray-300">
                            </div>
                            <!-- File Info -->
                            <div class="space-y-2">
                                <div>
                                    <label class="text-sm font-medium text-gray-700">File Name</label>
                                    <p class="text-gray-900" x-text="selectedFileDetails.name"></p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-gray-700">Type</label>
                                    <p class="text-gray-900 capitalize" x-text="selectedFileDetails.type"></p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-gray-700">Size</label>
                                    <p class="text-gray-900" x-text="formatFileSize(selectedFileDetails.size)"></p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-gray-700">URL</label>
                                    <div class="flex items-center gap-2">
                                        <input type="text" 
                                               :value="selectedFileDetails.url" 
                                               readonly
                                               class="flex-1 rounded-lg border-gray-300 focus:border-primary focus:ring-primary text-sm">
                                        <button @click="copyToClipboard(selectedFileDetails.url)" 
                                                class="px-3 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors text-sm">
                                            Copy
                                        </button>
                                    </div>
                                </div>
                                <div x-show="selectedFileDetails.thumbnail_url">
                                    <label class="text-sm font-medium text-gray-700">Thumbnail URL</label>
                                    <div class="flex items-center gap-2">
                                        <input type="text" 
                                               :value="selectedFileDetails.thumbnail_url" 
                                               readonly
                                               class="flex-1 rounded-lg border-gray-300 focus:border-primary focus:ring-primary text-sm">
                                        <button @click="copyToClipboard(selectedFileDetails.thumbnail_url)" 
                                                class="px-3 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors text-sm">
                                            Copy
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function mediaLibrary(initialMedia = [], initialFolders = [], currentPage = 1, lastPage = 1, initialFolder = null, initialType = '', initialSearch = '') {
            return {
                media: initialMedia.map(item => ({
                    id: item.id,
                    name: item.name,
                    type: item.type,
                    size: item.size,
                    url: item.url || (item.path ? '{{ asset("storage/") }}/' + item.path : ''),
                    thumbnail_url: item.thumbnail_url || (item.type === 'image' && item.path ? '{{ asset("storage/") }}/' + item.path : null)
                })),
                folders: initialFolders.map(folder => ({
                    id: folder.id,
                    name: folder.name,
                    path: folder.path
                })),
                selectedMedia: [],
                selectedFolder: initialFolder,
                searchQuery: initialSearch || '',
                typeFilter: initialType || '',
                viewMode: 'grid',
                loading: false,
                showCreateFolderModal: false,
                showMoveModal: false,
                showFileDetailsModal: false,
                selectedFileDetails: null,
                moveToFolderId: null,
                newFolderName: '',
                currentPage: currentPage,
                lastPage: lastPage,

                init() {
                    // Data already loaded from server
                },

                async loadFolders() {
                    try {
                        const response = await axios.get('{{ route('admin.media.folders.index') }}');
                        // If AJAX request, update folders
                        // For now, folders are loaded from server
                    } catch (error) {
                        console.error('Error loading folders:', error);
                    }
                },

                async loadMedia() {
                    this.loading = true;
                    try {
                        const params = new URLSearchParams();
                        if (this.selectedFolder) params.append('folder_id', this.selectedFolder);
                        if (this.searchQuery) params.append('search', this.searchQuery);
                        if (this.typeFilter) params.append('type', this.typeFilter);
                        
                        window.location.href = '{{ route('admin.media.index') }}?' + params.toString();
                    } catch (error) {
                        console.error('Error loading media:', error);
                        this.loading = false;
                    }
                },

                async handleFileSelect(event) {
                    const files = event.target.files;
                    if (files.length === 0) return;

                    const formData = new FormData();
                    Array.from(files).forEach(file => {
                        formData.append('files[]', file);
                    });
                    if (this.selectedFolder) {
                        formData.append('folder_id', this.selectedFolder);
                    }

                    try {
                        const response = await axios.post('{{ route('admin.media.store') }}', formData, {
                            headers: { 'Content-Type': 'multipart/form-data' }
                        });
                        // Reload page to show new files
                        window.location.reload();
                    } catch (error) {
                        console.error('Error uploading files:', error);
                        alert('Error uploading files. Please try again.');
                    }
                },

                toggleSelect(mediaId) {
                    const index = this.selectedMedia.indexOf(mediaId);
                    if (index > -1) {
                        this.selectedMedia.splice(index, 1);
                    } else {
                        this.selectedMedia.push(mediaId);
                    }
                },

                viewFile(item) {
                    this.selectedFileDetails = item;
                    this.showFileDetailsModal = true;
                },

                async deleteFile(item) {
                    if (!confirm(`Are you sure you want to delete "${item.name}"?`)) return;

                    try {
                        const response = await axios.delete('{{ route('admin.media.destroy', '') }}/' + item.id, {
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            }
                        });
                        // Reload page to refresh list
                        window.location.reload();
                    } catch (error) {
                        console.error('Error deleting file:', error);
                        alert('Error deleting file. Please try again.');
                    }
                },

                async confirmDelete() {
                    if (this.selectedMedia.length === 0) return;
                    if (!confirm(`Are you sure you want to delete ${this.selectedMedia.length} file(s)?`)) return;

                    try {
                        for (const mediaId of this.selectedMedia) {
                            await axios.delete('{{ route('admin.media.destroy', '') }}/' + mediaId, {
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                }
                            });
                        }
                        // Reload page to refresh list
                        window.location.reload();
                    } catch (error) {
                        console.error('Error deleting files:', error);
                        alert('Error deleting files. Please try again.');
                    }
                },

                async createFolder() {
                    if (!this.newFolderName.trim()) return;

                    try {
                        const formData = new FormData();
                        formData.append('name', this.newFolderName);
                        formData.append('business_id', '{{ $business->id }}');
                        if (this.selectedFolder) {
                            formData.append('parent_id', this.selectedFolder);
                        }

                        await axios.post('{{ route('admin.media.folders.store') }}', formData, {
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            }
                        });
                        this.newFolderName = '';
                        this.showCreateFolderModal = false;
                        // Reload page to show new folder
                        window.location.reload();
                    } catch (error) {
                        console.error('Error creating folder:', error);
                        alert('Error creating folder. Please try again.');
                    }
                },

                async moveFiles() {
                    if (this.selectedMedia.length === 0) return;
                    if (!this.moveToFolderId && this.moveToFolderId !== 0) {
                        alert('Please select a folder');
                        return;
                    }

                    try {
                        for (const mediaId of this.selectedMedia) {
                            await axios.put('{{ route('admin.media.update', '') }}/' + mediaId, {
                                folder_id: this.moveToFolderId || null
                            }, {
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                    'Content-Type': 'application/json'
                                }
                            });
                        }
                        this.showMoveModal = false;
                        this.moveToFolderId = null;
                        this.selectedMedia = [];
                        // Reload page to refresh list
                        window.location.reload();
                    } catch (error) {
                        console.error('Error moving files:', error);
                        alert('Error moving files. Please try again.');
                    }
                },

                copyToClipboard(text) {
                    navigator.clipboard.writeText(text).then(() => {
                        alert('URL copied to clipboard!');
                    }).catch(() => {
                        alert('Failed to copy URL');
                    });
                },

                formatFileSize(bytes) {
                    if (!bytes) return '0 B';
                    const k = 1024;
                    const sizes = ['B', 'KB', 'MB', 'GB'];
                    const i = Math.floor(Math.log(bytes) / Math.log(k));
                    return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
                }
            }
        }
    </script>
    @endpush
</x-admin-layout>

