<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Domain\Businesses\Services\ResolveBusinessService;
use App\Support\CacheInvalidationService;
use App\Support\CacheMetricsService;
use Closure;
use Illuminate\Cache\TaggableStore;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class CachePublicPages
{
    public function __construct(
        private ResolveBusinessService $resolveBusinessService,
        private CacheInvalidationService $cacheInvalidationService,
        private CacheMetricsService $cacheMetricsService
    ) {}

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip cache for authenticated users
        if (Auth::check()) {
            return $next($request);
        }

        // Skip cache for non-GET requests
        if (! $request->isMethod('GET')) {
            return $next($request);
        }

        // Skip cache for admin/api routes
        if ($request->is('admin/*') || $request->is('api/*')) {
            return $next($request);
        }

        // Skip cache for auth routes (avoid stale CSRF tokens)
        $path = trim($request->path(), '/');
        foreach (config('routes.auth', []) as $authRoute) {
            if ($path === $authRoute || Str::startsWith($path, $authRoute.'/')) {
                return $next($request);
            }
        }

        $business = $this->resolveBusinessService->resolve($request);
        $businessId = $business?->id;

        // Generate cache key
        $cacheKey = 'page:'.$request->path().':'.app()->getLocale();
        $ttl = config('cache.page_ttl', 3600); // 1 hour default
        $cache = $this->cacheStore($businessId);

        // Try to get from cache
        $cached = $cache->get($cacheKey);

        if ($cached !== null && is_string($cached)) {
            $this->cacheMetricsService->increment('page.hit', $businessId);
            $response = response($cached);
            $this->setCacheHeaders($response, $ttl);

            return $response;
        }

        $this->cacheMetricsService->increment('page.miss', $businessId);

        // Generate response
        $response = $next($request);

        // Only cache successful responses with content
        if ($response->getStatusCode() === 200) {
            $content = $response->getContent();

            // Only cache if content is not empty and is a string
            if ($content !== false && is_string($content) && ! empty($content)) {
                try {
                    $cache->put($cacheKey, $content, $ttl);
                    $this->cacheInvalidationService->registerPageCacheKey($cacheKey, $businessId);
                    $this->setCacheHeaders($response, $ttl);
                } catch (\Exception $e) {
                    // If caching fails, just continue without caching
                    // Don't break the response
                }
            }
        }

        return $response;
    }

    /**
     * Set cache headers
     */
    private function setCacheHeaders(Response $response, int $ttl): void
    {
        try {
            $content = $response->getContent();
            if ($content !== false && is_string($content)) {
                $response->headers->set('Cache-Control', "public, max-age={$ttl}");
                $response->headers->set('ETag', md5($content));
            }
        } catch (\Exception $e) {
            // If setting headers fails, just continue
            // Don't break the response
        }
    }

    private function cacheStore(?int $businessId): Repository
    {
        if (Cache::getStore() instanceof TaggableStore) {
            $tags = ['pages'];
            if ($businessId !== null) {
                $tags[] = "business:{$businessId}";
            }

            return Cache::tags($tags);
        }

        return Cache::store();
    }
}
