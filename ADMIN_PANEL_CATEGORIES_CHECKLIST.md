# 📋 Admin Panel Categories - Complete Checklist

## 🎯 Στόχος
Λίστα με όλες τις κατηγορίες του admin panel και τα paths τους για να ελέγξεις ότι όλες οι αλλαγές έγιναν.

---

## 1️⃣ Dashboard

**Navigation Group**: Dashboard (default)

### Files:
- **Page**: `app/Filament/Pages/CMS/Dashboard.php`
- **View**: `resources/views/filament/pages/cms/dashboard.blade.php`

### Variables Category: `dashboard`
- ✅ `dashboard_widgets_enabled`
- ✅ `dashboard_show_statistics`
- ✅ `dashboard_show_recent_orders`
- ✅ `dashboard_recent_items_count`

**Check**: `/admin/dynamic-settings` → Tab "Dashboard"

---

## 2️⃣ Cache Management

**Navigation Group**: CMS

### Files:
- **Page**: `app/Filament/Pages/CMS/Cache/CacheManagement.php`
- **View**: `resources/views/filament/pages/cms/cache/cache-management.blade.php`

### Variables Category: `cache`
- ✅ `cache_enabled`
- ✅ `cache_ttl`
- ✅ `cache_driver`
- ✅ `cache_auto_clear`

**Check**: `/admin/dynamic-settings` → Tab "Cache"

---

## 3️⃣ CMS / Blog

**Navigation Group**: CMS

### Files:

#### Blog Posts
- **Resource**: `app/Filament/Resources/ContentResource.php`
- **Pages**: `app/Filament/Resources/ContentResource/Pages/`

#### Blog Categories
- **Resource**: `app/Filament/Resources/BlogCategoryResource.php`
- **Pages**: `app/Filament/Resources/BlogCategoryResource/Pages/`

#### Post Comments
- **Resource**: `app/Filament/Resources/BlogCommentResource.php`
- **Pages**: `app/Filament/Resources/BlogCommentResource/Pages/`

#### Blog Settings
- **Page**: `app/Filament/Pages/CMS/Blog/Settings.php`
- **View**: `resources/views/filament/pages/cms/blog/settings.blade.php`

### Variables Category: `blog`
- ✅ `blog_enabled`
- ✅ `blog_posts_per_page`
- ✅ `blog_comments_enabled`
- ✅ `blog_comments_moderation`
- ✅ `blog_auto_approve_comments`
- ✅ `blog_excerpt_length`
- ✅ `blog_related_posts_count`

**Check**: `/admin/dynamic-settings` → Tab "Blog"

---

## 4️⃣ CMS (Styles, Variables, Layouts, etc.)

**Navigation Group**: CMS

### Files:

#### Styles
- **Page**: `app/Filament/Pages/CMS/Styles.php`
- **View**: `resources/views/filament/pages/cms/styles.blade.php`

#### CMS Variables
- **Page**: `app/Filament/Pages/CMS/Variables.php`
- **View**: `resources/views/filament/pages/cms/variables.blade.php`

#### Dynamic Settings (NEW)
- **Page**: `app/Filament/Pages/CMS/DynamicSettings.php`
- **View**: `resources/views/filament/pages/cms/dynamic-settings.blade.php`

#### Layouts
- **Resource**: `app/Filament/Resources/LayoutResource.php`
- **Pages**: `app/Filament/Resources/LayoutResource/Pages/`

#### Skins (Theme Presets)
- **Resource**: `app/Filament/Resources/ThemePresetResource.php`
- **Pages**: `app/Filament/Resources/ThemePresetResource/Pages/`

#### Header
- **Page**: `app/Filament/Pages/CMS/Header.php`
- **View**: `resources/views/filament/pages/cms/header.blade.php`

#### Footer
- **Page**: `app/Filament/Pages/CMS/Footer.php`
- **View**: `resources/views/filament/pages/cms/footer.blade.php`

