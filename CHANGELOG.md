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

## Sprint 3 — Ordering System ✅

> **Status**: COMPLETED (2024-11-27)
> **Review**: Master DEV approved — all deliverables met

### Dev A
- [x] CartController (2024-11-27)
  - `App\Http\Controllers\CartController`
  - Session-based cart (add, update, remove, clear)
  - AJAX endpoints for cart data
  - Tax calculation (24% VAT)
- [x] CheckoutController (2024-11-27)
  - `App\Http\Controllers\CheckoutController`
  - Checkout form with validation
  - Order creation via CreateOrderService
  - Success/confirmation page
- [x] Admin OrderController (2024-11-27)
  - `App\Http\Controllers\Admin\OrderController`
  - Orders list with status filter
  - Order detail view
  - Status update functionality
- [x] Routes setup (2024-11-27)
  - `/cart` — cart page
  - `/cart/add`, `/cart/update`, `/cart/remove`, `/cart/clear` — AJAX
  - `/checkout`, `/checkout/success/{orderNumber}`
  - `/admin/orders`, `/admin/orders/{order}`

### Dev B
- [x] Migrations (2024-11-27)
  - `create_customers_table` — customer data με optional user_id
  - `create_orders_table` — orders με status enum, type enum
  - `create_order_items_table` — product snapshots
- [x] Models (2024-11-27)
  - `App\Domain\Customers\Models\Customer`
  - `App\Domain\Orders\Models\Order` — με scopes (status, pending)
  - `App\Domain\Orders\Models\OrderItem`
- [x] Services (2024-11-27)
  - `CalculateOrderTotalService` — subtotal, tax (24%), total
  - `CreateOrderService` — full order creation με transaction
  - `ValidateBusinessOperatingHoursService` — ώρες λειτουργίας
  - `ValidateOrderService` — business rules validation

### Dev C
- [x] Cart page (2024-11-27)
  - `cart/index.blade.php` — Full cart με quantity controls
  - Order summary sidebar
  - AJAX cart updates
  - Clear cart functionality
- [x] Checkout page (2024-11-27)
  - `checkout/index.blade.php` — Contact form, order type selection
  - Pickup/Delivery toggle με address field
  - Order notes
  - Validation error display
- [x] Order confirmation (2024-11-27)
  - `checkout/success.blade.php` — Order summary, status badge
  - Order items list, totals
- [x] Admin Orders views (2024-11-27)
  - `admin/orders/index.blade.php` — Orders list με status filter
  - `admin/orders/show.blade.php` — Order details, status update

### Sprint 3 Deliverables ✅
- [x] Cart functionality (add, update, remove, clear)
- [x] Checkout flow (form, validation, order creation)
- [x] Order confirmation page
- [x] Admin order management (list, view, status update)
- [x] Customer creation on checkout
- [x] Business rules validation (operating hours, delivery, min order)

### Sprint 3 Review Notes (Master DEV)
- Dev A: Bug fix (ValidateOrderService call signature)
- Dev B: No issues found
- Dev C: 2 bug fixes (route name, property name in view)

---

## Pre-Sprint 4 Fixes (Master DEV)

### Fixes (2024-11-27)
- [x] Cart button στο header → link στο `/cart` με dynamic count
- [x] Mobile menu → added Cart link
- [x] About page → created placeholder (`about.blade.php`)
- [x] Contact page → created placeholder (`contact.blade.php`)
- [x] Routes → added `/about` και `/contact`

---

## Sprint 4 — Multi-Business & Theming

**Status**: ✅ COMPLETED

### Dev A
- [x] SetCurrentBusiness middleware (2024-11-27)
  - `App\Http\Middleware\SetCurrentBusiness`
  - Resolves business from: route param, query param, session, or fallback
  - Shares `$currentBusiness` with all views
  - Registered as 'business' alias
- [x] Additional seeders (2024-11-27)
  - `GasStationSeeder` — QuickFuel με καύσιμα, σνακ, car care (10 products)
  - `BakerySeeder` — Artisan Bakery με ψωμιά, αρτοσκευάσματα, γλυκά (14 products)
  - DatabaseSeeder updated να καλεί όλα τα seeders
- [x] Verified business_id filtering (2024-11-27)
  - Όλες οι queries χρησιμοποιούν `where('business_id', ...)`

### Dev B
- [x] Business model enhancements (2024-11-27)
  - Added helper methods: `getSetting()`, `isDeliveryEnabled()`, `getTheme()`, `getCurrency()`
  - Added `scopeOfType()` for filtering by business type
- [x] GetBusinessSettingsService (2024-11-27)
  - Default settings per business type (cafe, restaurant, bakery, gas_station, salon)
  - Theme colors configuration (default, warm, elegant, modern, industrial)
  - `getThemeColors()` method for dynamic theming
- [x] BusinessSettingsDTO (2024-11-27)
  - Documented settings structure
  - Ordering: delivery, pickup, minimum_order
  - Display: show_images, color_theme
  - Currency & Tax: currency, symbol, tax_rate
  - Contact & Social: phone, email, facebook, instagram

### Dev C
- [x] Dynamic theme colors (2024-11-27)
  - CSS variables injected based on business theme
  - `--color-primary` and `--color-accent` from GetBusinessSettingsService
- [x] AJAX Add to Cart (2024-11-27)
  - `product-card.blade.php` updated με AJAX functionality
  - Visual feedback (checkmark icon, green color)
  - Dynamic cart count update in header

### Additional Tasks (from UI/UX Review)
- [ ] **Dev C**: Hero image — replace placeholder SVG with real image
- [x] **Dev B**: Image upload functionality for products/categories (2024-11-27)
  - `ImageUploadService` — upload, replace, delete, getUrl
  - Storage link created (`php artisan storage:link`)
  - FormRequests updated με image validation rules
  - ProductController integrated με image upload
  - Admin views updated με file input & image preview
- [x] **Dev C**: Add to cart button στο product-card (AJAX integration) (2024-11-27)

### Sprint 4 Review Notes (Master DEV)
- Dev A: No issues found
- Dev B: No issues found  
- Dev C: No issues found
- All tasks completed successfully

---

## Sprint 5 — Testing & Deployment

**Status**: Pending

### Additional Tasks (from UI/UX Review)
- [ ] **Dev C**: Mobile menu — upgrade to slide-in drawer
- [ ] **Dev C**: Contact form functionality

