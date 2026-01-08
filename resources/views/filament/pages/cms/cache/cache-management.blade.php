<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Cache Information -->
        <x-filament::section>
            <x-slot name="heading">
                Cache Information
            </x-slot>
            <x-slot name="description">
                Current cache configuration and status
            </x-slot>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow">
                    <div class="text-sm text-gray-500 dark:text-gray-400">Cache Driver</div>
                    <div class="text-lg font-semibold text-gray-900 dark:text-white">
                        {{ $this->getCacheInfo()['driver'] }}
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow">
                    <div class="text-sm text-gray-500 dark:text-gray-400">Store Class</div>
                    <div class="text-sm font-mono text-gray-900 dark:text-white break-all">
                        {{ class_basename($this->getCacheInfo()['store']) }}
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow">
                    <div class="text-sm text-gray-500 dark:text-gray-400">Tag Support</div>
                    <div class="text-lg font-semibold">
                        @if($this->getCacheInfo()['supports_tags'])
                            <span class="text-green-600 dark:text-green-400">Yes</span>
                        @else
                            <span class="text-yellow-600 dark:text-yellow-400">No</span>
                        @endif
                    </div>
                </div>
            </div>
        </x-filament::section>

        <!-- Cache Actions -->
        <x-filament::section>
            <x-slot name="heading">
                Cache Actions
            </x-slot>
            <x-slot name="description">
                Clear specific cache types or all cache
            </x-slot>

            <div class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-filament::button
                        wire:click="clearLayoutCache"
                        color="warning"
                        icon="heroicon-o-arrow-path"
                        class="w-full"
                    >
                        Clear Layout Cache
                    </x-filament::button>

                    <x-filament::button
                        wire:click="clearPageCache"
                        color="warning"
                        icon="heroicon-o-arrow-path"
                        class="w-full"
                    >
                        Clear Page Cache
                    </x-filament::button>

                    <x-filament::button
                        wire:click="clearModuleCache"
                        color="warning"
                        icon="heroicon-o-arrow-path"
                        class="w-full"
                    >
                        Clear Module Cache
                    </x-filament::button>

                    <x-filament::button
                        wire:click="clearAllCache"
                        color="danger"
                        icon="heroicon-o-trash"
                        class="w-full"
                    >
                        Clear All Cache
                    </x-filament::button>
                </div>

                <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">
                                Warning
                            </h3>
                            <div class="mt-2 text-sm text-yellow-700 dark:text-yellow-300">
                                <p>
                                    Clearing cache will temporarily slow down the site as pages need to be regenerated.
                                    Use with caution in production.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
