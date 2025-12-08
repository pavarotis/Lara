# 🏗 Architecture Documentation — LaraShop

## Overview

LaraShop follows a **Domain-Driven Design (DDD)** approach with a modular monolith architecture. Each domain is self-contained with its own models, services, and policies.

**v2 Evolution**: The platform is evolving from an e-commerce focus to a **CMS-first architecture** while maintaining backward compatibility with existing e-commerce features.

**Key v2 Features:**
- Block-based content system
- Multi-business support (maintained)
- RBAC (Roles & Permissions)
- Media library with variants
- Headless API support
- Plugin system

---

## Domain Structure

```
app/Domain/
├── Auth/                    # Authentication (uses Laravel Breeze)
├── Businesses/              # Multi-business support
│   ├── DTOs/
│   │   └── BusinessSettingsDTO.php
│   ├── Models/
│   │   └── Business.php
│   └── Services/
│       └── GetBusinessSettingsService.php
├── Catalog/                 # Products & Categories
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
├── Customers/               # Customer management
│   └── Models/
│       └── Customer.php
├── Orders/                  # Order processing
│   ├── Models/
│   │   ├── Order.php
│   │   └── OrderItem.php
│   └── Services/
│       ├── CreateOrderService.php
│       ├── CalculateOrderTotalService.php
│       ├── ValidateOrderService.php
│       └── ValidateBusinessOperatingHoursService.php
├── Content/                  # Content management (v2) — Pages, Blocks, ContentTypes
├── Media/                    # Media library (v2) — Files, Folders, Variants
├── Settings/                 # Global settings (v2) — System-wide configuration
└── CMS/                      # (Legacy — will be deleted in Sprint 1)
```

---

## Key Architectural Decisions

### 1. Domain Separation
Each domain is independent and communicates through well-defined interfaces (services).

### 2. Service Layer Pattern
- **Services** handle business logic
- **Controllers** are thin, only routing and response
- **Models** are data containers with relationships

### 3. Constructor Injection
All dependencies are injected via constructor for testability:
```php
public function __construct(
    private GetMenuForBusinessService $menuService
) {}
```

### 4. Multi-Business Support
Every entity belongs to a `Business`:
- All queries filter by `business_id`
- `SetCurrentBusiness` middleware resolves current business
- Business settings stored as JSON for flexibility

---

## Data Flow

```
Request → Middleware → Controller → Service → Model → Database
                                      ↓
                              Response ← View
```

### Example: Creating an Order

1. `CheckoutController` receives POST request
2. Validates via `FormRequest`
3. Calls `ValidateOrderService` for business rules
4. Calls `CreateOrderService` which:
   - Creates/finds Customer
   - Calculates totals via `CalculateOrderTotalService`
   - Creates Order and OrderItems in transaction
5. Returns Order to controller
6. Controller redirects to success page

---

## Adding New Business Types

1. **Add type to migration enum** (if not exists):
   ```php
   $table->enum('type', ['cafe', 'gas_station', 'salon', 'bakery', 'restaurant', 'YOUR_TYPE']);
   ```

2. **Add default settings** in `GetBusinessSettingsService`:
   ```php
   'your_type' => [
       'color_theme' => 'modern',
       'delivery_enabled' => true,
   ],
   ```

3. **Create seeder** (optional):
   ```php
   class YourTypeSeeder extends Seeder
   {
       public function run(): void
       {
           $business = Business::create([...]);
           // Add categories and products
       }
   }
   ```

4. **Add theme colors** (optional) in `GetBusinessSettingsService::getAvailableThemes()`

---

## Adding New Modules

### 1. Create Domain Folder
```
app/Domain/YourModule/
├── Models/
├── Services/
├── Policies/
└── DTOs/
```

### 2. Create Migration
```bash
php artisan make:migration create_your_table
```

### 3. Create Model
```php
namespace App\Domain\YourModule\Models;

class YourModel extends Model
{
    protected $fillable = [...];
    
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }
}
```

