# 🏗 Architecture Documentation — LaraShop

## Overview

LaraShop follows a **Domain-Driven Design (DDD)** approach with a modular monolith architecture. Each domain is self-contained with its own models, services, and policies.

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
└── CMS/                     # Content management (future)
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

