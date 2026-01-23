<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Domain\Businesses\Models\Business;
use App\Domain\Variables\Models\Variable;
use Illuminate\Database\Seeder;

/**
 * CompleteVariablesSeeder
 *
 * Comprehensive seeder that creates variables for ALL admin panel categories.
 * This makes every setting manageable through Dynamic Settings page.
 */
class CompleteVariablesSeeder extends Seeder
{
    public function run(): void
    {
        $business = Business::active()->first();

        if (! $business) {
            $this->command->warn('No active business found. Skipping complete variables seeding.');

            return;
        }

        $this->command->info('Seeding complete variables for all admin categories...');

        $allVariables = array_merge(
            $this->getDashboardVariables(),
            $this->getCacheManagementVariables(),
            $this->getCmsBlogVariables(),
            $this->getCmsVariables(),
            $this->getCatalogVariables(),
            $this->getCatalogSpareVariables(),
            $this->getExtensionsVariables(),
            $this->getSalesVariables(),
            $this->getCustomersVariables(),
            $this->getMarketingVariables(),
            $this->getSystemVariables(),
            $this->getReportsVariables()
        );

        foreach ($allVariables as $varData) {
            Variable::firstOrCreate(
                [
                    'business_id' => $business->id,
                    'key' => $varData['key'],
                ],
                [
                    'value' => $varData['value'],
                    'type' => $varData['type'],
                    'category' => $varData['category'],
                    'description' => $varData['description'],
                ]
            );
        }

        $categories = array_unique(array_column($allVariables, 'category'));
        $this->command->info('Complete variables seeded successfully!');
        $this->command->info('Categories created: '.implode(', ', $categories));
        $this->command->info('Total variables: '.count($allVariables));
    }

    /**
     * Dashboard Variables
     */
    protected function getDashboardVariables(): array
    {
        return [
            [
                'key' => 'dashboard_widgets_enabled',
                'value' => '1',
                'type' => 'boolean',
                'category' => 'dashboard',
                'description' => 'Enable Dashboard Widgets',
            ],
            [
                'key' => 'dashboard_show_statistics',
                'value' => '1',
                'type' => 'boolean',
                'category' => 'dashboard',
                'description' => 'Show Statistics Widget',
            ],
            [
                'key' => 'dashboard_show_recent_orders',
                'value' => '1',
                'type' => 'boolean',
                'category' => 'dashboard',
                'description' => 'Show Recent Orders Widget',
            ],
            [
                'key' => 'dashboard_recent_items_count',
                'value' => '10',
                'type' => 'number',
                'category' => 'dashboard',
                'description' => 'Recent Items Count',
            ],
        ];
    }

    /**
     * Cache Management Variables
     */
    protected function getCacheManagementVariables(): array
    {
        return [
            [
                'key' => 'cache_enabled',
                'value' => '1',
                'type' => 'boolean',
                'category' => 'cache',
                'description' => 'Enable Cache System',
            ],
            [
                'key' => 'cache_ttl',
                'value' => '3600',
                'type' => 'number',
                'category' => 'cache',
                'description' => 'Cache TTL (seconds)',
            ],
            [
                'key' => 'cache_driver',
                'value' => 'file',
                'type' => 'string',
                'category' => 'cache',
                'description' => 'Cache Driver (file, redis, memcached)',
            ],
            [
                'key' => 'cache_auto_clear',
                'value' => '1',
                'type' => 'boolean',
                'category' => 'cache',
                'description' => 'Auto Clear Cache on Updates',
            ],
        ];
    }