### 4. Create Service(s)
Follow single-responsibility principle:
- One service per action (Create, Update, Delete)
- Or one service for related read operations

### 5. Create Policy (if needed)
```php
namespace App\Domain\YourModule\Policies;

class YourModelPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->is_admin;
    }
}
```

### 6. Register in AuthServiceProvider
```php
protected $policies = [
    YourModel::class => YourModelPolicy::class,
];
```

---

## Service Layer Pattern (Detailed)

### Principles

1. **Services contain business logic**
2. **Controllers are thin** — only routing and response formatting
3. **No base service class** — direct service classes
4. **Constructor injection** for all dependencies
5. **Single responsibility** — one service per operation or related operations

### Service Structure

```
app/Domain/{Domain}/Services/
├── Create{Model}Service.php
├── Update{Model}Service.php
├── Delete{Model}Service.php
├── Get{Model}Service.php
└── {Action}{Model}Service.php
```

### Service Method Signature

**Standard method**: `execute()`
```php
public function execute([Business $business, array $data]): Model|Collection|array
```

### Error Handling

**Use exceptions for errors:**
- Custom exceptions in `app/Domain/{Domain}/Exceptions/`
- Throw exceptions, don't return null/errors
- Catch in controllers and format responses

### Transaction Handling

**Use DB transactions for multi-step operations:**
```php
return DB::transaction(function () {
    // Multiple database operations
});
```

---

## Caching Strategy (Detailed)

### Cache Keys

**Pattern**: `{domain}:{identifier}:{subkey}`

Examples:
- `business:1:menu`
- `business:1:settings`
- `content:1:rendered`
- `media:1:variants`

### Cache TTL

| Data Type | TTL | Invalidation |
|-----------|-----|--------------|
| Menu data | 30 minutes | On product/category update |
| Settings | 1 hour | On settings update |
| Content (rendered) | 15 minutes | On content update |
| Media variants | Forever | Never (immutable) |
| API responses | 5 minutes | Manual or TTL |

### Cache Invalidation

**Automatic (Model Events):**
```php
// In model
protected static function booted()
{
    static::updated(function ($model) {
        Cache::tags(['business:' . $model->business_id])->flush();
    });
}
```

**Manual (Services):**
```php
// In service
public function clearCache(Business $business): void
{
    Cache::forget("business:{$business->id}:menu");
}
```

### Cache Tags (Redis)

**Use tags for grouped invalidation:**
```php
Cache::tags(['business:1', 'menu'])->put('business:1:menu', $data);
Cache::tags(['business:1', 'menu'])->flush(); // Clear all menu caches
```

---

## File Storage Architecture

### Storage Disks

| Disk | Use Case | Path |
|------|----------|------|
| `public` | Public files (media, images) | `storage/app/public` |
| `local` | Private files (exports) | `storage/app` |
| `s3` | Production (cloud) | AWS S3 |

### File Naming

**UUID-based (recommended):**
```php
$filename = Str::uuid() . '.' . $file->extension();
```

**Timestamp-based (for exports):**
```php
$filename = now()->timestamp . '-' . Str::slug($name) . '.' . $extension;
```

### Path Structure

```
storage/app/public/
├── media/
│   └── {business_id}/
│       └── {year}/
│           └── {month}/
│               └── {filename}
├── products/
│   └── {business_id}/
│       └── {filename}
└── exports/
    └── {user_id}/
        └── {filename}
```

### File Permissions

- **Public**: `0644` (readable by all)
- **Private**: `0600` (owner only)

### Media Variants

**Generated on upload:**
- `thumb` (150x150)
- `small` (400x400)
- `medium` (800x800)
- `large` (1200x1200)

**Stored in**: `storage/app/public/media/{business_id}/variants/`

---

## Event-Driven Architecture

### When to Use Events

**Use Events for:**
- Side effects (emails, notifications)
- Cache invalidation
- Logging
- External integrations

**Don't use Events for:**
- Core business logic (use Services)
- Direct user responses

### Event Structure

```
app/Domain/{Domain}/Events/
├── {Model}Created.php
├── {Model}Updated.php
└── {Model}Deleted.php
```

