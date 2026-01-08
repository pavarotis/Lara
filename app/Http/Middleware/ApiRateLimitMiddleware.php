<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Domain\Api\Services\ApiRateLimitService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiRateLimitMiddleware
{
    public function __construct(
        private ApiRateLimitService $apiRateLimitService
    ) {}

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $business = $request->attributes->get('business');

        if (! $business) {
            return response()->json([
                'success' => false,
                'message' => 'Business not found',
                'errors' => [],
                'data' => null,
            ], 403);
        }

        $endpoint = $request->path();

        if (! $this->apiRateLimitService->check($business, $endpoint)) {
            $remaining = $this->apiRateLimitService->getRemaining($business, $endpoint);

            return response()->json([
                'success' => false,
                'message' => 'Rate limit exceeded',
                'errors' => [],
                'data' => null,
            ], 429)->header('X-RateLimit-Remaining', (string) $remaining);
        }

        $remaining = $this->apiRateLimitService->getRemaining($business, $endpoint);

        $response = $next($request);

        return $response->header('X-RateLimit-Remaining', (string) $remaining);
    }
}
