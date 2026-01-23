# 📋 Categories → Files Mapping

## Quick Reference Table

| Category | Navigation Group | Main Files | Variables Seeder Method | Variables Count |
|----------|----------------|------------|------------------------|-----------------|
| **Dashboard** | Dashboard | `app/Filament/Pages/CMS/Dashboard.php` | `getDashboardVariables()` | 4 |
| **Cache Management** | CMS | `app/Filament/Pages/CMS/Cache/CacheManagement.php` | `getCacheManagementVariables()` | 4 |
| **Blog** | CMS | `app/Filament/Resources/ContentResource.php`<br>`app/Filament/Pages/CMS/Blog/Settings.php` | `getCmsBlogVariables()` | 7 |
| **CMS** | CMS | `app/Filament/Pages/CMS/DynamicSettings.php` ⭐<br>`app/Filament/Pages/CMS/Variables.php`<br>`app/Filament/Pages/CMS/Styles.php`<br>`app/Filament/Resources/LayoutResource.php`<br>`app/Filament/Resources/ThemePresetResource.php` | `getCmsVariables()` | 5 |
| **Catalog** | Catalog | `app/Filament/Resources/CategoryResource.php`<br>`app/Filament/Resources/ProductResource.php`<br>`app/Filament/Resources/FilterGroupResource.php`<br>`app/Filament/Resources/AttributeResource.php`<br>`app/Filament/Pages/Catalog/Options.php` ⭐ | `getCatalogVariables()` | 14 |
| **Catalog Spare** | Catalog Spare | `app/Filament/Pages/Catalog/Downloads.php`<br>`app/Filament/Pages/Catalog/Reviews.php`<br>`app/Filament/Pages/Catalog/Information.php` | `getCatalogSpareVariables()` | 4 |
| **Extensions** | Extensions | `app/Filament/Pages/Extensions/Extensions.php`<br>`app/Filament/Resources/ApiKeyResource.php` | `getExtensionsVariables()` | 8 |
| **Sales** | Sales | `app/Filament/Resources/OrderResource.php`<br>`app/Filament/Resources/ReturnResource.php`<br>`app/Filament/Resources/TaxResource.php` | `getSalesVariables()` | 10 |
| **Customers** | Customers | `app/Filament/Resources/CustomerResource.php`<br>`app/Filament/Resources/CustomerGroupResource.php` | `getCustomersVariables()` | 7 |
| **Marketing** | Marketing | `app/Filament/Resources/CouponResource.php`<br>`app/Filament/Resources/MailCampaignResource.php` | `getMarketingVariables()` | 7 |
| **System** | System | `app/Filament/Pages/System/Settings.php`<br>`app/Filament/Resources/Users/UserResource.php`<br>`app/Filament/Pages/System/Localisation/*.php` | `getSystemVariables()` | 12 |
| **Reports** | Reports | `app/Filament/Pages/Reports/Reports.php`<br>`app/Filament/Pages/Reports/Statistics.php` | `getReportsVariables()` | 6 |

---

## 📁 Directory Structure

```
app/Filament/
├── Pages/
│   ├── CMS/
│   │   ├── Dashboard.php
│   │   ├── DynamicSettings.php ⭐ NEW
│   │   ├── Variables.php
│   │   ├── Styles.php
│   │   ├── Header.php
│   │   ├── Footer.php
│   │   ├── Blog/
│   │   │   └── Settings.php
│   │   ├── Cache/
│   │   │   └── CacheManagement.php
│   │   └── Performance/
│   │       └── Performance.php
│   ├── Catalog/
│   │   ├── Options.php
│   │   ├── Downloads.php
│   │   ├── Reviews.php
│   │   └── Information.php
│   ├── Extensions/
│   │   ├── Extensions.php
│   │   ├── Uploads.php
│   │   └── CompleteSEO.php
│   ├── Marketing/
│   │   └── GoogleAds.php
│   ├── Reports/
│   │   ├── Reports.php
│   │   ├── Statistics.php
│   │   └── WhosOnline.php
│   ├── System/
│   │   ├── Settings.php
│   │   ├── Localisation/
│   │   │   ├── Languages.php
│   │   │   ├── Currencies.php
│   │   │   ├── Taxes.php
│   │   │   └── ... (more)
│   │   └── Maintenance/
│   │       ├── BackupRestore.php
│   │       └── ErrorLogs.php
│   └── VqmodManager.php
│
└── Resources/
    ├── CategoryResource.php
    ├── ProductResource.php
    ├── OrderResource.php
    ├── CustomerResource.php
    ├── CouponResource.php
    ├── LayoutResource.php
    ├── ThemePresetResource.php
    └── ... (more)
```

---

## 🔍 Where to Check for Changes

### ✅ Dynamic Variables System
- **Main Page**: `app/Filament/Pages/CMS/DynamicSettings.php`
- **Seeder**: `database/seeders/CompleteVariablesSeeder.php`
- **Services**: `app/Domain/Variables/Services/`

### ✅ Each Category's Original Files
Check the files listed in the table above for each category.

### ✅ Variables in Database
```sql
-- Check all categories
SELECT category, COUNT(*) as count 
FROM variables 
GROUP BY category 
ORDER BY category;

-- Check specific category
SELECT * FROM variables WHERE category = 'catalog';
```

---

## 🎯 Verification Commands

```bash
# 1. Check if seeder ran
php artisan db:seed --class=CompleteVariablesSeeder

# 2. Check variables count
php artisan tinker
>>> \App\Domain\Variables\Models\Variable::groupBy('category')->selectRaw('category, count(*) as count')->get();

# 3. Check Dynamic Settings page
# Visit: http://larashop.test/admin/dynamic-settings
```

---

**Quick Access**: `/admin/dynamic-settings`  
**Total Variables**: 85+  
**Categories**: 12
