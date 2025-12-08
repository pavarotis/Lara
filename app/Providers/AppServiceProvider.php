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
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register response macros
        $this->registerResponseMacros();
    }

    /**
     * Register custom response macros
     */
    protected function registerResponseMacros(): void
    {
        // Success response macro
        \Illuminate\Support\Facades\Response::macro('success', function ($data = null, string $message = 'Operation successful', int $status = 200) {
            return response()->json([
                'success' => true,
                'data' => $data,
                'message' => $message,
            ], $status);
        });

        // Error response macro
        \Illuminate\Support\Facades\Response::macro('error', function (string $message, array $errors = [], int $status = 400) {
            return response()->json([
                'success' => false,
                'message' => $message,
                'errors' => $errors,
            ], $status);
        });

        // Paginated response macro
        \Illuminate\Support\Facades\Response::macro('paginated', function ($paginator, string $message = 'Data retrieved successfully') {
            return response()->json([
                'success' => true,
                'data' => $paginator->items(),
                'message' => $message,
                'meta' => [
                    'current_page' => $paginator->currentPage(),
                    'per_page' => $paginator->perPage(),
                    'total' => $paginator->total(),
                    'last_page' => $paginator->lastPage(),
                ],
                'links' => [
                    'first' => $paginator->url(1),
                    'last' => $paginator->url($paginator->lastPage()),
                    'prev' => $paginator->previousPageUrl(),
                    'next' => $paginator->nextPageUrl(),
                ],
            ]);
        });
    }
}
