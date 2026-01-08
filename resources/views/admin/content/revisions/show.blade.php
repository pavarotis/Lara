<x-admin-layout>
    <x-slot name="title">View Revision</x-slot>

    <!-- Header -->
    <div class="mb-6">
        <nav class="flex items-center gap-2 text-sm text-gray-500 mb-2">
            <a href="{{ route('admin.content.index') }}" class="hover:text-primary">Content</a>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
            <a href="{{ route('admin.content.show', $content) }}" class="hover:text-primary">{{ $content->title }}</a>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
            <a href="{{ route('admin.content.revisions.index', $content) }}" class="hover:text-primary">Revisions</a>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
            <span class="text-gray-900">Revision #{{ $revision->id }}</span>
        </nav>
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Revision #{{ $revision->id }}</h2>
                <p class="text-sm text-gray-500 mt-1">Created {{ $revision->created_at->format('M d, Y H:i') }} by {{ $revision->user?->name ?? 'System' }}</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.content.revisions.index', $content) }}" 
                   class="px-4 py-2 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200 transition-colors">
                    Back to Revisions
                </a>
                @if($revision->id !== $content->revisions()->latest()->first()?->id)
                    <form action="{{ route('admin.content.revisions.restore', [$content, $revision]) }}" 
                          method="POST" 
                          class="inline"
                          onsubmit="return confirm('Are you sure you want to restore this revision? This will create a backup of the current version.')">
                        @csrf
                        <button type="submit" 
                                class="px-4 py-2 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition-colors">
                            Restore This Revision
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <!-- Revision Details -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Revision Info -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Revision Information</h3>
                <dl class="grid grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Revision ID</dt>
                        <dd class="mt-1 text-sm text-gray-900">#{{ $revision->id }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Created At</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $revision->created_at->format('M d, Y H:i:s') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Created By</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $revision->user?->name ?? 'System' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Blocks Count</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ count($revision->body_json ?? []) }} blocks</dd>
                    </div>
                </dl>
            </div>

            <!-- Blocks Preview -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Content Blocks ({{ count($revision->body_json ?? []) }})</h3>
                <div class="space-y-4">
                    @forelse($revision->body_json ?? [] as $index => $block)
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
                                @else
                                    <pre class="text-xs bg-gray-50 p-2 rounded">{{ json_encode($block['props'] ?? [], JSON_PRETTY_PRINT) }}</pre>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-sm">No blocks in this revision.</p>
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
                    <a href="{{ route('admin.content.show', $content) }}" 
                       class="block w-full text-center px-4 py-2 bg-primary text-white font-medium rounded-lg hover:bg-primary-600 transition-colors">
                        View Current Content
                    </a>
                    @if($revision->id !== $content->revisions->sortByDesc('created_at')->first()?->id)
                        <form action="{{ route('admin.content.revisions.restore', [$content, $revision]) }}" 
                              method="POST" 
                              onsubmit="return confirm('Are you sure you want to restore this revision? This will create a backup of the current version.')">
                            @csrf
                            <button type="submit" 
                                    class="block w-full text-center px-4 py-2 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition-colors">
                                Restore This Revision
                            </button>
                        </form>
                    @endif
                    <a href="{{ route('admin.content.revisions.index', $content) }}" 
                       class="block w-full text-center px-4 py-2 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200 transition-colors">
                        Back to Revisions
                    </a>
                </div>
            </div>

            <!-- Compare -->
            @if($content->revisions->count() > 1)
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Compare</h3>
                    <p class="text-sm text-gray-600 mb-4">Compare this revision with another:</p>
                    <form action="{{ route('admin.content.revisions.compare', [$content, $revision, 'b' => 'latest']) }}" method="GET" class="space-y-2">
                        <select name="b" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary">
                            <option value="latest">Latest Revision</option>
                            @foreach($content->revisions->where('id', '!=', $revision->id)->sortByDesc('created_at') as $other)
                                <option value="{{ $other->id }}">Revision #{{ $other->id }} ({{ $other->created_at->format('M d, Y') }})</option>
                            @endforeach
                        </select>
                        <button type="submit" 
                                class="w-full px-4 py-2 bg-primary text-white font-medium rounded-lg hover:bg-primary-600 transition-colors">
                            Compare
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>
@endsection

