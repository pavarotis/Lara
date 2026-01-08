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
    </div>
</x-filament-panels::page>
