<?php

declare(strict_types=1);

namespace App\Filament\Pages\CMS\Cache;

use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

class CacheManagement extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrow-path';

    protected string $view = 'filament.pages.cms.cache.cache-management';

    protected static ?string $navigationLabel = 'Cache Management';

    protected static ?int $navigationSort = 101;

    public bool $themeCacheEnabled = true;

    public bool $sassCacheEnabled = true;

    public function mount(): void
    {
        // Load current state from cache/settings
        $this->themeCacheEnabled = Cache::get('cache.theme.enabled', true);
        $this->sassCacheEnabled = Cache::get('cache.sass.enabled', true);
    }

    public function clearAllCache(): void
    {
        try {
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('view:clear');
            Artisan::call('route:clear');

            Notification::make()
                ->title('Cache Cleared')
                ->success()
                ->body('All cache has been cleared successfully.')
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Error')
                ->danger()
                ->body('Failed to clear cache: '.$e->getMessage())
                ->send();
        }
    }

    public function clearLayoutCache(): void
    {
        try {
            // Clear layout cache using tags if available
            Cache::tags(['layouts'])->flush();

            Notification::make()
                ->title('Layout Cache Cleared')
                ->success()
                ->body('Layout cache has been cleared successfully.')
                ->send();
        } catch (\Exception $e) {
            // Fallback: clear all cache if tags not supported
            Cache::flush();

            Notification::make()
                ->title('Cache Cleared')
                ->warning()
                ->body('All cache cleared (tags not supported).')
                ->send();
        }
    }

    public function clearPageCache(): void
    {
        try {
            // Clear page cache using tags if available
            Cache::tags(['pages'])->flush();

            Notification::make()
                ->title('Page Cache Cleared')
                ->success()
                ->body('Page cache has been cleared successfully.')
                ->send();
        } catch (\Exception $e) {
            // Fallback: clear all cache if tags not supported
            Cache::flush();

            Notification::make()
                ->title('Cache Cleared')
                ->warning()
                ->body('All cache cleared (tags not supported).')
                ->send();
        }
    }

    public function clearModuleCache(): void
    {
        try {
            // Clear module cache using tags if available
            Cache::tags(['modules'])->flush();

            Notification::make()
                ->title('Module Cache Cleared')
                ->success()
                ->body('Module cache has been cleared successfully.')
                ->send();
        } catch (\Exception $e) {
            // Fallback: clear all cache if tags not supported
            Cache::flush();

            Notification::make()
                ->title('Cache Cleared')
                ->warning()
                ->body('All cache cleared (tags not supported).')
                ->send();
        }
    }

    public function clearModificationsCache(): void
    {
        try {
            // Clear optimized/compiled files (modifications)
            Artisan::call('config:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');

            Notification::make()
                ->title('Modifications Cleared')
                ->success()
                ->body('All optimized files (config, routes, views) have been cleared successfully.')
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Error')
                ->danger()
                ->body('Failed to clear modifications: '.$e->getMessage())
                ->send();
        }
    }

    public function resetModificationsCache(): void
    {
        try {
            // Rebuild optimized/compiled files (modifications)
            Artisan::call('config:cache');
            Artisan::call('route:cache');
            Artisan::call('view:cache');

            Notification::make()
                ->title('Modifications Reset')
                ->success()
                ->body('All optimized files (config, routes, views) have been rebuilt successfully.')
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Error')
                ->danger()
                ->body('Failed to reset modifications: '.$e->getMessage())
                ->send();
        }
    }

    public function resetAllCache(): void
    {
        try {
            // Clear all cache types
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('view:clear');
            Artisan::call('route:clear');

            // Clear tagged cache (layout, page, module)
            Cache::tags(['layouts'])->flush();
            Cache::tags(['pages'])->flush();
            Cache::tags(['modules'])->flush();

            // Rebuild optimized files
            Artisan::call('config:cache');
            Artisan::call('route:cache');
            Artisan::call('view:cache');

            Notification::make()
                ->title('All Cache Reset')
                ->success()
                ->body('All cache has been cleared and optimized files have been rebuilt successfully.')
                ->send();
        } catch (\Exception $e) {
            // If tags not supported, fallback to flush all
            try {
                Cache::flush();
                Artisan::call('config:cache');
                Artisan::call('route:cache');
                Artisan::call('view:cache');

                Notification::make()
                    ->title('All Cache Reset')
                    ->success()
                    ->body('All cache has been cleared and optimized files have been rebuilt successfully.')
                    ->send();
            } catch (\Exception $e2) {
                Notification::make()
                    ->title('Error')
                    ->danger()
                    ->body('Failed to reset all cache: '.$e2->getMessage())
                    ->send();
            }
        }
    }

    public function toggleThemeCache(): void
    {
        $this->themeCacheEnabled = ! $this->themeCacheEnabled;
        Cache::forever('cache.theme.enabled', $this->themeCacheEnabled);

        Notification::make()
            ->title('Theme Cache '.($this->themeCacheEnabled ? 'Enabled' : 'Disabled'))
            ->success()
            ->send();
    }

    public function updatedThemeCacheEnabled($value): void
    {
        Cache::forever('cache.theme.enabled', $value);
    }

    public function refreshThemeCache(): void
    {
        try {
            // Clear theme-related cache
            Cache::tags(['themes', 'theme-tokens'])->flush();
            Cache::forget('theme:all');

            Notification::make()
                ->title('Theme Cache Refreshed')
                ->success()
                ->body('Theme cache has been cleared and will be regenerated.')
                ->send();
        } catch (\Exception $e) {
            // Fallback if tags not supported
            Cache::flush();

            Notification::make()
                ->title('Theme Cache Refreshed')
                ->warning()
                ->body('All cache cleared (tags not supported).')
                ->send();
        }
    }

    public function toggleSassCache(): void
    {
        $this->sassCacheEnabled = ! $this->sassCacheEnabled;
        Cache::forever('cache.sass.enabled', $this->sassCacheEnabled);

        Notification::make()
            ->title('SASS Cache '.($this->sassCacheEnabled ? 'Enabled' : 'Disabled'))
            ->success()
            ->send();
    }

    public function updatedSassCacheEnabled($value): void
    {
        Cache::forever('cache.sass.enabled', $value);
    }

    public function refreshSassCache(): void
    {
        try {
            $basePath = base_path();
            $nodePath = 'D:\\laragon\\bin\\nodejs\\node-v22';

            // Set PATH and run npm build
            $command = "cd {$basePath} && set PATH={$nodePath};%PATH% && npm run build";

            exec($command.' 2>&1', $output, $returnCode);

            if ($returnCode === 0) {
                Notification::make()
                    ->title('SASS/Assets Refreshed')
                    ->success()
                    ->body('Assets have been rebuilt successfully.')
                    ->send();
            } else {
                Notification::make()
                    ->title('Build Error')
                    ->warning()
                    ->body('npm build completed with warnings. Check console for details.')
                    ->send();
            }
        } catch (\Exception $e) {
            Notification::make()
                ->title('Error')
                ->danger()
                ->body('Failed to refresh SASS cache: '.$e->getMessage())
                ->send();
        }
    }

    public function getCacheInfo(): array
    {
        $driver = config('cache.default');
        $store = Cache::getStore();

        return [
            'driver' => $driver,
            'store' => get_class($store),
            'supports_tags' => method_exists($store, 'tags'),
        ];
    }
}
