<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RobotsController extends Controller
{
    /**
     * Generate robots.txt
     */
    public function index(Request $request): Response
    {
        $content = "User-agent: *\n";
        $content .= "Allow: /\n";
        $content .= "\n";
        $content .= 'Sitemap: '.route('sitemap.index', absolute: true)."\n";

        return response($content, 200)
            ->header('Content-Type', 'text/plain');
    }
}
