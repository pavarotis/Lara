<x-admin-layout>
    <x-slot name="title">API Documentation</x-slot>

    <div class="space-y-6">
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h1 class="text-2xl font-bold text-gray-900 mb-4">API Documentation</h1>
            <p class="text-gray-600 mb-4">
                Complete API documentation for the headless API (v2). All endpoints require API key authentication.
            </p>
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h3 class="font-semibold text-blue-900 mb-2">Authentication</h3>
                <p class="text-sm text-blue-800 mb-2">
                    All API requests require the following headers:
                </p>
                <ul class="list-disc list-inside text-sm text-blue-800 space-y-1">
                    <li><code class="bg-blue-100 px-2 py-1 rounded">X-API-Key</code> - Your API key</li>
                    <li><code class="bg-blue-100 px-2 py-1 rounded">X-API-Secret</code> - Your API secret</li>
                </ul>
            </div>
        </div>

        @foreach($endpoints as $name => $endpoint)
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ ucfirst($name) }}</h3>
                        <p class="text-sm text-gray-600">{{ $endpoint['description'] }}</p>
                    </div>
                    <span class="bg-green-100 text-green-800 text-xs font-semibold px-3 py-1 rounded-full">
                        {{ $endpoint['method'] }}
                    </span>
                </div>

                <div class="space-y-3">
                    <div>
                        <p class="text-sm font-medium text-gray-700 mb-1">Path:</p>
                        <code class="bg-gray-100 text-gray-900 px-3 py-2 rounded-lg block text-sm">
                            {{ $endpoint['path'] }}
                        </code>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm font-medium text-gray-700 mb-1">Authentication:</p>
                            <p class="text-sm text-gray-600">{{ $endpoint['auth'] }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-700 mb-1">Rate Limit:</p>
                            <p class="text-sm text-gray-600">{{ $endpoint['rate_limit'] }}</p>
                        </div>
                    </div>

                    @if(isset($endpoint['scope']))
                        <div>
                            <p class="text-sm font-medium text-gray-700 mb-1">Required Scope:</p>
                            <code class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-sm">
                                {{ $endpoint['scope'] }}
                            </code>
                        </div>
                    @endif

                    @if(isset($endpoint['response']))
                        <div>
                            <p class="text-sm font-medium text-gray-700 mb-2">Example Response:</p>
                            <pre class="bg-gray-100 rounded-lg p-4 text-xs overflow-x-auto"><code>{{ json_encode($endpoint['response'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</code></pre>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</x-admin-layout>

