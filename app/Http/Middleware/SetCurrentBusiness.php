<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Domain\Businesses\Models\Business;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetCurrentBusiness
{
    /**
     * Handle an incoming request.
     * Sets the current business based on subdomain, route param, or session.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $business = $this->resolveBusiness($request);

        if (! $business) {
            abort(404, 'Business not found');
        }

        // Share business with all views
        view()->share('currentBusiness', $business);

        // Store in request for controllers
        $request->attributes->set('business', $business);

        // Store in session for persistence
        session(['current_business_id' => $business->id]);

        return $next($request);
    }

    private function resolveBusiness(Request $request): ?Business
    {
        // 1. Check route parameter (e.g., /b/{business:slug}/menu)
        if ($slug = $request->route('business')) {
            return Business::where('slug', $slug)->active()->first();
        }

        // 2. Check query parameter (e.g., ?business=demo-cafe)
        if ($slug = $request->query('business')) {
            $business = Business::where('slug', $slug)->active()->first();
            if ($business) {
                return $business;
            }
        }

        // 3. Check session
        if ($businessId = session('current_business_id')) {
            $business = Business::find($businessId);
            if ($business && $business->is_active) {
                return $business;
            }
        }

        // 4. Fallback to first active business
        return Business::active()->first();
    }
}
