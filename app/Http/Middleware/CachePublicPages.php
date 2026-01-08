<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class CachePublicPages
{
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

        // Generate cache key
        $cacheKey = 'page:'.$request->path().':'.app()->getLocale();
        $ttl = config('cache.page_ttl', 3600); // 1 hour default

        // Try to get from cache
        $cached = Cache::get($cacheKey);

        if ($cached !== null && is_string($cached)) {
            $response = response($cached);
            $this->setCacheHeaders($response, $ttl);

            return $response;
        }

        // Generate response
        $response = $next($request);

        // Only cache successful responses with content
        if ($response->getStatusCode() === 200) {
            $content = $response->getContent();

            // Only cache if content is not empty and is a string
            if ($content !== false && is_string($content) && ! empty($content)) {
                try {
                    Cache::put($cacheKey, $content, $ttl);
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
}
