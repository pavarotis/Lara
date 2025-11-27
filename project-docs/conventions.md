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

