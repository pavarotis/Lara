# 📊 Database Schema — LaraShop

## Overview

Domain-driven schema σχεδιασμένο για multi-business support.

---

## 🏢 Businesses Domain

### `businesses`
| Column | Type | Description |
|--------|------|-------------|
| id | bigint | PK |
| name | string | Επωνυμία επιχείρησης |
| slug | string | URL-friendly identifier |
| type | enum | cafe, gas_station, salon, bakery, etc |
| logo | string | Path to logo |
| settings | json | Ρυθμίσεις (theme, currency, etc) |
| is_active | boolean | Ενεργή/Ανενεργή |
| created_at | timestamp | |
| updated_at | timestamp | |

---

## 📦 Catalog Domain

### `categories`
| Column | Type | Description |
|--------|------|-------------|
| id | bigint | PK |
| business_id | bigint | FK → businesses |
| name | string | Όνομα κατηγορίας |
| slug | string | URL-friendly |
| description | text | Περιγραφή (optional) |
| image | string | Path to image |
| sort_order | int | Σειρά εμφάνισης |
| is_active | boolean | |
| created_at | timestamp | |
| updated_at | timestamp | |

### `products`
| Column | Type | Description |
|--------|------|-------------|
| id | bigint | PK |
| business_id | bigint | FK → businesses |
| category_id | bigint | FK → categories |
| name | string | Όνομα προϊόντος |
| slug | string | URL-friendly |
| description | text | Περιγραφή |
| price | decimal(10,2) | Τιμή |
| image | string | Path to image |
| is_available | boolean | Διαθέσιμο |
| is_featured | boolean | Προβεβλημένο |
| sort_order | int | Σειρά εμφάνισης |
| created_at | timestamp | |
| updated_at | timestamp | |

---

## 👤 Customers Domain

### `customers`
| Column | Type | Description |
|--------|------|-------------|
| id | bigint | PK |
| user_id | bigint | FK → users (nullable) |
| name | string | Ονοματεπώνυμο |
| email | string | Email |
| phone | string | Τηλέφωνο |
| address | text | Διεύθυνση (optional) |
| notes | text | Σημειώσεις |
| created_at | timestamp | |
| updated_at | timestamp | |

---

## 🛒 Orders Domain

### `orders`
| Column | Type | Description |
|--------|------|-------------|
| id | bigint | PK |
| business_id | bigint | FK → businesses |
| customer_id | bigint | FK → customers |
| order_number | string | Unique order reference |
| status | enum | pending, confirmed, preparing, ready, delivered, cancelled |
| type | enum | pickup, delivery |
| subtotal | decimal(10,2) | Υποσύνολο |
| tax | decimal(10,2) | ΦΠΑ |
| total | decimal(10,2) | Σύνολο |
| notes | text | Σχόλια πελάτη |
| delivery_address | text | Διεύθυνση παράδοσης |
| created_at | timestamp | |
| updated_at | timestamp | |

### `order_items`
| Column | Type | Description |
|--------|------|-------------|
| id | bigint | PK |
| order_id | bigint | FK → orders |
| product_id | bigint | FK → products |
| product_name | string | Snapshot όνομα |
| product_price | decimal(10,2) | Snapshot τιμή |
| quantity | int | Ποσότητα |
| subtotal | decimal(10,2) | quantity × price |
| notes | text | Ειδικές οδηγίες |
| created_at | timestamp | |
| updated_at | timestamp | |

---

## 🔐 Auth Domain

### `users` (Laravel default + extensions)
| Column | Type | Description |
|--------|------|-------------|
| id | bigint | PK |
| business_id | bigint | FK → businesses (nullable for super admin) |
| name | string | |
| email | string | |
| password | string | |
| role | enum | super_admin, admin, staff |
| is_active | boolean | |
| ... | | Laravel defaults |

---

## 📄 CMS Domain

### `pages`
| Column | Type | Description |
|--------|------|-------------|
| id | bigint | PK |
| business_id | bigint | FK → businesses |
| title | string | Τίτλος σελίδας |
| slug | string | URL |
| content | longtext | HTML/Markdown content |
| is_published | boolean | |
| created_at | timestamp | |
| updated_at | timestamp | |

---

## 🔗 Relationships Summary

```
businesses
  ├── categories (1:N)
  ├── products (1:N)
  ├── orders (1:N)
  ├── users (1:N)
  └── pages (1:N)

categories
  └── products (1:N)

customers
  └── orders (1:N)

orders
  └── order_items (1:N)

products
  └── order_items (1:N)
```

---

## 📐 Indexes

- `businesses`: unique(slug)
- `categories`: unique(business_id, slug), index(business_id)
- `products`: unique(business_id, slug), index(business_id), index(category_id)
- `orders`: unique(order_number), index(business_id), index(customer_id), index(status)
- `customers`: index(email), index(phone)
- `users`: index(business_id), index(role)

