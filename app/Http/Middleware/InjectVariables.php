<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Domain\Variables\Services\VariableService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

/**
 * InjectVariables Middleware
 *
 * Injects site configuration and variables into all views.
 * Makes variables accessible via $siteConfig and $variables in Blade templates.
 */
class InjectVariables
{
    protected VariableService $variableService;

    public function __construct(VariableService $variableService)
    {
        $this->variableService = $variableService;
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get site configuration
        $siteConfig = $this->variableService->getSiteConfig();
        $variables = $this->variableService->getAllVariables();

        // Share with all views
        View::share('siteConfig', $siteConfig);
        View::share('variables', $variables);

        return $next($request);
    }
}
