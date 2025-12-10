# 📏 Conventions & Coding Guidelines — LaraShop

## 1. Domain Structure

Κάθε domain έχει σταθερή δομή:

```
app/Domain/{DomainName}/
  ├── Models/           # Eloquent models
  ├── Services/         # Business logic
  ├── Actions/          # Single-purpose actions
  ├── Policies/         # Authorization
  ├── Repositories/     # Data access (optional)
  └── DTOs/             # Data Transfer Objects (optional)
```

---

## 2. Services vs Actions

| Type | Purpose | Example |
|------|---------|---------|
| **Service** | Complex business logic, multiple steps | `CreateOrderService` |
| **Action** | Single responsibility, one task | `CalculateOrderTotalAction` |

**Κανόνας**: Αν κάνει >1 πράγμα → Service. Αν κάνει ακριβώς 1 → Action.

---

## 3. Naming Conventions

### Files & Classes
| Type | Convention | Example |
|------|------------|---------|
| Model | Singular, PascalCase | `Product`, `OrderItem` |
| Controller | PascalCase + Controller | `ProductController` |
| Service | Verb + Noun + Service | `CreateProductService` |
| Action | Verb + Noun + Action | `CalculateTotalAction` |
| Policy | Model + Policy | `ProductPolicy` |
| Request | Verb + Model + Request | `StoreProductRequest` |
| Migration | Laravel default | `create_products_table` |

### Database
| Type | Convention | Example |
|------|------------|---------|
| Tables | plural, snake_case | `order_items` |
| Columns | snake_case | `created_at`, `business_id` |
| Foreign Keys | singular_table_id | `product_id` |
| Pivot Tables | alphabetical, singular | `order_product` |

### Routes
| Type | Convention | Example |
|------|------------|---------|
| Public | kebab-case | `/menu`, `/product-details` |
| Admin | `/admin/` prefix | `/admin/products` |
| API | `/api/v1/` prefix | `/api/v1/orders` |

---

## 4. Code Style

### PHP
- **PSR-12** standard
- Type hints παντού
- Return types παντού
- Strict types: `declare(strict_types=1);`

### Blade
- Components για επαναχρησιμοποίηση
- `x-` prefix για components
- Slots για flexible content

### CSS/Tailwind
- Utility-first approach
- Custom classes μόνο όταν χρειάζεται
- `@apply` για repeated patterns

---

## 5. Policies Location

```
app/Domain/{DomainName}/Policies/{Model}Policy.php
```

Register στο `AuthServiceProvider`:
```php
protected $policies = [
    Product::class => ProductPolicy::class,
];
```

---

## 6. Form Requests

```
app/Http/Requests/{Domain}/{Action}{Model}Request.php
```

Example: `app/Http/Requests/Catalog/StoreProductRequest.php`

---

## 7. Git Conventions

### Branches
- `main` — production
- `develop` — development
- `feature/xxx` — new features
- `fix/xxx` — bug fixes

### Commits
```
type(scope): message

feat(catalog): add product filtering
fix(orders): correct total calculation
docs(readme): update installation steps
```

---

## 8. Testing

```
tests/
  ├── Feature/
  │   └── {Domain}/
  │       └── {Feature}Test.php
  └── Unit/
      └── {Domain}/
          └── {Class}Test.php
```

---

## 9. Quick Reference

| Question | Answer |
|----------|--------|
| Πού μπαίνει business logic; | `Domain/{Name}/Services/` |
| Πού μπαίνει authorization; | `Domain/{Name}/Policies/` |
| Πού μπαίνει validation; | `Http/Requests/` |
| Πού μπαίνει single action; | `Domain/{Name}/Actions/` |
| Πού μπαίνει το model; | `Domain/{Name}/Models/` |

---

## 10. New Column Checklist

Όταν προσθέτεις νέο column στη βάση:

- [ ] Migration με σωστό type & default value
- [ ] Fillable στο model
- [ ] Cast (boolean, array, datetime, decimal, etc.)
- [ ] Accessor/Mutator αν χρειάζεται

