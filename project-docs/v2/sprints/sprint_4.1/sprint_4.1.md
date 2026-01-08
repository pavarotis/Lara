# Sprint 4.1 — Admin Panel Navigation Structure

**Status**: ⏳ IN PROGRESS  
**Start Date**: _TBD_  
**End Date**: _TBD_  
**Διάρκεια**: 1-2 ημέρες  
**Filament Version**: Χρησιμοποιούμε μόνο **Filament 4.x** στο admin panel (δεν επιτρέπονται Filament v2/v3 packages ή APIs).

---

## 📋 Sprint Goal

Δημιουργία πλήρους admin panel navigation structure με nested groups (OpenCart/Journal style), χωρίς λειτουργίες - μόνο placeholder Pages.

**Μετά το Sprint 4.1**: 👉 «Έχω πλήρη navigation structure στο admin panel, έτοιμο για υλοποίηση λειτουργιών»

---

## 🎯 High-Level Objectives

- ✅ Δημιουργία όλων των Navigation Groups (CMS, Catalog, Extensions, Sales, Customers, Marketing, System, Reports)
- ✅ Δημιουργία placeholder Pages για κάθε menu item
- ✅ Configuration Navigation Groups με icons και collapsible settings
- ✅ Proper navigation sorting για σωστή σειρά
- ✅ View templates για κάθε Page (placeholder content)

⚠️ **Δεν υλοποιείται ακόμα:**
- ❌ Λειτουργίες - μόνο structure
- ❌ CRUD operations
- ❌ Forms και Tables
- ❌ Business logic

---

## 🔄 Related Sprints

- **Sprint 4.2** — Filament 4 Migration (technical migration)
- **Sprint 4.3** — Filament 4 Alignment (code-level alignment)
- **Sprint 4.4** — MVC Audit & Completion (MVC flow audit)
- **Sprint 4.5** — Hybrid Admin Panel Guidelines (decision tree & patterns)

---

## 📁 File Structure

```
app/Filament/Pages/
├─ CMS/
│  ├─ Dashboard.php
│  ├─ Variables.php
│  ├─ Styles.php
│  ├─ Skins.php
│  ├─ Layouts.php
│  ├─ Header.php
│  ├─ Footer.php
│  ├─ Modules.php
│  ├─ ProductExtras.php
│  └─ Blog/
│     ├─ Settings.php
│     ├─ Categories.php
│     ├─ Posts.php
│     └─ Comments.php
├─ Catalog/
│  ├─ Categories.php
│  ├─ Products.php
│  ├─ RecurringProfiles.php
│  ├─ Filters.php
│  ├─ Attributes/
│  │  ├─ Attributes.php
│  │  └─ AttributeGroups.php
│  ├─ Options.php
│  ├─ Manufacturers.php
│  ├─ Downloads.php
│  ├─ Reviews.php
│  └─ Information.php
├─ Extensions/
│  ├─ Marketplace.php
│  ├─ Installer.php
│  ├─ Extensions.php
│  ├─ Modifications.php
│  ├─ CompleteSEO.php
│  └─ Events.php
├─ Sales/
│  ├─ Orders.php
│  ├─ RecurringOrders.php
│  ├─ Returns.php
│  └─ GiftVouchers/
│     ├─ GiftVouchers.php
│     └─ VoucherThemes.php
├─ Customers/
│  ├─ Customers.php
│  ├─ CustomerGroups.php
│  ├─ CustomerApprovals.php
│  └─ CustomFields.php
├─ Marketing/
│  ├─ Marketing.php
│  ├─ Coupons.php
│  ├─ Mail.php
│  └─ GoogleAds.php
├─ System/
│  ├─ Settings.php
│  ├─ Users/
│  │  ├─ Users.php
│  │  ├─ UserGroups.php
│  │  └─ API.php
│  ├─ Localisation/
│  │  ├─ StoreLocation.php
│  │  ├─ Languages.php
│  │  ├─ Currencies.php
│  │  ├─ StockStatuses.php
│  │  ├─ OrderStatuses.php
│  │  ├─ Returns.php
│  │  ├─ Countries.php
│  │  ├─ Zones.php
│  │  ├─ GeoZones.php
│  │  ├─ Taxes.php
│  │  ├─ LengthClasses.php
│  │  └─ WeightClasses.php
│  └─ Maintenance/
│     ├─ BackupRestore.php
│     ├─ Uploads.php
│     └─ ErrorLogs.php
├─ VqmodManager.php
└─ Reports/
   ├─ Reports.php
   ├─ WhosOnline.php
   └─ Statistics.php
```

---

