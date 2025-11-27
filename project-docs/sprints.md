# 🚀 LaraShop - Sprint Plan

## Επισκόπηση Project

Το project είναι:
- 👉 **Modular**, επαναχρησιμοποιήσιμο (για καφετέρια, βενζινάδικο, κομμωτήριο)
- 👉 **Laravel monolith**, modular domains
- 👉 **Public site + Admin + βασική παραγγελία**
- 👉 Με προοπτική για μελλοντική επέκταση

---

## 📋 Sprints Overview

| Sprint | Περιγραφή | Διάρκεια |
|--------|-----------|----------|
| Sprint 0 | Προετοιμασία | 2–3 ημέρες |
| Sprint 1 | Βασική δομή & Menu | 4–6 ημέρες |
| Sprint 2 | Admin System | 6–8 ημέρες |
| Sprint 3 | Ordering System | 7–10 ημέρες |
| Sprint 4 | Multi-business, Theming & Extensibility | 4–6 ημέρες |
| Sprint 5 | Testing, Refinement, Deployment | 3–5 ημέρες |

Κάθε sprint έχει:
- Υπο-tasks
- Ποιος developer κάνει τι
- Deliverables (τι θα παραδοθεί)

---

## 🟪 SPRINT 0 — Προετοιμασία (2–3 ημέρες)

### 🎯 Στόχος
Project setup + requirements + high-level αρχιτεκτονική

### Tasks

#### Dev A (practical implementer)
- [x] Στήνει νέο Laravel project
- [x] Στήνει Git repo
- [x] Ρυθμίζει basic `.env` / DB
- [x] Εγκαθιστά Laravel Breeze / auth scaffold
- [x] Φτιάχνει βασικό folder structure:

```
app/
  Domain/
    Catalog/
    Orders/
    Customers/
    Businesses/
    CMS/
    Auth/
routes/
  web.php
  api.php
resources/
  views/
    layouts/
```

#### Dev B (architect/domain)
- [ ] Ορίζει domains: Catalog, Orders, Customers, Businesses, CMS
- [ ] Σχεδιάζει database schema σε Miro/Draw.io
- [ ] Ορίζει conventions:
  - Πού μπαίνουν τα services
  - Πού μπαίνουν τα actions
  - Πού μπαίνουν policies
- [ ] Ορίζει naming conventions και coding guidelines (1 σελίδα)

#### Dev C (frontend/UX oriented)
- [ ] Φτιάχνει base layout Blade (public/admin layouts)
- [ ] Ρυθμίζει TailwindCSS
- [ ] Σχεδιάζει βασικό wireframe (public menu page + admin list page)
- [ ] Φτιάχνει demo homepage placeholder

### 📦 Deliverables Sprint 0
- ✅ Laravel project + repo
- ✅ Core folder structure
- ⬜ Database schema πρώτης έκδοσης
- ⬜ Base layouts (public/admin)
- ⬜ High-level architecture doc (1 σελίδα)

---

## 🟥 SPRINT 1 — Catalog & Public Menu (4–6 ημέρες)

### 🎯 Στόχος
Το site να δείχνει το μενού (public menu view)

### Tasks

#### Dev B
- [ ] Φτιάχνει migrations & models:
  - `businesses`
  - `categories`
  - `products`
- [ ] Φτιάχνει seeders:
  - Demo business (cafe)
  - Demo categories & products
- [ ] Φτιάχνει services:
  - `GetMenuForBusinessService`
  - `GetActiveProductsService`

#### Dev A
- [ ] Controllers:
  - `MenuController@show`
  - `CategoryController@show`
- [ ] Routes:
  - `/menu`
  - `/menu/{category}`
- [ ] Ενώνει services με controllers
- [ ] Βασικό caching: `Cache::remember('menu')`

#### Dev C
- [ ] Φτιάχνει public views:
  - `menu.blade.php`
  - `product-card.blade.php`
- [ ] Responsive layout με grid
- [ ] Εικόνες demo / icons
- [ ] Subtle animations με Tailwind transitions

### 📦 Deliverables Sprint 1
- ⬜ Public menu fully working
- ⬜ Real categories + products from DB
- ⬜ Responsive UI
- ⬜ Basic caching
- ⬜ Generic naming (όχι "coffees" αλλά "products")

---

## 🟫 SPRINT 2 — Admin Panel (6–8 ημέρες)

### 🎯 Στόχος
Ο διαχειριστής να αλλάζει προϊόντα/τιμές/κατηγορίες

### Tasks

#### Dev A (main implementer)
- [ ] Admin routes: `/admin/products`, `/admin/categories`
- [ ] `AdminAuthMiddleware` (restrict access)

#### Dev B
- [ ] Services:
  - `CreateProductService`
  - `UpdateProductService`
  - `DeleteProductService`
- [ ] Policies (RBAC):
  - `ProductPolicy`
  - `CategoryPolicy`
