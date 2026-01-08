<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Domain\Businesses\Models\Business;
use App\Domain\Seo\Services\GetSitemapService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function __construct(
        private GetSitemapService $getSitemapService
    ) {}

    /**
     * Generate sitemap.xml for current business
     */
    public function index(Request $request): Response
    {
        $business = $request->attributes->get('business') ?? Business::active()->first();

        if (! $business) {
            abort(404, 'Business not found');
        }

        $xml = $this->getSitemapService->generate($business);

        return response($xml, 200)
            ->header('Content-Type', 'application/xml');
    }
}