    /**
     * CMS / Blog Variables
     */
    protected function getCmsBlogVariables(): array
    {
        return [
            [
                'key' => 'blog_enabled',
                'value' => '1',
                'type' => 'boolean',
                'category' => 'blog',
                'description' => 'Enable Blog Feature',
            ],
            [
                'key' => 'blog_posts_per_page',
                'value' => '10',
                'type' => 'number',
                'category' => 'blog',
                'description' => 'Blog Posts Per Page',
            ],
            [
                'key' => 'blog_comments_enabled',
                'value' => '1',
                'type' => 'boolean',
                'category' => 'blog',
                'description' => 'Enable Blog Comments',
            ],
            [
                'key' => 'blog_comments_moderation',
                'value' => '1',
                'type' => 'boolean',
                'category' => 'blog',
                'description' => 'Require Comment Moderation',
            ],
            [
                'key' => 'blog_auto_approve_comments',
                'value' => '0',
                'type' => 'boolean',
                'category' => 'blog',
                'description' => 'Auto Approve Comments',
            ],
            [
                'key' => 'blog_excerpt_length',
                'value' => '150',
                'type' => 'number',
                'category' => 'blog',
                'description' => 'Blog Excerpt Length (characters)',
            ],
            [
                'key' => 'blog_related_posts_count',
                'value' => '5',
                'type' => 'number',
                'category' => 'blog',
                'description' => 'Related Posts Count',
            ],
        ];
    }

    /**
     * CMS Variables (Styles, Variables, Layouts, etc.)
     */
    protected function getCmsVariables(): array
    {
        return [
            // Already handled by DynamicVariablesSeeder, but adding CMS-specific ones
            [
                'key' => 'cms_layout_default',
                'value' => 'default',
                'type' => 'string',
                'category' => 'cms',
                'description' => 'Default CMS Layout',
            ],
            [
                'key' => 'cms_skin_default',
                'value' => 'cafe',
                'type' => 'string',
                'category' => 'cms',
                'description' => 'Default CMS Skin',
            ],
            [
                'key' => 'cms_header_variant',
                'value' => 'minimal',
                'type' => 'string',
                'category' => 'cms',
                'description' => 'Header Variant',
            ],
            [
                'key' => 'cms_footer_variant',
                'value' => 'simple',
                'type' => 'string',
                'category' => 'cms',
                'description' => 'Footer Variant',
            ],
            [
                'key' => 'cms_modules_enabled',
                'value' => json_encode(['banner', 'products', 'categories']),
                'type' => 'json',
                'category' => 'cms',
                'description' => 'Enabled CMS Modules',
            ],
        ];
    }

    /**
     * Catalog Variables
     */
    protected function getCatalogVariables(): array
    {
        return [
            [
                'key' => 'catalog_products_per_page',
                'value' => '12',
                'type' => 'number',
                'category' => 'catalog',
                'description' => 'Products Per Page',
            ],
            [
                'key' => 'catalog_show_out_of_stock',
                'value' => '0',
                'type' => 'boolean',
                'category' => 'catalog',
                'description' => 'Show Out of Stock Products',
            ],
            [
                'key' => 'catalog_allow_backorders',
                'value' => '0',
                'type' => 'boolean',
                'category' => 'catalog',
                'description' => 'Allow Backorders',
            ],
            [
                'key' => 'catalog_stock_threshold',
                'value' => '5',
                'type' => 'number',
                'category' => 'catalog',
                'description' => 'Low Stock Threshold',
            ],
            [
                'key' => 'catalog_reviews_enabled',
                'value' => '1',
                'type' => 'boolean',
                'category' => 'catalog',
                'description' => 'Enable Product Reviews',
            ],
            [
                'key' => 'catalog_reviews_moderation',
                'value' => '1',
                'type' => 'boolean',
                'category' => 'catalog',
                'description' => 'Require Review Moderation',
            ],
            [
                'key' => 'catalog_wishlist_enabled',
                'value' => '1',
                'type' => 'boolean',
                'category' => 'catalog',
                'description' => 'Enable Wishlist',
            ],
            [
                'key' => 'catalog_compare_enabled',
                'value' => '1',
                'type' => 'boolean',
                'category' => 'catalog',
                'description' => 'Enable Product Compare',
            ],
            [
                'key' => 'catalog_filter_groups',
                'value' => json_encode(['price', 'brand', 'category']),
                'type' => 'json',
                'category' => 'catalog',
                'description' => 'Available Filter Groups',
            ],
            [
                'key' => 'catalog_attributes_enabled',
                'value' => '1',
                'type' => 'boolean',
                'category' => 'catalog',
                'description' => 'Enable Product Attributes',
            ],
            // Product Options (moved from Catalog Spare to Catalog)
            [
                'key' => 'catalog_options_enabled',
                'value' => '1',
                'type' => 'boolean',
                'category' => 'catalog',
                'description' => 'Enable Product Options',
            ],
            [
                'key' => 'catalog_options_default_type',
                'value' => 'select',
                'type' => 'string',
                'category' => 'catalog',
                'description' => 'Default Option Type',
            ],
            [
                'key' => 'catalog_options_required_by_default',
                'value' => '0',
                'type' => 'boolean',
                'category' => 'catalog',
                'description' => 'Required by Default',
            ],
            [
                'key' => 'catalog_options_sort_order',
                'value' => '0',
                'type' => 'number',
                'category' => 'catalog',
                'description' => 'Default Sort Order',
            ],
        ];
    }

