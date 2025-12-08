<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Domain\Auth\Services\CheckPermissionService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    public function __construct(
        private CheckPermissionService $permissionService
    ) {}

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        if (! $request->user()) {
            abort(401, 'Unauthenticated');
        }

        if (! $this->permissionService->execute($request->user(), $permission)) {
            abort(403, 'Insufficient permissions');
        }

        return $next($request);
    }
}
