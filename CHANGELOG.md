# 📋 Changelog

Όλες οι αλλαγές του project καταγράφονται εδώ.

---

## [Unreleased]

---

## Sprint 0 — Review & Fixes (Master DEV)

### Fixes (2024-11-27)
- [x] **BUG FIX**: Component wrappers (`public-layout.blade.php`, `admin-layout.blade.php`) χρησιμοποιούσαν `@include` αντί για proper Blade component classes
  - Δημιουργήθηκαν `app/View/Components/PublicLayout.php` και `AdminLayout.php`
  - Διαγράφηκαν τα λανθασμένα blade wrapper files
  - Τώρα το `<x-public-layout>` και `<x-admin-layout>` δουλεύουν σωστά με `$slot`

---

## Sprint 0 — Προετοιμασία

### Dev A (Implementer)
- [x] Laravel 12 project setup (2024-11-27)
- [x] Git repository initialization (2024-11-27)
- [x] Laravel Breeze installation (2024-11-27)
- [x] Domain folder structure (2024-11-27)
  - `app/Domain/Catalog/`
  - `app/Domain/Orders/`
  - `app/Domain/Customers/`
  - `app/Domain/Businesses/`
  - `app/Domain/CMS/`
  - `app/Domain/Auth/`

### Dev B (Architect)
- [x] Database schema design (2024-11-27)
  - `project-docs/database-schema.md`
  - Tables: businesses, categories, products, customers, orders, order_items, users, pages
- [x] Conventions document (2024-11-27)
  - `project-docs/conventions.md`
  - Services vs Actions, naming conventions, code style
- [x] Domain boundaries definition (2024-11-27)
  - Catalog, Orders, Customers, Businesses, CMS, Auth

### Dev C (Frontend)
- [x] Base layouts (2024-11-27)
  - `resources/views/layouts/public.blade.php` — Public site layout με header, footer, mobile menu
  - `resources/views/layouts/admin.blade.php` — Admin panel layout με sidebar navigation
- [x] TailwindCSS configuration (2024-11-27)
  - Custom color palette (primary: amber, accent: teal)
  - Outfit font family
  - Surface & content semantic colors
- [x] Demo homepage (2024-11-27)
  - `resources/views/home.blade.php` — Hero section, features, CTA
  - Route updated to serve home view

---

## Sprint 1 — Catalog & Public Menu ✅

> **Status**: COMPLETED (2024-11-27)
> **Review**: Master DEV approved — all deliverables met

### Dev A
- [x] MenuController (2024-11-27)
  - `App\Http\Controllers\MenuController@show`
  - Χρησιμοποιεί `GetMenuForBusinessService`
- [x] CategoryController (2024-11-27)
  - `App\Http\Controllers\CategoryController@show`
  - Χρησιμοποιεί `GetActiveProductsService`
- [x] Routes setup (2024-11-27)
  - `/menu` → MenuController@show
  - `/menu/{slug}` → CategoryController@show
- [x] Basic caching — ήδη υλοποιημένο στο GetMenuForBusinessService (30 min)

### Dev B
- [x] Migrations (2024-11-27)
  - `create_businesses_table` — businesses με type, settings JSON
  - `create_categories_table` — categories με business_id FK
  - `create_products_table` — products με category_id FK
- [x] Models (2024-11-27)
  - `App\Domain\Businesses\Models\Business`
  - `App\Domain\Catalog\Models\Category`
  - `App\Domain\Catalog\Models\Product`
- [x] Seeders (2024-11-27)
  - `BusinessSeeder` — Demo Cafe
  - `CategorySeeder` — Καφέδες, Ροφήματα, Σνακ, Γλυκά
  - `ProductSeeder` — 15 demo products
- [x] Services (2024-11-27)
  - `GetMenuForBusinessService` — Full menu με caching
  - `GetActiveProductsService` — Products by business/category/featured

