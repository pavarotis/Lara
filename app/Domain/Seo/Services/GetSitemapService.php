<?php

declare(strict_types=1);

namespace App\Domain\Seo\Services;

use App\Domain\Businesses\Models\Business;
use App\Domain\Content\Models\Content;

class GetSitemapService
{
    /**
     * Generate sitemap XML for a business
     */
    public function generate(Business $business): string
    {
        $urls = [];

        // Home page
        $baseUrl = config('app.url');
        $urls[] = [
            'loc' => $baseUrl.'/',
            'lastmod' => now()->toIso8601String(),
            'priority' => '1.0',
            'changefreq' => 'daily',
        ];

        // Content pages
        $contents = Content::where('business_id', $business->id)
            ->where('status', 'published')
            ->whereNotNull('published_at')
            ->get();

        $baseUrl = config('app.url');
        foreach ($contents as $content) {
            $slug = $content->slug === '/' ? '' : '/'.$content->slug;
            $urls[] = [
                'loc' => $baseUrl.$slug,
                'lastmod' => $content->updated_at->toIso8601String(),
                'priority' => $content->slug === '/' ? '1.0' : '0.8',
                'changefreq' => 'weekly',
            ];
        }

        // Generate XML
        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";

        foreach ($urls as $url) {
            $xml .= '  <url>'."\n";
            $xml .= '    <loc>'.htmlspecialchars($url['loc']).'</loc>'."\n";
            $xml .= '    <lastmod>'.htmlspecialchars($url['lastmod']).'</lastmod>'."\n";
            $xml .= '    <priority>'.htmlspecialchars($url['priority']).'</priority>'."\n";
            $xml .= '    <changefreq>'.htmlspecialchars($url['changefreq']).'</changefreq>'."\n";
            $xml .= '  </url>'."\n";
        }

        $xml .= '</urlset>';

        return $xml;
    }
}