## 🗂️ Navigation Groups Structure

### CMS Group
- Dashboard
- Variables
- Styles
- Skins
- Layouts
- Header
- Footer
- Modules
- Product Extras
- Blog (με υποκαρτέλες: Settings, Categories, Posts, Comments)

### Catalog Group
- Categories
- Products
- Recurring Profiles
- Filters
- Attributes (με υποκαρτέλες: Attributes, Attribute Groups)
- Options
- Manufacturers
- Downloads
- Reviews
- Information

### Extensions Group
- Marketplace
- Installer
- Extensions
- Modifications
- Complete SEO
- Events

### Sales Group
- Orders
- Recurring Orders
- Returns
- Gift Vouchers (με υποκαρτέλες: Gift Vouchers, Voucher Themes)

### Customers Group
- Customers
- Customer Groups
- Customer Approvals
- Custom Fields

### Marketing Group
- Marketing
- Coupons
- Mail
- Google Ads

### System Group
- Settings
- Users (με υποκαρτέλες: Users, User Groups, API)
- Localisation (με υποκαρτέλες: Store Location, Languages, Currencies, Stock Statuses, Order Statuses, Returns, Countries, Zones, Geo Zones, Taxes, Length Classes, Weight Classes)
- Maintenance (με υποκαρτέλες: Backup / Restore, Uploads, Error Logs)

### Reports Group
- Reports
- Who's Online
- Statistics

### Standalone
- Vqmod Manager

---

## 🔧 Technical Implementation

### Navigation Groups Configuration

Στο `AdminPanelProvider.php`:

```php
->navigationGroups([
    NavigationGroup::make('CMS')
        ->icon('heroicon-o-document-text')
        ->collapsible(false),
    NavigationGroup::make('Catalog')
        ->icon('heroicon-o-shopping-bag')
        ->collapsible(false),
    NavigationGroup::make('Extensions')
        ->icon('heroicon-o-puzzle-piece')
        ->collapsible(false),
    NavigationGroup::make('Sales')
        ->icon('heroicon-o-currency-dollar')
        ->collapsible(false),
    NavigationGroup::make('Customers')
        ->icon('heroicon-o-users')
        ->collapsible(false),
    NavigationGroup::make('Marketing')
        ->icon('heroicon-o-megaphone')
        ->collapsible(false),
    NavigationGroup::make('System')
        ->icon('heroicon-o-cog-6-tooth')
        ->collapsible(false),
    NavigationGroup::make('Reports')
        ->icon('heroicon-o-chart-bar')
        ->collapsible(false),
])
```

### Page Template Structure

Κάθε Page θα έχει:

```php
<?php

namespace App\Filament\Pages\[Group];

use Filament\Pages\Page;

class [PageName] extends Page
{
    protected static ?string $navigationGroup = '[Group]';
    protected static ?int $navigationSort = [number];
    protected static string $view = 'filament.pages.[group].[pagename]';
    protected static ?string $navigationIcon = 'heroicon-o-[icon]';
    protected static ?string $navigationLabel = '[Label]';
    
    public function getTitle(): string
    {
        return '[Page Title]';
    }
}
```

### View Template Structure

Κάθε view θα έχει:

```blade
<x-filament-panels::page>
    <div class="p-6">
        <h2 class="text-2xl font-bold mb-4">[Page Title]</h2>
        <p class="text-gray-600">[Page description] functionality will be implemented here.</p>
    </div>
</x-filament-panels::page>
```

---

## ✅ Deliverables

- [ ] Όλα τα CMS Pages
- [ ] Όλα τα Catalog Pages
- [ ] Όλα τα Extensions Pages
- [ ] Όλα τα Sales Pages
- [ ] Όλα τα Customers Pages
- [ ] Όλα τα Marketing Pages
- [ ] Όλα τα System Pages
- [ ] VqmodManager Page
- [ ] Όλα τα Reports Pages
- [ ] Navigation Groups Configuration
- [ ] View templates για όλα τα Pages
- [ ] Navigation sorting configuration

---

## 📝 Notes

- Όλα τα Pages είναι placeholders - δεν έχουν λειτουργίες
- Navigation Groups είναι πάντα expanded (collapsible: false)
- Navigation sorting ρυθμίζεται για σωστή σειρά
- Icons χρησιμοποιούν Heroicons
- View templates είναι απλά placeholders

---

## 🚀 Next Steps (After Sprint 4.1)

- Sprint 4.5: Header/Footer/Layout/Pages functionality
- Sprint 5: Theming, Information Pages, Media Integration
- Sprint 6: Platform Hardening, Routing, API, Release

