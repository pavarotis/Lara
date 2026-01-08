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

    public function mount(): void
    {
        //
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
