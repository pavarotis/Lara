# 🎯 LaraShop v2 — CMS-First Platform Overview

## 📋 Overview

Μετατροπή του LaraShop από e-commerce platform σε **ισχυρό CMS-first πλατφόρμα** που:
- Δεν εξαρτάται από συγκεκριμένο public site
- Επαναχρησιμοποιείται για διαφορετικούς τύπους επιχειρήσεων (cafe, gas station, bakery, κλπ)
- Υποστηρίζει block-based content editor
- Παρέχει headless API για future extensibility
- Επιτρέπει plugins/extensions

---

## 🏗️ Αρχιτεκτονικές Αποφάσεις (από Meeting)

### Core Decisions
- **Architecture**: Modular Monolith (Laravel) + optional headless API
- **Content Model**: Hybrid relational + JSON blocks
- **Theming**: Themes per business (`resources/views/themes/<name>/`)
- **Extensibility**: Plugin system (Laravel Service Providers)
- **Dev Approach**: Hybrid — Sprint 0 (infra) → Vertical slices (features)
- **Admin Panel**: Hybrid Filament/Blade
  - **Filament** for standard CRUD (Products, Categories, Orders, Users, Roles)
  - **Blade** for custom features (Content Editor, Media Library, Plugins, Dashboard)

### Modules (v2)
| Module | Περιεχόμενο |
|--------|-------------|
| **Content** | Pages, Blocks, ContentTypes, Revisions |
| **Media** | Media library, folders, responsive variants |
| **Catalog** | Products, Categories (existing) |
| **Orders** | Orders, OrderItems (existing) |
| **Customers** | Customers (existing) |
| **Businesses** | Businesses, Settings (existing) |
| **Auth/Roles** | Users, Roles, Permissions (upgrade) |
| **Settings** | Global & per-business settings |
| **Plugins** | Plugin system & hooks |

---

## 🚀 Migration Strategy

### Phase 1: Infrastructure (Sprint 0)
- Setup API routes
- Upgrade Auth → RBAC (Roles/Permissions)
- Content module structure
- Media module structure
- Plugin system skeleton

### Phase 2: Core CMS (Sprint 1-2)
- Content types & blocks
- Media library
- Content editor UI
- Versioning/revisions

### Phase 3: Integration (Sprint 3-4)
- Migrate public pages → CMS content
- Block renderer per theme
- API endpoints
- RBAC implementation

### Phase 4: Polish & Extend (Sprint 5-6)
- API & Headless Support
- Plugin system
- Documentation
- Testing & deployment

---

## 📅 Sprint Overview

| Sprint | Focus | Duration | Status |
|--------|-------|----------|--------|
| **Sprint 0** | Infrastructure & Foundation | 2 weeks | ⏳ Pending |
| **Sprint 1** | Content Module (Core) | 1 week | ⏳ Pending |
| **Sprint 2** | Media Library | 1 week | ⏳ Pending |
| **Sprint 3** | Content Rendering & Theming | 1 week | ⏳ Pending |
| **Sprint 4** | RBAC & Permissions | 1 week | ⏳ Pending |
| **Sprint 5** | API & Headless Support | 1 week | ⏳ Pending |
| **Sprint 6** | Plugins & Polish | 1 week | ⏳ Pending |
| **Sprint 7** | Lightweight Public Site & Performance | 1-2 weeks | ⏳ Pending |

**📝 Detailed Tasks**: See individual sprint files in [sprints/](./sprints/) folder.

---

## 📋 Technical Specifications

### Content Block Structure

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

Content `body_json` = array of blocks:
```json
[
  {"type": "hero", "props": {...}},
  {"type": "text", "props": {"content": "..."}},
  {"type": "gallery", "props": {"images": [...]}}
]
```

### Block Renderer Flow

1. `ContentController` loads `Content` model
2. Decodes `body_json` → array of blocks
3. For each block:
   - Resolve theme: `themes/{business->theme}/blocks/{type}.blade.php`
   - Fallback: `themes/default/blocks/{type}.blade.php`
   - Include view with `$block['props']` as variables

### Media Variants

On upload, generate:
- `thumb` (150x150)
- `small` (400x400)
- `medium` (800x800)
- `large` (1200x1200)
- Original preserved

### Plugin Structure

```
plugins/
└── example/
    ├── src/
    │   ├── ExampleServiceProvider.php
    │   ├── Blocks/
    │   │   └── TestimonialBlock.php
    │   └── Routes/
    │       └── web.php
    ├── resources/
    │   └── views/
    │       └── blocks/
    │           └── testimonial.blade.php
    └── plugin.json
```

