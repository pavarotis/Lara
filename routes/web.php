<?php

use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\ContentController as AdminContentController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MediaController as AdminMediaController;
use App\Http\Controllers\Admin\MediaFolderController as AdminMediaFolderController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ContentController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RobotsController;
use App\Http\Controllers\SitemapController;
use App\Support\RouteHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Static Pages (migrated to CMS)
// Note: These routes are now handled by ContentController@show via dynamic route /{slug}
// Run: php artisan cms:migrate-static-pages to migrate content
// Old static views can be deleted after migration

// Public Menu Routes
Route::get('/menu', [MenuController::class, 'show'])->name('menu');
Route::get('/menu/{slug}', [CategoryController::class, 'show'])->name('category.show');

// Cart Routes
Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/add', [CartController::class, 'add'])->name('add');
    Route::post('/update', [CartController::class, 'update'])->name('update');
    Route::post('/remove', [CartController::class, 'remove'])->name('remove');
    Route::post('/clear', [CartController::class, 'clear'])->name('clear');
    Route::get('/data', [CartController::class, 'data'])->name('data');
});

// Checkout Routes
Route::get('/checkout', [CheckoutController::class, 'show'])->name('checkout');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
Route::get('/checkout/success/{orderNumber}', [CheckoutController::class, 'success'])->name('checkout.success');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// SEO Routes
Route::get('/sitemap.xml', [SitemapController::class, 'index'])
    ->middleware(['business'])
    ->name('sitemap.index');
Route::get('/robots.txt', [RobotsController::class, 'index'])->name('robots.index');

// Admin API Documentation & Testing
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/api-docs', [\App\Http\Controllers\Admin\ApiDocsController::class, 'index'])->name('admin.api-docs');
    Route::get('/testing', [\App\Http\Controllers\Admin\TestingController::class, 'index'])->name('admin.testing');
});

// Content Preview Route (Admin only)
Route::get('/preview/{contentId}', [ContentController::class, 'preview'])
    ->middleware(['auth'])
    ->name('content.preview');

// Load auth routes FIRST to ensure /login, /register, etc. work before catch-all routes
require __DIR__.'/auth.php';

// Canonical Business Routes (Sprint 6)
// Format: /{business:slug}/{page:slug?}
// Uses RouteHelper to ensure business slugs don't conflict with system routes
Route::prefix('{business:slug}')->group(function () {
    // Business home page
    Route::get('/', [ContentController::class, 'showBusinessHome'])
        ->where('business', RouteHelper::businessSlugPattern())
        ->name('business.home');

    // Content pages
    Route::get('/{page:slug}', [ContentController::class, 'show'])
        ->where('business', RouteHelper::businessSlugPattern())
        ->where('page', '[a-z0-9-/]+')
        ->name('content.show');
})->middleware(['business']);

// Fallback: Legacy routes (for backward compatibility)
Route::get('/', function (Request $request) {
    $business = app(\App\Domain\Businesses\Services\ResolveBusinessService::class)->resolve($request);

    // If no business found via route/query/session, try first active business
    if (! $business) {
        $business = \App\Domain\Businesses\Models\Business::active()->first();
    }

    if ($business) {
        return redirect()->route('business.home', ['business' => $business->slug]);
    }

    abort(404, 'Business not found. Please create an active business in the admin panel.');
})->name('home');

// Legacy dynamic content route (fallback)
// Uses RouteHelper to exclude system routes automatically
Route::get('/{slug}', [ContentController::class, 'showLegacy'])
    ->where('slug', RouteHelper::exclusionPattern())
    ->name('content.show.legacy');

// Admin Routes (Blade - Custom Pages)
// Note: Filament handles /admin for main dashboard and resources
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Removed /admin/ route - Filament dashboard handles this
    // Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('products', AdminProductController::class);
    Route::resource('categories', AdminCategoryController::class);
    Route::resource('content', AdminContentController::class);
    Route::post('content/{content}/publish', [AdminContentController::class, 'publish'])->name('content.publish');
    Route::prefix('content/{content}/revisions')->name('content.revisions.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\ContentRevisionController::class, 'index'])->name('index');
        Route::get('/{revision}', [\App\Http\Controllers\Admin\ContentRevisionController::class, 'show'])->name('show');
        Route::post('/{revision}/restore', [\App\Http\Controllers\Admin\ContentRevisionController::class, 'restore'])->name('restore');
        Route::get('/compare/{a}/{b}', [\App\Http\Controllers\Admin\ContentRevisionController::class, 'compare'])->name('compare')->where('b', 'latest|\d+');
    });
    Route::prefix('content/{content}/modules')->name('content.modules.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\ContentModuleController::class, 'index'])->name('index');
        Route::post('/add', [\App\Http\Controllers\Admin\ContentModuleController::class, 'addModule'])->name('add');
        Route::post('/reorder', [\App\Http\Controllers\Admin\ContentModuleController::class, 'reorder'])->name('reorder');
        Route::post('/{assignment}/toggle', [\App\Http\Controllers\Admin\ContentModuleController::class, 'toggle'])->name('toggle');
        Route::delete('/{assignment}', [\App\Http\Controllers\Admin\ContentModuleController::class, 'remove'])->name('remove');
    });
    Route::resource('media', AdminMediaController::class);
    Route::prefix('media/folders')->name('media.folders.')->group(function () {
        Route::get('/', [AdminMediaFolderController::class, 'index'])->name('index');
        Route::post('/', [AdminMediaFolderController::class, 'store'])->name('store');
        Route::put('/{folder}', [AdminMediaFolderController::class, 'update'])->name('update');
        Route::delete('/{folder}', [AdminMediaFolderController::class, 'destroy'])->name('destroy');
    });
    Route::get('orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::patch('orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.updateStatus');
    Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('settings', [SettingsController::class, 'update'])->name('settings.update');
});

/**
 * Legacy compatibility route:
 * Some Filament views / code still expect route('filament.admin.pages.settings').
 * We alias that route name to a redirect towards the Filament System Settings page
 * (admin/system-settings, route name filament.admin.pages.system-settings).
 */
Route::get('/admin/system-settings-legacy', function () {
    return redirect()->route('filament.admin.pages.system-settings');
})->middleware(['auth', 'admin'])->name('filament.admin.pages.settings');

/**
 * Legacy compatibility route for Filament Blog Categories page.
 * Some code still references route('filament.admin.pages.categories'), but the
 * Filament page now lives at /admin/blog-categories with route name
 * filament.admin.pages.blog-categories. We alias the old name to a redirect.
 */
Route::get('/admin/blog-categories-legacy', function () {
    return redirect()->route('filament.admin.pages.blog-categories');
})->middleware(['auth', 'admin'])->name('filament.admin.pages.categories');

/**
 * Legacy compatibility route for Filament Catalog Products page.
 * Some code still references route('filament.admin.pages.products'), but the
 * Filament page now lives at /admin/catalog-products with route name
 * filament.admin.pages.catalog-products. We alias the old name to a redirect.
 */
Route::get('/admin/catalog-products-legacy', function () {
    return redirect()->route('filament.admin.pages.catalog-products');
})->middleware(['auth', 'admin'])->name('filament.admin.pages.products');

/**
 * Legacy compatibility route for Filament System Users page.
 * Some code still references route('filament.admin.pages.users'), but the
 * Filament page now lives at /admin/system-users with route name
 * filament.admin.pages.system-users. We alias the old name to a redirect.
 */
Route::get('/admin/system-users-legacy', function () {
    return redirect()->route('filament.admin.pages.system-users');
})->middleware(['auth', 'admin'])->name('filament.admin.pages.users');