> **Lesson Learned**: Ποτέ μην ξεχνάς το cast — χωρίς αυτό, booleans επιστρέφουν 0/1 αντί true/false.

---

## 11. Testing Before Delivery

### Κανόνας
**Κάθε feature πρέπει να τεστάρεται στο browser πριν το mark ως complete.**

### Checklist για Dev C (Frontend)
- [ ] `php artisan serve` και verify ότι η σελίδα φορτώνει
- [ ] Blade components με `$slot` δουλεύουν σωστά
- [ ] No Laravel/PHP errors στο browser
- [ ] Responsive check (mobile/desktop)

### Blade Components Best Practice

```php
// ❌ ΛΑΘΟΣ - @include δεν περνάει $slot
@include('layouts.public')

// ✅ ΣΩΣΤΟ - PHP Component Class
// app/View/Components/PublicLayout.php
class PublicLayout extends Component {
    public function render() {
        return view('layouts.public');
    }
}
```

> **Lesson Learned**: Ποτέ assumptions χωρίς verification. Always test!

---

## 11. Dependency Injection

### Κανόνας
**Χρήση Constructor Injection αντί για `app()` helper για services.**

### Γιατί;
- Το `app(ClassName::class)` δεν ενεργοποιεί IDE autocomplete
- Εύκολο να ξεχάσεις το `use` statement
- Constructor injection = explicit dependencies + testable code

```php
// ❌ ΛΑΘΟΣ - app() helper χωρίς IDE support
class UpdateProductService
{
    public function execute(Product $product, array $data): Product
    {
        $product->update($data);
        app(GetMenuForBusinessService::class)->clearCache($product->business);
        return $product;
    }
}

// ✅ ΣΩΣΤΟ - Constructor Injection
class UpdateProductService
{
    public function __construct(
        private GetMenuForBusinessService $menuService
    ) {}

    public function execute(Product $product, array $data): Product
    {
        $product->update($data);
        $this->menuService->clearCache($product->business);
        return $product;
    }
}
```

> **Lesson Learned**: Constructor injection = IDE autocomplete + explicit dependencies + easier testing.

---

## 12. Service Integration Checklist

Πριν καλέσεις ένα service:

- [ ] Διάβασε το method signature (parameters & types)
- [ ] Επιβεβαίωσε τη σειρά των arguments
- [ ] Έλεγξε τον return type

> **Lesson Learned**: Ποτέ μην υποθέτεις τα arguments ενός service. Πάντα διάβαζε το signature.

---

## 13. Cross-File Reference Checklist (Dev C)

Πριν χρησιμοποιήσεις routes ή model columns σε views:

### Routes
- [ ] Διάβασε το `routes/web.php` για το exact route name
- [ ] Τρέξε `php artisan route:list` για verification
- [ ] Πρόσεξε: camelCase vs kebab-case (`updateStatus` vs `update-status`)

### Model Columns
- [ ] Διάβασε το model ή migration για τα exact column names
- [ ] Μην υποθέτεις: `unit_price` vs `product_price` κλπ.

### Verification Commands
```bash
# List all routes with names
php artisan route:list --name=admin

# Check model columns
php artisan tinker
>>> Schema::getColumnListing('order_items')
```

> **Lesson Learned**: Assumptions = Bugs. Πάντα verify πριν χρησιμοποιήσεις routes/columns από άλλους devs.

---

## 14. Blade Template Logic Checklist (Dev C)

**Κρίσιμο**: Στο Blade, πάντα διάκρινε μεταξύ **key** (όνομα) και **value** (τιμή).

### Common Mistake: Key vs Value Confusion

**❌ Λάθος:**
```blade
{{ $setting->key === '1' ? 'Enabled' : 'Disabled' }}
```
Αυτό ελέγχει αν το **όνομα** του setting είναι '1', όχι την **τιμή**!

**✅ Σωστό:**
```blade
{{ ($settings[$setting->key] ?? false) ? 'Enabled' : 'Disabled' }}
```
Αυτό ελέγχει την **τιμή** του setting.

### Checklist για Blade Logic

Πριν γράψεις conditional logic σε Blade:

