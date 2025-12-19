# Filament Objects Map — Sprint 4.3

**Status**: ⏳ In Progress (Sprint 4.3)  
**Scope**: Όλα τα `App\Filament\*` elements (Pages, Resources, Widgets, Panel Providers).

---

## 📋 Σκοπός

- Να υπάρχει **μία κεντρική λίστα** με όλα τα Filament objects στο project.
- Να ξέρουμε για κάθε class:
  - Τύπο (Page / Resource / Widget / PanelProvider),
  - Domain (Content, Media, Layout, RBAC, System, κ.λπ.),
  - Σε ποιο sprint ανήκει λογικά,
  - Σημειώσεις για Filament 4 migration status.

Χρησιμοποιείται ως reference για τα Sprints 4.2–4.3 (Filament 4 migration).

---

## 🧩 Panel Provider

| Class | Type | Domain | Sprint | Notes |
|-------|------|--------|--------|-------|
| `App\Providers\Filament\AdminPanelProvider` | PanelProvider | Admin Panel / Navigation | Sprint 0 / 4.1 / 4.3 | Main Filament 4 panel (path `/admin`, navigation groups). |

---

## 📄 Pages (`app/Filament/Pages/**`)

> Συμπληρώνεται σταδιακά στο Sprint 4.3 καθώς γίνεται το full audit.

