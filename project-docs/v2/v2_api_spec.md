# 📡 v2 API Specification

## Overview

REST API για headless consumption του CMS. Υποστηρίζει mobile apps, third-party integrations, και future frontend frameworks.

**Related Documentation:**
- [v2 Overview](./v2_overview.md) — Architecture, strategy & technical specs
- [v2 Migration Guide](./v2_migration_guide.md) — Migration steps

---

## 🔐 Authentication

### Sanctum Token Authentication

**Get Token:**
```http
POST /api/v1/auth/token
Content-Type: application/json

{
  "email": "admin@example.com",
  "password": "password"
}
```

**Response:**
```json
{
  "token": "1|abcdef123456...",
  "user": {
    "id": 1,
    "name": "Admin User",
    "email": "admin@example.com"
  }
}
```

**Use Token:**
```http
Authorization: Bearer {token}
```

---

## 📍 Base URL

```
/api/v1
```

---

## 🏢 Businesses

### Get Business
```http
GET /api/v1/businesses/{id}
```

**Response:**
```json
{
  "id": 1,
  "name": "Demo Cafe",
  "type": "cafe",
  "settings": {
    "color_theme": "warm",
    "delivery_enabled": true
  },
  "theme": "default"
}
```

### Get Business Settings
```http
GET /api/v1/businesses/{id}/settings
```

---

## 📄 Content

### Get Content by Slug
```http
GET /api/v1/businesses/{businessId}/content/{slug}
```

**Response:**
```json
{
  "id": 1,
  "type": "page",
  "slug": "about",
  "title": "About Us",
  "body": [
    {
      "type": "hero",
      "props": {
        "title": "Welcome",
        "image": "/media/hero.jpg"
      }
    },
    {
      "type": "text",
      "props": {
        "content": "<p>Our story...</p>"
      }
    }
  ],
  "meta": {
    "description": "About our company",
    "keywords": ["about", "company"]
  },
  "published_at": "2024-01-01T00:00:00Z"
}
```

### List Content by Type
```http
GET /api/v1/businesses/{businessId}/content?type=page
GET /api/v1/businesses/{businessId}/content?type=article
```

**Query Parameters:**
- `type` — Filter by content type
- `status` — Filter by status (draft, published, archived)
- `page` — Pagination

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "slug": "about",
      "title": "About Us",
      "type": "page",
      "status": "published",
      "published_at": "2024-01-01T00:00:00Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "total": 10
  }
}
```

---

## 🖼️ Media

### List Media
```http
GET /api/v1/businesses/{businessId}/media
```

**Query Parameters:**
- `folder_id` — Filter by folder
- `type` — Filter by type (image, video, document)
- `page` — Pagination

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "name": "hero.jpg",
      "path": "/storage/media/hero.jpg",
      "type": "image",
      "mime": "image/jpeg",
      "size": 102400,
      "variants": {
        "thumb": "/storage/media/hero-thumb.jpg",
        "small": "/storage/media/hero-small.jpg",
        "medium": "/storage/media/hero-medium.jpg",
        "large": "/storage/media/hero-large.jpg"
      }
    }
  ]
}
```

### Upload Media (Admin)
```http
POST /api/v1/businesses/{businessId}/media
Authorization: Bearer {token}
Content-Type: multipart/form-data

file: [binary]
folder_id: 1 (optional)
```

---

## 🛒 Catalog

### Get Products
```http
GET /api/v1/businesses/{businessId}/products
```

**Query Parameters:**
- `category_id` — Filter by category
- `featured` — Filter featured only
- `available` — Filter available only

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "name": "Espresso",
      "slug": "espresso",
      "description": "Strong coffee",
      "price": 2.50,
      "image": "/storage/media/espresso.jpg",
      "category": {
        "id": 1,
        "name": "Coffee",
        "slug": "coffee"
      },
      "is_available": true,
      "is_featured": false
    }
  ]
}
```

### Get Product
```http
GET /api/v1/businesses/{businessId}/products/{id}
```

### Get Categories
```http
GET /api/v1/businesses/{businessId}/categories
```

---

## 🛍️ Orders

### Create Order
```http
POST /api/v1/businesses/{businessId}/orders
Authorization: Bearer {token}
Content-Type: application/json

{
  "items": [
    {
      "product_id": 1,
      "quantity": 2
    }
  ],
  "customer": {
    "name": "John Doe",
    "email": "john@example.com",
    "phone": "+1234567890"
  },
  "type": "pickup",
  "notes": "Extra hot"
}
```

**Response:**
```json
{
  "id": 1,
  "order_number": "ORD-2024-001",
  "status": "pending",
  "total": 5.00,
  "items": [
    {
      "product_id": 1,
      "product_name": "Espresso",
      "quantity": 2,
      "price": 2.50,
      "subtotal": 5.00
    }
  ],
  "created_at": "2024-01-01T00:00:00Z"
}
```

### Get Order
```http
GET /api/v1/orders/{orderNumber}
Authorization: Bearer {token}
```

---

## ⚠️ Error Responses

### 400 Bad Request
```json
{
  "error": "Validation failed",
  "errors": {
    "email": ["The email field is required."]
  }
}
```

### 401 Unauthorized
```json
{
  "error": "Unauthenticated"
}
```

### 403 Forbidden
```json
{
  "error": "Insufficient permissions"
}
```

### 404 Not Found
```json
{
  "error": "Resource not found"
}
```

### 429 Too Many Requests
```json
{
  "error": "Rate limit exceeded",
  "retry_after": 60
}
```

---

## 🔄 Rate Limiting

- **Public endpoints**: 60 requests/minute
- **Authenticated endpoints**: 120 requests/minute
- **Admin endpoints**: 200 requests/minute

Headers:
```
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 59
X-RateLimit-Reset: 1234567890
```

---

## 📝 Versioning

Current version: `v1`

Future versions: `/api/v2/...`

---

**End of API Specification**

---

**Last Updated**: 2024-11-27