- [ ] **Διάκρισε key από value:**
  - `$setting->key` = όνομα του setting (π.χ. `'maintenance_mode'`)
  - `$settings[$setting->key]` = τιμή του setting (π.χ. `true`, `false`, `'value'`)
- [ ] **Για arrays/collections:**
  - `$settings[$key]` = value από array
  - `$setting->key` = property από model
- [ ] **Για boolean checks:**
  - Χρησιμοποίησε `($value ?? false)` για safe defaults
  - Μην ελέγχεις το key, πάντα το value
- [ ] **Test με different values:**
  - Test με `true`/`false` για booleans
  - Test με `null` values
  - Test με empty strings

### Verification Pattern

```blade
{{-- ❌ Λάθος --}}
{{ $item->key === '1' ? 'Yes' : 'No' }}

{{-- ✅ Σωστό --}}
{{ ($item->value ?? false) ? 'Yes' : 'No' }}

{{-- ✅ Σωστό (με array access) --}}
{{ ($data[$item->key] ?? false) ? 'Yes' : 'No' }}

{{-- ✅ Σωστό (με model property) --}}
{{ ($item->is_active ?? false) ? 'Active' : 'Inactive' }}
```

### Common Patterns

| Context | Key | Value | Example |
|---------|-----|-------|---------|
| Model property | `$product->id` | `$product->price` | `$product->name` |
| Array access | `$settings['key']` | `$settings[$key]` | `$settings['site_name']` |
| Collection | `$item->key` | `$item->value` | `$setting->key` vs `$settings[$setting->key]` |

> **Lesson Learned**: Key = όνομα/identifier, Value = δεδομένα. Ποτέ μην τα συγχέεις! Πάντα test με different values.

---

## 15. Admin Panel Architecture (v2) — Hybrid Filament/Blade

### Κανόνας
**Hybrid approach: Filament για standard CRUD, Blade για custom features.**

### Πότε Filament;
- ✅ Standard CRUD operations (Products, Categories, Orders, Users, Roles)
- ✅ List views με filters, search, pagination
- ✅ Form-based create/edit
- ✅ Bulk actions

### Πότε Blade;
- ✅ Custom content editor (block-based)
- ✅ Media library (drag & drop, folder tree)
- ✅ Dashboard widgets
- ✅ Plugin system UI
- ✅ Complex custom workflows

### File Structure

```
app/
├── Filament/
│   └── Resources/              # Filament Resources (CRUD)
│       ├── ProductResource.php
│       ├── CategoryResource.php
│       ├── OrderResource.php
│       ├── UserResource.php
│       └── RoleResource.php
└── Http/
    └── Controllers/
        └── Admin/              # Blade Controllers (Custom)
            ├── ContentController.php
            ├── MediaController.php
            └── DashboardController.php

resources/
├── views/
│   └── admin/                  # Blade views (Custom)
│       ├── content/
│       ├── media/
│       └── dashboard/
└── filament/                   # Filament customizations
    └── resources/
        └── views/              # Override Filament views (if needed)
```

### Routing

- **Filament routes**: Auto-registered at `/admin/*` (via Filament panel)
- **Blade routes**: Manual registration in `routes/web.php`:
  ```php
  Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
      Route::get('/content', [ContentController::class, 'index']);
      Route::get('/media', [MediaController::class, 'index']);
      Route::get('/dashboard', [DashboardController::class, 'index']);
  });
  ```

### Filament Resource Naming

```php
// app/Filament/Resources/ProductResource.php
namespace App\Filament\Resources;

use Filament\Resources\Resource;
use App\Domain\Catalog\Models\Product;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    // ...
}
```

### Integration Checklist

Όταν προσθέτεις νέο admin feature:

- [ ] **Είναι standard CRUD?** → Use Filament Resource
- [ ] **Είναι custom UI?** → Use Blade Controller + View
- [ ] **Filament Resource:**
  - [ ] Create `app/Filament/Resources/{Model}Resource.php`
  - [ ] Register in Filament panel
  - [ ] Use Policies for authorization
