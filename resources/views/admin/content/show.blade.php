<x-admin-layout>
    <x-slot name="title">View Content</x-slot>

    <!-- Header -->
    <div class="mb-6">
        <nav class="flex items-center gap-2 text-sm text-gray-500 mb-2">
            <a href="{{ route('admin.content.index') }}" class="hover:text-primary">Content</a>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
            <span class="text-gray-900">{{ $content->title }}</span>
        </nav>
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">{{ $content->title }}</h2>
                <p class="text-sm text-gray-500 mt-1">Slug: /{{ $content->slug }}</p>
            </div>
            <div class="flex items-center gap-2">
                @if($content->status !== 'published')
                    <form action="{{ route('admin.content.publish', $content) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" 
                                class="px-4 py-2 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition-colors">
                            Publish
                        </button>
                    </form>
                @endif
                <a href="{{ route('admin.content.edit', $content) }}" 
                   class="px-4 py-2 bg-primary text-white font-medium rounded-lg hover:bg-primary-600 transition-colors">
                    Edit
                </a>
            </div>
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

    <!-- Content Details -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Content Info -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Content Details</h3>
                <dl class="grid grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Type</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                {{ ucfirst($content->type) }}
                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            @if($content->status === 'published')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Published
                                </span>
                            @elseif($content->status === 'draft')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    Draft
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    Archived
                                </span>
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Created</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $content->created_at->format('M d, Y H:i') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Last Updated</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $content->updated_at->format('M d, Y H:i') }}</dd>
                    </div>
                    @if($content->published_at)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Published At</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $content->published_at->format('M d, Y H:i') }}</dd>
                        </div>
                    @endif
                    @if($content->creator)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Created By</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $content->creator->name }}</dd>
                        </div>
                    @endif
                </dl>
            </div>

            <!-- Blocks Preview -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Content Blocks ({{ count($content->body_json ?? []) }})</h3>
                <div class="space-y-4">
                    @forelse($content->body_json ?? [] as $index => $block)
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-semibold text-gray-700">{{ ucfirst($block['type'] ?? 'Unknown') }} Block</span>
                                <span class="text-xs text-gray-500">Block #{{ $index + 1 }}</span>
                            </div>
                            <div class="text-sm text-gray-600">
                                @if($block['type'] === 'text')
                                    <p class="line-clamp-3">{{ strip_tags($block['props']['content'] ?? '') }}</p>
                                @elseif($block['type'] === 'hero')
                                    <p><strong>Title:</strong> {{ $block['props']['title'] ?? 'N/A' }}</p>
                                    @if(!empty($block['props']['subtitle']))
                                        <p><strong>Subtitle:</strong> {{ $block['props']['subtitle'] }}</p>
                                    @endif
                                @elseif($block['type'] === 'gallery')
                                    <p><strong>Images:</strong> {{ count($block['props']['images'] ?? []) }} image(s)</p>
                                    <p><strong>Columns:</strong> {{ $block['props']['columns'] ?? 3 }}</p>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-sm">No blocks in this content.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Actions -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Actions</h3>
                <div class="space-y-2">
                    <a href="{{ route('admin.content.edit', $content) }}" 
                       class="block w-full text-center px-4 py-2 bg-primary text-white font-medium rounded-lg hover:bg-primary-600 transition-colors">
                        Edit Content
                    </a>
                    <form action="{{ route('admin.content.destroy', $content) }}" method="POST" 
                          onsubmit="return confirm('Are you sure you want to delete this content?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="block w-full text-center px-4 py-2 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 transition-colors">
                            Delete Content
                        </button>
                    </form>
                </div>
            </div>

            <!-- Revisions -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Revisions</h3>
                @if($content->revisions->count() > 0)
                    <div class="space-y-3 mb-4">
                        @foreach($content->revisions->take(5) as $revision)
                            <div class="text-sm">
                                <p class="font-medium text-gray-900">{{ $revision->created_at->format('M d, Y H:i') }}</p>
                                @if($revision->user)
                                    <p class="text-gray-500">by {{ $revision->user->name }}</p>
                                @endif
                            </div>
                        @endforeach
                        @if($content->revisions->count() > 5)
                            <p class="text-xs text-gray-500">+ {{ $content->revisions->count() - 5 }} more revisions</p>
                        @endif
                    </div>
                    <a href="{{ route('admin.content.revisions.index', $content) }}" 
                       class="block w-full text-center px-4 py-2 bg-primary text-white font-medium rounded-lg hover:bg-primary-600 transition-colors">
                        View All Revisions ({{ $content->revisions->count() }})
                    </a>
                @else
                    <p class="text-sm text-gray-500 mb-4">No revisions yet. Revisions are created automatically when content is updated.</p>
                    <a href="{{ route('admin.content.revisions.index', $content) }}" 
                       class="block w-full text-center px-4 py-2 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200 transition-colors">
                        View Revisions
                    </a>
                @endif
            </div>
        </div>
    </div>
</x-admin-layout>

