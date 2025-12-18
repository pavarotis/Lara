<x-admin-layout>
    <x-slot name="title">Manage Modules - {{ $content->title }}</x-slot>

    <!-- Header -->
    <div class="mb-6">
        <nav class="flex items-center gap-2 text-sm text-gray-500 mb-2">
            <a href="{{ route('admin.content.index') }}" class="hover:text-primary">Content</a>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
            <a href="{{ route('admin.content.edit', $content) }}" class="hover:text-primary">{{ $content->title }}</a>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
            <span class="text-gray-900">Modules</span>
        </nav>
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Manage Modules</h2>
                <p class="text-gray-600 text-sm mt-1">Layout: {{ $layout->name }} ({{ $layout->type }})</p>
            </div>
            <a href="{{ route('admin.content.edit', $content) }}" 
               class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Content
            </a>
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

    <!-- Regions -->
    <div class="space-y-6">
        @foreach($regions as $region)
            @php
                $regionLabel = ucwords(str_replace('_', ' ', $region));
                $assignments = $modulesByRegion[$region] ?? collect();
            @endphp

            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">{{ $regionLabel }}</h3>
                    <button type="button" 
                            onclick="document.getElementById('add-module-modal-{{ $region }}').classList.remove('hidden')"
                            class="inline-flex items-center px-3 py-1.5 text-sm bg-primary text-white font-medium rounded-lg hover:bg-primary-600 transition-colors">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Add Module
                    </button>
                </div>

                <!-- Modules List -->
                <div x-data="moduleRegionData('{{ $region }}', @js($assignments->map(fn($a) => ['id' => $a->id, 'module_id' => $a->moduleInstance->id, 'module_name' => $a->moduleInstance->name, 'module_type' => $a->moduleInstance->type, 'module_enabled' => $a->moduleInstance->enabled])->values()))" 
                class="space-y-2">
                    <template x-if="assignments.length === 0">
                        <div class="text-center py-8 text-gray-500">
                            <p>No modules assigned to this region.</p>
                            <p class="text-sm mt-1">Click "Add Module" to assign one.</p>
                        </div>
                    </template>

                    <template x-for="(assignment, index) in assignments" :key="assignment.id">
                        <div 
                            :data-id="assignment.id"
                            @dragstart="draggedIndex = index; $event.dataTransfer.effectAllowed = 'move'"
                            @dragover.prevent="if (draggedIndex !== null && draggedIndex !== index) draggedOverIndex = index"
                            @dragleave="if (draggedOverIndex === index) draggedOverIndex = null"
                            @drop.prevent="handleDrop(index)"
                            @dragend="draggedIndex = null; draggedOverIndex = null"
                            draggable="true"
                            :class="{
                                'opacity-50': draggedIndex === index,
                                'border-2 border-primary bg-primary-50': draggedOverIndex === index && draggedIndex !== index
                            }"
                            class="flex items-center gap-4 p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors cursor-move">
                            
                            <!-- Drag Handle -->
                            <div class="text-gray-400 cursor-move">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16" />
                                </svg>
                            </div>

                            <!-- Module Info -->
                            <div class="flex-1">
                                <div class="flex items-center gap-2">
                                    <span class="font-medium text-gray-900" x-text="assignment.module_name || assignment.module_type"></span>
                                    <span class="text-xs px-2 py-0.5 bg-gray-100 text-gray-600 rounded" x-text="assignment.module_type"></span>
                                    <span x-show="!assignment.module_enabled" class="text-xs px-2 py-0.5 bg-yellow-100 text-yellow-700 rounded">Disabled</span>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex items-center gap-2">
                                <form :action="`{{ route('admin.content.modules.toggle', ['content' => $content, 'assignment' => ':id']) }}`.replace(':id', assignment.id)" 
                                      method="POST">
                                    @csrf
                                    @method('POST')
                                    <button type="submit" 
                                            class="px-3 py-1.5 text-sm rounded-lg transition-colors"
                                            :class="assignment.module_enabled ? 'text-yellow-700 hover:bg-yellow-50' : 'text-green-700 hover:bg-green-50'"
                                            x-text="assignment.module_enabled ? 'Disable' : 'Enable'">
                                    </button>
                                </form>
                                <form :action="`{{ route('admin.content.modules.remove', ['content' => $content, 'assignment' => ':id']) }}`.replace(':id', assignment.id)" 
                                      method="POST"
                                      onsubmit="return confirm('Are you sure you want to remove this module from this region?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="px-3 py-1.5 text-sm text-red-700 hover:bg-red-50 rounded-lg transition-colors">
                                        Remove
                                    </button>
                                </form>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Add Module Modal -->
            <div id="add-module-modal-{{ $region }}" 
                 class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
                 onclick="if(event.target === this) this.classList.add('hidden')">
                <div class="bg-white rounded-xl shadow-xl max-w-2xl w-full max-h-[80vh] overflow-y-auto">
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900">Add Module to {{ $regionLabel }}</h3>
                            <button type="button" 
                                    onclick="document.getElementById('add-module-modal-{{ $region }}').classList.add('hidden')"
                                    class="text-gray-400 hover:text-gray-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="p-6">
                        <form action="{{ route('admin.content.modules.add', $content) }}" method="POST">
                            @csrf
                            <input type="hidden" name="region" value="{{ $region }}">
                            
                            <div class="space-y-4">
                                @foreach($availableModules->groupBy('type') as $moduleType => $modules)
                                    @php
                                        $moduleConfig = $moduleTypes[$moduleType] ?? null;
                                    @endphp
                                    @if($moduleConfig)
                                        <div>
                                            <h4 class="font-medium text-gray-900 mb-2">{{ $moduleConfig['name'] }}</h4>
                                            <p class="text-sm text-gray-500 mb-3">{{ $moduleConfig['description'] ?? '' }}</p>
                                            <div class="space-y-2">
                                                @foreach($modules as $module)
                                                    <label class="flex items-center gap-3 p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                                        <input type="radio" name="module_instance_id" value="{{ $module->id }}" required class="text-primary focus:ring-primary">
                                                        <div class="flex-1">
                                                            <span class="font-medium text-gray-900">{{ $module->name ?: $module->type }}</span>
                                                            @if($module->name)
                                                                <span class="text-xs text-gray-500 ml-2">({{ $module->type }})</span>
                                                            @endif
                                                        </div>
                                                    </label>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>

                            @if($availableModules->isEmpty())
                                <div class="text-center py-8 text-gray-500">
                                    <p>No modules available. Create modules first.</p>
                                    <a href="#" class="text-primary hover:underline mt-2 inline-block">Create Module</a>
                                </div>
                            @endif

                            <div class="mt-6 flex justify-end gap-3">
                                <button type="button" 
                                        onclick="document.getElementById('add-module-modal-{{ $region }}').classList.add('hidden')"
                                        class="px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                                    Cancel
                                </button>
                                <button type="submit" 
                                        class="px-4 py-2 bg-primary text-white font-medium rounded-lg hover:bg-primary-600 transition-colors">
                                    Add Module
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    @push('scripts')
    <script>
        function moduleRegionData(region, assignments) {
            return {
                region: region,
                assignments: assignments,
                draggedIndex: null,
                draggedOverIndex: null,
                
                handleDrop(targetIndex) {
                    if (this.draggedIndex === null || this.draggedIndex === targetIndex) {
                        this.draggedIndex = null;
                        this.draggedOverIndex = null;
                        return;
                    }
                    
                    const draggedItem = this.assignments[this.draggedIndex];
                    this.assignments.splice(this.draggedIndex, 1);
                    this.assignments.splice(targetIndex, 0, draggedItem);
                    
                    // Save new order to server
                    const assignmentIds = this.assignments.map(a => a.id);
                    fetch('{{ route('admin.content.modules.reorder', $content) }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            region: this.region,
                            assignment_ids: assignmentIds
                        })
                    }).then(response => {
                        if (!response.ok) {
                            // Reload page on error
                            location.reload();
                        }
                    }).catch(() => {
                        location.reload();
                    });
                    
                    this.draggedIndex = null;
                    this.draggedOverIndex = null;
                }
            };
        }
    </script>
    @endpush
</x-admin-layout>