---

## 🔄 Migration Checklist

### Pre-Migration
- [ ] Backup database
- [ ] Create feature flag: `CMS_ENABLED` (allow rollback)
- [ ] Document current routes/views
- [ ] List all v1 code to be replaced/deleted

### During Migration
- [ ] Run migrations (Sprint 0)
- [ ] Migrate users: `is_admin` → roles
- [ ] Convert static pages → CMS content (Sprint 3)
- [ ] Test all existing features still work

### Post-Migration & Cleanup
- [ ] **Delete deprecated code:**
  - [ ] `app/Domain/CMS/` (empty folder)
  - [ ] Static views replaced by CMS (`home.blade.php`, `about.blade.php`, `contact.blade.php`)
  - [ ] `is_admin` logic (after role migration verified)
  - [ ] `AdminMiddleware` (replaced by `CheckPermission`)
- [ ] **Remove v2_ prefix from migrations** (rename to standard naming)
- [ ] **Update documentation**
- [ ] **Run full test suite**
- [ ] **Enable CMS feature flag**
- [ ] **Deploy to staging**

**📝 Detailed Migration Steps**: See [v2_migration_guide.md](./v2_migration_guide.md)

---

## 🧹 Cleanup Tasks Summary

### Per Sprint
- **Sprint 0**: None (new infrastructure)
- **Sprint 1**: Delete `app/Domain/CMS/` (empty folder)
- **Sprint 2**: Refactor `ImageUploadService` to use Media model
- **Sprint 3**: Delete static views, update routes
- **Sprint 4**: Remove `is_admin` logic completely
- **Sprint 5**: None (new API code)
- **Sprint 6**: None (new plugin system)

### Final Cleanup (After All Sprints)
- [ ] Remove `v2_` prefix from migrations
- [ ] Delete `project-docs/steps_versions/v1_steps.md` (optional)
- [ ] Archive old documentation (optional)
- [ ] Remove unused routes/controllers
- [ ] Clean up test files for deleted features

**📝 Detailed Cleanup Tasks**: See individual sprint files in [sprints/](./sprints/)

---

## ✅ Definition of Done (per Sprint)

- [ ] All tasks completed (see detailed sprint files)
- [ ] Code reviewed
- [ ] Tests written & passing
- [ ] Documentation updated
- [ ] No breaking changes (or documented)
- [ ] Feature flag tested (if applicable)
- [ ] **Cleanup tasks completed** (deprecated code removed)
- [ ] **No legacy code left behind**

**📝 Detailed Tasks & Acceptance Criteria**: See [sprints/](./sprints/) folder

---

## 📚 Documentation

### Core Documentation
- [Migration Guide](./v2_migration_guide.md) — Step-by-step migration instructions
- [API Specification](./v2_api_spec.md) — REST API documentation
- [Plugin Guide](./v2_plugin_guide.md) — Plugin development
- [Content Model](./v2_content_model.md) — Content structure & blocks
- [Developer Responsibilities](./dev-responsibilities.md) — Quality checks & best practices

### Sprint Files
- [Sprint 0 — Infrastructure & Foundation](./sprints/sprint_0.md)
- [Sprint 1 — Content Module](./sprints/sprint_1.md)
- [Sprint 2 — Media Library](./sprints/sprint_2.md)
- [Sprint 3 — Content Rendering & Theming](./sprints/sprint_3.md)
- [Sprint 4 — RBAC & Permissions](./sprints/sprint_4.md)
- [Sprint 5 — API & Headless Support](./sprints/sprint_5.md)
- [Sprint 6 — Plugins & Polish](./sprints/sprint_6.md)
- [Sprint 7 — Lightweight Public Site & Performance](./sprints/sprint_7/sprint_7.md)

### Other Documentation
- [Architecture Documentation](../architecture.md)
- [Database Schema](../database-schema.md)
- [Conventions](../conventions.md)

---

## 🎯 Quick Reference

**Για detailed tasks**: → [sprints/](./sprints/) folder  
**Για migration steps**: → [v2_migration_guide.md](./v2_migration_guide.md)  
**Για API endpoints**: → [v2_api_spec.md](./v2_api_spec.md)  
**Για plugin development**: → [v2_plugin_guide.md](./v2_plugin_guide.md)  
**Για quality checks**: → [dev-responsibilities.md](./dev-responsibilities.md)

---

**Last Updated**: 2024-11-27

