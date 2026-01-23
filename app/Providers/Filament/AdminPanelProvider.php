<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Assets\Css;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->darkMode()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->assets([
                Css::make('error-logs', resource_path('css/error-logs.css')),
                Css::make('fileupload-overrides', resource_path('css/filament-fileupload.css')),
                Css::make('backup-restore', resource_path('css/backup-restore.css')),
                Css::make('mail-campaign', resource_path('css/mail-campaign.css')),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->navigationGroups([
                NavigationGroup::make('CMS')
                    ->collapsible(true),
                NavigationGroup::make('Catalog')
                    ->collapsible(true),
                NavigationGroup::make('Catalog Spare')
                    ->collapsible(true),
                NavigationGroup::make('Extensions')
                    ->collapsible(true),
                NavigationGroup::make('Sales')
                    ->collapsible(true),
                NavigationGroup::make('Customers')
                    ->collapsible(true),
                NavigationGroup::make('Marketing')
                    ->collapsible(true),
                NavigationGroup::make('System')
                    ->collapsible(false),
                NavigationGroup::make('Reports')
                    ->collapsible(true),
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->authGuard('web')
            ->middleware([
                \App\Http\Middleware\AdminMiddleware::class,
            ], isPersistent: false);
    }
}
