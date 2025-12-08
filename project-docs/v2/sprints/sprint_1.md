# Sprint 1 — Content Module (Core) — REVISED

**Status**: ⏳ Pending  
**Start Date**: _TBD_  
**End Date**: _TBD_  
**Διάρκεια**: 1 εβδομάδα

---

## 📋 Sprint Goal

Block-based content system. Δημιουργία του core CMS content engine με blocks, content types, και versioning.

---

## 🎯 High-Level Objectives

- Content Module fully functional (models, services, controllers)
- Block-based content editor (admin UI)
- Content types system (page, article, block)
- Content versioning/revisions
- API endpoints for content
- Admin UI για content management

⚠️ **Όχι full Media Library, Όχι public rendering, Όχι page builder UI, Όχι advanced blocks.** 

**Block Strategy για Sprint 1:**
- ✅ 3 βασικά blocks: text, hero, gallery
- ❌ Advanced blocks (products-list, κλπ) → Sprint 3
- ⚠️ Simple URL inputs για images (media picker → Sprint 2)

**Content Type Strategy:**
- ✅ Default content types (page, article, block) seeded
- ⚠️ Content Type CRUD optional (χωρίς field_definitions editing)

---

## 👥 Tasks by Developer

### Dev B — Domain Logic & Database

#### Task B1 — Content Migrations

**Περιγραφή**: Database schema για content system.

**Deliverables:**
- `create_content_types_table` migration
  - id, name, slug, field_definitions JSON, created_at, updated_at
- `create_contents_table` migration
  - id, business_id, type, slug, title, body_json, meta JSON, status (draft/published/archived), published_at, created_by, created_at, updated_at
- `create_content_revisions_table` migration
  - id, content_id, body_json, meta JSON, user_id, created_at
- Foreign keys, indexes
- Seeders: default content types (page, article, block)

**Acceptance Criteria:**
- `php artisan migrate` runs successfully
- Default content types seeded
- Database ready for content entries

---

#### Task B2 — Content Models

**Περιγραφή**: Eloquent models με relationships, scopes, casts.

**Deliverables:**
- `Content` model:
  - Relationships: `business()`, `contentType()`, `revisions()`, `creator()`
  - Scopes: `published()`, `draft()`, `archived()`, `ofType()`, `forBusiness()`
  - Casts: `body_json` → array, `meta` → array, `published_at` → datetime
  - Helper methods: `isPublished()`, `isDraft()`, `publish()`, `archive()`
- `ContentType` model:
  - Relationships: `contents()`
  - Helper: `getFieldDefinitions()`
- `ContentRevision` model:
  - Relationships: `content()`, `user()`
  - Helper: `restore()`
- `Block` value object class (optional, για type safety)

**Acceptance Criteria:**
- All relationships working
- Scopes tested
- Models ready for services

---

#### Task B3 — Content Services

**Περιγραφή**: Business logic για content operations.

**Deliverables:**
- `GetContentService`:
  - `bySlug($businessId, $slug)` — get published content by slug
  - `byType($businessId, $type)` — get all content of type
  - `withRevisions($contentId)` — get content with revision history
- `CreateContentService`:
  - Create content entry
  - Create initial revision
  - Validate business rules
- `UpdateContentService`:
  - Update content
  - Auto-create new revision before update
  - Validate business rules
- `DeleteContentService`:
  - Soft delete option
  - Cascade to revisions (optional)
- `CreateRevisionService`:
  - Manual revision creation
  - Store current state
- `RenderContentService` (skeleton, full implementation in Sprint 3):
  - Block → view renderer (placeholder)

**Acceptance Criteria:**
- All services tested
- Revision system working
- Business rules validated

---

### Dev A — Controllers, Routes & API

#### Task A1 — Admin Content Controllers

**Περιγραφή**: Full CRUD για content management.

**Deliverables:**
- `Admin/ContentController`:
  - `index()` — list with filters (type, status, business)
  - `create()` — show create form
  - `store()` — create new content
  - `edit()` — show edit form
  - `update()` — update content
  - `destroy()` — delete content
  - `preview()` — preview content (optional)
- `Admin/ContentTypeController` (optional, για Sprint 1):
  - Basic CRUD για content types
- Routes: `/admin/content`, `/admin/content-types`
- Policies: `ContentPolicy` (viewAny, view, create, update, delete)

**Acceptance Criteria:**
- All CRUD operations working
- Policies enforced
- Routes protected

---

#### Task A2 — API Content Controllers

**Περιγραφή**: Public API endpoints για headless consumption.

**Deliverables:**
- `Api/ContentController`:
  - `show($businessId, $slug)` — GET published content by slug
  - `index($businessId)` — GET all published content (with filters)
  - `byType($businessId, $type)` — GET content by type
- Routes: `/api/v1/businesses/{id}/content/{slug}`, `/api/v1/businesses/{id}/content`
- API Resources: `ContentResource` (consistent JSON format)
- Rate limiting

**Acceptance Criteria:**
- API returns consistent JSON
- Only published content accessible
- Rate limiting working

---

#### Task A3 — Form Requests & Validation

**Deliverables:**
- `StoreContentRequest`:
  - Validation: title (required), slug (unique per business), type (required), body_json (required, valid JSON array)
  - Block validation rules
- `UpdateContentRequest`:
  - Same as StoreContentRequest
  - Allow slug update (with unique check)
- Block validation helper (validate block structure)