### Listener Structure

```
app/Domain/{Domain}/Listeners/
├── ClearCache.php
├── SendNotification.php
└── LogActivity.php
```

### Async Listeners

**Use queues for heavy operations:**
```php
// In EventServiceProvider
protected $listen = [
    ContentCreated::class => [
        ClearContentCache::class, // Sync
        SendContentNotification::class => 'queue', // Async
    ],
];
```

### Event Flow

```
Controller → Service → Model (Event Fired)
                            ↓
                    Event Dispatcher
                            ↓
                    Listeners (Sync/Queue)
```

---

## Caching Strategy

- **Menu data**: Cached for 30 minutes per business
- **Cache invalidation**: Automatic on product/category update/delete
- **Production**: Use `php artisan app:optimize-production`

---

## Testing Strategy

```
tests/
├── Feature/
│   ├── Catalog/
│   │   └── ViewMenuTest.php
│   ├── Orders/
│   │   └── CreateOrderTest.php
│   └── Admin/
│       └── ProductCrudTest.php
└── Unit/
    └── (domain-specific unit tests)
```

Run tests:
```bash
php artisan test
php artisan test --filter=ViewMenuTest
```

---

## Deployment Checklist

1. `composer install --optimize-autoloader --no-dev`
2. `php artisan app:optimize-production`
3. `php artisan migrate --force`
4. `php artisan storage:link`
5. Set `APP_ENV=production` and `APP_DEBUG=false`

---

## Admin Panel Architecture (v2)

### Hybrid Approach: Filament + Blade

**Strategy**: Use the right tool for each feature.

#### Filament (Standard CRUD)
- **Location**: `app/Filament/Resources/`
- **Use for**: Products, Categories, Orders, Users, Roles
- **Benefits**: Fast development, built-in filters/actions, consistent UI
- **Routing**: Auto-registered at `/admin/*`

#### Blade (Custom Features)
- **Location**: `app/Http/Controllers/Admin/` + `resources/views/admin/`
- **Use for**: Content Editor, Media Library, Dashboard, Plugins
- **Benefits**: Full control, custom UI/UX, extensibility
- **Routing**: Manual registration in `routes/web.php`

### File Structure

```
app/
├── Filament/
│   └── Resources/              # Filament CRUD
│       ├── ProductResource.php
│       ├── CategoryResource.php
│       └── OrderResource.php
└── Http/
    └── Controllers/
        └── Admin/              # Blade Controllers
            ├── ContentController.php
            └── MediaController.php

resources/
├── views/
│   └── admin/                  # Blade views
│       ├── content/
│       └── media/
└── filament/                   # Filament customizations
    └── resources/
        └── views/
```

### Decision Tree

```
New Admin Feature
    │
    ├─ Is it standard CRUD? ──YES──> Use Filament Resource
    │
    └─ NO (custom UI/workflow) ──> Use Blade Controller + View
```

### Authorization

Both Filament and Blade use the same **Policies**:

```php
// app/Domain/Catalog/Policies/ProductPolicy.php
class ProductPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('products.view');
    }
}
```

- **Filament**: Automatically uses Policies
- **Blade**: Manual `$this->authorize()` in controllers

---

## Quick Reference

| Need | Location |
|------|----------|
| Business logic | `Domain/{Name}/Services/` |
| Data models | `Domain/{Name}/Models/` |
| Authorization | `Domain/{Name}/Policies/` |
| Validation | `Http/Requests/` |
| Controllers | `Http/Controllers/` |
| Views | `resources/views/` |
| Routes | `routes/web.php` |
| **Filament Resources** | `app/Filament/Resources/` |
| **Admin Blade Views** | `resources/views/admin/` |
| **Services** | `Domain/{Name}/Services/` |
| **Events** | `Domain/{Name}/Events/` |
| **Listeners** | `Domain/{Name}/Listeners/` |
| **Jobs** | `app/Jobs/` |
| **Exceptions** | `Domain/{Name}/Exceptions/` |

