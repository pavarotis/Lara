# 🔄 v2 Migration Guide — Step-by-Step

## Overview

Αυτός ο οδηγός περιγράφει την **clean migration** από v1 → v2, με focus στο **cleanup** και **replacement** αντί για parallel coexistence.

**Related Documentation:**
- [v2 Overview](./v2_overview.md) — Architecture, strategy & technical specs
- [v2 Content Model](./v2_content_model.md) — Content structure & blocks
- [v2 API Spec](./v2_api_spec.md) — API endpoints

---

## 🎯 Αρχές Migration

1. **Replace, don't duplicate** — v2 modules αντικαθιστούν v1, όχι parallel
2. **Feature flags** — smooth transition με rollback capability
3. **Cleanup** — διαγραφή deprecated code μετά τη migration
4. **No legacy code** — δεν κρατάμε v1 code για "ιστορικούς λόγους"

---

## 📋 Pre-Migration Checklist

### 1. Backup & Documentation
- [ ] **Database backup** (`mysqldump` ή `php artisan db:backup`)
- [ ] **Code backup** (git tag: `v1.0-stable`)
- [ ] **Document current routes** (`php artisan route:list > routes_v1.txt`)
- [ ] **Document current views** (list all Blade files)

### 2. Environment Setup
- [ ] Create `.env.v1` backup
- [ ] Add feature flag: `CMS_ENABLED=false` (v1 active)
- [ ] Verify all v1 features work

### 3. Code Audit
- [ ] List all v1 code to be replaced:
  - `app/Domain/CMS/` (empty, will be replaced by Content/)
  - Static views: `home.blade.php`, `about.blade.php` (will be CMS content)
  - `is_admin` logic (will be RBAC)
  - Old admin views (if replaced by CMS editor)

---

## 🚀 Migration Phases

---

## Phase 1: Infrastructure (Sprint 0)

### Step 1.1: API Routes Setup
```bash
# Create api.php if not exists
touch routes/api.php
```

**Actions:**
- Setup API middleware group
- Add versioning (`/api/v1/...`)
- Configure Sanctum

**Cleanup:** None (new code)

---

### Step 1.2: Settings Module
```bash
php artisan make:migration v2_create_settings_table
php artisan make:model Domain/Settings/Models/Setting
```

**Actions:**
- Create `app/Domain/Settings/` structure
- Migrations, Models, Services
- Integrate with existing `BusinessSettingsService`

**Cleanup:** None (new module)

---

### Step 1.3: RBAC Migration
```bash
php artisan make:migration v2_create_roles_table
php artisan make:migration v2_create_permissions_table
php artisan make:migration v2_create_role_user_table
php artisan make:migration v2_create_permission_role_table
php artisan make:migration v2_migrate_is_admin_to_roles
```

**Actions:**
- Create RBAC tables
- Create `Role` & `Permission` models
- **Migration script**: Convert `is_admin=true` → `admin` role
- Update `User` model (remove `is_admin`, add `roles()`)

**Cleanup (after migration):**
- [ ] Remove `is_admin` column from users (new migration)
- [ ] Remove `AdminMiddleware` (replace with `CheckPermission`)
- [ ] Update all policies (remove `is_admin` checks)

---

## Phase 2: Content Module (Sprint 1)

### Step 2.1: Content Module Structure
```bash
php artisan make:migration v2_create_content_types_table
php artisan make:migration v2_create_contents_table
php artisan make:migration v2_create_content_revisions_table
```

**Actions:**
- Create `app/Domain/Content/` structure
- Models, Services, Policies
- Seeders: default content types

**Cleanup:** 
- [ ] Delete `app/Domain/CMS/` (empty folder)

---

### Step 2.2: Content Controllers & Routes
**Actions:**
- Create `Admin/ContentController`
- Create `ContentController` (public)
- Add routes

**Cleanup:** None (new code)

---

### Step 2.3: Content Editor UI
**Actions:**
- Create admin content editor views
- Block builder UI

**Cleanup:** None (new code)

---

## Phase 3: Media Library (Sprint 2)

### Step 3.1: Media Module
```bash
php artisan make:migration v2_create_media_folders_table
php artisan make:migration v2_create_media_table
```

**Actions:**
- Create `app/Domain/Media/` structure
- Refactor `ImageUploadService` → use Media module
- Media library UI

