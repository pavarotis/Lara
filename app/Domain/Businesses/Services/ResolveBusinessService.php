<?php

declare(strict_types=1);

namespace App\Domain\Businesses\Services;

use App\Domain\Businesses\Models\Business;
use Illuminate\Http\Request;

class ResolveBusinessService
{
    /**
     * Resolve business from request (route → query → session)
     */
    public function resolve(Request $request): ?Business
    {
        // 1. Route parameter (canonical) - e.g., /{business:slug}/page
        if ($slug = $request->route('business')) {
            $business = Business::where('slug', $slug)->active()->first();
            if ($business) {
                return $business;
            }

            // Don't abort here - let middleware handle 404
            return null;
        }

        // 2. Query parameter (fallback) - e.g., ?business=demo-cafe
        if ($slug = $request->query('business')) {
            $business = Business::where('slug', $slug)->active()->first();
            if ($business) {
                return $business;
            }
        }

        // 3. Session (fallback)
        if ($businessId = session('current_business_id')) {
            $business = Business::find($businessId);
            if ($business && $business->is_active) {
                return $business;
            }
        }

        // 4. No fallback to first business (security - only for admin routes)
        return null;
    }
}