    /**
     * Catalog Spare Variables (Downloads, Reviews, Information)
     * Note: Options moved to Catalog group
     */
    protected function getCatalogSpareVariables(): array
    {
        return [
            [
                'key' => 'catalog_downloads_enabled',
                'value' => '1',
                'type' => 'boolean',
                'category' => 'catalog_spare',
                'description' => 'Enable Digital Downloads',
            ],
            [
                'key' => 'catalog_download_expiry_days',
                'value' => '30',
                'type' => 'number',
                'category' => 'catalog_spare',
                'description' => 'Download Expiry Days',
            ],
            [
                'key' => 'catalog_download_max_attempts',
                'value' => '5',
                'type' => 'number',
                'category' => 'catalog_spare',
                'description' => 'Max Download Attempts',
            ],
            [
                'key' => 'catalog_information_pages_enabled',
                'value' => '1',
                'type' => 'boolean',
                'category' => 'catalog_spare',
                'description' => 'Enable Information Pages',
            ],
        ];
    }

    /**
     * Extensions Variables
     */
    protected function getExtensionsVariables(): array
    {
        return [
            [
                'key' => 'extensions_auto_update',
                'value' => '0',
                'type' => 'boolean',
                'category' => 'extensions',
                'description' => 'Auto Update Extensions',
            ],
            [
                'key' => 'extensions_upload_max_size',
                'value' => '10',
                'type' => 'number',
                'category' => 'extensions',
                'description' => 'Max Upload Size (MB)',
            ],
            [
                'key' => 'languages_default',
                'value' => 'en',
                'type' => 'string',
                'category' => 'extensions',
                'description' => 'Default Language Code',
            ],
            [
                'key' => 'languages_enabled',
                'value' => json_encode(['en', 'el']),
                'type' => 'json',
                'category' => 'extensions',
                'description' => 'Enabled Languages',
            ],
            [
                'key' => 'seo_enabled',
                'value' => '1',
                'type' => 'boolean',
                'category' => 'extensions',
                'description' => 'Enable SEO Features',
            ],
            [
                'key' => 'seo_auto_generate_sitemap',
                'value' => '1',
                'type' => 'boolean',
                'category' => 'extensions',
                'description' => 'Auto Generate Sitemap',
            ],
            [
                'key' => 'api_keys_enabled',
                'value' => '1',
                'type' => 'boolean',
                'category' => 'extensions',
                'description' => 'Enable API Keys',
            ],
            [
                'key' => 'api_rate_limit',
                'value' => '100',
                'type' => 'number',
                'category' => 'extensions',
                'description' => 'API Rate Limit (per minute)',
            ],
        ];
    }

