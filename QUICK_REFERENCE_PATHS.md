# 🗂️ Quick Reference - Admin Panel Categories & File Paths

## 📍 Navigation Groups & File Locations

### 1. **Dashboard**
```
📁 app/Filament/Pages/CMS/Dashboard.php
📁 resources/views/filament/pages/cms/dashboard.blade.php
🔧 Variables: database/seeders/CompleteVariablesSeeder.php → getDashboardVariables()
```

### 2. **Cache Management**
```
📁 app/Filament/Pages/CMS/Cache/CacheManagement.php
📁 resources/views/filament/pages/cms/cache/cache-management.blade.php
🔧 Variables: CompleteVariablesSeeder.php → getCacheManagementVariables()
```

### 3. **CMS / Blog**
```
📁 Posts: app/Filament/Resources/ContentResource.php
📁 Categories: app/Filament/Resources/BlogCategoryResource.php
📁 Comments: app/Filament/Resources/BlogCommentResource.php
📁 Settings: app/Filament/Pages/CMS/Blog/Settings.php
🔧 Variables: CompleteVariablesSeeder.php → getCmsBlogVariables()
```

### 4. **CMS** (Styles, Variables, Layouts, etc.)
```
📁 Styles: app/Filament/Pages/CMS/Styles.php
📁 Variables: app/Filament/Pages/CMS/Variables.php
📁 Dynamic Settings: app/Filament/Pages/CMS/DynamicSettings.php ⭐ NEW
📁 Layouts: app/Filament/Resources/LayoutResource.php
📁 Skins: app/Filament/Resources/ThemePresetResource.php
📁 Header: app/Filament/Pages/CMS/Header.php
📁 Footer: app/Filament/Pages/CMS/Footer.php
📁 Modules: app/Filament/Resources/ModuleInstanceResource.php
📁 Product Extras: app/Filament/Resources/ProductExtraResource.php
🔧 Variables: CompleteVariablesSeeder.php → getCmsVariables()
```

### 5. **Catalog**
```
📁 Categories: app/Filament/Resources/CategoryResource.php
📁 Products: app/Filament/Resources/ProductResource.php
📁 Filter Groups: app/Filament/Resources/FilterGroupResource.php
📁 Filter Values: app/Filament/Resources/FilterValueResource.php
📁 Attribute Groups: app/Filament/Resources/AttributeGroupResource.php
📁 Attributes: app/Filament/Resources/AttributeResource.php
📁 Manufacturers: app/Filament/Resources/ManufacturerResource.php
🔧 Variables: CompleteVariablesSeeder.php → getCatalogVariables()
```

### 6. **Catalog Spare**
```
📁 Options: app/Filament/Pages/Catalog/Options.php
📁 Downloads: app/Filament/Pages/Catalog/Downloads.php
📁 Reviews: app/Filament/Pages/Catalog/Reviews.php
📁 Information: app/Filament/Pages/Catalog/Information.php
🔧 Variables: CompleteVariablesSeeder.php → getCatalogSpareVariables()
```

### 7. **Extensions**
```
📁 Extensions: app/Filament/Pages/Extensions/Extensions.php
📁 Uploads: app/Filament/Pages/Extensions/Uploads.php
📁 Languages: app/Filament/Pages/System/Localisation/Languages.php
📁 Complete SEO: app/Filament/Pages/Extensions/CompleteSEO.php
📁 Vqmod Manager: app/Filament/Pages/VqmodManager.php
📁 API Keys: app/Filament/Resources/ApiKeyResource.php
🔧 Variables: CompleteVariablesSeeder.php → getExtensionsVariables()
```

### 8. **Sales**
```
📁 Orders: app/Filament/Resources/OrderResource.php
📁 Recurring Profiles: app/Filament/Resources/RecurringProfileResource.php
📁 Returns: app/Filament/Resources/ReturnResource.php
📁 Geo Zones: app/Filament/Resources/GeoZoneResource.php
📁 Gift Vouchers: app/Filament/Resources/GiftVoucherResource.php
📁 Taxes: app/Filament/Resources/TaxResource.php
📁 Voucher Themes: app/Filament/Resources/VoucherThemeResource.php
🔧 Variables: CompleteVariablesSeeder.php → getSalesVariables()
```

### 9. **Customers**
```
📁 Customers: app/Filament/Resources/CustomerResource.php
📁 Customer Groups: app/Filament/Resources/CustomerGroupResource.php
📁 Customer Approvals: app/Filament/Resources/CustomerApprovalResource.php
📁 Custom Fields: app/Filament/Resources/CustomFieldResource.php
🔧 Variables: CompleteVariablesSeeder.php → getCustomersVariables()
```