| Class | Type | Domain | Sprint | Notes |
|-------|------|--------|--------|-------|
| `App\Filament\Pages\CMS\Dashboard` | Page | CMS | 4.1 | Placeholder CMS dashboard (Filament 4 page). |
| `App\Filament\Pages\CMS\Variables` | Page | CMS | 4.1 | CMS Variables placeholder. |
| `App\Filament\Pages\CMS\Styles` | Page | CMS | 4.1 | CMS Styles placeholder. |
| `App\Filament\Pages\CMS\Skins` | Page | CMS | 4.1 | CMS Skins placeholder. |
| `App\Filament\Pages\CMS\Layouts` | Page | Layout System | 4.1 / 4 | Layouts placeholder. |
| `App\Filament\Pages\CMS\Header` | Page | Layout System | 4.1 / 4 | Header placeholder. |
| `App\Filament\Pages\CMS\Footer` | Page | Layout System | 4.1 / 4 | Footer placeholder. |
| `App\Filament\Pages\CMS\Modules` | Page | Layout / Modules | 4.1 / 4 | Modules admin placeholder. |
| `App\Filament\Pages\CMS\ProductExtras` | Page | CMS / Catalog | 4.1 | Product extras placeholder. |
| `App\Filament\Pages\CMS\Blog\Settings` | Page | CMS / Blog | 4.1 | Blog settings placeholder. |
| `App\Filament\Pages\CMS\Blog\Categories` | Page | CMS / Blog | 4.1 | Blog categories placeholder. |
| `App\Filament\Pages\CMS\Blog\Posts` | Page | CMS / Blog | 4.1 | Blog posts placeholder. |
| `App\Filament\Pages\CMS\Blog\Comments` | Page | CMS / Blog | 4.1 | Blog comments placeholder. |
| `App\Filament\Pages\Catalog\Categories` | Page | Catalog | 4.1 | Catalog categories placeholder. |
| `App\Filament\Pages\Catalog\Products` | Page | Catalog | 4.1 | Catalog products placeholder. |
| `App\Filament\Pages\Catalog\RecurringProfiles` | Page | Catalog | 4.1 | Recurring profiles placeholder. |
| `App\Filament\Pages\Catalog\Filters` | Page | Catalog | 4.1 | Filters placeholder. |
| `App\Filament\Pages\Catalog\Attributes\Attributes` | Page | Catalog | 4.1 | Attributes placeholder. |
| `App\Filament\Pages\Catalog\Attributes\AttributeGroups` | Page | Catalog | 4.1 | Attribute groups placeholder. |
| `App\Filament\Pages\Catalog\Options` | Page | Catalog | 4.1 | Options placeholder. |
| `App\Filament\Pages\Catalog\Manufacturers` | Page | Catalog | 4.1 | Manufacturers placeholder. |
| `App\Filament\Pages\Catalog\Downloads` | Page | Catalog | 4.1 | Downloads placeholder. |
| `App\Filament\Pages\Catalog\Reviews` | Page | Catalog | 4.1 | Reviews placeholder. |
| `App\Filament\Pages\Catalog\Information` | Page | Catalog | 4.1 | Information pages placeholder. |
| `App\Filament\Pages\Extensions\Marketplace` | Page | Extensions | 4.1 | Marketplace placeholder. |
| `App\Filament\Pages\Extensions\Installer` | Page | Extensions | 4.1 | Installer placeholder. |
| `App\Filament\Pages\Extensions\Extensions` | Page | Extensions | 4.1 | Extensions placeholder. |
| `App\Filament\Pages\Extensions\Modifications` | Page | Extensions | 4.1 | Modifications placeholder. |
| `App\Filament\Pages\Extensions\CompleteSEO` | Page | Extensions | 4.1 | SEO extension placeholder. |
| `App\Filament\Pages\Extensions\Events` | Page | Extensions | 4.1 | Events placeholder. |
| `App\Filament\Pages\Sales\Orders` | Page | Sales | 4.1 | Orders placeholder. |
| `App\Filament\Pages\Sales\RecurringOrders` | Page | Sales | 4.1 | Recurring orders placeholder. |
| `App\Filament\Pages\Sales\Returns` | Page | Sales | 4.1 | Returns placeholder. |
| `App\Filament\Pages\Sales\GiftVouchers\GiftVouchers` | Page | Sales | 4.1 | Gift vouchers placeholder. |
| `App\Filament\Pages\Sales\GiftVouchers\VoucherThemes` | Page | Sales | 4.1 | Voucher themes placeholder. |
| `App\Filament\Pages\Customers\Customers` | Page | Customers | 4.1 | Customers placeholder. |
| `App\Filament\Pages\Customers\CustomerGroups` | Page | Customers | 4.1 | Customer groups placeholder. |
| `App\Filament\Pages\Customers\CustomerApprovals` | Page | Customers | 4.1 | Customer approvals placeholder. |
| `App\Filament\Pages\Customers\CustomFields` | Page | Customers | 4.1 | Custom fields placeholder. |
| `App\Filament\Pages\Marketing\Marketing` | Page | Marketing | 4.1 | Marketing dashboard placeholder. |
| `App\Filament\Pages\Marketing\Coupons` | Page | Marketing | 4.1 | Coupons placeholder. |
| `App\Filament\Pages\Marketing\Mail` | Page | Marketing | 4.1 | Mail campaigns placeholder. |
| `App\Filament\Pages\Marketing\GoogleAds` | Page | Marketing | 4.1 | Google Ads placeholder. |
| `App\Filament\Pages\System\Settings` | Page | System | 0 / 4.1 | Global/system settings placeholder. |
| `App\Filament\Pages\System\Users\Users` | Page | System / Users | 4.1 | Users admin placeholder (non-Resource). |
| `App\Filament\Pages\System\Users\UserGroups` | Page | System / Users | 4.1 | User groups placeholder. |
| `App\Filament\Pages\System\Users\API` | Page | System / Users | 4.1 | API users/keys placeholder. |
| `App\Filament\Pages\System\Localisation\StoreLocation` | Page | System / Localisation | 4.1 | Store location placeholder. |
| `App\Filament\Pages\System\Localisation\Languages` | Page | System / Localisation | 4.1 | Languages placeholder. |
| `App\Filament\Pages\System\Localisation\Currencies` | Page | System / Localisation | 4.1 | Currencies placeholder. |
| `App\Filament\Pages\System\Localisation\StockStatuses` | Page | System / Localisation | 4.1 | Stock statuses placeholder. |
| `App\Filament\Pages\System\Localisation\OrderStatuses` | Page | System / Localisation | 4.1 | Order statuses placeholder. |
| `App\Filament\Pages\System\Localisation\Returns` | Page | System / Localisation | 4.1 | Returns reasons/statuses placeholder. |
| `App\Filament\Pages\System\Localisation\Countries` | Page | System / Localisation | 4.1 | Countries placeholder. |
| `App\Filament\Pages\System\Localisation\Zones` | Page | System / Localisation | 4.1 | Zones placeholder. |
| `App\Filament\Pages\System\Localisation\GeoZones` | Page | System / Localisation | 4.1 | Geo zones placeholder. |
| `App\Filament\Pages\System\Localisation\Taxes` | Page | System / Localisation | 4.1 | Taxes placeholder. |
| `App\Filament\Pages\System\Localisation\LengthClasses` | Page | System / Localisation | 4.1 | Length classes placeholder. |
| `App\Filament\Pages\System\Localisation\WeightClasses` | Page | System / Localisation | 4.1 | Weight classes placeholder. |
| `App\Filament\Pages\System\Maintenance\BackupRestore` | Page | System / Maintenance | 4.1 | Backup / restore placeholder. |
| `App\Filament\Pages\System\Maintenance\Uploads` | Page | System / Maintenance | 4.1 | Uploads placeholder. |
| `App\Filament\Pages\System\Maintenance\ErrorLogs` | Page | System / Maintenance | 4.1 | Error logs placeholder. |
| `App\Filament\Pages\Reports\Reports` | Page | Reports | 4.1 | Reports overview placeholder. |
| `App\Filament\Pages\Reports\WhosOnline` | Page | Reports | 4.1 | Who's online placeholder. |
| `App\Filament\Pages\Reports\Statistics` | Page | Reports | 4.1 | Statistics placeholder. |
| `App\Filament\Pages\VqmodManager` | Page | System / Extensions | 4.1 | Vqmod manager placeholder. |

