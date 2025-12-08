# 📄 v2 Content Model Specification

## Overview

Block-based content system με hybrid storage (relational + JSON blocks). Κάθε content entry αποτελείται από blocks που αποθηκεύονται ως JSON array.

**Related Documentation:**
- [v2 Overview](./v2_overview.md) — Architecture, strategy & technical specs
- [v2 Migration Guide](./v2_migration_guide.md) — Migration steps
- [v2 Plugin Guide](./v2_plugin_guide.md) — Custom blocks via plugins

---

## 🗄️ Database Schema

### `content_types` Table

```sql
CREATE TABLE content_types (
    id BIGINT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    field_definitions JSON,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

**Field Definitions Example:**
```json
{
  "fields": [
    {
      "name": "meta_description",
      "type": "text",
      "required": false
    },
    {
      "name": "featured_image",
      "type": "media",
      "required": false
    }
  ]
}
```

---

### `contents` Table

```sql
CREATE TABLE contents (
    id BIGINT PRIMARY KEY,
    business_id BIGINT NOT NULL,
    type VARCHAR(255) NOT NULL, -- 'page', 'article', 'block'
    slug VARCHAR(255) NOT NULL,
    title VARCHAR(255) NOT NULL,
    body_json JSON NOT NULL, -- Array of blocks
    meta JSON, -- SEO, custom fields
    status ENUM('draft', 'published', 'archived') DEFAULT 'draft',
    published_at TIMESTAMP NULL,
    created_by BIGINT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    UNIQUE(business_id, slug),
    FOREIGN KEY (business_id) REFERENCES businesses(id),
    FOREIGN KEY (created_by) REFERENCES users(id)
);
```

**Indexes:**
- `business_id`
- `slug`
- `type`
- `status`
- `published_at`

---

### `content_revisions` Table

```sql
CREATE TABLE content_revisions (
    id BIGINT PRIMARY KEY,
    content_id BIGINT NOT NULL,
    body_json JSON NOT NULL,
    meta JSON,
    user_id BIGINT,
    created_at TIMESTAMP,
    
    FOREIGN KEY (content_id) REFERENCES contents(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id)
);
```

---

## 🧩 Block Structure

### Block JSON Format

```json
{
  "type": "hero",
  "props": {
    "title": "Welcome",
    "subtitle": "To our cafe",
    "image": "/media/hero.jpg",
    "cta_text": "Order Now",
    "cta_link": "/menu"
  }
}
```

### Content `body_json` Format

Array of blocks:

```json
[
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
      "content": "<p>Our story begins...</p>"
    }
  },
  {
    "type": "gallery",
    "props": {
      "images": [
        "/media/image1.jpg",
        "/media/image2.jpg"
      ]
    }
  },
  {
    "type": "products-list",
    "props": {
      "category_id": 1,
      "limit": 6,
      "featured_only": false
    }
  }
]
```

---

## 📋 Built-in Block Types

### 1. Hero Block

```json
{
  "type": "hero",
  "props": {
    "title": "string",
    "subtitle": "string (optional)",
    "image": "string (media path)",
    "cta_text": "string (optional)",
    "cta_link": "string (optional)",
    "overlay": "boolean (default: false)"
  }
}
```

### 2. Text Block

```json
{
  "type": "text",
  "props": {
    "content": "string (HTML)",
    "alignment": "left|center|right (default: left)",
    "max_width": "string (optional, e.g. '800px')"
  }
}
```

### 3. Gallery Block

```json
{
  "type": "gallery",
  "props": {
    "images": ["array of media paths"],
    "columns": "number (1-4, default: 3)",
    "spacing": "small|medium|large (default: medium)"
  }
}
```

### 4. Products List Block

```json
{
  "type": "products-list",
  "props": {
    "category_id": "number (optional)",
    "limit": "number (default: 12)",
    "featured_only": "boolean (default: false)",
    "columns": "number (1-4, default: 3)"
  }
}
```

### 5. Image Block

```json
{
  "type": "image",
  "props": {
    "image": "string (media path)",
    "alt": "string",
    "caption": "string (optional)",
    "alignment": "left|center|right (default: center)",
    "width": "string (optional, e.g. '100%')"
  }
}
```

### 6. Video Block

```json
{
  "type": "video",
  "props": {
    "url": "string (YouTube/Vimeo URL)",
    "autoplay": "boolean (default: false)",
    "controls": "boolean (default: true)"
  }
}
```

---

## 🔄 Content Lifecycle

### 1. Create (Draft)
```php
$content = Content::create([
    'business_id' => 1,
    'type' => 'page',
    'slug' => 'about',
    'title' => 'About Us',
    'body_json' => [...],
    'status' => 'draft',
]);
```

### 2. Update (Creates Revision)
```php
// Auto-creates revision before update
$content->update([
    'body_json' => [...],
]);
```

### 3. Publish
```php
$content->update([
    'status' => 'published',
    'published_at' => now(),
]);
```

### 4. Archive
```php
$content->update([
    'status' => 'archived',
]);
```

---

## 🔍 Querying Content

### By Slug
```php
$content = Content::where('business_id', $businessId)
    ->where('slug', 'about')
    ->where('status', 'published')
    ->first();
```

### By Type
```php
$pages = Content::where('business_id', $businessId)
    ->where('type', 'page')
    ->where('status', 'published')
    ->get();
```

### Search in Blocks (JSON)
```php
// Search in body_json (requires JSON search support)
$results = Content::where('business_id', $businessId)
    ->whereJsonContains('body_json', ['type' => 'hero'])
    ->get();
```

---

## 🎨 Rendering Content

### Server-Side Rendering

```php
// In ContentController
$content = Content::where('slug', $slug)->first();
$blocks = json_decode($content->body_json, true);

foreach ($blocks as $block) {
    $view = "themes.{$theme}.blocks.{$block['type']}";
    if (!view()->exists($view)) {
        $view = "themes.default.blocks.{$block['type']}";
    }
    echo view($view, $block['props'])->render();
}
```

### Theme Block Views

Location: `resources/views/themes/{theme}/blocks/{type}.blade.php`

Example: `resources/views/themes/default/blocks/hero.blade.php`

```blade
<div class="hero" style="background-image: url('{{ $image }}')">
    <h1>{{ $title }}</h1>
    @if($subtitle)
        <p>{{ $subtitle }}</p>
    @endif
    @if($cta_text && $cta_link)
        <a href="{{ $cta_link }}">{{ $cta_text }}</a>
    @endif
</div>
```

---

## 🔐 Permissions

- `content.view` — View published content
- `content.create` — Create content
- `content.edit` — Edit content
- `content.delete` — Delete content
- `content.publish` — Publish content

---

## 📊 Content Meta

The `meta` JSON field can store:

```json
{
  "seo": {
    "description": "Meta description",
    "keywords": ["keyword1", "keyword2"],
    "og_image": "/media/og-image.jpg"
  },
  "custom": {
    "featured": true,
    "priority": 10
  }
}
```

---

**End of Content Model Specification**

---

**Last Updated**: 2024-11-27

