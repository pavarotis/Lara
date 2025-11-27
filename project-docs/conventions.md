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

## 10. Testing Before Delivery

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