---

## 📦 Resources (`app/Filament/Resources/**`)

> Θα εμπλουτιστεί στο Task B1/B2 του Sprint 4.3 με αναλυτικότερα notes.

| Class | Type | Domain | Sprint | Notes |
|-------|------|--------|--------|-------|
| `App\Filament\Resources\Users\UserResource` | Resource | RBAC / Users | 0 | User management (Filament 4 CRUD). |
| `App\Filament\Resources\Users\Tables\UsersTable` | Table Schema | RBAC / Users | 0 | Users table definition. |
| `App\Filament\Resources\Users\Schemas\UserForm` | Form Schema | RBAC / Users | 0 | Users form definition. |
| `App\Filament\Resources\Users\Pages\ListUsers` | Resource Page | RBAC / Users | 0 | List users page. |
| `App\Filament\Resources\Users\Pages\CreateUser` | Resource Page | RBAC / Users | 0 | Create user page. |
| `App\Filament\Resources\Users\Pages\EditUser` | Resource Page | RBAC / Users | 0 | Edit user page. |
| `App\Filament\Resources\Domain\Auth\Models\Roles\RoleResource` | Resource | RBAC / Roles | 0 | Roles management resource. |
| `App\Filament\Resources\Domain\Auth\Models\Roles\Tables\RolesTable` | Table Schema | RBAC / Roles | 0 | Roles table definition. |
| `App\Filament\Resources\Domain\Auth\Models\Roles\Schemas\RoleForm` | Form Schema | RBAC / Roles | 0 | Roles form definition. |
| `App\Filament\Resources\Domain\Auth\Models\Roles\Pages\ListRoles` | Resource Page | RBAC / Roles | 0 | List roles page. |
| `App\Filament\Resources\Domain\Auth\Models\Roles\Pages\CreateRole` | Resource Page | RBAC / Roles | 0 | Create role page. |
| `App\Filament\Resources\Domain\Auth\Models\Roles\Pages\EditRole` | Resource Page | RBAC / Roles | 0 | Edit role page. |
| `App\Filament\Resources\ModuleInstanceResource` | Resource | Layout / Modules | 4 | Module instances admin (layout system). |
| `App\Filament\Resources\ModuleInstanceResource\Pages\ListModuleInstances` | Resource Page | Layout / Modules | 4 | List module instances page. |
| `App\Filament\Resources\ModuleInstanceResource\Pages\CreateModuleInstance` | Resource Page | Layout / Modules | 4 | Create module instance page. |
| `App\Filament\Resources\ModuleInstanceResource\Pages\EditModuleInstance` | Resource Page | Layout / Modules | 4 | Edit module instance page. |

---

## 📊 Migration Status Legend

- **OK (v4)**: Έχει ήδη ελεγχθεί ότι χρησιμοποιεί Filament 4 API (properties, methods, types).
- **CHECK**: Θέλει έλεγχο/πιθανό refactor για Filament 4.
- **DEPRECATED**: Προορίζεται για αφαίρεση ή αντικατάσταση.

> Τα αναλυτικά status θα συμπληρωθούν κατά τη διάρκεια του Sprint 4.3.


