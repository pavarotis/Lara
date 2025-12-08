# 🛒 LaraShop

![PHP Version](https://img.shields.io/badge/php-8.2-blue)
![Laravel Version](https://img.shields.io/badge/laravel-12.x-red)
![License](https://img.shields.io/github/license/pavarotis/larashop)

Modular Laravel πλατφόρμα για **multi-business e-commerce & CMS**. Σχεδιασμένη για καφετέριες, βενζινάδικα, κομμωτήρια και κάθε τύπο επιχείρησης με κατάλογο προϊόντων, online παραγγελίες, και block-based content management.

> **🚧 v2.0 Migration in Progress**: Μετατροπή σε CMS-first πλατφόρμα με content editor, media library, RBAC, και plugin system. [Δείτε το overview →](project-docs/v2/v2_overview.md)

---

## 📊 Current Status

### v1.0 (Completed)
| Sprint | Status |
|--------|--------|
| Sprint 0 — Setup | ✅ Complete |
| Sprint 1 — Catalog & Menu | ✅ Complete |
| Sprint 2 — Admin Panel | ✅ Complete |
| Sprint 3 — Ordering | ✅ Complete |
| Sprint 4 — Multi-Business | ✅ Complete |
| Sprint 5 — Testing | ✅ Complete |

### v2.0 (In Progress)
| Sprint | Status |
|--------|--------|
| Sprint 0 — Infrastructure & Foundation | ✅ Complete |
| Sprint 1 — Content Module | ⏳ Pending |
| Sprint 2 — Media Library | ⏳ Pending |
| Sprint 3 — Content Rendering & Theming | ⏳ Pending |
| Sprint 4 — RBAC & Permissions | ⏳ Pending |
| Sprint 5 — API & Headless Support | ⏳ Pending |
| Sprint 6 — Plugins & Polish | ⏳ Pending |

---

## 🚀 Features

### v1.0 (Current)
- **Multi-business support** ✅ — Μία εγκατάσταση, πολλές επιχειρήσεις
- **Modular architecture** ✅ — Domain-driven design με καθαρά boundaries
- **Public catalog** ✅ — Responsive menu με κατηγορίες & προϊόντα
- **Admin panel** ✅ — Full CRUD για προϊόντα & κατηγορίες
- **Ordering system** ✅ — Cart, checkout, order management
- **Theming** ✅ — Διαφορετικό theme ανά επιχείρηση
- **Authentication** ✅ — Laravel Breeze με admin flag
- **Modern frontend** ✅ — TailwindCSS + Vite

### v2.0 (In Progress)
- **RBAC** ✅ — Roles & Permissions system (custom implementation)
- **Settings System** ✅ — Global settings with caching
- **API Foundation** ✅ — REST API structure with versioning
- **Admin Panel (Hybrid)** ✅ — Filament + Blade integration
- **User Management** ✅ — Filament UserResource with role assignment
- **CMS Content Editor** 🚧 — Block-based page builder (Sprint 1)
- **Media Library** 🚧 — File management με folders & variants (Sprint 2)
- **Content Versioning** 🚧 — Revisions & rollback (Sprint 1)
- **Headless API** 🚧 — REST API for mobile apps / third-party (Sprint 5)
- **Plugin System** 🚧 — Extensible architecture (Sprint 6)
- **Theme System** 🚧 — Enhanced per-business theming (Sprint 3)

---

## 🏗 Tech Stack

| Layer | Technology |
|-------|------------|
| Backend | Laravel 12.x |
| Frontend | Blade + TailwindCSS + Vite |
| Admin Panel | Filament v4.0 (Hybrid with Blade) |
| Database | MySQL / SQLite |
| Auth | Laravel Breeze + RBAC (Custom) |

---

## 📦 Installation

```bash
# Clone repository
git clone https://github.com/pavarotis/larashop.git
cd larashop

# Install dependencies
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Database
php artisan migrate
php artisan db:seed

# Build assets
npm run build

# Start server
php artisan serve
```

---

## 📁 Project Structure

```
app/
  Domain/
    Catalog/      # Products, Categories
    Orders/       # Orders, Cart
    Customers/    # Customer management
    Businesses/   # Multi-business logic
    CMS/          # Pages, Content
    Auth/         # Authentication
  Http/
    Controllers/
      MenuController.php      # Public menu display
      CategoryController.php  # Category products
      Admin/
        ProductController.php   # Admin CRUD products
        CategoryController.php  # Admin CRUD categories
    Middleware/
      AdminMiddleware.php     # Admin access control
resources/
  views/
    layouts/
      public.blade.php   # Public site layout
      admin.blade.php    # Admin panel layout
    components/
      product-card.blade.php  # Product card component
    admin/
      products/          # Product CRUD views
      categories/        # Category CRUD views
      orders/            # Order management views
    cart/                # Cart page
    checkout/            # Checkout & success pages
    home.blade.php       # Homepage
    menu.blade.php       # Menu categories page
    category.blade.php   # Single category products
app/
  View/
    Components/
      PublicLayout.php   # Public layout component class
      AdminLayout.php    # Admin layout component class
routes/
  web.php
  api.php
```

---

## 📚 Documentation

### v1.0
- [Development Steps (v1)](project-docs/steps_versions/v1_steps.md)
- [Database Schema](project-docs/database-schema.md)
- [Architecture](project-docs/architecture.md)
- [Dev Commands](project-docs/dev-commands.md)

### v2.0 (Migration)
- [**v2 Overview**](project-docs/v2/v2_overview.md) — Architecture, strategy & technical specs
- [**v2 Migration Guide**](project-docs/v2/v2_migration_guide.md) — Step-by-step migration instructions
- [**v2 API Specification**](project-docs/v2/v2_api_spec.md) — REST API documentation
- [**v2 Plugin Guide**](project-docs/v2/v2_plugin_guide.md) — Plugin development
- [**v2 Content Model**](project-docs/v2/v2_content_model.md) — Content structure & blocks
- [**v2 Documentation Index**](project-docs/v2/README.md) — All v2 docs

---

## 🤝 Contributing

Pull requests είναι ευπρόσδεκτα. Για μεγάλες αλλαγές, ανοίξτε πρώτα ένα issue.

---

## 📜 License

[MIT License](LICENSE)
