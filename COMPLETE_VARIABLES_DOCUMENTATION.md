# Complete Variables System - Full Documentation

## 🎯 Overview

Ένα πλήρως δυναμικό σύστημα που μετατρέπει **όλες** τις ρυθμίσεις του admin panel σε μεταβλητές που διαχειρίζονται μέσω του Dynamic Settings page.

## 📊 Statistics

- **Total Variables**: 85+
- **Categories**: 12
- **Types Supported**: string, number, boolean, json
- **Auto-Generated**: 100% Dynamic

## 📁 Categories & Variables

### 1. Dashboard (4 variables)
- `dashboard_widgets_enabled` (boolean) - Enable Dashboard Widgets
- `dashboard_show_statistics` (boolean) - Show Statistics Widget
- `dashboard_show_recent_orders` (boolean) - Show Recent Orders Widget
- `dashboard_recent_items_count` (number) - Recent Items Count

### 2. Cache Management (4 variables)
- `cache_enabled` (boolean) - Enable Cache System
- `cache_ttl` (number) - Cache TTL (seconds)
- `cache_driver` (string) - Cache Driver (file, redis, memcached)
- `cache_auto_clear` (boolean) - Auto Clear Cache on Updates

### 3. Blog / CMS Blog (7 variables)
- `blog_enabled` (boolean) - Enable Blog Feature
- `blog_posts_per_page` (number) - Blog Posts Per Page
- `blog_comments_enabled` (boolean) - Enable Blog Comments
- `blog_comments_moderation` (boolean) - Require Comment Moderation
- `blog_auto_approve_comments` (boolean) - Auto Approve Comments
- `blog_excerpt_length` (number) - Blog Excerpt Length (characters)
- `blog_related_posts_count` (number) - Related Posts Count

### 4. CMS (5 variables)
- `cms_layout_default` (string) - Default CMS Layout
- `cms_skin_default` (string) - Default CMS Skin
- `cms_header_variant` (string) - Header Variant
- `cms_footer_variant` (string) - Footer Variant
- `cms_modules_enabled` (json) - Enabled CMS Modules

### 5. Catalog (10 variables)
- `catalog_products_per_page` (number) - Products Per Page
- `catalog_show_out_of_stock` (boolean) - Show Out of Stock Products
- `catalog_allow_backorders` (boolean) - Allow Backorders
- `catalog_stock_threshold` (number) - Low Stock Threshold
- `catalog_reviews_enabled` (boolean) - Enable Product Reviews
- `catalog_reviews_moderation` (boolean) - Require Review Moderation
- `catalog_wishlist_enabled` (boolean) - Enable Wishlist
- `catalog_compare_enabled` (boolean) - Enable Product Compare
- `catalog_filter_groups` (json) - Available Filter Groups
- `catalog_attributes_enabled` (boolean) - Enable Product Attributes

### 6. Catalog Spare (5 variables)
- `catalog_options_enabled` (boolean) - Enable Product Options
- `catalog_downloads_enabled` (boolean) - Enable Digital Downloads
- `catalog_download_expiry_days` (number) - Download Expiry Days
- `catalog_download_max_attempts` (number) - Max Download Attempts
- `catalog_information_pages_enabled` (boolean) - Enable Information Pages

### 7. Extensions (8 variables)
- `extensions_auto_update` (boolean) - Auto Update Extensions
- `extensions_upload_max_size` (number) - Max Upload Size (MB)
- `languages_default` (string) - Default Language Code
- `languages_enabled` (json) - Enabled Languages
- `seo_enabled` (boolean) - Enable SEO Features
- `seo_auto_generate_sitemap` (boolean) - Auto Generate Sitemap
- `api_keys_enabled` (boolean) - Enable API Keys
- `api_rate_limit` (number) - API Rate Limit (per minute)

### 8. Sales (10 variables)
- `sales_order_status_new` (string) - New Order Status
- `sales_order_status_complete` (string) - Complete Order Status
- `sales_auto_invoice` (boolean) - Auto Generate Invoices
- `sales_recurring_enabled` (boolean) - Enable Recurring Orders
- `sales_returns_enabled` (boolean) - Enable Returns
- `sales_returns_period_days` (number) - Returns Period (days)
- `sales_gift_vouchers_enabled` (boolean) - Enable Gift Vouchers
- `sales_voucher_expiry_days` (number) - Gift Voucher Expiry (days)
- `tax_calculation_method` (string) - Tax Calculation Method
- `tax_geo_zones_enabled` (boolean) - Enable Geo Zone Tax Rules

