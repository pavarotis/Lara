# Sprint 2 — Media Library (Core) — REVISED

**Status**: ⏳ Pending  
**Start Date**: _TBD_  
**End Date**: _TBD_  
**Διάρκεια**: 1 εβδομάδα

---

## 📋 Sprint Goal

Full-featured media management system (uploads, folders, transformations) με integration στο Content Editor.

---

## 🎯 High-Level Objectives

- Media Library module (Domain + Admin + API)
- File uploads (local + S3-ready architecture)
- Media picker UI για το Content Editor
- Folders system (nested structure)
- Image transformations (thumbnails + variants)
- API endpoints for media
- Base permissions (RBAC integration)
- Integration με blocks (Hero, Gallery)

⚠️ **Δεν υλοποιείται ακόμα:**
- ❌ Media optimization queue workers (Sprint 3)
- ❌ Advanced transformations (webp, watermarking)
- ❌ Public asset-serving (Sprint 3)
- ❌ Direct-to-S3 uploads (Sprint 4)
- ❌ Collections system (optional, deferred)

---

## 👥 Tasks by Developer

### Dev B — Domain Logic & Database

#### Task B1 — Media Database Migrations

**Περιγραφή**: Ο πλήρης database σκελετός για media library.

**Deliverables:**
- `create_media_table` migration:
  - id, business_id, folder_id (nullable), name, path, type, mime, size, metadata JSON, created_at, updated_at
  - Foreign keys, indexes
  - Soft deletes (optional)
- `create_media_folders_table` migration:
  - id, business_id, parent_id (nullable), name, path, created_by, created_at, updated_at
  - Foreign keys, indexes
  - Nested structure support

**Acceptance Criteria:**
- Migrations run without errors
- Foreign keys & indexes correct
- Folders support nested structure

---

#### Task B2 — Media Models

**Deliverables:**
- `Media` model:
  - Relationships: `business()`, `folder()`, `creator()`
  - Scopes: `ofBusiness()`, `inFolder()`, `ofType()`, `search()`
  - Accessors: `url`, `thumbnail_url`
  - Casts: `metadata` → array
- `MediaFolder` model:
  - Relationships: `children()`, `parent()`, `files()`, `business()`
  - Scopes: `ofBusiness()`, `root()` (no parent)
  - Helper: `getPath()` (full folder path)

**Acceptance Criteria:**
- All Eloquent relationships tested and correct
- Scopes working

---

#### Task B3 — Media Services

**Περιγραφή**: Business logic για media operations. **Χρησιμοποιούμε Services pattern, όχι Actions.**

**Deliverables:**
- `UploadMediaService`:
  - File validation (mime types, size)
  - Generate unique filename
  - Store in correct disk (local or S3-ready)
  - Create Media model record
  - Return Media instance
- `DeleteMediaService`:
  - Delete file from storage
  - Delete thumbnails/variants
  - Delete Media DB record
  - Handle folder cleanup (if empty)
- `GenerateVariantsService`:
  - Generate responsive image variants:
    - `thumb` (150x150)
    - `small` (400x400)
    - `medium` (800x800)
    - `large` (1200x1200)
  - Store variants in storage
  - Update Media metadata with variant paths
- `GetMediaService`:
  - `byBusiness($businessId)` — get all media for business
  - `byFolder($folderId)` — get media in folder
  - `search($businessId, $query)` — search media
  - `byType($businessId, $type)` — filter by type

**Acceptance Criteria:**
- All services tested
- File storage working (local)
- Thumbnails/variants generated
- Deletion works correctly

---

#### Task B4 — Media Policies

**Deliverables:**
- `MediaPolicy`:
  - `viewAny()` — can view media library
  - `view()` — can view specific media
  - `create()` — can upload media
  - `update()` — can edit media (rename, move)
  - `delete()` — can delete media
- `MediaFolderPolicy`:
  - `viewAny()` — can view folders
  - `create()` — can create folders
  - `update()` — can rename folders
  - `delete()` — can delete folders

**Acceptance Criteria:**
- RBAC fully enforced
- Policies tested

---

### Dev A — Controllers, Routes & API

#### Task A1 — Admin Media Controllers

**Deliverables:**
- `Admin/MediaController`:
  - `index()` — list files (filters: folder, type, search)
  - `store()` — upload file
  - `update()` — update media (rename, move to folder)
  - `destroy()` — delete file
- `Admin/MediaFolderController`:
  - `index()` — list folder structure (tree)
  - `store()` — create folder
  - `update()` — rename folder
  - `destroy()` — delete folder
- Routes:
  - `/admin/media` (GET, POST)
  - `/admin/media/{id}` (PUT, DELETE)
  - `/admin/media/folders` (GET, POST)
  - `/admin/media/folders/{id}` (PUT, DELETE)

**Acceptance Criteria:**
- All CRUD actions working
- Only users with permissions can upload/delete
- Folder operations working

---

#### Task A2 — API Endpoints (Headless)

**Deliverables:**
- `Api/MediaController`:
  - `index($businessId)` — GET list media
  - `store($businessId)` — POST upload (multipart)
  - `show($businessId, $id)` — GET single media
  - `destroy($businessId, $id)` — DELETE media
