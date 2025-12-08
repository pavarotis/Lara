# 🎯 v2 CMS Core Concepts

## Overview

Το LaraShop v2 είναι ένα **CMS-first platform** με block-based content system. Αυτό το έγγραφο εξηγεί τα βασικά concepts και αρχιτεκτονικές αποφάσεις.

---

## Core Concepts

### 1. Content = Blocks

**Κεντρική Ιδέα**: Κάθε content entry (page, article) αποτελείται από **blocks** (hero, text, gallery, etc.).

**Storage**: Hybrid approach
- **Relational** (metadata): id, slug, title, status, published_at
- **JSON** (blocks): `body_json` column με array of blocks

**Example**:
```json
{
  "id": 1,
  "slug": "homepage",
  "title": "Welcome",
  "body_json": [
    {
      "type": "hero",
      "props": {
        "title": "Welcome to Our Cafe",
        "image_id": 5,
        "cta_text": "Order Now",
        "cta_link": "/menu"
      }
    },
    {
      "type": "text",
      "props": {
        "content": "<p>We serve the best coffee...</p>"
      }
    },
    {
      "type": "gallery",
      "props": {
        "images": [5, 6, 7]
      }
    }
  ]
}
```

---

### 2. Block System

**Block Definition**: Ένα block είναι ένα reusable component που:
- Έχει ένα **type** (hero, text, gallery, etc.)
- Έχει **props** (customizable properties)
- Μπορεί να render σε Blade view

**Block Structure**:
```php
[
    'type' => 'hero',
    'props' => [
        'title' => '...',
        'image_id' => 5,
        // ... other props
    ]
]
```

**Block Registry**: 
- Blocks register στο `BlockRegistry`
- Validation: Only registered block types allowed
- Rendering: Theme-based (per business)

---

### 3. Theme-Based Rendering

**Concept**: Κάθε business μπορεί να έχει διαφορετικό theme.

**Rendering Flow**:
1. Load content από database
2. Decode `body_json` → array of blocks
3. For each block:
   - Resolve theme: `themes/{business->theme}/blocks/{type}.blade.php`
   - Fallback: `themes/default/blocks/{type}.blade.php`
   - Render with `$block['props']` as variables

**Theme Structure**:
```
resources/views/themes/
├── default/
│   └── blocks/
│       ├── hero.blade.php
│       ├── text.blade.php
│       └── gallery.blade.php
├── warm/
│   └── blocks/
│       └── hero.blade.php
└── modern/
    └── blocks/
        └── hero.blade.php
```

---

### 4. Content Types

**Static Content Types**:
- `page` — Static pages (home, about, contact)
- `article` — Blog posts, news
- `block` — Reusable content blocks

**Dynamic Content Types** (future):
- Custom content types via `ContentType` model
- Field definitions stored as JSON
- Flexible schema per content type

---

### 5. Revisions (Version History)

**Concept**: Κάθε update δημιουργεί revision snapshot.

**Storage**:
- `content_revisions` table
- Stores `body_json` snapshot
- Tracks `user_id` and `created_at`

**Use Cases**:
- Rollback to previous version
- Compare versions
- Audit trail

---

### 6. Publishing Workflow

**Status Values**:
- `draft` — Not published
- `published` — Live on site
- `archived` — Hidden but kept

**Published At**:
- `published_at` timestamp
- Controls when content goes live
- Can schedule future publishing

---

## Content Lifecycle

### Creation

1. **Editor creates content**:
   - Selects content type
   - Adds blocks via drag & drop
   - Configures block props
   - Saves as `draft`

2. **Service layer**:
   - `CreateContentService` validates
   - Saves to `contents` table
   - `body_json` stored as JSON

### Editing

1. **Load existing content**
2. **Modify blocks** (add, remove, reorder)
3. **Update props** within blocks
4. **Save revision** (automatic)
5. **Update main content**

### Publishing

