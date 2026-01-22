<x-filament-panels::page>
        <!-- Theme & Assets -->
        <x-section-three-parts
            heading="Theme & Assets"
            description="Manage theme cache and SASS/asset compilation"
        >
            <x-slot name="actions">
                <x-section-three-parts-split-content>
                    <x-slot name="left">
                        <!-- Theme Cache -->
                        <div class="flex flex-col p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                            <div class="mb-4">
                                <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-1">Theme Cache</h4>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Enable or disable theme caching</p>
                            </div>
                            <div class="flex items-center gap-4" style="align-items: center;">
                                <button
                                    type="button"
                                    wire:click="toggleThemeCache"
                                    class="relative inline-flex items-center justify-center h-8 rounded-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
                                    style="width: 4rem; height: 2rem; border-radius: 0.5rem; padding: 0; line-height: 2rem; vertical-align: middle; {{ $themeCacheEnabled ? 'background-color: rgb(22 163 74);' : 'background-color: rgb(220 38 38);' }}"
                                >
                                    <span class="text-xs font-semibold text-white">
                                        {{ $themeCacheEnabled ? 'ON' : 'OFF' }}
                                    </span>
                                </button>
                                <x-filament::button
                                    wire:click="refreshThemeCache"
                                    color="warning"
                                    icon="heroicon-o-arrow-path"
                                    size="sm"
                                    style="padding: 9px; margin-top: 24px;"
                                >
                                    Refresh
                                </x-filament::button>
                            </div>
                        </div>
                    </x-slot>

                    <x-slot name="right">
                        <!-- SASS Cache -->
                        <div class="flex flex-col p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                            <div class="mb-4">
                                <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-1">SASS</h4>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Enable or disable SASS/asset compilation cache</p>
                            </div>
                            <div class="flex items-start gap-4">
                                <button
                                    type="button"
                                    wire:click="toggleSassCache"
                                    class="relative inline-flex items-center justify-center h-8 rounded-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
                                    style="width: 4rem; height: 2rem; border-radius: 0.5rem; margin-top: 0px; padding: 0; line-height: 2rem; {{ $sassCacheEnabled ? 'background-color: rgb(22 163 74);' : 'background-color: rgb(220 38 38);' }}"
                                >
                                    <span class="text-xs font-semibold text-white">
                                        {{ $sassCacheEnabled ? 'ON' : 'OFF' }}
                                    </span>
                                </button>
                                <x-filament::button
                                    wire:click="refreshSassCache"
                                    color="warning"
                                    icon="heroicon-o-arrow-path"
                                    size="sm"
                                    style="padding: 9px; margin-top: 24px;"
                                >
                                    Refresh
                                </x-filament::button>
                            </div>
                        </div>
                    </x-slot>
                </x-section-three-parts-split-content>
            </x-slot>

            <x-slot name="info">
                <p class="text-sm mb-4">
                    <strong class="text-gray-900 dark:text-white">Theme Cache : </strong><span style="color: rgb(156 163 175);" class="dark:!text-gray-500">Controls whether theme tokens and styling are cached for better performance.</span>
                </p>
                <br>
                <p class="text-sm">
                    <strong class="text-gray-900 dark:text-white">SASS : </strong><span style="color: rgb(156 163 175);" class="dark:!text-gray-500">Controls whether SASS/SCSS files are compiled and cached. Refresh rebuilds all assets.</span>
                </p>
            </x-slot>
        </x-section-three-parts>

        <!-- Modifications -->
        <x-section-three-parts
            heading="Modifications"
            description="Clear or rebuild optimized/compiled files (config, routes, views)"
        >
            <x-slot name="actions">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-filament::button
                        wire:click="clearModificationsCache"
                        color="warning"
                        icon="heroicon-o-arrow-path"
                        class="w-full"
                    >
                        Clear Modifications
                    </x-filament::button>

                    <x-filament::button
                        wire:click="resetModificationsCache"
                        color="success"
                        icon="heroicon-o-arrow-path"
                        class="w-full"
                    >
                        Reset Modifications
                    </x-filament::button>
                </div>
            </x-slot>

            <x-slot name="info">
                <p class="text-sm mb-4">
                    <strong class="text-gray-900 dark:text-white">Clear Modifications : </strong><span style="color: rgb(156 163 175);" class="dark:!text-gray-500">Clears optimized/compiled files: config, routes, and views cache.</span>
                </p>
                <br>
                <p class="text-sm">
                    <strong class="text-gray-900 dark:text-white">Reset Modifications : </strong><span style="color: rgb(156 163 175);" class="dark:!text-gray-500">Rebuilds optimized/compiled files: config, routes, and views cache for better performance.</span>
                </p>
            </x-slot>
        </x-section-three-parts>

        <!-- Cache Actions -->
        <x-section-three-parts
            heading="Cache Actions"
            description="Clear specific cache types or all cache"
        >
            <x-slot name="actions">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
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

                    <x-filament::button
                        wire:click="resetAllCache"
                        color="success"
                        icon="heroicon-o-arrow-path"
                        class="w-full"
                    >
                        Reset All Cache
                    </x-filament::button>
                </div>
            </x-slot>

            <x-slot name="info">
                <p class="text-sm mb-4">
                    <strong class="text-gray-900 dark:text-white">Clear Layout Cache : </strong><span style="color: rgb(156 163 175);" class="dark:!text-gray-500">Clears cached layout templates and structure data.</span>
                </p>
                <br>
                <p class="text-sm mb-4">
                    <strong class="text-gray-900 dark:text-white">Clear Page Cache : </strong><span style="color: rgb(156 163 175);" class="dark:!text-gray-500">Clears cached page content and rendered HTML.</span>
                </p>
                <br>
                <p class="text-sm mb-4">
                    <strong class="text-gray-900 dark:text-white">Clear Module Cache : </strong><span style="color: rgb(156 163 175);" class="dark:!text-gray-500">Clears cached module data and configurations.</span>
                </p>
                <br>
                <p class="text-sm mb-4">
                    <strong class="text-gray-900 dark:text-white">Clear All Cache : </strong><span style="color: rgb(156 163 175);" class="dark:!text-gray-500">Clears all cache types: application, config, view, and route cache.</span>
                </p>
                <br>
                <p class="text-sm">
                    <strong class="text-gray-900 dark:text-white">Reset All Cache : </strong><span style="color: rgb(156 163 175);" class="dark:!text-gray-500">Clears all cache types and rebuilds optimized files for optimal performance. Recommended for production deployments.</span>
                </p>
            </x-slot>
        </x-section-three-parts>

        <!-- Warning -->
        <x-section-header 
            heading="Warning"
            description="Clearing cache will temporarily slow down the site as pages need to be regenerated. Use with caution in production."
        />
</x-filament-panels::page>