### Dev C
- [x] menu.blade.php (2024-11-27)
  - Categories grid με hover effects
  - Featured products section
  - Empty state handling
- [x] product-card.blade.php (2024-11-27)
  - Reusable component με props
  - Featured badge, unavailable overlay
  - Add to cart button placeholder
- [x] category.blade.php (2024-11-27)
  - Products grid για single category
  - Breadcrumb navigation
  - Back to menu link
- [x] Responsive grid layout — mobile-first με Tailwind breakpoints

### Sprint 1 Deliverables ✅
- [x] Public menu fully working
- [x] Real categories + products from DB
- [x] Responsive UI (mobile-first)
- [x] Basic caching (30 min TTL)
- [x] Generic naming (products, not coffees)

---

## Sprint 2 — Admin Panel ✅

> **Status**: COMPLETED (2024-11-27)
> **Review**: Master DEV approved — all deliverables met

### Dev A
- [x] AdminMiddleware (2024-11-27)
  - `App\Http\Middleware\AdminMiddleware`
  - Registered as 'admin' alias in bootstrap/app.php
- [x] Migration: add_is_admin_to_users_table (2024-11-27)
  - Added `is_admin` boolean column to users
- [x] Admin routes (2024-11-27)
  - `/admin/products` — full CRUD resource
  - `/admin/categories` — full CRUD resource
  - Protected by `auth` + `admin` middleware
- [x] Admin ProductController (2024-11-27)
  - `App\Http\Controllers\Admin\ProductController`
  - index, create, store, edit, update, destroy
- [x] Admin CategoryController (2024-11-27)
  - `App\Http\Controllers\Admin\CategoryController`
  - index, create, store, edit, update, destroy

### Dev B
- [x] CRUD Services (2024-11-27)
  - `CreateProductService`, `UpdateProductService`, `DeleteProductService`
  - `CreateCategoryService`, `UpdateCategoryService`, `DeleteCategoryService`
  - Auto cache invalidation on update/delete
- [x] Policies (2024-11-27)
  - `App\Domain\Catalog\Policies\ProductPolicy`
  - `App\Domain\Catalog\Policies\CategoryPolicy`
  - RBAC based on `is_admin` flag
- [x] FormRequests (2024-11-27)
  - `StoreProductRequest`, `UpdateProductRequest`
  - `StoreCategoryRequest`, `UpdateCategoryRequest`
  - Greek validation messages

### Dev C
- [x] Admin Products views (2024-11-27)
  - `admin/products/index.blade.php` — List με pagination, status badges
  - `admin/products/create.blade.php` — Form με validation errors
  - `admin/products/edit.blade.php` — Edit form με pre-filled values
- [x] Admin Categories views (2024-11-27)
  - `admin/categories/index.blade.php` — List με product count
  - `admin/categories/create.blade.php` — Create form
  - `admin/categories/edit.blade.php` — Edit form
- [x] UI Features
  - Flash messages (success/error)
  - Breadcrumb navigation
  - Responsive tables
  - Delete confirmation dialogs
- [ ] Image upload form (deferred to Sprint 4)

### Sprint 2 Deliverables ✅
- [x] Full admin catalog management
- [x] CRUD for products & categories
- [x] Safe validation & policies
- [x] Clean admin UI
- [x] Ready for demo to client

### Sprint 2 Review Notes (Master DEV)
- Dev A: Minor fix (added `is_admin` cast to User model)
- Dev B: Bug fix (added missing `use` statement in services)
- Dev C: No issues found

---

## Sprint 3 — Ordering System

**Status**: Ready to start

### Planned Tasks
- Dev A: CartController, CheckoutController
- Dev B: Orders/Customers migrations, CreateOrderService
- Dev C: Cart views, checkout flow UI

---

## Sprint 4 — Multi-Business & Theming

*Pending...*

---

## Sprint 5 — Testing & Deployment

*Pending...*

