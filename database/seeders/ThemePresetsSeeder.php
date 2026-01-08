<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Domain\Themes\Models\ThemePreset;
use Illuminate\Database\Seeder;

class ThemePresetsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $presets = [
            [
                'slug' => 'cafe',
                'name' => 'Cafe',
                'tokens' => [
                    'colors' => [
                        'primary' => '#D97706',
                        'secondary' => '#F59E0B',
                        'accent' => '#FCD34D',
                        'background' => '#FFFFFF',
                        'text' => '#1F2937',
                    ],
                    'fonts' => [
                        'heading' => ['family' => 'Outfit', 'weight' => '700'],
                        'body' => ['family' => 'Outfit', 'weight' => '400'],
                    ],
                    'spacing' => [
                        'section' => '4rem',
                        'gap' => '2rem',
                    ],
                    'radius' => [
                        'small' => '0.5rem',
                        'medium' => '1rem',
                        'large' => '1.5rem',
                    ],
                ],
                'default_modules' => [],
                'default_header_variant' => 'minimal',
                'default_footer_variant' => 'simple',
                'is_active' => true,
            ],
            [
                'slug' => 'restaurant',
                'name' => 'Restaurant',
                'tokens' => [
                    'colors' => [
                        'primary' => '#991B1B',
                        'secondary' => '#DC2626',
                        'accent' => '#FCA5A5',
                        'background' => '#FFFFFF',
                        'text' => '#1F2937',
                    ],
                    'fonts' => [
                        'heading' => ['family' => 'Outfit', 'weight' => '700'],
                        'body' => ['family' => 'Outfit', 'weight' => '400'],
                    ],
                    'spacing' => [
                        'section' => '4rem',
                        'gap' => '2rem',
                    ],
                    'radius' => [
                        'small' => '0.5rem',
                        'medium' => '1rem',
                        'large' => '1.5rem',
                    ],
                ],
                'default_modules' => [],
                'default_header_variant' => 'with-topbar',
                'default_footer_variant' => 'extended',
                'is_active' => true,
            ],
            [
                'slug' => 'retail',
                'name' => 'Retail',
                'tokens' => [
                    'colors' => [
                        'primary' => '#1E40AF',
                        'secondary' => '#3B82F6',
                        'accent' => '#93C5FD',
                        'background' => '#FFFFFF',
                        'text' => '#1F2937',
                    ],
                    'fonts' => [
                        'heading' => ['family' => 'Outfit', 'weight' => '700'],
                        'body' => ['family' => 'Outfit', 'weight' => '400'],
                    ],
                    'spacing' => [
                        'section' => '4rem',
                        'gap' => '2rem',
                    ],
                    'radius' => [
                        'small' => '0.5rem',
                        'medium' => '1rem',
                        'large' => '1.5rem',
                    ],
                ],
                'default_modules' => [],
                'default_header_variant' => 'centered',
                'default_footer_variant' => 'business-info',
                'is_active' => true,
            ],
        ];

        foreach ($presets as $preset) {
            ThemePreset::updateOrCreate(
                ['slug' => $preset['slug']],
                $preset
            );
        }

        $this->command->info('Theme presets seeded successfully.');
    }
}