- [ ] **Blade Controller:**
  - [ ] Create `app/Http/Controllers/Admin/{Name}Controller.php`
  - [ ] Add route in `routes/web.php`
  - [ ] Create view in `resources/views/admin/{name}/`
  - [ ] Use existing admin layout

### Authorization

- **Filament**: Uses Policies (same as Blade)
- **Blade**: Uses Policies via `authorize()` in controllers

```php
// Both use the same Policy
public function index()
{
    $this->authorize('viewAny', Product::class);
    // ...
}
```

> **Lesson Learned**: Filament = fast CRUD, Blade = full control. Choose based on complexity.

---

## 15. Service Layer Pattern (Detailed)

### Κανόνας
**Services handle business logic. Controllers are thin, only routing and response.**

### Service Structure

**No Base Service Class** — Direct service classes:
```php
// app/Domain/Catalog/Services/CreateProductService.php
namespace App\Domain\Catalog\Services;

use App\Domain\Catalog\Models\Product;

class CreateProductService
{
    public function __construct(
        private GetMenuForBusinessService $menuService
    ) {}

    public function execute(Business $business, array $data): Product
    {
        // Business logic here
        $product = Product::create([...]);
        $this->menuService->clearCache($business);
        return $product;
    }
}
```

### Service Naming Conventions

| Pattern | Example | Use Case |
|---------|---------|----------|
| `Create{Model}Service` | `CreateProductService` | Create operations |
| `Update{Model}Service` | `UpdateProductService` | Update operations |
| `Delete{Model}Service` | `DeleteProductService` | Delete operations |
| `Get{Model}Service` | `GetContentService` | Read operations (single) |
| `Get{Plural}Service` | `GetActiveProductsService` | Read operations (multiple) |
| `{Action}{Model}Service` | `CalculateOrderTotalService` | Complex operations |

### Service Method Signature

**Standard method name**: `execute()`
```php
public function execute([params]): [return type]
```

**Return Types:**
- Single model: `Product`, `Content`, `Order`
- Collections: `Collection<Product>`, `Collection<Content>`
- Arrays: `array` (for DTOs or complex data)
- Booleans: `bool` (for validation services)
- Void: `void` (for side effects only)

### Error Handling in Services

**Use Exceptions for errors:**
```php
// ✅ ΣΩΣΤΟ - Throw exceptions
public function execute(Business $business, array $data): Product
{
    if (!$business->isActive()) {
        throw new BusinessNotActiveException("Business is not active");
    }
    // ...
}

// ❌ ΛΑΘΟΣ - Return null/errors
public function execute(...): ?Product
{
    if ($error) {
        return null; // Don't do this
    }
}
```

**Custom Exceptions:**
- Location: `app/Domain/{Domain}/Exceptions/`
- Naming: `{Domain}Exception` (e.g., `ContentNotFoundException`)

### Transaction Handling

**Use DB transactions for multi-step operations:**
```php
use Illuminate\Support\Facades\DB;

public function execute(...): Order
{
    return DB::transaction(function () {
        $order = Order::create([...]);
        foreach ($items as $item) {
            OrderItem::create([...]);
        }
        return $order;
    });
}
```

### Service Dependencies

**Constructor Injection only:**
```php
// ✅ ΣΩΣΤΟ
public function __construct(
    private GetMenuForBusinessService $menuService,
    private ImageUploadService $imageService
) {}

// ❌ ΛΑΘΟΣ
public function execute(...)
{
    $service = app(GetMenuForBusinessService::class); // Don't do this
}
```

---

## 15.5. Hardcoded Values Prevention

**⚠️ IMPORTANT**: Never use hardcoded values in business logic.

### Common Hardcoded Values to Avoid

**❌ Wrong:**
```php
// Hardcoded user ID
'created_by' => 1

// Hardcoded business ID
'business_id' => 1

// Hardcoded URLs
'url' => 'https://example.com'

// Hardcoded file paths
$path = '/storage/uploads';
```

**✅ Correct:**
```php
// Dynamic user ID
'created_by' => auth()->id()
// Or: User::where('is_admin', true)->first()->id

// Dynamic business ID
'business_id' => Business::active()->firstOrFail()->id

// Dynamic URLs
'url' => url('/path')
// Or: route('route.name')

// Dynamic file paths
$path = Storage::disk('public')->path('uploads');
```