**Acceptance Criteria:**
- All validation rules working
- Greek error messages
- Block structure validated

---

#### Task A4 — API Error Handling Enhancement

**Περιγραφή**: Standardize API error responses (enhancement από Sprint 0).

**Deliverables:**
- Global API exception handler
- Standardized response format:
  ```json
  {
    "success": true/false,
    "message": "string",
    "data": {},
    "errors": {}
  }
  ```
- Validation error formatter
- Error codes documentation

**Acceptance Criteria:**
- Consistent error format
- All API errors follow standard

---

### Dev C — Content Editor UI

#### Task C1 — Content List Page

**Περιγραφή**: Admin list view για content management.

**Deliverables:**
- `admin/content/index.blade.php`:
  - Table/list με columns: title, type, status, updated_at, actions
  - Filters: type dropdown, status dropdown, search (title/slug)
  - Status badges (draft, published, archived)
  - Quick actions: edit, duplicate, delete, preview
  - Pagination
  - Empty state

**Acceptance Criteria:**
- Filters working
- Search functional
- Actions working (edit, delete)

---

#### Task C2 — Content Editor (Create/Edit)

**Περιγραφή**: Block-based content editor.

**Deliverables:**
- `admin/content/create.blade.php` & `edit.blade.php`:
  - Basic fields form:
    - Title (required)
    - Slug (auto-generate from title, editable)
    - Content Type (dropdown)
    - Status (draft/published/archived)
  - Block builder UI:
    - Add block button (dropdown με available blocks)
    - Block list (ordered, draggable για reorder)
    - Block config forms (dynamic based on block type)
    - Remove block button
  - Preview button (opens preview modal/page)
  - Save draft / Publish buttons
  - Validation errors display

**Acceptance Criteria:**
- Block builder functional
- Block config forms working
- Save/Publish working
- Preview working (basic)

---

#### Task C3 — Block Components (Admin) — Simple Blocks Only

**Περιγραφή**: Admin UI components για βασικά block types. **Κρατάμε simple για Sprint 1** — μόνο 2-3 βασικά blocks.

**Deliverables:**
- `components/admin/blocks/` folder:
  - `text.blade.php` (WYSIWYG editor):
    - Content field (rich text editor)
    - Alignment (left/center/right) — optional
  - `hero.blade.php` (config form):
    - Fields: title, subtitle, image (URL input για Sprint 1, media picker στο Sprint 2)
    - CTA text, CTA link
  - `gallery.blade.php` (simple):
    - Images array (URL inputs για Sprint 1, media picker στο Sprint 2)
    - Columns (1-4)

**⚠️ Advanced blocks (products-list, κλπ) θα έρθουν στο Sprint 3** (μετά Media + Catalog integration).

**Acceptance Criteria:**
- 3 βασικά block config forms working
- WYSIWYG editor working
- Simple URL inputs για images (media picker στο Sprint 2)

---

#### Task C4 — Content Type Management UI (Optional, Deferred)

**Περιγραφή**: Basic UI για content types. **⚠️ Deferred για Sprint 1** — το field_definitions JSON schema είναι complex και καλύτερα να έρθει αργότερα.

**Deliverables:**
- `admin/content-types/index.blade.php` (list) — optional
- `admin/content-types/create.blade.php` (form) — optional
- Basic CRUD — **χωρίς field_definitions editing** (για αργότερα)

**⚠️ Σημείωση**: Το Content Type CRUD μπορεί να παραμείνει optional για Sprint 1. Τα default content types (page, article, block) μπορούν να είναι seeded και αρκετά για Sprint 1. Το dynamic field_definitions editing είναι advanced feature και μπορεί να έρθει σε μελλοντικό sprint.

**Acceptance Criteria:**
- Can view content types (list)
- Create/edit basic content types (name, slug) — **χωρίς field_definitions**

---

## ✅ Deliverables (End of Sprint 1)

- [ ] Content Module fully functional
- [ ] Block-based editor working
- [ ] Content types system (page, article, block)
- [ ] Content versioning/revisions
- [ ] Admin UI for content management
- [ ] API endpoints for content
- [ ] All CRUD operations working
- [ ] Policies enforced

---

## ❌ Δεν θα υπάρχουν ακόμα

- Media Library (Sprint 2)
- Public content rendering (Sprint 3)
- Full page builder UI (Sprint 3)
- Media picker full integration (Sprint 2)
- **Advanced blocks** (products-list, testimonials, κλπ) — Sprint 3
- **Content Type field_definitions editing** — Deferred (complex JSON schema)

**Αυτά θα έρθουν στα Sprint 2-3.**

---

## 📝 Sprint Notes

_Εδώ μπορείς να γράφεις ελεύθερο κείμενο για το sprint:_
- Progress updates
- Issues encountered
- Decisions made
- Questions for team
- Any other notes

---

## 🐛 Issues & Blockers

_Καταγράψε εδώ οποιαδήποτε issues ή blockers_

---

## 🧹 Cleanup Tasks

- [ ] Delete `app/Domain/CMS/` (empty folder, replaced by Content/)

---

## 📚 References

- [v2 Overview](../v2_overview.md) — Architecture & strategy
- [Content Model](../v2_content_model.md)
- [**Developer Responsibilities**](../dev-responsibilities.md) ⭐ **Read this for quality checks & best practices**
- [Migration Guide](../v2_migration_guide.md)

---

**Last Updated**: _TBD_