### 10. **Marketing**
```
📁 Coupons: app/Filament/Resources/CouponResource.php
📁 Mail Campaigns: app/Filament/Resources/MailCampaignResource.php
📁 Google Ads: app/Filament/Pages/Marketing/GoogleAds.php
🔧 Variables: CompleteVariablesSeeder.php → getMarketingVariables()
```

### 11. **System**
```
📁 Settings: app/Filament/Pages/System/Settings.php
📁 Roles: app/Filament/Resources/Domain/Auth/Models/Roles/RoleResource.php
📁 Users: app/Filament/Resources/Users/UserResource.php
📁 Localisation:
   - Store Location: app/Filament/Pages/System/Localisation/StoreLocation.php
   - Languages: app/Filament/Pages/System/Localisation/Languages.php
   - Currencies: app/Filament/Pages/System/Localisation/Currencies.php
   - Stock Statuses: app/Filament/Pages/System/Localisation/StockStatuses.php
   - Order Statuses: app/Filament/Pages/System/Localisation/OrderStatuses.php
   - Returns: app/Filament/Pages/System/Localisation/Returns.php
   - Zones: app/Filament/Pages/System/Localisation/Zones.php
   - Geo Zones: app/Filament/Pages/System/Localisation/GeoZones.php
   - Taxes: app/Filament/Pages/System/Localisation/Taxes.php
   - Length Classes: app/Filament/Pages/System/Localisation/LengthClasses.php
   - Weight Classes: app/Filament/Pages/System/Localisation/WeightClasses.php
📁 Maintenance:
   - Backup/Restore: app/Filament/Pages/System/Maintenance/BackupRestore.php
   - Error Logs: app/Filament/Pages/System/Maintenance/ErrorLogs.php
🔧 Variables: CompleteVariablesSeeder.php → getSystemVariables()
```

### 12. **Reports**
```
📁 Reports: app/Filament/Pages/Reports/Reports.php
📁 Who's Online: app/Filament/Pages/Reports/WhosOnline.php
📁 Statistics: app/Filament/Pages/Reports/Statistics.php
📁 Performance: app/Filament/Pages/CMS/Performance/Performance.php
🔧 Variables: CompleteVariablesSeeder.php → getReportsVariables()
```

---

## 🔧 Core Dynamic Variables System Files

### Services
```
📁 app/Domain/Variables/Services/VariableService.php
📁 app/Domain/Variables/Services/ThemeService.php
```

### Helpers & Components
```
📁 app/Support/VariableHelper.php
📁 app/View/Components/DynamicTheme.php
📁 resources/views/components/dynamic-theme.blade.php
```

### Middleware
```
📁 app/Http/Middleware/InjectVariables.php
📁 bootstrap/app.php (registered here)
```

### Pages
```
📁 app/Filament/Pages/CMS/DynamicSettings.php ⭐ MAIN PAGE
📁 resources/views/filament/pages/cms/dynamic-settings.blade.php
```

### Seeders
```
📁 database/seeders/CompleteVariablesSeeder.php (85+ variables)
📁 database/seeders/DynamicVariablesSeeder.php (20 examples)
📁 database/seeders/VariablesSeeder.php (CMS defaults)
```

### Config & Migration
```
📁 config/variables.php
📁 database/migrations/2026_01_23_121903_add_category_to_variables_table.php
📁 database/migrations/2026_01_20_130000_create_variables_table.php
```

### Model
```
📁 app/Domain/Variables/Models/Variable.php
```

---

## ✅ Verification Checklist

### 1. Check All Categories Exist
```bash
# Count variables per category
SELECT category, COUNT(*) FROM variables GROUP BY category;
```

### 2. Check Dynamic Settings Page
- URL: `/admin/dynamic-settings`
- Should show 12 tabs
- Each tab should have variables

### 3. Check Files Exist
```bash
# Check main page
ls app/Filament/Pages/CMS/DynamicSettings.php

# Check services
ls app/Domain/Variables/Services/*.php

# Check seeder
ls database/seeders/CompleteVariablesSeeder.php
```

### 4. Test Helper Functions
```php
// In tinker
php artisan tinker
>>> variable('blog_enabled')
>>> site_config()
```

---

## 📊 Quick Stats

- **Total Categories**: 12
- **Total Variables**: 85+
- **Main Page**: `/admin/dynamic-settings`
- **Seeder File**: `CompleteVariablesSeeder.php`
- **Service Files**: 2 (VariableService, ThemeService)

---

**Last Updated**: 2026-01-23