    /**
     * Sales Variables
     */
    protected function getSalesVariables(): array
    {
        return [
            [
                'key' => 'sales_order_status_new',
                'value' => 'pending',
                'type' => 'string',
                'category' => 'sales',
                'description' => 'New Order Status',
            ],
            [
                'key' => 'sales_order_status_complete',
                'value' => 'completed',
                'type' => 'string',
                'category' => 'sales',
                'description' => 'Complete Order Status',
            ],
            [
                'key' => 'sales_auto_invoice',
                'value' => '1',
                'type' => 'boolean',
                'category' => 'sales',
                'description' => 'Auto Generate Invoices',
            ],
            [
                'key' => 'sales_recurring_enabled',
                'value' => '1',
                'type' => 'boolean',
                'category' => 'sales',
                'description' => 'Enable Recurring Orders',
            ],
            [
                'key' => 'sales_returns_enabled',
                'value' => '1',
                'type' => 'boolean',
                'category' => 'sales',
                'description' => 'Enable Returns',
            ],
            [
                'key' => 'sales_returns_period_days',
                'value' => '30',
                'type' => 'number',
                'category' => 'sales',
                'description' => 'Returns Period (days)',
            ],
            [
                'key' => 'sales_gift_vouchers_enabled',
                'value' => '1',
                'type' => 'boolean',
                'category' => 'sales',
                'description' => 'Enable Gift Vouchers',
            ],
            [
                'key' => 'sales_voucher_expiry_days',
                'value' => '365',
                'type' => 'number',
                'category' => 'sales',
                'description' => 'Gift Voucher Expiry (days)',
            ],
            [
                'key' => 'tax_calculation_method',
                'value' => 'inclusive',
                'type' => 'string',
                'category' => 'sales',
                'description' => 'Tax Calculation Method (inclusive/exclusive)',
            ],
            [
                'key' => 'tax_geo_zones_enabled',
                'value' => '1',
                'type' => 'boolean',
                'category' => 'sales',
                'description' => 'Enable Geo Zone Tax Rules',
            ],
        ];
    }

    /**
     * Customers Variables
     */
    protected function getCustomersVariables(): array
    {
        return [
            [
                'key' => 'customers_registration_enabled',
                'value' => '1',
                'type' => 'boolean',
                'category' => 'customers',
                'description' => 'Enable Customer Registration',
            ],
            [
                'key' => 'customers_approval_required',
                'value' => '0',
                'type' => 'boolean',
                'category' => 'customers',
                'description' => 'Require Customer Approval',
            ],
            [
                'key' => 'customers_groups_enabled',
                'value' => '1',
                'type' => 'boolean',
                'category' => 'customers',
                'description' => 'Enable Customer Groups',
            ],
            [
                'key' => 'customers_default_group',
                'value' => '1',
                'type' => 'number',
                'category' => 'customers',
                'description' => 'Default Customer Group ID',
            ],
            [
                'key' => 'customers_custom_fields_enabled',
                'value' => '1',
                'type' => 'boolean',
                'category' => 'customers',
                'description' => 'Enable Custom Fields',
            ],
            [
                'key' => 'customers_login_attempts',
                'value' => '5',
                'type' => 'number',
                'category' => 'customers',
                'description' => 'Max Login Attempts',
            ],
            [
                'key' => 'customers_lockout_duration',
                'value' => '15',
                'type' => 'number',
                'category' => 'customers',
                'description' => 'Account Lockout Duration (minutes)',
            ],
        ];
    }

    /**
     * Marketing Variables
     */
    protected function getMarketingVariables(): array
    {
        return [
            [
                'key' => 'marketing_coupons_enabled',
                'value' => '1',
                'type' => 'boolean',
                'category' => 'marketing',
                'description' => 'Enable Coupons',
            ],
            [
                'key' => 'marketing_coupon_min_amount',
                'value' => '0',
                'type' => 'number',
                'category' => 'marketing',
                'description' => 'Minimum Order Amount for Coupons',
            ],
            [
                'key' => 'marketing_mail_campaigns_enabled',
                'value' => '1',
                'type' => 'boolean',
                'category' => 'marketing',
                'description' => 'Enable Mail Campaigns',
            ],
            [
                'key' => 'marketing_google_ads_enabled',
                'value' => '0',
                'type' => 'boolean',
                'category' => 'marketing',
                'description' => 'Enable Google Ads Integration',
            ],
            [
                'key' => 'marketing_google_ads_conversion_id',
                'value' => '',
                'type' => 'string',
                'category' => 'marketing',
                'description' => 'Google Ads Conversion ID',
            ],
            [
                'key' => 'marketing_abandoned_cart_enabled',
                'value' => '1',
                'type' => 'boolean',
                'category' => 'marketing',
                'description' => 'Enable Abandoned Cart Emails',
            ],
            [
                'key' => 'marketing_abandoned_cart_hours',
                'value' => '24',
                'type' => 'number',
                'category' => 'marketing',
                'description' => 'Abandoned Cart Email Delay (hours)',
            ],
        ];
    }

