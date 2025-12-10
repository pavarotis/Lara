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

// Content Preview Route (Admin only)
Route::get('/preview/{contentId}', [ContentController::class, 'preview'])
    ->middleware(['auth'])
    ->name('content.preview');

// Dynamic Content Routes (CMS)
// Note: This must be after all static routes to avoid conflicts
// Route priority: static routes first, then dynamic content
// Handle root URL (/) explicitly for home page (slug: '/')
Route::get('/', function () {
    return app(ContentController::class)->show('/');
})->name('home');
// Handle all other dynamic content routes
Route::get('/{slug}', [ContentController::class, 'show'])
    ->where('slug', '^(?!admin|api|cart|checkout|menu|dashboard|profile|login|register|password|email-verification|preview).*')
    ->name('content.show');

// Admin Routes (Blade - Custom Pages)
// Note: Filament handles /admin for main dashboard and resources
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Removed /admin/ route - Filament dashboard handles this
    // Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('products', AdminProductController::class);
    Route::resource('categories', AdminCategoryController::class);
    Route::resource('content', AdminContentController::class);
    Route::post('content/{content}/publish', [AdminContentController::class, 'publish'])->name('content.publish');
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

require __DIR__.'/auth.php';
