<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Domain\Businesses\Models\Business;
use App\Domain\Seo\Models\Redirect;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HandleRedirects
{
    /**
     * Handle an incoming request and check for redirects.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only handle GET requests and non-redirect responses
        if ($request->method() !== 'GET' || $response->isRedirection()) {
            return $response;
        }

        // Get current path (without query string)
        $path = $request->path();
        $fullPath = '/'.ltrim($path, '/');
        $queryString = $request->getQueryString();
        $fullUrl = $request->url().($queryString ? '?'.$queryString : '');

        // Try to find redirect
        $business = $request->attributes->get('business') ?? Business::active()->first();

        $redirect = Redirect::active()
            ->forBusiness($business?->id)
            ->where(function ($query) use ($path, $fullPath, $fullUrl) {
                $query->where('from_url', $path)
                    ->orWhere('from_url', $fullPath)
                    ->orWhere('from_url', '/'.$path)
                    ->orWhere('from_url', $fullUrl);
            })
            ->first();

        if ($redirect) {
            // Increment hits
            $redirect->incrementHits();

            // Perform redirect
            return redirect($redirect->to_url, (int) $redirect->type);
        }

        return $response;
    }
}
