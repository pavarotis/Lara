<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register VariableService as singleton
        $this->app->singleton(\App\Domain\Variables\Services\VariableService::class);
        $this->app->singleton(\App\Domain\Variables\Services\ThemeService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Load helper file
        require_once base_path('app/Support/VariableHelper.php');
    }
}