### 9. Customers (7 variables)
- `customers_registration_enabled` (boolean) - Enable Customer Registration
- `customers_approval_required` (boolean) - Require Customer Approval
- `customers_groups_enabled` (boolean) - Enable Customer Groups
- `customers_default_group` (number) - Default Customer Group ID
- `customers_custom_fields_enabled` (boolean) - Enable Custom Fields
- `customers_login_attempts` (number) - Max Login Attempts
- `customers_lockout_duration` (number) - Account Lockout Duration (minutes)

### 10. Marketing (7 variables)
- `marketing_coupons_enabled` (boolean) - Enable Coupons
- `marketing_coupon_min_amount` (number) - Minimum Order Amount for Coupons
- `marketing_mail_campaigns_enabled` (boolean) - Enable Mail Campaigns
- `marketing_google_ads_enabled` (boolean) - Enable Google Ads Integration
- `marketing_google_ads_conversion_id` (string) - Google Ads Conversion ID
- `marketing_abandoned_cart_enabled` (boolean) - Enable Abandoned Cart Emails
- `marketing_abandoned_cart_hours` (number) - Abandoned Cart Email Delay (hours)

### 11. System (12 variables)
- `system_maintenance_mode` (boolean) - Maintenance Mode
- `system_timezone` (string) - System Timezone
- `system_date_format` (string) - Date Format
- `system_time_format` (string) - Time Format
- `system_currency_default` (string) - Default Currency
- `system_currencies_enabled` (json) - Enabled Currencies
- `system_length_class_default` (string) - Default Length Class
- `system_weight_class_default` (string) - Default Weight Class
- `system_backup_enabled` (boolean) - Enable Backup System
- `system_backup_frequency` (string) - Backup Frequency
- `system_error_logging_enabled` (boolean) - Enable Error Logging
- `system_error_log_retention_days` (number) - Error Log Retention (days)

### 12. Reports (6 variables)
- `reports_sales_period` (string) - Default Sales Report Period
- `reports_show_online_users` (boolean) - Show Online Users
- `reports_online_timeout_minutes` (number) - Online User Timeout (minutes)
- `reports_statistics_enabled` (boolean) - Enable Statistics Reports
- `reports_performance_enabled` (boolean) - Enable Performance Reports
- `reports_cache_metrics_enabled` (boolean) - Enable Cache Metrics

## 🚀 Usage

### Access Dynamic Settings

Πήγαινε στο `/admin/dynamic-settings` και θα δεις όλες τις μεταβλητές οργανωμένες σε tabs ανά category.

### Use in Code

```php
// Get single variable
$blogEnabled = variable('blog_enabled', false);

// Get all variables
$allVars = variable_service()->getAllVariables();

// Get by category
$catalogVars = variable_service()->getByCategory('catalog');
```

### Use in Blade

```blade
@if(variable('blog_enabled'))
    <div>Blog is enabled</div>
@endif

@if(variable('catalog_wishlist_enabled'))
    <button>Add to Wishlist</button>
@endif

{{ variable('catalog_products_per_page', 12) }} products per page
```

## 🔄 Workflow

1. **Admin changes setting** → `/admin/dynamic-settings`
2. **Save** → Variable updated in database
3. **Cache cleared** → New value available immediately
4. **Site uses new value** → Automatic update

## ✨ Benefits

- ✅ **Zero Hardcoding**: Όλες οι ρυθμίσεις από βάση
- ✅ **Auto-Update**: Αλλαγές εφαρμόζονται αυτόματα
- ✅ **Scalable**: Προσθήκη νέων μεταβλητών χωρίς code changes
- ✅ **Organized**: Tabs ανά category
- ✅ **Type-Safe**: Proper casting για κάθε type
- ✅ **Performance**: Caching για optimal speed

## 📝 Adding New Variables

Απλά προσθέστε νέα μεταβλητή στη βάση:

```php
Variable::create([
    'business_id' => $business->id,
    'key' => 'new_feature_enabled',
    'value' => '1',
    'type' => 'boolean',
    'category' => 'system',
    'description' => 'Enable New Feature',
]);
```

Θα εμφανιστεί **αυτόματα** στο Dynamic Settings!

---

**Created**: 2026-01-23  
**Total Variables**: 85+  
**Categories**: 12  
**Status**: ✅ Complete
