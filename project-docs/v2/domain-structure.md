# 📁 v2 Domain Structure Documentation

## Overview

Αυτό το έγγραφο περιγράφει την **complete domain structure** για το LaraShop v2, συμπεριλαμβανομένων των νέων modules (Content, Media, Settings, Auth/RBAC) και των υπάρχοντων (Catalog, Orders, Customers, Businesses).

---

## Complete Domain Structure

```
app/Domain/
├── Auth/                      # Authentication & Authorization (v2 RBAC)
│   ├── Models/
│   │   ├── Role.php
│   │   └── Permission.php
│   ├── Services/
│   │   ├── AssignRoleService.php
│   │   ├── RevokeRoleService.php
│   │   ├── CheckPermissionService.php
│   │   └── MigrateAdminToRolesService.php
│   ├── Policies/
│   │   └── (Authorization policies)
│   └── README.md
│
├── Businesses/                # Multi-business support (existing + v2)
│   ├── DTOs/
│   │   └── BusinessSettingsDTO.php
│   ├── Models/
│   │   └── Business.php
│   └── Services/
│       └── GetBusinessSettingsService.php
│
├── Catalog/                   # Products & Categories (existing)
│   ├── Models/
│   │   ├── Category.php
│   │   └── Product.php
│   ├── Policies/
│   │   ├── CategoryPolicy.php
│   │   └── ProductPolicy.php
│   └── Services/
│       ├── CreateProductService.php
│       ├── UpdateProductService.php
│       ├── DeleteProductService.php
│       ├── CreateCategoryService.php
│       ├── UpdateCategoryService.php
│       ├── DeleteCategoryService.php
│       ├── GetMenuForBusinessService.php
│       ├── GetActiveProductsService.php
│       └── ImageUploadService.php
│
├── Content/                   # CMS Content Module (v2) — NEW
│   ├── Models/
│   │   ├── Content.php
│   │   ├── ContentType.php
│   │   └── ContentRevision.php
│   ├── Services/
│   │   ├── CreateContentService.php
│   │   ├── UpdateContentService.php
│   │   ├── DeleteContentService.php
│   │   ├── GetContentService.php
│   │   ├── RenderContentService.php
│   │   ├── SaveRevisionService.php
│   │   └── BlockRegistry.php
│   ├── Policies/
│   │   └── ContentPolicy.php
│   ├── DTOs/
│   │   └── BlockPropsDTO.php
│   └── README.md
│
├── Customers/                 # Customer management (existing)
│   └── Models/
│       └── Customer.php
│
├── Media/                     # Media Library (v2) — NEW
│   ├── Models/
│   │   ├── Media.php
│   │   └── MediaFolder.php
│   ├── Services/
│   │   ├── UploadMediaService.php
│   │   ├── DeleteMediaService.php
│   │   ├── GenerateVariantsService.php
│   │   ├── MoveMediaService.php
│   │   └── GetMediaService.php
│   ├── Policies/
│   │   └── MediaPolicy.php
│   ├── Jobs/
│   │   └── GenerateImageVariantsJob.php
│   └── README.md
│
├── Orders/                    # Order processing (existing)
│   ├── Models/
│   │   ├── Order.php
│   │   └── OrderItem.php
│   └── Services/
│       ├── CreateOrderService.php
│       ├── CalculateOrderTotalService.php
│       ├── ValidateOrderService.php
│       └── ValidateBusinessOperatingHoursService.php
│
├── Settings/                  # Global Settings (v2) — NEW
│   ├── Models/
│   │   └── Setting.php
│   ├── Services/
│   │   ├── GetSettingsService.php
│   │   ├── UpdateSettingsService.php
│   │   └── ClearSettingsCacheService.php
│   └── README.md
│
└── CMS/                       # (Legacy — empty, will be deleted in Sprint 1)
```

---

## Domain Responsibilities

### Auth Domain (v2)

**Purpose**: Role-Based Access Control (RBAC)

**Key Components**:
- `Role` model — User roles (admin, editor, viewer, etc.)
- `Permission` model — Granular permissions (content.create, media.upload, etc.)
- Many-to-many relationships: `User ↔ Role`, `Role ↔ Permission`

**Services**:
- `AssignRoleService` — Assign role to user
- `RevokeRoleService` — Remove role from user
- `CheckPermissionService` — Verify user has permission
- `MigrateAdminToRolesService` — Migrate `is_admin` flag to roles