#### Modules
- **Resource**: `app/Filament/Resources/ModuleInstanceResource.php`
- **Pages**: `app/Filament/Resources/ModuleInstanceResource/Pages/`

#### Product Extras
- **Resource**: `app/Filament/Resources/ProductExtraResource.php`
- **Pages**: `app/Filament/Resources/ProductExtraResource/Pages/`

### Variables Categories:
- **`cms`**: 
  - ✅ `cms_layout_default`
  - ✅ `cms_skin_default`
  - ✅ `cms_header_variant`
  - ✅ `cms_footer_variant`
  - ✅ `cms_modules_enabled`

- **`general`** (from DynamicVariablesSeeder):
  - ✅ `site_name`
  - ✅ `items_per_page`
  - ✅ `enable_maintenance`

- **`appearance`**:
  - ✅ `primary_color`
  - ✅ `theme_colors` (JSON)
  - ✅ `logo_width`

**Check**: `/admin/dynamic-settings` → Tabs "CMS", "General", "Appearance"

---

## 5️⃣ Catalog

**Navigation Group**: Catalog

### Files:

#### Categories
- **Resource**: `app/Filament/Resources/CategoryResource.php`
- **Pages**: `app/Filament/Resources/CategoryResource/Pages/`

#### Products
- **Resource**: `app/Filament/Resources/ProductResource.php`
- **Pages**: `app/Filament/Resources/ProductResource/Pages/`

#### Filter Groups
- **Resource**: `app/Filament/Resources/FilterGroupResource.php`
- **Pages**: `app/Filament/Resources/FilterGroupResource/Pages/`

#### Filter Values
- **Resource**: `app/Filament/Resources/FilterValueResource.php`
- **Pages**: `app/Filament/Resources/FilterValueResource/Pages/`

#### Attribute Groups
- **Resource**: `app/Filament/Resources/AttributeGroupResource.php`
- **Pages**: `app/Filament/Resources/AttributeGroupResource/Pages/`

#### Attributes
- **Resource**: `app/Filament/Resources/AttributeResource.php`
- **Pages**: `app/Filament/Resources/AttributeResource/Pages/`

#### Manufacturers
- **Resource**: `app/Filament/Resources/ManufacturerResource.php`
- **Pages**: `app/Filament/Resources/ManufacturerResource/Pages/`

### Variables Category: `catalog`
- ✅ `catalog_products_per_page`
- ✅ `catalog_show_out_of_stock`
- ✅ `catalog_allow_backorders`
- ✅ `catalog_stock_threshold`
- ✅ `catalog_reviews_enabled`
- ✅ `catalog_reviews_moderation`
- ✅ `catalog_wishlist_enabled`
- ✅ `catalog_compare_enabled`
- ✅ `catalog_filter_groups` (JSON)
- ✅ `catalog_attributes_enabled`
- ✅ `catalog_options_enabled` ⭐ (moved from Catalog Spare)
- ✅ `catalog_options_default_type` ⭐
- ✅ `catalog_options_required_by_default` ⭐
- ✅ `catalog_options_sort_order` ⭐

**Check**: `/admin/dynamic-settings` → Tab "Catalog"

**Options Page**: `app/Filament/Pages/Catalog/Options.php` (moved from Catalog Spare to Catalog)

---

## 6️⃣ Catalog Spare

**Navigation Group**: Catalog Spare

### Files:

#### Downloads
- **Page**: `app/Filament/Pages/Catalog/Downloads.php`
- **View**: `resources/views/filament/pages/catalog/downloads.blade.php`

#### Reviews
- **Page**: `app/Filament/Pages/Catalog/Reviews.php`
- **View**: `resources/views/filament/pages/catalog/reviews.blade.php`

#### Information
- **Page**: `app/Filament/Pages/Catalog/Information.php`
- **View**: `resources/views/filament/pages/catalog/information.blade.php`

