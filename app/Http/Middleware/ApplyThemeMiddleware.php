<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Domain\Themes\Services\ApplyThemeTokensService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApplyThemeMiddleware
{
    public function __construct(
        private ApplyThemeTokensService $applyThemeTokensService
    ) {}

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get business from request attributes (set by SetCurrentBusiness middleware)
        $business = $request->attributes->get('business');

        // If no business in attributes, try to get from session (for admin routes)
        if (! $business) {
            $businessId = session('current_business_id');
            if ($businessId) {
                $business = \App\Domain\Businesses\Models\Business::find($businessId);
            }
        }

        // If still no business, try to get active business (fallback)
        if (! $business) {
            $business = \App\Domain\Businesses\Models\Business::active()->first();
        }

        if ($business) {
            $this->applyThemeTokensService->apply($business);
        }

        return $next($request);
    }
}
