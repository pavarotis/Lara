<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Domain\Businesses\Models\Business;
use App\Domain\Variables\Models\Variable;
use Illuminate\Database\Seeder;

class VariablesSeeder extends Seeder
{
    /**
     * Seed default CMS variables for active business
     * This seeder populates the variables table with default values
     * (similar to Journal theme approach - values stored in database)
     */
    public function run(): void
    {
        $business = Business::active()->first();

        if (! $business) {
            $this->command->warn('No active business found. Skipping variables seeding.');

            return;
        }

        $this->command->info('Seeding default CMS variables...');

        $defaultVariables = $this->getDefaultVariables();

        foreach ($defaultVariables as $varData) {
            Variable::firstOrCreate(
                [
                    'business_id' => $business->id,
                    'key' => $varData['key'],
                ],
                [
                    'value' => $varData['value'],
                    'type' => $varData['type'],
                ]
            );
        }

        $this->command->info('Default CMS variables seeded successfully.');
    }

    /**
     * Get default variables (same defaults as in Variables.php)
     */
    protected function getDefaultVariables(): array
    {
        return [
            // Breakpoints
            ['key' => 'general.breakpoint.sm', 'value' => '640px', 'type' => 'string'],
            ['key' => 'general.breakpoint.md', 'value' => '768px', 'type' => 'string'],
            ['key' => 'general.breakpoint.lg', 'value' => '1024px', 'type' => 'string'],
            ['key' => 'general.breakpoint.xl', 'value' => '1280px', 'type' => 'string'],
            ['key' => 'general.breakpoint.2xl', 'value' => '1536px', 'type' => 'string'],

            // Colors
            ['key' => 'general.color.primary', 'value' => '#3b82f6', 'type' => 'string'],
            ['key' => 'general.color.secondary', 'value' => '#8b5cf6', 'type' => 'string'],
            ['key' => 'general.color.accent', 'value' => '#10b981', 'type' => 'string'],
            ['key' => 'general.color.success', 'value' => '#22c55e', 'type' => 'string'],
            ['key' => 'general.color.warning', 'value' => '#f59e0b', 'type' => 'string'],
            ['key' => 'general.color.danger', 'value' => '#ef4444', 'type' => 'string'],
            ['key' => 'general.color.info', 'value' => '#06b6d4', 'type' => 'string'],
            ['key' => 'general.color.background', 'value' => '#ffffff', 'type' => 'string'],
            ['key' => 'general.color.text', 'value' => '#1f2937', 'type' => 'string'],

            // Color Styles (JSON)
            [
                'key' => 'general.color_styles',
                'value' => json_encode([
                    [
                        'name' => 'Blue Minimal',
                        'color_primary' => '#3b82f6',
                        'color_secondary' => '#ffffff',
                        'color_accent' => '#10b981',
                        'color_success' => '#22c55e',
                        'color_warning' => '#f59e0b',
                        'color_danger' => '#ef4444',
                        'color_info' => '#06b6d4',
                        'color_background' => '#ffffff',
                        'color_text' => '#1f2937',
                    ],
                    [
                        'name' => 'Violet Minimal',
                        'color_primary' => '#8b5cf6',
                        'color_secondary' => '#ffffff',
                        'color_accent' => '#3b82f6',
                        'color_success' => '#22c55e',
                        'color_warning' => '#f59e0b',
                        'color_danger' => '#ef4444',
                        'color_info' => '#06b6d4',
                        'color_background' => '#ffffff',
                        'color_text' => '#1f2937',
                    ],
                    [
                        'name' => 'Green Minimal',
                        'color_primary' => '#10b981',
                        'color_secondary' => '#ffffff',
                        'color_accent' => '#3b82f6',
                        'color_success' => '#22c55e',
                        'color_warning' => '#f59e0b',
                        'color_danger' => '#ef4444',
                        'color_info' => '#06b6d4',
                        'color_background' => '#ffffff',
                        'color_text' => '#1f2937',
                    ],
                    [
                        'name' => 'Soft Red Minimal',
                        'color_primary' => '#ef4444',
                        'color_secondary' => '#ffffff',
                        'color_accent' => '#f59e0b',
                        'color_success' => '#22c55e',
                        'color_warning' => '#f59e0b',
                        'color_danger' => '#ef4444',
                        'color_info' => '#06b6d4',
                        'color_background' => '#ffffff',
                        'color_text' => '#1f2937',
                    ],
                    [
                        'name' => 'Aqua Minimal',
                        'color_primary' => '#06b6d4',
                        'color_secondary' => '#ffffff',
                        'color_accent' => '#3b82f6',
                        'color_success' => '#22c55e',
                        'color_warning' => '#f59e0b',
                        'color_danger' => '#ef4444',
                        'color_info' => '#06b6d4',
                        'color_background' => '#ffffff',
                        'color_text' => '#1f2937',
                    ],
                ]),
                'type' => 'json',
            ],

            // Shadow Presets (JSON)
            [
                'key' => 'general.shadow_presets',
                'value' => json_encode([
                    ['name' => 'Small Shadow', 'value' => '0 1px 2px 0 rgb(0 0 0 / 0.05)'],
                    ['name' => 'Medium Shadow', 'value' => '0 4px 6px -1px rgb(0 0 0 / 0.1)'],
                    ['name' => 'Large Shadow', 'value' => '0 10px 15px -3px rgb(0 0 0 / 0.1)'],
                    ['name' => 'Extra Large Shadow', 'value' => '0 20px 25px -5px rgb(0 0 0 / 0.1)'],
                    ['name' => 'Tiny Shadow', 'value' => '0 1px 1px 0 rgb(0 0 0 / 0.04)'],
                    ['name' => 'Small + Diffused', 'value' => '0 2px 4px -1px rgb(0 0 0 / 0.06)'],
                    ['name' => 'Medium + Soft', 'value' => '0 6px 10px -2px rgb(0 0 0 / 0.08)'],
                    ['name' => 'Medium Dark', 'value' => '0 5px 15px -3px rgb(0 0 0 / 0.15)'],
                    ['name' => 'Large + Elevated', 'value' => '0 15px 20px -4px rgb(0 0 0 / 0.12)'],
                    ['name' => 'Extra Large + Dramatic', 'value' => '0 25px 50px -12px rgb(0 0 0 / 0.25)'],
                    ['name' => 'Inset Shadow', 'value' => 'inset 0 2px 4px 0 rgb(0 0 0 / 0.06)'],
                    ['name' => 'Soft Glow', 'value' => '0 0 10px 0 rgb(0 0 0 / 0.08)'],
                    ['name' => 'Floating Shadow', 'value' => '0 12px 24px -6px rgb(0 0 0 / 0.18)'],
                    ['name' => 'Subtle Emphasis', 'value' => '0 1px 3px 0 rgb(0 0 0 / 0.05), 0 1px 2px 0 rgb(0 0 0 / 0.04)'],
                ]),
                'type' => 'json',
            ],

            // Border Radius (JSON)
            [
                'key' => 'general.border_radius',
                'value' => json_encode([
                    ['name' => 'Small', 'top_left' => 0.125, 'top_right' => 0.125, 'bottom_right' => 0.125, 'bottom_left' => 0.125, 'unit' => 'rem'],
                    ['name' => 'Medium', 'top_left' => 0.375, 'top_right' => 0.375, 'bottom_right' => 0.375, 'bottom_left' => 0.375, 'unit' => 'rem'],
                    ['name' => 'Large', 'top_left' => 0.5, 'top_right' => 0.5, 'bottom_right' => 0.5, 'bottom_left' => 0.5, 'unit' => 'rem'],
                    ['name' => 'Extra Large', 'top_left' => 0.75, 'top_right' => 0.75, 'bottom_right' => 0.75, 'bottom_left' => 0.75, 'unit' => 'rem'],
                    ['name' => 'Full (Circle)', 'top_left' => 9999, 'top_right' => 9999, 'bottom_right' => 9999, 'bottom_left' => 9999, 'unit' => 'px'],
                ]),
                'type' => 'json',
            ],

            // Spacing
            ['key' => 'general.spacing.xs', 'value' => '0.25rem', 'type' => 'string'],
            ['key' => 'general.spacing.sm', 'value' => '0.5rem', 'type' => 'string'],
            ['key' => 'general.spacing.md', 'value' => '1rem', 'type' => 'string'],
            ['key' => 'general.spacing.lg', 'value' => '1.5rem', 'type' => 'string'],
            ['key' => 'general.spacing.xl', 'value' => '2rem', 'type' => 'string'],
            ['key' => 'general.spacing.2xl', 'value' => '3rem', 'type' => 'string'],

            // Typography EN
            ['key' => 'typography.en.font.primary', 'value' => 'Inter', 'type' => 'string'],
            ['key' => 'typography.en.font.secondary', 'value' => 'Inter', 'type' => 'string'],
            ['key' => 'typography.en.font.heading', 'value' => 'Inter', 'type' => 'string'],
            ['key' => 'typography.en.font.body', 'value' => 'Inter', 'type' => 'string'],
            ['key' => 'typography.en.size.xs', 'value' => '0.75rem', 'type' => 'string'],
            ['key' => 'typography.en.size.sm', 'value' => '0.875rem', 'type' => 'string'],
            ['key' => 'typography.en.size.base', 'value' => '1rem', 'type' => 'string'],
            ['key' => 'typography.en.size.lg', 'value' => '1.125rem', 'type' => 'string'],
            ['key' => 'typography.en.size.xl', 'value' => '1.25rem', 'type' => 'string'],
            ['key' => 'typography.en.size.2xl', 'value' => '1.5rem', 'type' => 'string'],
            ['key' => 'typography.en.size.3xl', 'value' => '1.875rem', 'type' => 'string'],
            ['key' => 'typography.en.size.4xl', 'value' => '2.25rem', 'type' => 'string'],
            ['key' => 'typography.en.line_height.tight', 'value' => '1.25', 'type' => 'number'],
            ['key' => 'typography.en.line_height.normal', 'value' => '1.5', 'type' => 'number'],
            ['key' => 'typography.en.line_height.relaxed', 'value' => '1.75', 'type' => 'number'],

            // Typography GR
            ['key' => 'typography.gr.font.primary', 'value' => 'Inter', 'type' => 'string'],
            ['key' => 'typography.gr.font.secondary', 'value' => 'Inter', 'type' => 'string'],
            ['key' => 'typography.gr.font.heading', 'value' => 'Inter', 'type' => 'string'],
            ['key' => 'typography.gr.font.body', 'value' => 'Inter', 'type' => 'string'],
            ['key' => 'typography.gr.size.xs', 'value' => '0.75rem', 'type' => 'string'],
            ['key' => 'typography.gr.size.sm', 'value' => '0.875rem', 'type' => 'string'],
            ['key' => 'typography.gr.size.base', 'value' => '1rem', 'type' => 'string'],
            ['key' => 'typography.gr.size.lg', 'value' => '1.125rem', 'type' => 'string'],
            ['key' => 'typography.gr.size.xl', 'value' => '1.25rem', 'type' => 'string'],
            ['key' => 'typography.gr.size.2xl', 'value' => '1.5rem', 'type' => 'string'],
            ['key' => 'typography.gr.size.3xl', 'value' => '1.875rem', 'type' => 'string'],
            ['key' => 'typography.gr.size.4xl', 'value' => '2.25rem', 'type' => 'string'],
            ['key' => 'typography.gr.line_height.tight', 'value' => '1.25', 'type' => 'number'],
            ['key' => 'typography.gr.line_height.normal', 'value' => '1.5', 'type' => 'number'],
            ['key' => 'typography.gr.line_height.relaxed', 'value' => '1.75', 'type' => 'number'],

            // S.I. Classes (JSON)
            [
                'key' => 'si.length_classes',
                'value' => json_encode([
                    ['name' => 'Centimeter', 'unit' => 'cm', 'value' => 1.0],
                    ['name' => 'Millimeter', 'unit' => 'mm', 'value' => 0.1],
                    ['name' => 'Inch', 'unit' => 'in', 'value' => 0.393701],
                ]),
                'type' => 'json',
            ],
            [
                'key' => 'si.weight_classes',
                'value' => json_encode([
                    ['name' => 'Kilogram', 'unit' => 'kg', 'value' => 1.0],
                    ['name' => 'Gram', 'unit' => 'g', 'value' => 0.001],
                    ['name' => 'Pound', 'unit' => 'lb', 'value' => 0.453592],
                ]),
                'type' => 'json',
            ],

            // Legacy
            ['key' => 'legacy.items_per_row', 'value' => '4', 'type' => 'number'],
            ['key' => 'legacy.spacing.small', 'value' => '0.5rem', 'type' => 'string'],
            ['key' => 'legacy.spacing.medium', 'value' => '1rem', 'type' => 'string'],
            ['key' => 'legacy.spacing.large', 'value' => '2rem', 'type' => 'string'],
        ];
    }
}
