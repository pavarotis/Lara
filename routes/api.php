<?php

use App\Http\Controllers\Api\V1\ContentController as ApiContentController;
use App\Http\Controllers\Api\V1\SettingsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| API routes for v2 CMS platform. All routes are versioned under /api/v1/
|
*/

Route::prefix('v1')->group(function () {
    // Public routes (no auth required)
    Route::get('/settings', [SettingsController::class, 'index'])->name('api.v1.settings.index');
    Route::get('/settings/{key}', [SettingsController::class, 'show'])->name('api.v1.settings.show');

    // Content API
    Route::prefix('businesses/{businessId}/content')->group(function () {
        Route::get('/', [ApiContentController::class, 'index'])->name('api.v1.content.index');
        Route::get('/{slug}', [ApiContentController::class, 'show'])->name('api.v1.content.show');
        Route::get('/type/{type}', [ApiContentController::class, 'byType'])->name('api.v1.content.byType');
    });

    // Protected routes (require authentication)
    // ⚠️ Note: Sanctum package needs to be installed for auth:sanctum to work
    // For now, using 'auth' middleware (session-based) until Sanctum is installed
    Route::middleware(['auth'])->group(function () {
        // Settings management (admin only)
        Route::post('/settings', [SettingsController::class, 'store'])->name('api.v1.settings.store');
        Route::put('/settings/{key}', [SettingsController::class, 'update'])->name('api.v1.settings.update');
        Route::delete('/settings/{key}', [SettingsController::class, 'destroy'])->name('api.v1.settings.destroy');
    });
});