### Variables Category: `catalog_spare`
- ✅ `catalog_downloads_enabled`
- ✅ `catalog_download_expiry_days`
- ✅ `catalog_download_max_attempts`
- ✅ `catalog_information_pages_enabled`

**Check**: `/admin/dynamic-settings` → Tab "Catalog Spare"

---

## 7️⃣ Extensions

**Navigation Group**: Extensions

### Files:

#### Extensions
- **Page**: `app/Filament/Pages/Extensions/Extensions.php`
- **View**: `resources/views/filament/pages/extensions/extensions.blade.php`

#### Uploads
- **Page**: `app/Filament/Pages/Extensions/Uploads.php`
- **View**: `resources/views/filament/pages/extensions/uploads.blade.php`

#### Languages
- **Page**: `app/Filament/Pages/System/Localisation/Languages.php`
- **View**: `resources/views/filament/pages/system/localisation/languages.blade.php`

#### Complete SEO
- **Page**: `app/Filament/Pages/Extensions/CompleteSEO.php`
- **View**: `resources/views/filament/pages/extensions/complete-s-e-o.blade.php`

#### Vqmod Manager
- **Page**: `app/Filament/Pages/VqmodManager.php`
- **View**: `resources/views/filament/pages/vqmod-manager.blade.php`

#### API Keys
- **Resource**: `app/Filament/Resources/ApiKeyResource.php`
- **Pages**: `app/Filament/Resources/ApiKeyResource/Pages/`

### Variables Category: `extensions`
- ✅ `extensions_auto_update`
- ✅ `extensions_upload_max_size`
- ✅ `languages_default`
- ✅ `languages_enabled` (JSON)
- ✅ `seo_enabled`
- ✅ `seo_auto_generate_sitemap`
- ✅ `api_keys_enabled`
- ✅ `api_rate_limit`

**Check**: `/admin/dynamic-settings` → Tab "Extensions"

---

## 8️⃣ Sales

**Navigation Group**: Sales

### Files:

#### Orders
- **Resource**: `app/Filament/Resources/OrderResource.php`
- **Pages**: `app/Filament/Resources/OrderResource/Pages/`

#### Recurring Profiles
- **Resource**: `app/Filament/Resources/RecurringProfileResource.php`
- **Pages**: `app/Filament/Resources/RecurringProfileResource/Pages/`

#### Returns
- **Resource**: `app/Filament/Resources/ReturnResource.php`
- **Pages**: `app/Filament/Resources/ReturnResource/Pages/`

#### Geo Zones
- **Resource**: `app/Filament/Resources/GeoZoneResource.php`
- **Pages**: `app/Filament/Resources/GeoZoneResource/Pages/`

#### Gift Vouchers
- **Resource**: `app/Filament/Resources/GiftVoucherResource.php`
- **Pages**: `app/Filament/Resources/GiftVoucherResource/Pages/`

#### Taxes
- **Resource**: `app/Filament/Resources/TaxResource.php`
- **Pages**: `app/Filament/Resources/TaxResource/Pages/`

#### Voucher Themes
- **Resource**: `app/Filament/Resources/VoucherThemeResource.php`
- **Pages**: `app/Filament/Resources/VoucherThemeResource/Pages/`

### Variables Category: `sales`
- ✅ `sales_order_status_new`
- ✅ `sales_order_status_complete`
- ✅ `sales_auto_invoice`
- ✅ `sales_recurring_enabled`
- ✅ `sales_returns_enabled`
- ✅ `sales_returns_period_days`
- ✅ `sales_gift_vouchers_enabled`
- ✅ `sales_voucher_expiry_days`
- ✅ `tax_calculation_method`
- ✅ `tax_geo_zones_enabled`

**Check**: `/admin/dynamic-settings` → Tab "Sales"

---

## 9️⃣ Customers

**Navigation Group**: Customers

### Files:

#### Customers
- **Resource**: `app/Filament/Resources/CustomerResource.php`
- **Pages**: `app/Filament/Resources/CustomerResource/Pages/`

