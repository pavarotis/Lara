<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Domain\Api\Services\ApiAuthService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiAuthMiddleware
{
    public function __construct(
        private ApiAuthService $apiAuthService
    ) {}

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get API key and secret from headers
        $apiKey = $request->header('X-API-Key');
        $apiSecret = $request->header('X-API-Secret');

        if (! $apiKey || ! $apiSecret) {
            return response()->json([
                'success' => false,
                'message' => 'API key and secret required',
                'errors' => [],
                'data' => null,
            ], 401);
        }

        // Authenticate
        $authenticatedKey = $this->apiAuthService->authenticate($apiKey, $apiSecret);

        if (! $authenticatedKey) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid API key or secret',
                'errors' => [],
                'data' => null,
            ], 401);
        }

        // Set business from API key
        $business = $authenticatedKey->business;
        if (! $business || ! $business->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Business not found or inactive',
                'errors' => [],
                'data' => null,
            ], 403);
        }

        // Share with request
        $request->attributes->set('business', $business);
        $request->attributes->set('api_key', $authenticatedKey);

        return $next($request);
    }
}