### When Hardcoded Values Are OK

- **Migrations/Seeders**: Data values can be hardcoded (they're data, not logic)
- **Constants**: Use class constants for magic values
- **Config Defaults**: Default values in config files

### Verification Checklist

Before committing:
- [ ] No hardcoded user IDs (use `auth()->id()` or get from DB)
- [ ] No hardcoded business IDs (get from context)
- [ ] No hardcoded URLs (use `url()`, `route()`, `config()`)
- [ ] No hardcoded file paths (use `Storage`, `config()`)
- [ ] Magic numbers replaced with constants or config

**See**: `project-docs/v2/dev-responsibilities.md` for detailed checklist.

---

## 16. API Response Format

**⚠️ IMPORTANT**: Always use `BaseController` helper methods for API responses.

### Paginated Responses

**✅ Correct:**
```php
$items = $query->paginate(15);
$items->setCollection(Resource::collection($items->items())->collection);
return $this->paginated($items, 'Data retrieved successfully');
```

**❌ Wrong:**
```php
// DON'T create manual JSON for paginated responses
return response()->json([
    'success' => true,
    'data' => $items,
    'meta' => [...], // Manual pagination data
]);
```

### Single Resource Responses

**✅ Correct:**
```php
return $this->success(new Resource($model), 'Data retrieved successfully');
```

### Error Responses

**✅ Correct:**
```php
return $this->error('Error message', $errors, 404);
```

**See**: `project-docs/v2/api_response_patterns.md` for complete guide.

### Standard Response Structure

**Success Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "Product Name"
  },
  "message": "Operation successful"
}
```

**Error Response:**
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "email": ["The email field is required."]
  }
}
```

**Pagination Response:**
```json
{
  "success": true,
  "data": [...],
  "meta": {
    "current_page": 1,
    "per_page": 15,
    "total": 100,
    "last_page": 7
  },
  "links": {
    "first": "...",
    "last": "...",
    "prev": null,
    "next": "..."
  }
}
```

### HTTP Status Codes

| Code | Use Case |
|------|----------|
| `200` | Success (GET, PUT, PATCH) |
| `201` | Created (POST) |
| `204` | No Content (DELETE) |
| `400` | Bad Request (validation errors) |
| `401` | Unauthorized (not authenticated) |
| `403` | Forbidden (no permission) |
| `404` | Not Found |
| `422` | Unprocessable Entity (validation) |
| `429` | Too Many Requests (rate limit) |
| `500` | Server Error |

### API Resources

**Use Laravel API Resources for consistent formatting:**
```php
// app/Http/Resources/ProductResource.php
class ProductResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'price' => $this->price,
            'category' => new CategoryResource($this->category),
        ];
    }
}
```

---

## 17. Caching Conventions

### Cache Key Naming

**Pattern**: `{domain}:{identifier}:{subkey}`

```php
// Examples
'business:1:menu'
'business:1:settings'
'content:1:rendered'
'media:1:variants'
```

### Cache Tags (if using Redis)

```php
Cache::tags(['business:1', 'menu'])->put('business:1:menu', $data);
Cache::tags(['business:1', 'menu'])->flush(); // Clear all menu caches for business
```

### Cache TTL

| Data Type | TTL | Reason |
|-----------|-----|--------|
| Menu data | 30 minutes | Changes infrequently |
| Settings | 1 hour | Rarely changes |
| Content (rendered) | 15 minutes | May change |
| Media variants | Forever | Never changes |
| API responses | 5 minutes | Balance freshness/performance |

### Cache Invalidation

**Automatic on model events:**
```php
// In model
protected static function booted()
{
    static::updated(function ($product) {
        Cache::tags(['business:' . $product->business_id, 'menu'])->flush();
    });
}
```

**Manual invalidation:**
```php
// In service
public function clearCache(Business $business): void
{
    Cache::forget("business:{$business->id}:menu");
}
```

---

## 18. File Storage Conventions

### Storage Disks

