<x-admin-layout>
    <x-slot name="title">Compare Revisions</x-slot>

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
            <span class="text-gray-900">Compare</span>
        </nav>
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Compare Revisions</h2>
                <p class="text-sm text-gray-500 mt-1">Compare revision #{{ $revisionA->id }} with revision #{{ $revisionB->id }}</p>
            </div>
            <a href="{{ route('admin.content.revisions.index', $content) }}" 
               class="px-4 py-2 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200 transition-colors">
                Back to Revisions
            </a>
        </div>
    </div>

    <!-- Comparison -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Revision A -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Revision #{{ $revisionA->id }}</h3>
                <a href="{{ route('admin.content.revisions.show', [$content, $revisionA]) }}" 
                   class="text-sm text-primary hover:text-primary-600">View Full</a>
            </div>
            <div class="space-y-2 mb-4 text-sm text-gray-600">
                <p><strong>Created:</strong> {{ $revisionA->created_at->format('M d, Y H:i') }}</p>
                <p><strong>By:</strong> {{ $revisionA->user?->name ?? 'System' }}</p>
                <p><strong>Blocks:</strong> {{ count($revisionA->body_json ?? []) }}</p>
            </div>
            <div class="space-y-3 max-h-96 overflow-y-auto">
                @forelse($revisionA->body_json ?? [] as $index => $block)
                    <div class="border border-gray-200 rounded-lg p-3">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-xs font-semibold text-gray-700">{{ ucfirst($block['type'] ?? 'Unknown') }}</span>
                            <span class="text-xs text-gray-500">#{{ $index + 1 }}</span>
                        </div>
                        <div class="text-xs text-gray-600">
                            @if($block['type'] === 'text')
                                <p class="line-clamp-2">{{ strip_tags($block['props']['content'] ?? '') }}</p>
                            @elseif($block['type'] === 'hero')
                                <p><strong>Title:</strong> {{ $block['props']['title'] ?? 'N/A' }}</p>
                            @else
                                <p>{{ count($block['props'] ?? []) }} properties</p>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-sm">No blocks</p>
                @endforelse
            </div>
        </div>

        <!-- Revision B -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Revision #{{ $revisionB->id }}</h3>
                <a href="{{ route('admin.content.revisions.show', [$content, $revisionB]) }}" 
                   class="text-sm text-primary hover:text-primary-600">View Full</a>
            </div>
            <div class="space-y-2 mb-4 text-sm text-gray-600">
                <p><strong>Created:</strong> {{ $revisionB->created_at->format('M d, Y H:i') }}</p>
                <p><strong>By:</strong> {{ $revisionB->user?->name ?? 'System' }}</p>
                <p><strong>Blocks:</strong> {{ count($revisionB->body_json ?? []) }}</p>
            </div>
            <div class="space-y-3 max-h-96 overflow-y-auto">
                @forelse($revisionB->body_json ?? [] as $index => $block)
                    <div class="border border-gray-200 rounded-lg p-3">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-xs font-semibold text-gray-700">{{ ucfirst($block['type'] ?? 'Unknown') }}</span>
                            <span class="text-xs text-gray-500">#{{ $index + 1 }}</span>
                        </div>
                        <div class="text-xs text-gray-600">
                            @if($block['type'] === 'text')
                                <p class="line-clamp-2">{{ strip_tags($block['props']['content'] ?? '') }}</p>
                            @elseif($block['type'] === 'hero')
                                <p><strong>Title:</strong> {{ $block['props']['title'] ?? 'N/A' }}</p>
                            @else
                                <p>{{ count($block['props'] ?? []) }} properties</p>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-sm">No blocks</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Differences Summary -->
    <div class="mt-6 bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Differences Summary</h3>
        <div class="space-y-2 text-sm">
            @php
                $blocksA = $revisionA->body_json ?? [];
                $blocksB = $revisionB->body_json ?? [];
                $countDiff = count($blocksA) - count($blocksB);
            @endphp
            <p><strong>Block Count:</strong> 
                Revision A: {{ count($blocksA) }}, 
                Revision B: {{ count($blocksB) }}
                @if($countDiff !== 0)
                    <span class="text-{{ $countDiff > 0 ? 'red' : 'green' }}-600">
                        ({{ $countDiff > 0 ? '+' : '' }}{{ $countDiff }})
                    </span>
                @endif
            </p>
            <p><strong>Created:</strong> 
                {{ $revisionA->created_at->diffForHumans($revisionB->created_at) }} difference
            </p>
        </div>
    </div>
@endsection

