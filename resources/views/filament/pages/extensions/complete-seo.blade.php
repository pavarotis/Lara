<x-filament-panels::page>
    <x-filament::tabs>
        <x-filament::tabs.item
            :active="$activeTab === 'global'"
            wire:click="$set('activeTab', 'global')"
        >
            Global SEO
        </x-filament::tabs.item>
        <x-filament::tabs.item
            :active="$activeTab === 'sitemap'"
            wire:click="$set('activeTab', 'sitemap')"
        >
            Sitemap
        </x-filament::tabs.item>
        <x-filament::tabs.item
            :active="$activeTab === 'jsonld'"
            wire:click="$set('activeTab', 'jsonld')"
        >
            JSON-LD
        </x-filament::tabs.item>
        <x-filament::tabs.item
            :active="$activeTab === 'robots'"
            wire:click="$set('activeTab', 'robots')"
        >
            Robots.txt
        </x-filament::tabs.item>
        <x-filament::tabs.item
            :active="$activeTab === 'redirects'"
            wire:click="$set('activeTab', 'redirects')"
        >
            URL Redirection
        </x-filament::tabs.item>
    </x-filament::tabs>

    <div class="mt-6">
        @if($activeTab === 'global')
            <form wire:submit="save">
                {{ $this->form }}

                <div class="mt-6">
                    <x-filament::button type="submit">
                        Save SEO Settings
                    </x-filament::button>
                </div>
            </form>
        @elseif($activeTab === 'sitemap')
            <x-filament::section>
                <x-slot name="heading">
                    Sitemap
                </x-slot>
                <x-slot name="description">
                    Generate and preview your sitemap.xml
                </x-slot>

                <div class="space-y-4">
                    <x-filament::button
                        wire:click="generateSitemap"
                        color="success"
                        icon="heroicon-o-arrow-path"
                    >
                        Generate Sitemap
                    </x-filament::button>

                    @if($this->getSitemapPreview())
                        <div class="mt-4">
                            <h4 class="text-sm font-semibold mb-2">Sitemap Preview:</h4>
                            <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4 overflow-x-auto">
                                <pre class="text-xs text-gray-700 dark:text-gray-300"><code>{{ $this->getSitemapPreview() }}</code></pre>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                                Sitemap URL: <a href="{{ route('sitemap.index') }}" target="_blank" class="text-primary-600 hover:underline">{{ route('sitemap.index') }}</a>
                            </p>
                        </div>
                    @endif
                </div>
            </x-filament::section>
        @elseif($activeTab === 'jsonld')
            <x-filament::section>
                <x-slot name="heading">
                    JSON-LD Structured Data
                </x-slot>
                <x-slot name="description">
                    Preview your structured data for search engines
                </x-slot>

                @if($jsonLd = $this->getJsonLdPreview())
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4 overflow-x-auto">
                        <pre class="text-xs text-gray-700 dark:text-gray-300"><code>{{ json_encode($jsonLd, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</code></pre>
                    </div>
                @else
                    <p class="text-sm text-gray-500 dark:text-gray-400">No active business found to generate structured data.</p>
                @endif
            </x-filament::section>
        @elseif($activeTab === 'robots')
            <x-filament::section>
                <x-slot name="heading">
                    Robots.txt
                </x-slot>
                <x-slot name="description">
                    Preview your robots.txt file
                </x-slot>

                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                    <pre class="text-xs text-gray-700 dark:text-gray-300"><code>User-agent: *
Allow: /

Sitemap: {{ route('sitemap.index', absolute: true) }}</code></pre>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                    Robots.txt URL: <a href="{{ route('robots.index') }}" target="_blank" class="text-primary-600 hover:underline">{{ route('robots.index') }}</a>
                </p>
            </x-filament::section>
        @elseif($activeTab === 'redirects')
            <x-filament::section>
                <x-slot name="heading">
                    URL Redirection
                </x-slot>
                <x-slot name="description">
                    Manage URL redirects (301/302) for SEO and broken links
                </x-slot>

                {{ $this->table }}
            </x-filament::section>
        @endif
    </div>
</x-filament-panels::page>
