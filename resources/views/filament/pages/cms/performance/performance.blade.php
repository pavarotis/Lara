<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Cache Statistics -->
        <x-filament::section>
            <x-slot name="heading">
                Cache Statistics
            </x-slot>
            <x-slot name="description">
                Performance metrics for page and layout caching
            </x-slot>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow">
                    <div class="text-sm text-gray-500 dark:text-gray-400">Cache Hit Rate</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ number_format($this->getPerformanceData()['cache']['hit_rate'], 1) }}%
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow">
                    <div class="text-sm text-gray-500 dark:text-gray-400">Cache Miss Rate</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ number_format($this->getPerformanceData()['cache']['miss_rate'], 1) }}%
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow">
                    <div class="text-sm text-gray-500 dark:text-gray-400">Total Requests</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ number_format($this->getPerformanceData()['cache']['total_requests']) }}
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow">
                    <div class="text-sm text-gray-500 dark:text-gray-400">Cached Pages</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ number_format($this->getPerformanceData()['cache']['cached_pages']) }}
                    </div>
                </div>
            </div>
        </x-filament::section>

        <!-- Layout Compilation -->
        <x-filament::section>
            <x-slot name="heading">
                Layout Compilation
            </x-slot>
            <x-slot name="description">
                Status of layout compilation pipeline
            </x-slot>

            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Compiled Layouts</span>
                    <span class="text-lg font-semibold">
                        {{ $this->getPerformanceData()['compilation']['compiled'] }} / {{ $this->getPerformanceData()['compilation']['total'] }}
                    </span>
                </div>
                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-4">
                    <div class="bg-primary-600 h-4 rounded-full" style="width: {{ $this->getPerformanceData()['compilation']['percentage'] }}%"></div>
                </div>
                <div class="text-sm text-gray-500 dark:text-gray-400">
                    {{ $this->getPerformanceData()['compilation']['percentage'] }}% of layouts are compiled
                </div>
            </div>
        </x-filament::section>

        <!-- Asset Statistics -->
        <x-filament::section>
            <x-slot name="heading">
                Asset Statistics
            </x-slot>
            <x-slot name="description">
                Widget asset declarations and loading
            </x-slot>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow">
                    <div class="text-sm text-gray-500 dark:text-gray-400">Modules with Assets</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ $this->getPerformanceData()['assets']['modules_with_assets'] }}
                    </div>
                    <div class="text-xs text-gray-400 mt-1">
                        out of {{ $this->getPerformanceData()['assets']['total_modules'] }} total
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow">
                    <div class="text-sm text-gray-500 dark:text-gray-400">Total Assets</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ $this->getPerformanceData()['assets']['total_assets'] }}
                    </div>
                    <div class="text-xs text-gray-400 mt-1">
                        CSS and JS files
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow">
                    <div class="text-sm text-gray-500 dark:text-gray-400">Coverage</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">
                        @php
                            $coverage = $this->getPerformanceData()['assets']['total_modules'] > 0
                                ? round(($this->getPerformanceData()['assets']['modules_with_assets'] / $this->getPerformanceData()['assets']['total_modules']) * 100, 1)
                                : 0;
                        @endphp
                        {{ $coverage }}%
                    </div>
                    <div class="text-xs text-gray-400 mt-1">
                        modules with assets
                    </div>
                </div>
            </div>
        </x-filament::section>

        <!-- Bundle Size Report -->
        <x-filament::section>
            <x-slot name="heading">
                Bundle Size Report
            </x-slot>
            <x-slot name="description">
                Latest Vite bundle report from storage/app/performance/bundle-report.json
            </x-slot>

            @php
                $bundle = $this->getPerformanceData()['bundle'];
                $formatBytes = function (?int $bytes): string {
                    if ($bytes === null) {
                        return '—';
                    }
                    if ($bytes >= 1024 * 1024) {
                        return number_format($bytes / (1024 * 1024), 2).' MB';
                    }
                    if ($bytes >= 1024) {
                        return number_format($bytes / 1024, 1).' KB';
                    }
                    return $bytes.' B';
                };
            @endphp

            @if($bundle['exists'] && $bundle['totals'])
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow">
                        <div class="text-sm text-gray-500 dark:text-gray-400">Total</div>
                        <div class="text-2xl font-bold text-gray-900 dark:text-white">
                            {{ $formatBytes((int) $bundle['totals']['total']) }}
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow">
                        <div class="text-sm text-gray-500 dark:text-gray-400">JS</div>
                        <div class="text-2xl font-bold text-gray-900 dark:text-white">
                            {{ $formatBytes((int) $bundle['totals']['js']) }}
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow">
                        <div class="text-sm text-gray-500 dark:text-gray-400">CSS</div>
                        <div class="text-2xl font-bold text-gray-900 dark:text-white">
                            {{ $formatBytes((int) $bundle['totals']['css']) }}
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow">
                        <div class="text-sm text-gray-500 dark:text-gray-400">Other</div>
                        <div class="text-2xl font-bold text-gray-900 dark:text-white">
                            {{ $formatBytes((int) $bundle['totals']['other']) }}
                        </div>
                    </div>
                </div>

                <div class="mt-3 text-xs text-gray-500 dark:text-gray-400">
                    Generated at: {{ $bundle['generated_at'] ?? '—' }}
                </div>

                @if(!empty($bundle['files']))
                    <div class="mt-4 rounded-lg border border-gray-200 dark:border-gray-700 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach(array_slice($bundle['files'], 0, 5) as $file)
                            <div class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300 flex items-center justify-between">
                                <div class="font-medium text-gray-900 dark:text-white">{{ $file['file'] }}</div>
                                <div class="text-xs text-gray-500">{{ $formatBytes((int) $file['bytes']) }}</div>
                            </div>
                        @endforeach
                    </div>
                @endif
            @else
                <div class="text-sm text-gray-500 dark:text-gray-400">
                    No bundle report yet. Run
                    <code class="px-1 py-0.5 bg-gray-100 dark:bg-gray-800 rounded">npm run build</code>
                    and then
                    <code class="px-1 py-0.5 bg-gray-100 dark:bg-gray-800 rounded">npm run bundle:report</code>.
                </div>
            @endif
        </x-filament::section>

        <!-- Lighthouse Audits -->
        <x-filament::section>
            <x-slot name="heading">
                Lighthouse Audits
            </x-slot>
            <x-slot name="description">
                Latest Lighthouse scores imported from JSON reports
            </x-slot>

            @php
                $lighthouse = $this->getPerformanceData()['lighthouse'];
                $latest = $lighthouse['latest'];
            @endphp

            @if($latest)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow">
                        <div class="text-sm text-gray-500 dark:text-gray-400">Performance</div>
                        <div class="text-2xl font-bold text-gray-900 dark:text-white">
                            {{ number_format($latest->performance ?? 0, 1) }}
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow">
                        <div class="text-sm text-gray-500 dark:text-gray-400">Best Practices</div>
                        <div class="text-2xl font-bold text-gray-900 dark:text-white">
                            {{ number_format($latest->best_practices ?? 0, 1) }}
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow">
                        <div class="text-sm text-gray-500 dark:text-gray-400">SEO</div>
                        <div class="text-2xl font-bold text-gray-900 dark:text-white">
                            {{ number_format($latest->seo ?? 0, 1) }}
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow">
                        <div class="text-sm text-gray-500 dark:text-gray-400">Accessibility</div>
                        <div class="text-2xl font-bold text-gray-900 dark:text-white">
                            {{ number_format($latest->accessibility ?? 0, 1) }}
                        </div>
                    </div>
                </div>

                <div class="mt-4 text-sm text-gray-600 dark:text-gray-400">
                    Latest: {{ $latest->url }} ({{ $latest->device }})
                    @if($latest->audited_at)
                        — {{ $latest->audited_at->format('Y-m-d H:i') }}
                    @endif
                </div>

                @if($lighthouse['recent']->count() > 1)
                    <div class="mt-4 rounded-lg border border-gray-200 dark:border-gray-700 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($lighthouse['recent'] as $audit)
                            <div class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300 flex items-center justify-between">
                                <div>
                                    <div class="font-medium text-gray-900 dark:text-white">{{ $audit->url }}</div>
                                    <div class="text-xs text-gray-500">{{ $audit->device }} • {{ optional($audit->audited_at)->format('Y-m-d H:i') ?? '—' }}</div>
                                </div>
                                <div class="text-xs text-gray-500">
                                    P {{ $audit->performance ?? '—' }} /
                                    BP {{ $audit->best_practices ?? '—' }} /
                                    SEO {{ $audit->seo ?? '—' }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            @else
                <div class="text-sm text-gray-500 dark:text-gray-400">
                    No Lighthouse audits yet. Import a JSON report with
                    <code class="px-1 py-0.5 bg-gray-100 dark:bg-gray-800 rounded">php artisan performance:lighthouse:import path/to/report.json</code>
                </div>
            @endif
        </x-filament::section>
    </div>
</x-filament-panels::page>
