<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class ApiDocsController extends Controller
{
    /**
     * Display API documentation
     */
    public function index(): View
    {
        $endpoints = [
            'business' => [
                'method' => 'GET',
                'path' => '/api/v2/business',
                'description' => 'Get business information',
                'auth' => 'API Key + Secret (X-API-Key, X-API-Secret headers)',
                'rate_limit' => '100 requests/minute',
                'scope' => 'read:business',
                'response' => [
                    'success' => true,
                    'message' => 'Business retrieved successfully',
                    'data' => [
                        'id' => 1,
                        'name' => 'Demo Cafe',
                        'slug' => 'demo-cafe',
                        'type' => 'cafe',
                        'logo' => 'https://example.com/logo.png',
                        'settings' => [],
                        'is_active' => true,
                    ],
                ],
            ],
            'menu' => [
                'method' => 'GET',
                'path' => '/api/v2/menu',
                'description' => 'Get menu structure (categories with products)',
                'auth' => 'API Key + Secret',
                'rate_limit' => '100 requests/minute',
                'scope' => 'read:menu',
                'response' => [
                    'success' => true,
                    'message' => 'Menu retrieved successfully',
                    'data' => [
                        'data' => [
                            [
                                'id' => 1,
                                'name' => 'Beverages',
                                'slug' => 'beverages',
                                'products' => [],
                            ],
                        ],
                    ],
                ],
            ],
            'categories' => [
                'method' => 'GET',
                'path' => '/api/v2/categories',
                'description' => 'List all categories',
                'auth' => 'API Key + Secret',
                'rate_limit' => '100 requests/minute',
                'scope' => 'read:categories',
            ],
            'category' => [
                'method' => 'GET',
                'path' => '/api/v2/categories/{id}',
                'description' => 'Get single category by ID or slug',
                'auth' => 'API Key + Secret',
                'rate_limit' => '100 requests/minute',
                'scope' => 'read:categories',
            ],
            'products' => [
                'method' => 'GET',
                'path' => '/api/v2/products',
                'description' => 'List all products',
                'auth' => 'API Key + Secret',
                'rate_limit' => '100 requests/minute',
                'scope' => 'read:products',
            ],
            'product' => [
                'method' => 'GET',
                'path' => '/api/v2/products/{id}',
                'description' => 'Get single product by ID or slug',
                'auth' => 'API Key + Secret',
                'rate_limit' => '100 requests/minute',
                'scope' => 'read:products',
            ],
            'pages' => [
                'method' => 'GET',
                'path' => '/api/v2/pages',
                'description' => 'List all published pages',
                'auth' => 'API Key + Secret',
                'rate_limit' => '100 requests/minute',
                'scope' => 'read:pages',
            ],
            'page' => [
                'method' => 'GET',
                'path' => '/api/v2/pages/{slug}',
                'description' => 'Get single page by slug',
                'auth' => 'API Key + Secret',
                'rate_limit' => '100 requests/minute',
                'scope' => 'read:pages',
            ],
        ];

        return view('admin.api-docs', compact('endpoints'));
    }
}