| Disk | Use Case | Location |
|------|----------|----------|
| `public` | Public files (images, media) | `storage/app/public` |
| `local` | Private files (exports, backups) | `storage/app` |
| `s3` | Production (cloud storage) | AWS S3 |

### File Naming

**Pattern**: `{uuid}.{extension}` or `{timestamp}-{slug}.{extension}`

```php
// UUID-based (recommended for media)
$filename = Str::uuid() . '.' . $file->extension();

// Timestamp-based (for exports)
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

- **Public files**: `0644` (readable by all)
- **Private files**: `0600` (owner only)

---

## 19. Database Conventions

### Soft Deletes

**Use when:**
- Data might need restoration
- Audit trail required
- Data referenced by other tables

**Example:**
```php
// Migration
$table->softDeletes();

// Model
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;
}
```

### Timestamps

**Always use:**
- `created_at` — Auto-managed
- `updated_at` — Auto-managed
- `deleted_at` — For soft deletes
- `published_at` — For content publishing

### Index Naming

**Pattern**: `idx_{table}_{columns}`

```php
// Examples
$table->index(['business_id', 'slug'], 'idx_contents_business_slug');
$table->index(['status', 'published_at'], 'idx_contents_status_published');
```

### Foreign Key Naming

**Pattern**: `fk_{table}_{referenced_table}`

```php
$table->foreign('business_id', 'fk_contents_businesses')
    ->references('id')
    ->on('businesses')
    ->onDelete('cascade');
```

---

## 20. Events & Listeners

### When to Use Events

**Use Events for:**
- Side effects (emails, notifications)
- Cache invalidation
- Logging
- Integration with external services

**Don't use Events for:**
- Core business logic (use Services)
- Direct responses to user actions

### Event Naming

**Pattern**: `{Model}{Action}` (past tense)

```php
// Examples
ContentCreated::class
OrderPlaced::class
ProductUpdated::class
MediaUploaded::class
```

### Event Structure

```php
// app/Domain/Content/Events/ContentCreated.php
class ContentCreated
{
    public function __construct(
        public Content $content
    ) {}
}
```

### Listener Structure

```php
// app/Domain/Content/Listeners/ClearContentCache.php
class ClearContentCache
{
    public function handle(ContentCreated $event): void
    {
        Cache::forget("business:{$event->content->business_id}:content");
    }
}
```

### Async Listeners

**Use queues for heavy operations:**
```php
// In EventServiceProvider
protected $listen = [
    ContentCreated::class => [
        SendContentNotification::class, // Sync
        GenerateContentPreview::class => 'queue', // Async
    ],
];
```

---

## 21. Jobs & Queues

### When to Use Queues

**Use Queues for:**
- Image processing
- Email sending
- Heavy computations
- External API calls
- Report generation

**Don't use Queues for:**
- Immediate user feedback
- Critical business logic
- Database transactions (unless designed for it)

### Job Naming

**Pattern**: `{Action}{Model}Job`

```php
// Examples
GenerateImageVariantsJob::class
SendOrderConfirmationJob::class
ProcessMediaUploadJob::class
```

### Job Structure

```php
// app/Jobs/GenerateImageVariantsJob.php
class GenerateImageVariantsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public Media $media
    ) {}

    public function handle(GenerateVariantsService $service): void
    {
        $service->execute($this->media);
    }
}
```

### Queue Configuration

**Default queue**: `default`
**Failed jobs**: Store in database, retry 3 times

---

## 22. Exception Handling

### Custom Exceptions

**Location**: `app/Domain/{Domain}/Exceptions/`

**Naming**: `{Domain}{Type}Exception`

```php
// app/Domain/Content/Exceptions/ContentNotFoundException.php
class ContentNotFoundException extends Exception
{
    public function __construct(string $slug)
    {
        parent::__construct("Content with slug '{$slug}' not found.");
    }
}
```

### Exception Handling in Controllers

```php
// In controller
try {
    $content = $this->getContentService->bySlug($businessId, $slug);
} catch (ContentNotFoundException $e) {
    return response()->json([
        'success' => false,
        'message' => $e->getMessage()
    ], 404);
}
```

### Logging

**Log levels:**
- `error` — Critical errors
- `warning` — Warnings
- `info` — Important events
- `debug` — Debug information

```php
Log::error('Content not found', [
    'business_id' => $businessId,
    'slug' => $slug,
    'user_id' => auth()->id(),
]);
```

### User-Friendly Error Messages

**Don't expose internal errors:**
```php
// ❌ ΛΑΘΟΣ
return response()->json(['error' => $exception->getMessage()], 500);