- [ ] Validation rules (FormRequests)

#### Dev C
- [ ] Admin views:
  - List products (sortable, searchable)
  - Create/edit product forms
  - Image upload form
- [ ] Livewire components (optional): editable list table

### 📦 Deliverables Sprint 2
- ⬜ Full admin catalog management
- ⬜ CRUD for products & categories
- ⬜ Safe validation & policies
- ⬜ Clean admin UI
- ⬜ Ready for demo to client

---

## 🟧 SPRINT 3 — Ordering System (7–10 ημέρες)

### 🎯 Στόχος
Απλό καλάθι → checkout → αποθήκευση παραγγελίας

### Tasks

#### Dev B
- [ ] Database:
  - `orders`
  - `order_items`
  - `customers`
- [ ] Services:
  - `CreateOrderService`
  - `CalculateOrderTotal`
  - `ValidateBusinessOperatingHours`
- [ ] Business rules:
  - Delivery enabled?
  - Hours
  - Minimum order amount?

#### Dev A
- [ ] Controllers:
  - `CartController`
  - `CheckoutController`
- [ ] Logic:
  - Add/remove item
  - Update quantity
  - Apply rules from Dev B services
- [ ] API endpoints (optional): `/api/order`

#### Dev C
- [ ] UI:
  - Cart drawer
  - Checkout page
  - Forms
  - Success page
- [ ] AJAX or Livewire for cart updates
- [ ] Mobile-first checkout design

### 📦 Deliverables Sprint 3
- ⬜ Functional ordering system
- ⬜ Orders in database
- ⬜ Admin can view orders
- ⬜ Customer sees confirmation page
- ⬜ System is generic for ANY type of business

---

## 🟨 SPRINT 4 — Multi-Business, Theming & Extensibility (4–6 ημέρες)

### 🎯 Στόχος
Να μπορεί η πλατφόρμα να υποστηρίξει εύκολα άλλες επιχειρήσεις

### Tasks

#### Dev B
- [ ] Προσθέτει `business_type` σε businesses (cafe, gas_station, etc)
- [ ] Ρυθμίσεις JSON:

```json
{
  "delivery_enabled": true,
  "show_catalog_images": true,
  "color_theme": "dark",
  "currency": "EUR"
}
```

- [ ] Services:
  - `GetBusinessSettingsService`

#### Dev A
- [ ] Νέα seeders για δεύτερο demo business:
  - 1 gas station
  - 1 bakery
- [ ] Ελέγχει ότι όλες οι queries έχουν: `->where('business_id', $businessId)`

#### Dev C
- [ ] Theme switcher:
  - Χρώματα
  - Logo
  - Layout variations
- [ ] Folder structure:

```
resources/views/public/themes/cafe/
resources/views/public/themes/gas_station/
```

### 📦 Deliverables Sprint 4
- ⬜ Έτοιμο σύστημα για πολλές επιχειρήσεις
- ⬜ Εύκολα εναλλάξιμο theme
- ⬜ Πλήρη independence ανά business

---

## 🟩 SPRINT 5 — Testing, Refactoring, Deployment (3–5 ημέρες)

### 🎯 Στόχος
Σταθερότητα + quality + production release

### Tasks

#### Dev A
- [ ] Feature tests:
  - View menu
  - Create order
  - Admin CRUD
- [ ] Performance tweaks:
  - Queries optimization
  - Laravel caching
  - Config cache

#### Dev B
- [ ] Refactoring:
  - Services should be single-responsibility
  - Remove code duplication
  - Domain boundaries clean
- [ ] Documentation:
  - Architecture
  - How to add new business types
  - How to add new modules

#### Dev C
- [ ] UX polish (forms, error messages, mobile layouts)
- [ ] Admin manual (PDF or page)
- [ ] Public site polish (animations, spacing, typography)

### 📦 Deliverables Sprint 5
- ⬜ Production-ready release
- ⬜ Tested features
- ⬜ Deployment pipeline (Forge/RunCloud)
- ⬜ Documentation for future reuse
- ⬜ Clean modular architecture
- ⬜ Themeable, reusable project

---

## 🎉 Τελικό Αποτέλεσμα μετά από 5 Sprints

Έχεις ένα:

- ✔ Modular Laravel σύστημα
- ✔ Έτοιμο για καφετέρια, βενζινάδικο, κομμωτήριο
- ✔ Επεκτάσιμο σε multi-business
- ✔ Με admin panel
- ✔ Με ordering logic
- ✔ Με theme switching
- ✔ Με σχεδόν πλήρη separation σε modules
- ✔ Εύκολα συντηρήσιμο από 1 dev
- ✔ Εύκολα επεκτάσιμο από ομάδα
- ✔ Με καθαρό domain-driven σχεδιασμό
- ✔ Με κάθετη ανάπτυξη (feature-based)
