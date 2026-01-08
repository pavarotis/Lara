# Supporting Models — Documentation

**Last Updated**: 2025-01-27  
**Status**: ✅ Complete

---

## 📋 Overview

Models που δεν χρειάζονται standalone Controllers/Views. Αυτά τα models είναι:
- Junction/Pivot models (part of parent)
- Configuration models (service-based)
- Internal models (used by other components)

---

## 🔍 Supporting Models

### ContentModuleAssignment

**Type**: Junction/Pivot Model  
**Location**: `app/Domain/Modules/Models/ContentModuleAssignment.php`

**Description**:  
Links Content to ModuleInstances in specific regions. Acts as a pivot table with additional metadata (region, sort_order).

**Access Pattern**:
- **Via**: `Admin\ContentModuleController`
- **View**: Embedded in `resources/views/admin/content/modules.blade.php`
- **Reason**: No standalone meaning — always part of Content → Modules relationship

**Why No Standalone Controller/View**:
- Always accessed in context of Content
- Managed through Content's module management UI
- No use case for standalone CRUD

**Example Usage**:
```php
// In ContentModuleController
$assignment = ContentModuleAssignment::create([
    'content_id' => $content->id,
    'module_instance_id' => $module->id,
    'region' => 'content',
    'sort_order' => 1,
]);
```

---

### Permission

**Type**: Configuration Model  
**Location**: `app/Domain/Auth/Models/Permission.php`

**Description**:  
Represents permissions in the RBAC system. Permissions are managed through Roles, not directly.

**Access Pattern**:
- **Via**: `RoleResource` (Filament)
- **View**: Filament handles (embedded in Role form)
- **Reason**: Permissions are assigned to Roles, not managed standalone

**Why No Standalone Controller/View**:
- Permissions are configuration, not user-managed content
- Managed through Role management UI
- No use case for standalone permission CRUD

**Example Usage**:
```php
// In RoleResource (Filament)
// Permissions shown as checkboxes in Role form
```

---

### Business

**Type**: Configuration Model  
**Location**: `app/Domain/Businesses/Models/Business.php`

**Description**:  
Represents a business/tenant in the multi-business system. Managed through settings, not direct CRUD.

**Access Pattern**:
- **Via**: Services (`GetBusinessSettingsService`, etc.)
- **View**: Settings pages, not direct CRUD
- **Reason**: Business is configuration/settings, not content

**Why No Standalone Controller/View**:
- Business settings managed through SettingsController
- Business creation is setup/installation task
- No use case for business CRUD in admin panel

**Example Usage**:
```php
// Via Service
$settings = app(GetBusinessSettingsService::class)->getThemeColors($business);
```

---

### Layout

**Type**: Configuration Model  
**Location**: `app/Domain/Layouts/Models/Layout.php`

**Description**:  
Represents page layouts with regions. Managed through services, not direct CRUD.

**Access Pattern**:
- **Via**: Services (`CreateLayoutService`, `GetLayoutService`)
- **View**: Used in Content forms (dropdown), not standalone
- **Reason**: Layouts are configuration, managed during content creation

**Why No Standalone Controller/View**:
- Layouts are created/configured during setup
- Managed through Content creation/editing
- No use case for standalone layout CRUD

**Example Usage**:
```php
// Via Service
$layout = app(GetLayoutService::class)->forBusiness($businessId, 'page');
```

---

### ContentType

**Type**: Configuration Model  
**Location**: `app/Domain/Content/Models/ContentType.php`

**Description**:  
Represents content type definitions (page, article, block). Currently config-based, not DB-managed.

**Access Pattern**:
- **Via**: Dropdowns in Content forms
- **View**: Used in Content create/edit forms
- **Reason**: Configuration, not user-managed content

**Why No Standalone Controller/View**:
- Content types are defined in config or seeders
- Not intended for user management
- If dynamic management needed → Create Filament Resource

**Decision**: Keep as config-based (no CRUD needed)

**Example Usage**:
```php
// In ContentController
$contentTypes = ContentType::all(); // Used in dropdown
```

---

### OrderItem

**Type**: Embedded Model  
**Location**: `app/Domain/Orders/Models/OrderItem.php`

**Description**:  
Represents items in an order. Part of Order, not standalone.

**Access Pattern**:
- **Via**: `Admin\OrderController`
- **View**: Embedded in `resources/views/admin/orders/show.blade.php`
- **Reason**: Always part of Order, no standalone meaning

**Why No Standalone Controller/View**:
- OrderItems are created with Order
- Viewed as part of Order details
- No use case for standalone OrderItem management

**Example Usage**:
```php
// In OrderController
$order->load('items'); // Load items with order
```

---

## 📊 Summary

| Model | Type | Access Via | Reason |
|-------|------|------------|--------|
| `ContentModuleAssignment` | Junction | ContentModuleController | Part of Content → Modules |
| `Permission` | Configuration | RoleResource | Managed through Roles |
| `Business` | Configuration | Services | Settings-based |
| `Layout` | Configuration | Services | Setup/configuration |
| `ContentType` | Configuration | Dropdowns | Config-based |
| `OrderItem` | Embedded | OrderController | Part of Order |

---

## 🎯 Guidelines for Future Models

### When a Model Doesn't Need Controller/View

1. **Junction/Pivot Models**
   - Always accessed in context of parent
   - No standalone meaning
   - Example: `ContentModuleAssignment`, `OrderItem`

2. **Configuration Models**
   - Managed through settings/services
   - Not user-managed content
   - Example: `Business`, `Layout`, `ContentType`

3. **Embedded Models**
   - Part of parent entity
   - No standalone CRUD needed
   - Example: `OrderItem`, `Permission` (via Roles)

4. **Internal Models**
   - Used by other components
   - Not exposed to admin UI
   - Example: Internal tracking models

### Decision Process

```
New Model?
├─ Standalone entity?
│  ├─ User needs to manage it? → Create Controller/View
│  └─ System-only? → Service-based
├─ Part of another entity?
│  └─ Junction/Pivot? → Embedded in parent
└─ Configuration?
   └─ Settings-based? → Service-based
```

---

## 📝 Notes

- **Supporting models** are documented, not implemented with Controllers/Views
- **Decision** για κάθε model is documented with reasoning
- **Future models** should follow these guidelines
- **If requirements change** (e.g., ContentType needs dynamic management), create Controller/View

---

**Last Updated**: 2025-01-27