**Cleanup:**
- [ ] Refactor `ImageUploadService` to use Media model
- [ ] Update Product/Category controllers to use Media

---

## Phase 4: Content Rendering (Sprint 3)

### Step 4.1: Block Renderer
**Actions:**
- Create theme block views
- `RenderContentService` implementation

**Cleanup:** None (new code)

---

### Step 4.2: Migrate Static Pages → CMS
**Actions:**
- Create CMS content entries for:
  - Homepage (`/`)
  - About (`/about`)
  - Contact (`/contact`)
- Update routes to use `ContentController`

**Cleanup:**
- [ ] Delete `resources/views/home.blade.php` (replaced by CMS)
- [ ] Delete `resources/views/about.blade.php` (replaced by CMS)
- [ ] Delete `resources/views/contact.blade.php` (replaced by CMS)
- [ ] Update routes (remove static closures, use ContentController)

---

## Phase 5: RBAC Implementation (Sprint 4)

### Step 5.1: Permissions System
**Actions:**
- Define all permissions
- Update all policies
- Create role management UI

**Cleanup:**
- [ ] Remove `AdminMiddleware` (replace with `CheckPermission`)
- [ ] Update all `is_admin` checks → permissions
- [ ] Remove `is_admin` from User model casts

---

## Phase 6: API & Headless (Sprint 5)

**Actions:**
- API controllers
- API resources
- Documentation

**Cleanup:** None (new code)

---

## Phase 7: Plugins & Polish (Sprint 6)

**Actions:**
- Plugin system
- Dashboard widgets
- UX polish

**Cleanup:** None (new code)

---

## 🧹 Final Cleanup (After All Sprints)

### Code Cleanup
- [ ] Delete `app/Domain/CMS/` (empty folder)
- [ ] Delete static views replaced by CMS:
  - `resources/views/home.blade.php`
  - `resources/views/about.blade.php`
  - `resources/views/contact.blade.php`
- [ ] Remove `is_admin` logic:
  - Migration: `drop_is_admin_from_users_table`
  - Remove from User model
  - Remove AdminMiddleware
- [ ] Remove `v2_` prefix from migrations (rename to standard)
- [ ] Clean up unused routes/controllers

### Database Cleanup
- [ ] Remove `is_admin` column (after role migration verified)
- [ ] Archive old migrations (optional, keep for DB history)

### Documentation Cleanup
- [ ] Update `README.md`
- [ ] Update `architecture.md`
- [ ] Delete `project-docs/steps_versions/v1_steps.md` (optional)
- [ ] Archive old docs (optional)

### Testing Cleanup
- [ ] Remove tests for deleted features
- [ ] Update tests for new v2 features
- [ ] Run full test suite

---

## ✅ Activation Checklist

### Before Enabling v2
- [ ] All sprints completed
- [ ] All tests passing
- [ ] Database migrations run
- [ ] Data migrated (users → roles, pages → CMS)
- [ ] Feature flag: `CMS_ENABLED=false` (still v1)

### Enable v2
1. Set `CMS_ENABLED=true` in `.env`
2. Clear cache: `php artisan config:clear`
3. Test all features
4. Monitor for 24-48 hours

### Rollback Plan (if needed)
1. Set `CMS_ENABLED=false`
2. Restore database backup (if data issues)
3. Revert code (git revert)

---

## 📊 Migration Tracking

### Progress Tracker
- [ ] Sprint 0 — Infrastructure
- [ ] Sprint 1 — Content Module
- [ ] Sprint 2 — Media Library
- [ ] Sprint 3 — Content Rendering
- [ ] Sprint 4 — RBAC
- [ ] Sprint 5 — API
- [ ] Sprint 6 — Plugins
- [ ] Final Cleanup
- [ ] v2 Activated

---

## 🚨 Common Issues & Solutions

### Issue: Migration fails on `is_admin` column
**Solution:** Run migration script to convert users first, then drop column

### Issue: Static pages still showing old content
**Solution:** Verify routes updated, clear route cache: `php artisan route:clear`

### Issue: Permissions not working
**Solution:** Verify roles assigned, check middleware, clear config cache

---

**End of Migration Guide**

---

**Last Updated**: 2024-11-27

