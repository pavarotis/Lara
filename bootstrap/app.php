<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'business' => \App\Http\Middleware\SetCurrentBusiness::class,
            'permission' => \App\Http\Middleware\CheckPermission::class,
            'api.auth' => \App\Http\Middleware\ApiAuthMiddleware::class,
            'api.rate_limit' => \App\Http\Middleware\ApiRateLimitMiddleware::class,
        ]);

        // Apply theme tokens to public routes (after business is set)
        // Note: This runs after SetCurrentBusiness middleware (which is applied via route middleware)
        $middleware->web(append: [
            \App\Http\Middleware\ApplyThemeMiddleware::class,
        ]);

        // Handle URL redirects (before caching)
        $middleware->web(append: [
            \App\Http\Middleware\HandleRedirects::class,
        ]);

        // Cache public pages (after theme middleware and redirects)
        $middleware->web(append: [
            \App\Http\Middleware\CachePublicPages::class,
        ]);

        // API middleware groups
        $middleware->api(append: [
            \Illuminate\Routing\Middleware\ThrottleRequests::class.':api',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // API Error Handling - Standardized response format
        // All API errors follow: { "success": false, "message": "...", "errors": {}, "data": null }
        //
        // Error Codes Documentation:
        // 400 - Bad Request: Invalid request parameters
        // 401 - Unauthenticated: Authentication required
        // 403 - Forbidden: Insufficient permissions
        // 404 - Not Found: Resource not found
        // 405 - Method Not Allowed: HTTP method not allowed for this endpoint
        // 422 - Validation Error: Request validation failed (errors array contains field-specific errors)
        // 429 - Too Many Requests: Rate limit exceeded
        // 500 - Internal Server Error: Server error (details only in debug mode)

        // Validation errors
        $exceptions->render(function (\Illuminate\Validation\ValidationException $e, $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors(),
                    'data' => null,
                ], 422);
            }
        });

        // Authentication errors
        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated',
                    'errors' => [],
                    'data' => null,
                ], 401);
            }
        });

        // Authorization errors
        $exceptions->render(function (\Illuminate\Auth\Access\AuthorizationException $e, $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage() ?: 'Forbidden',
                    'errors' => [],
                    'data' => null,
                ], 403);
            }
        });

        // Not found errors
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Resource not found',
                    'errors' => [],
                    'data' => null,
                ], 404);
            }
        });

        // Method not allowed
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException $e, $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Method not allowed',
                    'errors' => [],
                    'data' => null,
                ], 405);
            }
        });

        // Rate limiting
        $exceptions->render(function (\Illuminate\Http\Exceptions\ThrottleRequestsException $e, $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Too many requests. Please try again later.',
                    'errors' => [],
                    'data' => null,
                ], 429);
            }
        });

        // General exceptions (only for API routes, hide details in production)
        $exceptions->render(function (\Throwable $e, $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                $message = config('app.debug') ? $e->getMessage() : 'An error occurred';

                return response()->json([
                    'success' => false,
                    'message' => $message,
                    'errors' => config('app.debug') ? ['exception' => get_class($e)] : [],
                    'data' => null,
                ], 500);
            }
        });
    })->create();