- Routes: `/api/v1/businesses/{id}/media`
- API Resources:
  - `MediaResource` — consistent JSON format with thumbnails
  - `MediaFolderResource` — folder structure
- Filter by folder, type, search

**Acceptance Criteria:**
- All responses follow API formatting standard (Sprint 1)
- Thumbnails included in JSON output
- Rate limiting working

---

#### Task A3 — Form Requests & Validation

**Deliverables:**
- `UploadMediaRequest`:
  - Validation: file (required, mime types, max size)
  - folder_id (optional)
- `CreateFolderRequest`:
  - Validation: name (required, unique per business/parent)
- `UpdateMediaRequest`:
  - Validation: name (optional), folder_id (optional)
- `UpdateFolderRequest`:
  - Validation: name (required, unique per business/parent)

**Acceptance Criteria:**
- All validation rules working
- Greek error messages
- File size/mime restrictions enforced

---

### Dev C — Admin Panel UI & Content Editor Integration

#### Task C1 — Media Library Admin UI

**Περιγραφή**: Δημιουργία πλήρους Media Manager μέσα στο admin.

**Deliverables:**
- `admin/media/index.blade.php`:
  - Grid view of files (image previews with thumbnails)
  - Left sidebar: folder tree (nested)
  - Top bar: upload button, search, filters (type, folder)
  - Actions:
    - Upload (file input + drag & drop optional)
    - Move files to folder (bulk)
    - Delete (bulk)
    - Rename (optional, single file)
  - Modal: File details (preview, size, copy URL, variants)
  - Empty state (no files)

**Acceptance Criteria:**
- UI responsive
- Folder tree functional
- Upload works from UI
- Bulk actions working

---

#### Task C2 — Media Picker Component

**Περιγραφή**: Το UI component που θα χρησιμοποιήσει το Content Editor.

**Deliverables:**
- `components/admin/media-picker.blade.php`:
  - Modal-based picker
  - Features:
    - Thumbnail grid (responsive)
    - Search bar
    - Folder navigation (breadcrumb)
    - Multiple select (for galleries)
    - Single select (for hero image)
    - Upload button (quick upload in modal)
  - Emits selected media to parent form
  - Returns clean structure:
    ```json
    {
      "id": 1,
      "url": "/storage/media/image.jpg",
      "thumbnail": "/storage/media/image-thumb.jpg"
    }
    ```

**Acceptance Criteria:**
- Works in Content Editor
- Emits clean JSON structure
- Search & folder navigation working
- Single/multiple select modes working

---

#### Task C3 — Content Editor Integration

**Deliverables:**
- Update block components to use media picker:
  - `hero.blade.php`:
    - Replace URL input → Media picker (single select)
    - Show image preview in block config
    - Save media ID in block JSON
  - `gallery.blade.php`:
    - Replace URL inputs → Media picker (multiple select)
    - Show image previews in block config
    - Save media IDs array in block JSON
- Block JSON structure:
  ```json
  {
    "type": "hero",
    "props": {
      "title": "Welcome",
      "image_id": 1,
      "image_url": "/storage/media/hero.jpg"
    }
  }
  ```

**Acceptance Criteria:**
- Blocks save/load correctly with media IDs
- Previews visible in edit mode
- Media picker integrated in both blocks

---

#### Task C4 — Drag & Drop Upload (Optional)

**Περιγραφή**: Αν προλάβει.

**Deliverables:**
- Dropzone in Media Library
- Auto-upload when file dropped in grid
- Progress indicator

**Acceptance Criteria:**
- Drag & drop working
- Progress shown

---

## ✅ Deliverables (End of Sprint 2)

- [ ] Media Library domain + DB
- [ ] Media upload working
- [ ] Image variants generated (thumb, small, medium, large)
- [ ] Folder system with tree navigation
- [ ] Media Manager UI (admin)
- [ ] Media Picker component
- [ ] Media Picker integrated with blocks (hero, gallery)
- [ ] Headless API for media
- [ ] Permissions enforced
- [ ] Content Editor supports hero & gallery blocks fully (με media picker)

---

## ❌ Δεν θα υπάρχουν ακόμα

- Full image optimization queue workers
- Video transcoding
- Direct S3 uploads (frontend)
- Public asset-serving system
- CDN integration
- Collections system (optional, deferred)

**Αυτά μπαίνουν στα Sprint 3–4.**

---

## 🧹 Cleanup Tasks

- [ ] Refactor `ImageUploadService` (existing) to use Media model
  - **Location**: `app/Domain/Catalog/Services/ImageUploadService.php`
  - **Action**: Update to use `Media` model instead of direct file storage
  - **Update**: Product/Category controllers to use Media model
  - **Note**: Don't create new service — refactor existing one

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

## 📚 References

- [v2 Overview](../v2_overview.md) — Architecture & strategy
- [Migration Guide](../v2_migration_guide.md)
- [**Developer Responsibilities**](../dev-responsibilities.md) ⭐ **Read this for quality checks & best practices**

---

**Last Updated**: _TBD_
