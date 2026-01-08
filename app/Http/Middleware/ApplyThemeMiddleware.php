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
        $business = $request->attributes->get('business');
        if ($business) {
            $this->applyThemeTokensService->apply($business);
        }

        return $next($request);
    }
}
