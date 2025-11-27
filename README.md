# 🛒 LaraShop

![PHP Version](https://img.shields.io/badge/php-8.2-blue)
![Laravel Version](https://img.shields.io/badge/laravel-12.x-red)
![License](https://img.shields.io/github/license/pavarotis/larashop)

Modular Laravel πλατφόρμα για **multi-business e-commerce**. Σχεδιασμένη για καφετέριες, βενζινάδικα, κομμωτήρια και κάθε τύπο επιχείρησης με κατάλογο προϊόντων και online παραγγελίες.

---

## 📊 Current Status

| Sprint | Status |
|--------|--------|
| Sprint 0 — Setup | ✅ Complete |
| Sprint 1 — Catalog & Menu | ✅ Complete |
| Sprint 2 — Admin Panel | ✅ Complete |
| Sprint 3 — Ordering | 🔄 Ready |

---

## 🚀 Features

- **Multi-business support** — Μία εγκατάσταση, πολλές επιχειρήσεις
- **Modular architecture** — Domain-driven design με καθαρά boundaries
- **Public catalog** ✅ — Responsive menu με κατηγορίες & προϊόντα
- **Admin panel** ✅ — Full CRUD για προϊόντα & κατηγορίες
- **Ordering system** ⏳ — Cart, checkout, order management
- **Theming** ⏳ — Διαφορετικό theme ανά επιχείρηση
- **Authentication** — Laravel Breeze με role-based access
- **Modern frontend** — TailwindCSS + Vite

---

## 🏗 Tech Stack

| Layer | Technology |
|-------|------------|
| Backend | Laravel 12.x |
| Frontend | Blade + TailwindCSS + Vite |
| Database | MySQL / SQLite |
| Auth | Laravel Breeze |

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

- [Sprint Plan](project-docs/sprints.md)
- [Development Steps](project-docs/steps.md)
- [Database Schema](project-docs/database-schema.md)
- [Conventions & Guidelines](project-docs/conventions.md)

---

## 🤝 Contributing

Pull requests είναι ευπρόσδεκτα. Για μεγάλες αλλαγές, ανοίξτε πρώτα ένα issue.

---

## 📜 License

[MIT License](LICENSE)