#### Customer Groups
- **Resource**: `app/Filament/Resources/CustomerGroupResource.php`
- **Pages**: `app/Filament/Resources/CustomerGroupResource/Pages/`

#### Customer Approvals
- **Resource**: `app/Filament/Resources/CustomerApprovalResource.php`
- **Pages**: `app/Filament/Resources/CustomerApprovalResource/Pages/`

#### Custom Fields
- **Resource**: `app/Filament/Resources/CustomFieldResource.php`
- **Pages**: `app/Filament/Resources/CustomFieldResource/Pages/`

### Variables Category: `customers`
- ✅ `customers_registration_enabled`
- ✅ `customers_approval_required`
- ✅ `customers_groups_enabled`
- ✅ `customers_default_group`
- ✅ `customers_custom_fields_enabled`
- ✅ `customers_login_attempts`
- ✅ `customers_lockout_duration`

**Check**: `/admin/dynamic-settings` → Tab "Customers"

---

## 🔟 Marketing

**Navigation Group**: Marketing

### Files:

#### Coupons
- **Resource**: `app/Filament/Resources/CouponResource.php`
- **Pages**: `app/Filament/Resources/CouponResource/Pages/`

#### Mail Campaigns
- **Resource**: `app/Filament/Resources/MailCampaignResource.php`
- **Pages**: `app/Filament/Resources/MailCampaignResource/Pages/`

#### Google Ads
- **Page**: `app/Filament/Pages/Marketing/GoogleAds.php`
- **View**: `resources/views/filament/pages/marketing/google-ads.blade.php`

### Variables Category: `marketing`
- ✅ `marketing_coupons_enabled`
- ✅ `marketing_coupon_min_amount`
- ✅ `marketing_mail_campaigns_enabled`
- ✅ `marketing_google_ads_enabled`
- ✅ `marketing_google_ads_conversion_id`
- ✅ `marketing_abandoned_cart_enabled`
- ✅ `marketing_abandoned_cart_hours`

**Check**: `/admin/dynamic-settings` → Tab "Marketing"

---

## 1️⃣1️⃣ System

**Navigation Group**: System

### Files:

#### Settings
- **Page**: `app/Filament/Pages/System/Settings.php`
- **View**: `resources/views/filament/pages/system/settings.blade.php`

#### Roles
- **Resource**: `app/Filament/Resources/Domain/Auth/Models/Roles/RoleResource.php`
- **Pages**: `app/Filament/Resources/Domain/Auth/Models/Roles/Pages/`

#### Users
- **Resource**: `app/Filament/Resources/Users/UserResource.php`
- **Pages**: `app/Filament/Resources/Users/Pages/`

#### Localisation:
- **Store Location**: `app/Filament/Pages/System/Localisation/StoreLocation.php`
- **Languages**: `app/Filament/Pages/System/Localisation/Languages.php`
- **Currencies**: `app/Filament/Pages/System/Localisation/Currencies.php`
- **Stock Statuses**: `app/Filament/Pages/System/Localisation/StockStatuses.php`
- **Order Statuses**: `app/Filament/Pages/System/Localisation/OrderStatuses.php`
- **Returns**: `app/Filament/Pages/System/Localisation/Returns.php`
- **Countries**: (Check if exists)
- **Zones**: `app/Filament/Pages/System/Localisation/Zones.php`
- **Geo Zones**: `app/Filament/Pages/System/Localisation/GeoZones.php`
- **Taxes**: `app/Filament/Pages/System/Localisation/Taxes.php`
- **Length Classes**: `app/Filament/Pages/System/Localisation/LengthClasses.php`
- **Weight Classes**: `app/Filament/Pages/System/Localisation/WeightClasses.php`

#### Maintenance:
- **Backup / Restore**: `app/Filament/Pages/System/Maintenance/BackupRestore.php`
- **Error Logs**: `app/Filament/Pages/System/Maintenance/ErrorLogs.php`

