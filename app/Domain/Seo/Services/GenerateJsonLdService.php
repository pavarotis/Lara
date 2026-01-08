<?php

declare(strict_types=1);

namespace App\Domain\Seo\Services;

use App\Domain\Businesses\Models\Business;

class GenerateJsonLdService
{
    /**
     * Generate JSON-LD structured data for a business
     */
    public function forBusiness(Business $business): array
    {
        $settings = $business->settings ?? [];

        $jsonLd = [
            '@context' => 'https://schema.org',
            '@type' => 'LocalBusiness',
            'name' => $business->name,
            'image' => $business->logo ? asset($business->logo) : null,
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => $settings['address'] ?? '',
                'addressLocality' => $settings['city'] ?? '',
                'addressRegion' => $settings['region'] ?? '',
                'postalCode' => $settings['postal_code'] ?? '',
                'addressCountry' => $settings['country'] ?? 'GR',
            ],
            'telephone' => $settings['phone'] ?? '',
            'url' => route('content.show', ['slug' => '/'], false),
        ];

        // Add opening hours if available
        if (isset($settings['opening_hours'])) {
            $jsonLd['openingHoursSpecification'] = $this->formatOpeningHours($settings['opening_hours']);
        }

        // Remove null values
        return array_filter($jsonLd, fn ($value) => $value !== null);
    }

    /**
     * Format opening hours for JSON-LD
     */
    private function formatOpeningHours(array $openingHours): array
    {
        $formatted = [];

        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

        foreach ($days as $index => $day) {
            if (isset($openingHours[$index]) && $openingHours[$index]['open']) {
                $formatted[] = [
                    '@type' => 'OpeningHoursSpecification',
                    'dayOfWeek' => $day,
                    'opens' => $openingHours[$index]['opens'] ?? '09:00',
                    'closes' => $openingHours[$index]['closes'] ?? '17:00',
                ];
            }
        }

        return $formatted;
    }
}