    /**
     * System Variables
     */
    protected function getSystemVariables(): array
    {
        return [
            [
                'key' => 'system_maintenance_mode',
                'value' => '0',
                'type' => 'boolean',
                'category' => 'system',
                'description' => 'Maintenance Mode',
            ],
            [
                'key' => 'system_timezone',
                'value' => 'Europe/Athens',
                'type' => 'string',
                'category' => 'system',
                'description' => 'System Timezone',
            ],
            [
                'key' => 'system_date_format',
                'value' => 'd/m/Y',
                'type' => 'string',
                'category' => 'system',
                'description' => 'Date Format',
            ],
            [
                'key' => 'system_time_format',
                'value' => 'H:i',
                'type' => 'string',
                'category' => 'system',
                'description' => 'Time Format',
            ],
            [
                'key' => 'system_currency_default',
                'value' => 'EUR',
                'type' => 'string',
                'category' => 'system',
                'description' => 'Default Currency',
            ],
            [
                'key' => 'system_currencies_enabled',
                'value' => json_encode(['EUR', 'USD', 'GBP']),
                'type' => 'json',
                'category' => 'system',
                'description' => 'Enabled Currencies',
            ],
            [
                'key' => 'system_length_class_default',
                'value' => 'cm',
                'type' => 'string',
                'category' => 'system',
                'description' => 'Default Length Class',
            ],
            [
                'key' => 'system_weight_class_default',
                'value' => 'kg',
                'type' => 'string',
                'category' => 'system',
                'description' => 'Default Weight Class',
            ],
            [
                'key' => 'system_backup_enabled',
                'value' => '1',
                'type' => 'boolean',
                'category' => 'system',
                'description' => 'Enable Backup System',
            ],
            [
                'key' => 'system_backup_frequency',
                'value' => 'daily',
                'type' => 'string',
                'category' => 'system',
                'description' => 'Backup Frequency (daily, weekly, monthly)',
            ],
            [
                'key' => 'system_error_logging_enabled',
                'value' => '1',
                'type' => 'boolean',
                'category' => 'system',
                'description' => 'Enable Error Logging',
            ],
            [
                'key' => 'system_error_log_retention_days',
                'value' => '30',
                'type' => 'number',
                'category' => 'system',
                'description' => 'Error Log Retention (days)',
            ],
        ];
    }

    /**
     * Reports Variables
     */
    protected function getReportsVariables(): array
    {
        return [
            [
                'key' => 'reports_sales_period',
                'value' => 'month',
                'type' => 'string',
                'category' => 'reports',
                'description' => 'Default Sales Report Period (day, week, month, year)',
            ],
            [
                'key' => 'reports_show_online_users',
                'value' => '1',
                'type' => 'boolean',
                'category' => 'reports',
                'description' => 'Show Online Users',
            ],
            [
                'key' => 'reports_online_timeout_minutes',
                'value' => '15',
                'type' => 'number',
                'category' => 'reports',
                'description' => 'Online User Timeout (minutes)',
            ],
            [
                'key' => 'reports_statistics_enabled',
                'value' => '1',
                'type' => 'boolean',
                'category' => 'reports',
                'description' => 'Enable Statistics Reports',
            ],
            [
                'key' => 'reports_performance_enabled',
                'value' => '1',
                'type' => 'boolean',
                'category' => 'reports',
                'description' => 'Enable Performance Reports',
            ],
            [
                'key' => 'reports_cache_metrics_enabled',
                'value' => '1',
                'type' => 'boolean',
                'category' => 'reports',
                'description' => 'Enable Cache Metrics',
            ],
        ];
    }
}