**Integration**: Used by all domains for authorization (Policies).

---

### Content Domain (v2)

**Purpose**: Block-based content management

**Key Components**:
- `Content` model — Pages, articles, blocks
- `ContentType` model — Dynamic content types
- `ContentRevision` model — Version history
- `BlockRegistry` — Block type registration

**Services**:
- `CreateContentService` — Create new content
- `UpdateContentService` — Update content
- `RenderContentService` — Render blocks to HTML
- `SaveRevisionService` — Save version snapshot

**Storage**: Hybrid — relational (metadata) + JSON (blocks)

---

### Media Domain (v2)

**Purpose**: Media file management

**Key Components**:
- `Media` model — Files (images, documents, videos)
- `MediaFolder` model — Folder structure
- Variant generation (thumb, small, medium, large)

**Services**:
- `UploadMediaService` — Handle file uploads
- `GenerateVariantsService` — Create image variants
- `DeleteMediaService` — Remove files and variants
- `MoveMediaService` — Organize files in folders

**Jobs**: `GenerateImageVariantsJob` — Async variant generation

---

### Settings Domain (v2)

**Purpose**: Global system settings

**Key Components**:
- `Setting` model — Key-value storage
- Settings types: string, boolean, json

**Services**:
- `GetSettingsService` — Retrieve settings (with caching)
- `UpdateSettingsService` — Update settings
- `ClearSettingsCacheService` — Cache invalidation

**Separation**: 
- **Global Settings** (this domain) — System-wide configuration
- **Business Settings** (Businesses domain) — Per-business configuration

---

### Catalog Domain (existing)

**Purpose**: Product and category management

**Status**: Maintained from v1, fully functional

**Integration**: Works with v2 Content system (products can reference media from Media domain).

---

### Orders Domain (existing)

**Purpose**: Order processing and management

**Status**: Maintained from v1, fully functional

**Integration**: Works with v2 (no breaking changes).

---

### Customers Domain (existing)

**Purpose**: Customer data management

**Status**: Maintained from v1, fully functional

---

### Businesses Domain (existing + v2)

**Purpose**: Multi-business support

**Status**: Maintained from v1, enhanced for v2

**v2 Changes**: 
- Integrates with RBAC (business-scoped permissions)
- Integrates with Content (per-business content)
- Integrates with Media (per-business media library)

---

## Domain Communication

### Service-to-Service Communication

Domains communicate through **services**, not direct model access:

```php
// ✅ CORRECT — Use service
$contentService = app(GetContentService::class);
$content = $contentService->bySlug($businessId, 'homepage');

// ❌ WRONG — Direct model access across domains
$content = Content::where('slug', 'homepage')->first();
```

### Shared Concepts

**Business ID**: All domains (except Settings) filter by `business_id`.

**User Context**: Authorization uses `User` model (shared across domains).

---

## Adding a New Domain

### 1. Create Domain Folder

```bash
mkdir -p app/Domain/YourDomain/{Models,Services,Policies}
```

### 2. Create Models

```php
namespace App\Domain\YourDomain\Models;

use Illuminate\Database\Eloquent\Model;

class YourModel extends Model
{
    protected $fillable = ['business_id', /* ... */];
    
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }
}
```

### 3. Create Services

```php
namespace App\Domain\YourDomain\Services;

class CreateYourModelService
{
    public function execute(Business $business, array $data): YourModel
    {
        // Business logic
    }
}
```

### 4. Create Policies (if needed)

```php
namespace App\Domain\YourDomain\Policies;

class YourModelPolicy
{
    public function create(User $user): bool
    {
        return $user->hasPermission('yourdomain.create');
    }
}
```

### 5. Create README.md

Document the domain's purpose, key components, and usage.

---

## Domain Isolation Principles

1. **No Cross-Domain Model Dependencies** — Use services for inter-domain communication
2. **Shared Infrastructure** — Business, User, Settings are shared concepts
3. **Clear Boundaries** — Each domain has its own folder, models, services
4. **Service Layer** — All business logic in services, not controllers

---

## References

- [v2 Overview](./v2_overview.md) — High-level architecture
- [Architecture Documentation](../architecture.md) — General architecture patterns
- [Conventions](../conventions.md) — Coding standards & conventions

---

**Last Updated**: 2024-11-27

