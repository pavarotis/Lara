<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Domain\Businesses\Models\Business;
use App\Domain\Catalog\Services\GetMenuForBusinessService;
use Illuminate\View\View;

class MenuController extends Controller
{
    public function __construct(
        private GetMenuForBusinessService $menuService
    ) {}

    /**
     * Display the menu for the default/active business
     */
    public function show(): View
    {
        // Get the first active business (for now, single-business mode)
        $business = Business::active()->first();

        if (!$business) {
            abort(404, 'No active business found');
        }

        $categories = $this->menuService->execute($business);

        return view('menu', [
            'business' => $business,
            'categories' => $categories,
        ]);
    }
}

