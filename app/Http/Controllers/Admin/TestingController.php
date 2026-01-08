<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class TestingController extends Controller
{
    /**
     * Display testing dashboard
     */
    public function index(): View
    {
        // Mock test suite data (in real implementation, this would come from test runner)
        $testSuites = [
            'Feature Tests' => [
                'status' => 'passing',
                'count' => 45,
                'passed' => 45,
                'failed' => 0,
                'last_run' => now()->subMinutes(5),
            ],
            'Unit Tests' => [
                'status' => 'passing',
                'count' => 120,
                'passed' => 120,
                'failed' => 0,
                'last_run' => now()->subMinutes(5),
            ],
            'Integration Tests' => [
                'status' => 'passing',
                'count' => 30,
                'passed' => 30,
                'failed' => 0,
                'last_run' => now()->subMinutes(5),
            ],
        ];

        return view('admin.testing', compact('testSuites'));
    }
}
