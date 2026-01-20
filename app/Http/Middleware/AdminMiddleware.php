<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Support\PermissionHelper;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()) {
            abort(401, 'Unauthenticated');
        }

        // Use PermissionHelper for consistent admin checking
        if (! PermissionHelper::isAdmin($request->user())) {
            abort(403, 'Access denied. Admin privileges required.');
        }

        return $next($request);
    }
}
