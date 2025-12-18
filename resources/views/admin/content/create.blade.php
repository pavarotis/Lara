<x-admin-layout>
    <x-slot name="title">Create Content</x-slot>

    <!-- Header -->
    <div class="mb-6">
        <nav class="flex items-center gap-2 text-sm text-gray-500 mb-2">
            <a href="{{ route('admin.content.index') }}" class="hover:text-primary">Content</a>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
            <span class="text-gray-900">Create Content</span>
        </nav>
        <h2 class="text-2xl font-bold text-gray-900">Create New Content</h2>
    </div>

    <!-- Form -->
    <form action="{{ route('admin.content.store') }}" method="POST" id="content-form" class="max-w-4xl">
        @csrf

        <div class="bg-white rounded-xl shadow-sm p-6 space-y-6 mb-6">
            <!-- Basic Fields -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Title -->
                <div class="md:col-span-2">
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Title *</label>
                    <input type="text" name="title" id="title" value="{{ old('title') }}" required
                           class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary"
                           placeholder="Content title">
                    @error('title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Slug -->
                <div>
                    <label for="slug" class="block text-sm font-medium text-gray-700 mb-1">Slug *</label>
                    <input type="text" name="slug" id="slug" value="{{ old('slug') }}" required
                           class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary"
                           placeholder="content-slug">
                    <p class="mt-1 text-xs text-gray-500">Auto-generated from title (editable)</p>
                    @error('slug')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Content Type -->
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Content Type *</label>
                    <select name="type" id="type" required
                            class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary">
                        <option value="">Select type</option>
                        @foreach($contentTypes ?? [] as $contentType)
                            <option value="{{ $contentType->slug }}" {{ old('type') === $contentType->slug ? 'selected' : '' }}>
                                {{ $contentType->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" id="status"
                            class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary">
                        <option value="draft" {{ old('status', 'draft') === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="published" {{ old('status') === 'published' ? 'selected' : '' }}>Published</option>
                        <option value="archived" {{ old('status') === 'archived' ? 'selected' : '' }}>Archived</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Layout -->
                <div class="md:col-span-2">
                    <label for="layout_id" class="block text-sm font-medium text-gray-700 mb-1">Layout</label>
                    <select name="layout_id" id="layout_id"
                            class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary">
                        <option value="">None (Use legacy blocks)</option>
                        @foreach($layouts ?? [] as $layout)
                            <option value="{{ $layout->id }}" {{ old('layout_id') == $layout->id ? 'selected' : '' }}>
                                {{ $layout->name }} ({{ $layout->type }})
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-gray-500">Select layout for this page. If not set, legacy blocks will be used.</p>
                    @error('layout_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Business ID -->
                <input type="hidden" name="business_id" value="{{ $business->id }}">
            </div>
        </div>

        <!-- Block Builder -->
        <div class="bg-white rounded-xl shadow-sm p-6 space-y-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Content Blocks</h3>
                    <p class="text-sm text-gray-500">Add and arrange content blocks</p>
                </div>
                <div class="relative">
                    <button type="button" id="add-block-btn"
                            class="inline-flex items-center px-4 py-2 bg-primary text-white font-medium rounded-lg hover:bg-primary-600 transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Add Block
                    </button>
                    <div id="add-block-menu" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-10">
                        <div class="py-1">
                            <button type="button" onclick="addBlock('text'); closeAddBlockMenu()"
                                    class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                Text Block
                            </button>
                            <button type="button" onclick="addBlock('hero'); closeAddBlockMenu()"
                                    class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                Hero Block
                            </button>
                            <button type="button" onclick="addBlock('gallery'); closeAddBlockMenu()"
                                    class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                Gallery Block
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Blocks Container -->
            <div id="blocks-container" class="space-y-4">
                <!-- Blocks will be inserted here -->
            </div>

            <!-- Empty State -->
            <div id="blocks-empty" class="text-center py-12 border-2 border-dashed border-gray-200 rounded-lg">
                <svg class="w-12 h-12 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                <p class="text-gray-500">No blocks added yet. Click "Add Block" to get started.</p>
            </div>

        </div>

        <!-- Actions -->
        <div class="flex items-center gap-4">
            <button type="submit" 
                    class="px-6 py-2 bg-primary text-white font-medium rounded-lg hover:bg-primary-600 transition-colors">
                Save Content
            </button>
            <a href="{{ route('admin.content.index') }}" 
               class="px-6 py-2 text-gray-700 hover:text-gray-900 transition-colors">
                Cancel
            </a>
        </div>
    </form>

    @push('scripts')
    <script>
        // Toggle add block menu
        document.getElementById('add-block-btn')?.addEventListener('click', function() {
            const menu = document.getElementById('add-block-menu');
            menu?.classList.toggle('hidden');
        });

        function closeAddBlockMenu() {
            document.getElementById('add-block-menu')?.classList.add('hidden');
        }

        // Close menu when clicking outside
        document.addEventListener('click', function(event) {
            const btn = document.getElementById('add-block-btn');
            const menu = document.getElementById('add-block-menu');
            if (menu && !menu.contains(event.target) && !btn?.contains(event.target)) {
                menu.classList.add('hidden');
            }
        });
    </script>
    <script>
        let blocks = [];
        let blockIndex = 0;

        // Initialize from old input if exists
        @if(old('body_json'))
            try {
                blocks = JSON.parse(@json(old('body_json')));
                blockIndex = blocks.length;
                renderBlocks();
            } catch(e) {
                console.error('Error parsing body_json:', e);
            }
        @endif

        function addBlock(type) {
            const block = {
                type: type,
                props: getDefaultProps(type)
            };
            blocks.push(block);
            blockIndex++;
            renderBlocks();
            updateBodyJson();
        }

        function removeBlock(index) {
            // Find block by data-block-index
            const blockElement = document.querySelector(`[data-block-index="${index}"]`);
            if (blockElement) {
                const idx = Array.from(document.querySelectorAll('[data-block-index]')).indexOf(blockElement);
                blocks.splice(idx, 1);
                renderBlocks();
                updateBodyJson();
            }
        }

        function getDefaultProps(type) {
            switch(type) {
                case 'text':
                    return { content: '', alignment: 'left' };
                case 'hero':
                    return { title: '', subtitle: '', image: '', cta_text: '', cta_link: '' };
                case 'gallery':
                    return { images: [], columns: 3 };
                default:
                    return {};
            }
        }

        function renderBlocks() {
            const container = document.getElementById('blocks-container');
            const empty = document.getElementById('blocks-empty');
            
            if (blocks.length === 0) {
                container.innerHTML = '';
                empty.style.display = 'block';
                return;
            }

            empty.style.display = 'none';
            container.innerHTML = blocks.map((block, index) => {
                return getBlockHTML(block, index);
            }).join('');

            // Re-index all blocks
            document.querySelectorAll('[data-block-index]').forEach((el, idx) => {
                el.setAttribute('data-block-index', idx);
                el.querySelectorAll('input, textarea, select').forEach(input => {
                    const name = input.getAttribute('name');
                    if (name) {
                        input.setAttribute('name', name.replace(/blocks\[\d+\]/, `blocks[${idx}]`));
                    }
                });
            });
        }

        function getBlockHTML(block, index) {
            const blockData = JSON.stringify(block).replace(/"/g, '&quot;');
            switch(block.type) {
                case 'text':
                    return `
                        <div class="border border-gray-200 rounded-lg p-4 bg-white" data-block-index="${index}">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="text-sm font-semibold text-gray-700">Text Block</h4>
                                <button type="button" onclick="removeBlock(${index})" class="text-red-600 hover:text-red-800 text-sm">Remove</button>
                            </div>
                            <div class="space-y-3">
                                <input type="hidden" name="blocks[${index}][type]" value="text">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Content *</label>
                                    <textarea name="blocks[${index}][props][content]" rows="6" class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary">${block.props?.content || ''}</textarea>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Alignment</label>
                                    <select name="blocks[${index}][props][alignment]" class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary">
                                        <option value="left" ${(block.props?.alignment || 'left') === 'left' ? 'selected' : ''}>Left</option>
                                        <option value="center" ${block.props?.alignment === 'center' ? 'selected' : ''}>Center</option>
                                        <option value="right" ${block.props?.alignment === 'right' ? 'selected' : ''}>Right</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    `;
                case 'hero':
                    return `
                        <div class="border border-gray-200 rounded-lg p-4 bg-white" data-block-index="${index}">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="text-sm font-semibold text-gray-700">Hero Block</h4>
                                <button type="button" onclick="removeBlock(${index})" class="text-red-600 hover:text-red-800 text-sm">Remove</button>
                            </div>
                            <div class="space-y-3">
                                <input type="hidden" name="blocks[${index}][type]" value="hero">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Title *</label>
                                    <input type="text" name="blocks[${index}][props][title]" value="${block.props?.title || ''}" class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Subtitle</label>
                                    <input type="text" name="blocks[${index}][props][subtitle]" value="${block.props?.subtitle || ''}" class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Image URL</label>
                                    <input type="text" name="blocks[${index}][props][image]" value="${block.props?.image || ''}" class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary" placeholder="https://example.com/image.jpg">
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">CTA Text</label>
                                        <input type="text" name="blocks[${index}][props][cta_text]" value="${block.props?.cta_text || ''}" class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">CTA Link</label>
                                        <input type="text" name="blocks[${index}][props][cta_link]" value="${block.props?.cta_link || ''}" class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary">
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                case 'gallery':
                    const images = Array.isArray(block.props?.images) ? block.props.images.join('\n') : (block.props?.images || '');
                    return `
                        <div class="border border-gray-200 rounded-lg p-4 bg-white" data-block-index="${index}">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="text-sm font-semibold text-gray-700">Gallery Block</h4>
                                <button type="button" onclick="removeBlock(${index})" class="text-red-600 hover:text-red-800 text-sm">Remove</button>
                            </div>
                            <div class="space-y-3">
                                <input type="hidden" name="blocks[${index}][type]" value="gallery">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Image URLs *</label>
                                    <textarea name="blocks[${index}][props][images]" rows="4" class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary">${images}</textarea>
                                    <p class="mt-1 text-xs text-gray-500">Enter one image URL per line</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Columns</label>
                                    <select name="blocks[${index}][props][columns]" class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary">
                                        <option value="1" ${(block.props?.columns || 3) == 1 ? 'selected' : ''}>1 Column</option>
                                        <option value="2" ${(block.props?.columns || 3) == 2 ? 'selected' : ''}>2 Columns</option>
                                        <option value="3" ${(block.props?.columns || 3) == 3 ? 'selected' : ''}>3 Columns</option>
                                        <option value="4" ${(block.props?.columns || 3) == 4 ? 'selected' : ''}>4 Columns</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    `;
                default:
                    return '';
            }
        }

        function updateBodyJson() {
            // Collect all block data from form
            const formData = new FormData(document.getElementById('content-form'));
            const collectedBlocks = [];
            const blockIndices = new Set();

            // Get all unique block indices
            for (const key of formData.keys()) {
                const match = key.match(/^blocks\[(\d+)\]/);
                if (match) {
                    blockIndices.add(parseInt(match[1]));
                }
            }

            // Collect blocks
            Array.from(blockIndices).sort().forEach(index => {
                const type = formData.get(`blocks[${index}][type]`);
                if (!type) return;

                const block = { type, props: {} };

                // Collect props
                for (const [key, value] of formData.entries()) {
                    const propMatch = key.match(/^blocks\[\d+\]\[props\]\[(.+)\]/);
                    if (propMatch && key.startsWith(`blocks[${index}][props]`)) {
                        const propName = propMatch[1];
                        if (propName === 'images') {
                            // Handle images as array (split by newline)
                            block.props[propName] = value.split('\n').filter(url => url.trim());
                        } else {
                            block.props[propName] = value;
                        }
                    }
                }

                collectedBlocks.push(block);
            });

            // Blocks are sent directly via form inputs, no need for hidden JSON field
        }

        // Ensure blocks are collected on form submit
        document.getElementById('content-form').addEventListener('submit', function(e) {
            // Blocks array is automatically collected from form inputs
        });

        // Auto-generate slug from title
        document.getElementById('title').addEventListener('input', function(e) {
            if (!document.getElementById('slug').value || document.getElementById('slug').value === '{{ old("slug") }}') {
                const slug = e.target.value
                    .toLowerCase()
                    .replace(/[^a-z0-9]+/g, '-')
                    .replace(/^-+|-+$/g, '');
                document.getElementById('slug').value = slug;
            }
        });
    </script>
    @endpush
</x-admin-layout>

