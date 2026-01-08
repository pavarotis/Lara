<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Domain\Businesses\Services\ResolveBusinessService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetCurrentBusiness
{
    public function __construct(
        private ResolveBusinessService $resolveBusinessService
    ) {}

    /**
     * Handle an incoming request.
     * Sets the current business based on route param, query param, or session.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $business = $this->resolveBusinessService->resolve($request);

        // Allow admin and API routes without business context
        if (! $business) {
            if ($request->is('admin/*') || $request->is('api/*')) {
                // Admin/API can work without business context
                return $next($request);
            }
            abort(404, 'Business not found');
        }

        // Validate business is active
        if (! $business->is_active) {
            abort(403, 'Business is inactive');
        }

        // Share business with all views
        view()->share('currentBusiness', $business);

        // Store in request for controllers
        $request->attributes->set('business', $business);

        // Store in session for persistence
        session(['current_business_id' => $business->id]);

        return $next($request);
    }
}