// ✅ ΣΩΣΤΟ
Log::error('Internal error', ['exception' => $exception]);
return response()->json(['error' => 'An error occurred'], 500);
```

---

## 23. Block System Conventions

### Block Registration

**Location**: `app/Domain/Content/Services/BlockRegistry.php`

```php
// Register blocks
BlockRegistry::register('hero', HeroBlock::class);
BlockRegistry::register('text', TextBlock::class);
```

### Block Structure

**JSON Format:**
```json
{
  "type": "hero",
  "props": {
    "title": "Welcome",
    "image_id": 1,
    "cta_text": "Order Now"
  }
}
```

### Block Validation

**Validate in Form Request:**
```php
// In StoreContentRequest
'body_json' => ['required', 'array'],
'body_json.*.type' => ['required', 'string', Rule::in(BlockRegistry::getTypes())],
'body_json.*.props' => ['required', 'array'],
```

### Block Rendering

**Theme Resolution:**
1. `themes/{business->theme}/blocks/{type}.blade.php`
2. `themes/default/blocks/{type}.blade.php`
3. Fallback message if not found

### Block Props Type Safety

**Use DTOs for block props (optional):**
```php
// app/Domain/Content/DTOs/HeroBlockProps.php
class HeroBlockProps
{
    public function __construct(
        public string $title,
        public ?int $imageId = null,
        public ?string $ctaText = null,
    ) {}
}
```

---

## 24. Testing Conventions

### Test Naming

**Pattern**: `{Action}{Model}Test` or `{Feature}Test`

```php
// Examples
CreateProductTest::class
ViewMenuTest::class
ContentEditorTest::class
```

### Test Structure

```php
// tests/Feature/Catalog/CreateProductTest.php
class CreateProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_product(): void
    {
        // Arrange
        $business = Business::factory()->create();
        
        // Act
        $response = $this->post('/admin/products', [...]);
        
        // Assert
        $response->assertStatus(201);
        $this->assertDatabaseHas('products', [...]);
    }
}
```

### Test Data

**Use Factories:**
```php
// database/factories/ProductFactory.php
Product::factory()->create(['business_id' => $business->id]);
```

**Use Seeders for complex data:**
```php
$this->seed(BusinessSeeder::class);
```

### Feature vs Unit Tests

| Type | Location | Use Case |
|------|----------|----------|
| **Feature** | `tests/Feature/` | Full HTTP requests, database |
| **Unit** | `tests/Unit/` | Service logic, no database |

---

## 25. Validation Conventions

### Form Request Location

```
app/Http/Requests/
├── Catalog/
│   ├── StoreProductRequest.php
│   └── UpdateProductRequest.php
├── Content/
│   ├── StoreContentRequest.php
│   └── UpdateContentRequest.php
```

### Custom Validation Rules

**Location**: `app/Rules/`

```php
// app/Rules/UniqueSlugPerBusiness.php
class UniqueSlugPerBusiness implements Rule
{
    public function passes($attribute, $value): bool
    {
        // Validation logic
    }
}
```

### Error Messages

**Localization**: `resources/lang/{locale}/validation.php`

**Custom messages in Form Request:**
```php
public function messages(): array
{
    return [
        'slug.unique' => 'This slug is already taken for this business.',
    ];
}
```

---

## 26. Pre-Commit Checklist (Enhanced)

### Service Integration Verification
- [ ] **Read service method signature** before calling
- [ ] **Verify method name** (execute, get, create, etc.)
- [ ] **Check parameter order** and types
- [ ] **Verify return type**
- [ ] **Test service call** with actual data

### Model Verification
- [ ] **Check fillable fields** match migration
- [ ] **Verify casts** — no unnecessary casts (e.g., `'value' => 'array'` when stored as string)
- [ ] **Check relationships** exist in both models
- [ ] **Verify scopes** use correct column names

### Cache Implementation
- [ ] **Tags consistency**: If using `Cache::tags()->flush()`, use tags in `remember()` too
- [ ] **Cache keys**: Follow naming pattern `{domain}:{identifier}:{subkey}`
- [ ] **Invalidation**: Clear cache after updates
- [ ] **TTL**: Set appropriate TTL values

### Dependencies & Packages
- [ ] **Package installed**: Verify package exists in `composer.json` before using
- [ ] **Middleware available**: Check middleware registered in `bootstrap/app.php`
- [ ] **Config exists**: Verify config file exists before using `config()`
- [ ] **Facade imported**: Check `use` statements

### Database & Migrations
- [ ] **Migration naming**: Use consistent prefix (`v2_` if applicable)
- [ ] **Column types**: Match migration with model fillable
- [ ] **Foreign keys**: Verify FK constraints exist
- [ ] **Indexes**: Add indexes for frequently queried columns

> **Lesson Learned from Sprint 0**: Assumptions = Bugs. Always verify:
> - Service method signatures
> - Model casts vs actual storage
> - Cache tag consistency
> - Package availability
> - Dependency existence

### Root Cause Analysis (Sprint 0 Bugs)

**Why did these bugs happen?**

1. **Service Method Mismatch**:
   - **Root Cause**: Assumed standard naming (`update()`) instead of reading service file
   - **Why**: Multiple tasks in parallel, rush to complete
   - **Prevention**: Always read service method signature first

2. **Model Cast Error**:
   - **Root Cause**: Assumed `value` should be cast as array because it's JSON-like
   - **Why**: Didn't check how value is actually stored in DB (string)
   - **Prevention**: Check migration first, then decide on casts

3. **Cache Tags Inconsistency**:
   - **Root Cause**: Incomplete implementation — added tags to flush but forgot remember
   - **Why**: Incremental development, didn't review full implementation
   - **Prevention**: Implement cache read/write/invalidation together

4. **Value Casting Missing Null Check**:
   - **Root Cause**: Assumed `getRawOriginal()` always returns value
   - **Why**: Didn't consider edge cases
   - **Prevention**: Always add null checks for methods that might return null

5. **Sanctum Missing**:
   - **Root Cause**: Used standard Laravel pattern (`auth:sanctum`) without checking if package installed
   - **Why**: Assumed package would be available
   - **Prevention**: Check `composer.json` before using package features

**Common Pattern**: All bugs came from **assumptions** instead of **verification**.

---

## 27. Service Integration Deep Dive

### Before Using a Service

1. **Read the Service File**:
   ```bash
   # Always read the service before calling it
   cat app/Domain/Settings/Services/UpdateSettingsService.php
   ```

2. **Check Method Signature**:
   ```php
   // ✅ VERIFY this before calling
   public function execute(string $key, mixed $value, string $type = 'string', string $group = 'general'): Setting
   ```

3. **Test the Call**:
   ```php
   // Test with actual data
   $service->execute('test_key', 'test_value', 'string', 'general');
   ```

### Common Mistakes to Avoid

❌ **Mistake 1**: Assuming method name
```php
// ❌ WRONG - Assuming it's called 'update'
$service->update($key, $value);

// ✅ CORRECT - Read service first
$service->execute($key, $value);
```

❌ **Mistake 2**: Wrong cast in model
```php
// ❌ WRONG - Value stored as string, not array
protected $casts = ['value' => 'array'];

// ✅ CORRECT - No cast, casting happens in service
protected $fillable = ['key', 'value', 'type'];
```

❌ **Mistake 3**: Cache tags inconsistency
```php
// ❌ WRONG - Tags in flush but not in remember
Cache::remember('key', ...);
Cache::tags(['settings'])->flush();

// ✅ CORRECT - Tags in both
Cache::tags(['settings'])->remember('key', ...);
Cache::tags(['settings'])->flush();
```

---

**Last Updated**: 2024-11-27