### Variables Category: `system`
- ✅ `system_maintenance_mode`
- ✅ `system_timezone`
- ✅ `system_date_format`
- ✅ `system_time_format`
- ✅ `system_currency_default`
- ✅ `system_currencies_enabled` (JSON)
- ✅ `system_length_class_default`
- ✅ `system_weight_class_default`
- ✅ `system_backup_enabled`
- ✅ `system_backup_frequency`
- ✅ `system_error_logging_enabled`
- ✅ `system_error_log_retention_days`

**Check**: `/admin/dynamic-settings` → Tab "System"

---

## 1️⃣2️⃣ Reports

**Navigation Group**: Reports

### Files:

#### Reports
- **Page**: `app/Filament/Pages/Reports/Reports.php`
- **View**: `resources/views/filament/pages/reports/reports.blade.php`

#### Who's Online
- **Page**: `app/Filament/Pages/Reports/WhosOnline.php`
- **View**: `resources/views/filament/pages/reports/whos-online.blade.php`

#### Statistics
- **Page**: `app/Filament/Pages/Reports/Statistics.php`
- **View**: `resources/views/filament/pages/reports/statistics.blade.php`

#### Performance
- **Page**: `app/Filament/Pages/CMS/Performance/Performance.php`
- **View**: `resources/views/filament/pages/cms/performance/performance.blade.php`

### Variables Category: `reports`
- ✅ `reports_sales_period`
- ✅ `reports_show_online_users`
- ✅ `reports_online_timeout_minutes`
- ✅ `reports_statistics_enabled`
- ✅ `reports_performance_enabled`
- ✅ `reports_cache_metrics_enabled`

**Check**: `/admin/dynamic-settings` → Tab "Reports"

---

## 📊 Summary

### Total Categories: 12
1. Dashboard
2. Cache Management
3. Blog / CMS Blog
4. CMS
5. Catalog
6. Catalog Spare
7. Extensions
8. Sales
9. Customers
10. Marketing
11. System
12. Reports

### Total Variables: 85+

### Key Files to Check:

#### Services:
- ✅ `app/Domain/Variables/Services/VariableService.php`
- ✅ `app/Domain/Variables/Services/ThemeService.php`

#### Helpers:
- ✅ `app/Support/VariableHelper.php`

#### Middleware:
- ✅ `app/Http/Middleware/InjectVariables.php`

#### Pages:
- ✅ `app/Filament/Pages/CMS/DynamicSettings.php`

#### Seeders:
- ✅ `database/seeders/CompleteVariablesSeeder.php`
- ✅ `database/seeders/DynamicVariablesSeeder.php`

#### Config:
- ✅ `config/variables.php`

#### Migration:
- ✅ `database/migrations/2026_01_23_121903_add_category_to_variables_table.php`

---

## ✅ Verification Steps

### Step 1: Check Database
```sql
SELECT category, COUNT(*) as count 
FROM variables 
GROUP BY category 
ORDER BY category;
```

Should show 12 categories with variables.

### Step 2: Check Dynamic Settings Page
1. Go to `/admin/dynamic-settings`
2. Verify all 12 tabs appear
3. Check each tab has variables
4. Try changing a value and saving

### Step 3: Check Helper Functions
```php
// In tinker or any controller
dd(variable('blog_enabled'));
dd(site_config());
```

### Step 4: Check Middleware
```blade
{{-- In any view --}}
{{ $siteConfig['site_name'] ?? 'Not set' }}
{{ $variables['blog_enabled']['value'] ?? 'Not set' }}
```

---

## 🎯 Quick Reference

**Dynamic Settings URL**: `/admin/dynamic-settings`

**All Variables**: Check `CompleteVariablesSeeder.php`

**Add New Variable**: Add to seeder or create directly in database

**Use in Code**: `variable('key', 'default')`

**Use in Blade**: `{{ variable('key') }}` or `{{ $siteConfig['key'] }}`

---

**Last Updated**: 2026-01-23  
**Status**: ✅ Complete