1. **Change status** to `published`
2. **Set `published_at`** timestamp
3. **Content becomes visible** on public site
4. **Cache invalidation** (if cached)

### Rendering

1. **Public request**: `/page/homepage`
2. **Controller** loads content by slug
3. **RenderContentService** decodes blocks
4. **Theme resolver** finds block views
5. **Blade renders** each block
6. **HTML output** returned to user

---

## Integration Points

### Media Integration

**Blocks can reference media**:
```json
{
  "type": "hero",
  "props": {
    "image_id": 5  // References Media model
  }
}
```

**Rendering**:
- Load `Media` model by `image_id`
- Generate URL for variant (thumb, medium, large)
- Pass to Blade view

### Business Integration

**All content is business-scoped**:
- `content.business_id` foreign key
- Queries filter by `business_id`
- Per-business themes

### SEO Integration

**Meta data stored in `meta` JSON**:
```json
{
  "meta": {
    "meta_description": "Best coffee in town",
    "meta_keywords": "coffee, cafe, espresso",
    "og_image": 5
  }
}
```

---

## Data Flow Example

### Creating a Page

```
User (Admin) 
  ↓
ContentController@create
  ↓
StoreContentRequest (validation)
  ↓
CreateContentService
  ├─ Validates blocks (BlockRegistry)
  ├─ Creates Content model
  ├─ Stores body_json
  └─ Returns Content
  ↓
Controller redirects to edit page
```

### Rendering a Page

```
Public Request: /page/homepage
  ↓
ContentController@show
  ↓
GetContentService
  ├─ Loads Content by slug
  ├─ Filters by business_id
  └─ Returns Content model
  ↓
RenderContentService
  ├─ Decodes body_json → blocks
  ├─ Resolves theme (business->theme)
  ├─ For each block:
  │   ├─ Resolve view path
  │   ├─ Load Media (if image_id)
  │   └─ Render Blade view
  └─ Returns rendered HTML
  ↓
View (layout + blocks)
  ↓
Response to browser
```

---

## Block Development

### Creating a New Block Type

**1. Register Block**:
```php
// In BlockRegistry or ServiceProvider
BlockRegistry::register('testimonial', TestimonialBlock::class);
```

**2. Create Blade View**:
```blade
{{-- resources/views/themes/default/blocks/testimonial.blade.php --}}
<div class="testimonial">
    <p>{{ $props['quote'] }}</p>
    <cite>{{ $props['author'] }}</cite>
</div>
```

**3. Validation** (in FormRequest):
```php
'body_json.*.type' => ['required', Rule::in(BlockRegistry::getTypes())],
'body_json.*.props.quote' => ['required', 'string'],
'body_json.*.props.author' => ['required', 'string'],
```

---

## Performance Considerations

### Caching

**Content Rendering**:
- Cache rendered HTML for published content
- Cache key: `content:{business_id}:{slug}:rendered`
- TTL: 15 minutes
- Invalidation: On content update/publish

**Block Registry**:
- Cache registered block types
- Clear on plugin install/uninstall

### Database Optimization

**Indexes**:
- `business_id` + `slug` (unique)
- `status` + `published_at`
- `type`

**JSON Queries**:
- Use JSON functions sparingly
- Prefer relational queries when possible

---

## Security Considerations

### Content Validation

- **Block types**: Only registered types allowed
- **Props validation**: Per-block validation rules
- **XSS prevention**: Escape HTML in props (Blade auto-escapes)

### Access Control

- **Authorization**: Policies check permissions
- **Business isolation**: All queries filter by `business_id`
- **Draft content**: Only admins/editors can view

---

## References

- [v2 Content Model](./v2_content_model.md) — Detailed data model
- [v2 Overview](./v2_overview.md) — High-level architecture
- [Block System Conventions](../conventions.md#23-block-system-conventions) — Coding standards

---

**Last Updated**: 2024-11-27

