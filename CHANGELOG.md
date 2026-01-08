# 📋 Changelog

Όλες οι αλλαγές του project καταγράφονται εδώ.

---

## [Unreleased]

### Sprint 7 — Lightweight Public Site & Performance Optimization (2026-01-08)

#### Added
- **Widget Contract Interface** (`app/Domain/Modules/Contracts/WidgetContract.php`)
  - Interface για widgets που δηλώνουν assets (CSS/JS)
  - AbstractWidget base class με default implementations
  - Asset declarations στο `config/modules.php` για hero, gallery, map modules

- **Layout Compilation Pipeline**
  - `CompileLayoutService` για compilation από JSON σε HTML
  - `CollectWidgetAssetsService` για asset collection
  - Database migration για compilation fields (`compiled_html`, `assets_manifest`, `critical_css`)

- **Per-Widget Asset Loading**
  - Vite configuration με per-widget chunks
  - Widget CSS/JS files (`resources/css/widgets/`, `resources/js/widgets/`)
  - Chunk naming: `widgets/[name]-[hash].[ext]`

- **Zero-JS Default Policy**
  - Conditional Alpine.js loading (μόνο αν χρειάζεται)
  - Base JS με mobile menu functionality (no Alpine dependency)
  - Widget-specific JS loading via `@stack('widget-scripts')`

- **Image Optimization Pipeline** (Placeholder)
  - `ImageOptimizationService` (χρειάζεται `intervention/image` package)
  - `optimized-image` Blade component με WebP/AVIF support
  - Database migration για `variants` field στο `media` table

- **Aggressive Caching Strategy**
  - `LayoutCacheService` για layout caching
  - `CachePublicPages` middleware για full page cache (guest users only)
  - Fragment cache στο `RenderModuleService` για modules
  - Cache invalidation via timestamps

- **Performance Monitoring Dashboard**
  - Filament Page: `CMS > Performance`
  - Cache statistics, layout compilation stats, asset statistics

- **Cache Management UI**
  - Filament Page: `CMS > Cache Management`
  - Cache clearing actions (all, layout, page, module)
  - Cache driver information display

#### Changed
- `config/modules.php` — Added asset declarations για hero, gallery, map modules
- `vite.config.js` — Per-widget chunks configuration
- `resources/js/app.js` — Conditional Alpine.js loading
- `resources/views/layouts/public.blade.php` — Conditional JS loading
- `app/Domain/Layouts/Models/Layout.php` — Added compilation fields
- `app/Domain/Modules/Services/RenderModuleService.php` — Fragment caching
- `app/Domain/Media/Models/Media.php` — Added variants field
- `bootstrap/app.php` — Registered CachePublicPages middleware

#### Notes
- Image optimization service είναι placeholder — ready για implementation όταν install-άρει το `intervention/image` package
- Όλα τα changes είναι backward compatible
- Zero breaking changes — existing code continues to work

---

### Sprint 6 — Platform Hardening, Routing Strategy, API, Release (2026-01-08)

#### Added
- **API v2 (Headless)**: Read-only API endpoints for business, menu, categories, products, and pages
- **API Authentication**: API key + secret authentication with scope-based access control
- **API Rate Limiting**: Per-business, per-endpoint rate limiting (100 requests/minute default)
- **API Key Management**: Filament Resource for managing API keys with auto-generation
- **API Documentation**: Admin page with complete API documentation (`/admin/api-docs`)
- **Testing Dashboard**: Admin dashboard for test suite status (`/admin/testing`)
- **Canonical Routing**: Business-based routing (`/{business:slug}/{page:slug}`)
- **Business Resolution**: Enhanced business resolution service with route → query → session fallback

#### Changed
- **API Routes**: Added v2 API routes with authentication and rate limiting middleware
- **ContentController**: Enhanced with canonical routing support (`showBusinessHome`, `show` methods)
- **Middleware**: Added `api.auth` and `api.rate_limit` middleware aliases

#### Technical
- Created `ApiKey` model with relationships, scopes, and expiration checks
- Created `ApiAuthService` and `ApiRateLimitService` for API management
- Created 5 API v2 controllers (Business, Menu, Categories, Products, Pages)
- Created 5 API v2 resources for data formatting
- Created `ApiKeyResource` Filament resource for admin management
- Created `config/api.php` for API configuration

---

### v2.0 — CMS-First Platform (In Progress)

#### Sprint 4 — OpenCart-like Layout System — ✅ **COMPLETE**

##### Dev B (Architecture/Domain) — ✅ **COMPLETE**

**Tasks Completed** (2024-12-18):
- [x] **Task B1: RenderModuleService (3-Level Rows)** (2024-12-18)
  - Completed `app/Domain/Modules/Services/RenderModuleService.php`
  - Created `resources/views/components/module-row.blade.php` component
  - 3-level pattern: row → container → content
  - Width modes: `contained`, `full`, `full-bg-contained-content`
  - Style support: background, background_image, padding, margin
  - Error handling for disabled modules and missing views
- [x] **Task B2: GetModuleViewService (Theme Resolution)** (2024-12-18)
  - Verified existing service (already complete)
  - Theme resolution with fallback chain
  - Business theme support
- [x] **Task B3: Module View Structure** (2024-12-18)
  - Created 14 module views in `resources/views/themes/default/modules/`:
    - hero, rich-text, image, gallery
    - banner, cta, menu, products-grid
    - categories-list, map, opening-hours
    - contact-card, faq, testimonials
  - All views use `$settings` from module instance
  - Media loading with variants support
  - Responsive design with TailwindCSS
- [x] **Task B4: Layout View Structure** (2024-12-18)
  - Created `default.blade.php` (with sidebars)
  - Created `full-width.blade.php` (without sidebars)
  - Created `landing.blade.php` (minimal structure)
  - All layouts extend `layouts.public`
  - Regions rendering support
  - Responsive layout structure
- [x] **Task B5: GetThemeDefaultModulesService** (2024-12-18)
  - Created `app/Domain/Themes/Services/GetThemeDefaultModulesService.php`
  - JSON file support for theme defaults
  - Module instance creation from defaults
  - Error handling & logging

##### Dev A (Backend/Infrastructure) — ✅ **COMPLETE**

**Tasks Completed** (2024-11-27):
- [x] **Task A1: Database Migrations** (2024-11-27)
  - Created `v2_2024_11_27_000006_create_layouts_table.php`
  - Created `v2_2024_11_27_000007_create_module_instances_table.php`
  - Created `v2_2024_11_27_000008_create_content_module_assignments_table.php`
  - Created `v2_2024_11_27_000009_add_layout_id_to_contents_table.php`
  - All migrations run successfully
  - Foreign keys with `cascadeOnDelete()` for business isolation
  - Indexes for performance (business_id, type, region, sort_order)
  - Unique constraint on junction table
  - `layout_id` nullable for backward compatibility
- [x] **Task A2: Layout & Module Models** (2024-11-27)
  - Created `app/Domain/Layouts/Models/Layout.php`
  - Created `app/Domain/Modules/Models/ModuleInstance.php`
  - Created `app/Domain/Modules/Models/ContentModuleAssignment.php`
  - All relationships implemented (business, contents, assignments, etc.)
  - Scopes implemented (forBusiness, ofType, enabled, reusable, etc.)
  - Helper methods implemented (hasRegion, getRegions, isReusable, getSetting)
  - JSON casts working (regions, settings, style)
  - Updated `Content` model with `layout_id` and `layout()` relationship
- [x] **Task A3: Layout & Module Services (Core)** (2024-11-27)
  - Created `app/Domain/Layouts/Services/GetLayoutService.php`
  - Created `app/Domain/Layouts/Services/CreateLayoutService.php`
  - Created `app/Domain/Modules/Services/GetModulesForRegionService.php`
  - Created `app/Domain/Modules/Services/CreateModuleInstanceService.php`
  - Created `app/Domain/Modules/Services/UpdateModuleInstanceService.php`
  - Created `app/Domain/Modules/Services/AssignModuleToContentService.php`
  - Created `app/Domain/Modules/Services/ValidateModuleTypeService.php`
  - All services use `declare(strict_types=1);`
  - Type hints & return types everywhere
  - Constructor injection for dependencies
  - Proper validation & error handling
  - Business isolation enforced
- [x] **Task A4: RenderLayoutService (Core Rendering)** (2024-11-27)
  - Created `app/Domain/Layouts/Services/RenderLayoutService.php`
  - Created `app/Domain/Modules/Services/RenderModuleService.php` (completed by Dev B in Task B1)
  - Created `app/Domain/Modules/Services/GetModuleViewService.php`
  - Enhanced `RenderContentService` with dual mode:
    - If `layout_id` exists → use `RenderLayoutService` (layout-based)
    - If `layout_id` is NULL → render legacy `body_json` blocks
  - Backward compatibility maintained (legacy blocks still work)
- [x] **Task A5: Module Registry Configuration** (2024-11-27)
  - Created `config/modules.php`
  - All v1 modules defined (hero, rich_text, image, banner, menu, products_grid, categories_list, gallery, cta, map, opening_hours, contact_card, faq, testimonials)
  - Each module has: name, icon, category, settings_form, view, description
  - Configuration is extensible

##### Dev C (Frontend/UI) — ✅ **COMPLETE**

**Tasks Completed** (2024-12-18):
- [x] **Task C1: Module Settings Forms (Form Requests)** (2024-12-18)
  - Created 14 Form Request classes in `app/Http/Requests/Modules/`:
    - HeroModuleRequest, RichTextModuleRequest, ImageModuleRequest
    - GalleryModuleRequest, BannerModuleRequest, CtaModuleRequest
    - MenuModuleRequest, ProductsGridModuleRequest, CategoriesListModuleRequest
    - MapModuleRequest, OpeningHoursModuleRequest, ContactCardModuleRequest
    - FaqModuleRequest, TestimonialsModuleRequest
  - All use `declare(strict_types=1);`
  - Proper validation rules for each module type
  - Authorization checks (`isAdmin()`)
  - Custom validation logic where needed (GalleryModuleRequest, MapModuleRequest)
- [x] **Task C2: Admin UI: Layout Selection** (2024-12-18)
  - Layout dropdown added to `ContentController::create()` and `edit()`
  - Layout selection in `resources/views/admin/content/create.blade.php`
  - Layout selection in `resources/views/admin/content/edit.blade.php`
  - Business scoping: `Layout::forBusiness($business->id)`
  - "Manage Modules" button when layout is selected
- [x] **Task C3: Admin UI: Region → Modules Management** (2024-12-18)
  - Created `app/Http/Controllers/Admin/ContentModuleController.php`
  - Created `resources/views/admin/content/modules.blade.php`
  - All CRUD operations: add, reorder, toggle, remove
  - Drag & drop reordering with Alpine.js
  - Enable/disable toggles
  - Add module modal per region
  - Module grouping by type
  - Assignment count validation before delete
- [x] **Task C4: Admin UI: Module Instance CRUD (Filament)** (2024-12-18)
  - Created `app/Filament/Resources/ModuleInstanceResource.php`
  - Created `app/Filament/Resources/ModuleInstanceResource/Pages/ListModuleInstances.php`
  - Created `app/Filament/Resources/ModuleInstanceResource/Pages/CreateModuleInstance.php`
  - Created `app/Filament/Resources/ModuleInstanceResource/Pages/EditModuleInstance.php`
  - Form fields: business_id, type, name, enabled, settings, style, width_mode
  - Table columns: id, type, name, business, enabled, width_mode, assignments_count
  - Filters: type, enabled, business_id
  - Delete protection (checks assignments)
- [x] **Task C5: Module Row Component Styling** (2024-12-18)
  - Verified `resources/views/components/module-row.blade.php` (created by Dev B)
  - Responsive container with TailwindCSS
  - Width modes: contained, full, full-bg-contained-content
  - Style support: background, background_image, padding, margin
  - Proper inline styles handling

**Code Quality**:
- ✅ All code uses `declare(strict_types=1);`
- ✅ Type hints & return types everywhere
- ✅ Proper validation & error handling
- ✅ Business isolation enforced (fixed)
- ✅ User-friendly UI/UX
- ✅ Responsive design

**Master DEV Review** (2024-12-18):
- ✅ All tasks completed with excellent quality
- ✅ **3 critical issues found and fixed**:
  1. Business Isolation Missing in ContentModuleController — Module loading now business-scoped
  2. Business Isolation Missing in ModuleInstanceResource — Query now scoped by business
  3. Business ID Not Auto-Set in CreateModuleInstance — Auto-set business_id added
- ✅ Code quality excellent
- ✅ All deliverables present
- ✅ User-friendly UI/UX
- **Status**: Approved — All tasks complete, all issues fixed

**Detailed Review**: See `project-docs/v2/sprints/sprint_4/reviews/sprint_4_review_devc.md`  
**Fixes Guide**: See `project-docs/v2/sprints/sprint_4/reviews/sprint_4_review_devc_fixes_guide.md`

#### Sprint 3 — Content Rendering & Theming — ✅ **COMPLETE**

##### Dev A (Backend/Infrastructure) — ✅ **REVIEWED & APPROVED**

**Master DEV Review** (2024-11-27):
- ✅ All tasks completed with excellent quality
- ✅ No missing deliverables found
- ✅ Code quality excellent
- ✅ Route priority correctly configured
- ✅ Migration command ready and well-structured
- ✅ Controller ready for service integration (pending Dev B)
- **Status**: Approved — Ready for Dev B & Dev C

**Detailed Review**: See `project-docs/v2/sprints/sprint_3/reviews/sprint_3_review_deva.md`

##### Dev A (Backend/Infrastructure) — ✅ **COMPLETE**

**Tasks Completed** (2024-11-27):
- [x] **Task A1: Content Controller (Public)** (2024-11-27)
  - Created `ContentController@show` for public content rendering
  - Gets content by slug & business using `GetContentService`
  - Checks if published (404 if not found)
  - Renders via `RenderContentService` (placeholder from Dev B)
  - Returns view `themes.default.layouts.page` with content
  - Route: `/{slug}` (dynamic, after static routes)
  - Route priority: static routes first, then dynamic content
  - Route constraint: excludes admin, api, cart, checkout, menu, dashboard, profile, auth routes
- [x] **Task A2: Migration: Static Pages → CMS** (2024-11-27)
  - Created Artisan command: `php artisan cms:migrate-static-pages`
  - Migrates home page (slug: `/`) → CMS content with hero + text blocks
  - Migrates about page (slug: `about`) → CMS content with text blocks
  - Migrates contact page (slug: `contact`) → CMS content with text blocks
  - All content set to `published` status
  - Includes SEO meta tags (description, keywords)
  - Note: Contact form functionality kept separate from CMS
- [x] **Task A3: Route Priority & Fallback** (2024-11-27)
  - Route ordering: static routes first, then dynamic content
  - Dynamic route `/{slug}` placed after all static routes
  - Route constraint prevents conflicts with existing routes
  - 404 handling for non-existent content (via `abort(404)`)
  - Updated routes: removed static closures, added comments for migration

**Code Quality**:
- ✅ All controllers use `declare(strict_types=1);`
- ✅ Type hints & return types everywhere
- ✅ Constructor injection for dependencies
- ✅ Follows Service Layer Pattern
- ✅ Route priority properly configured
- ✅ No linting errors

##### Dev B (Architecture/Domain) — ✅ **REVIEWED & APPROVED**

**Master DEV Review** (2024-11-27):
- ✅ All tasks completed with excellent quality
- ✅ No missing deliverables found
- ✅ Code quality excellent
- ✅ All services, theme structure, and block views properly implemented
- ✅ Complete theme system with fallback mechanisms
- **Status**: Approved — Ready for Dev C

**Detailed Review**: See `project-docs/v2/sprints/sprint_3/reviews/sprint_3_review_devb.md`

##### Dev B (Architecture/Domain) — ✅ **COMPLETE**

**Tasks Completed** (2024-11-27):
- [x] **Task B1: Block Renderer Service** (2024-11-27)
  - Enhanced `RenderContentService` from skeleton to full implementation
  - Added `render()` method: Renders full content (array of blocks)
  - Added `renderBlock()` method: Renders single block
  - Theme resolution: Get from business settings, fallback to 'default'
  - View path resolution: `themes.{theme}.blocks.{type}` → fallback to `themes.default.blocks.{type}`
  - Block props injection to views
  - Error handling: Missing block views → fallback message, logs warnings
  - Backward compatibility: `execute()` method aliases to `render()`
- [x] **Task B2: Theme Structure** (2024-11-27)
  - Created `resources/views/themes/default/` folder structure
  - Created `blocks/` directory for block views
  - Created `layouts/` directory for page layout
- [x] **Task B3: Block Views Implementation** (2024-11-27)
  - Created `hero.blade.php`: Hero section with title, subtitle, image, CTA button
  - Created `text.blade.php`: WYSIWYG content with alignment support
  - Created `gallery.blade.php`: Image gallery with responsive grid (1-4 columns)
  - All blocks load media from IDs
  - All blocks are responsive
- [x] **Task B4: Page Layout Wrapper** (2024-11-27)
  - Created `layouts/page.blade.php`: Wrapper for CMS pages
  - Extends public layout
  - SEO meta tags from content meta (description, keywords, OG image)
  - Dynamic title per page
  - OG tags and Twitter card support

**Code Quality**:
- ✅ All services use `declare(strict_types=1);`
- ✅ Type hints & return types everywhere
- ✅ Proper theme resolution with fallback
- ✅ Graceful error handling
- ✅ Media loading from IDs
- ✅ Responsive design
- ✅ Complete SEO implementation
- ✅ Follows existing patterns from other domains
- ✅ No linting errors

**Architecture Decisions**:
- ✅ Theme resolution: Get from business settings, fallback to 'default'
- ✅ Block props: Pass directly to views as variables
- ✅ Media loading: Load in views (simple for Sprint 3 scope)
- ✅ Error handling: Graceful degradation with HTML comments

##### Dev C (Frontend/UI) — ✅ **REVIEWED & APPROVED**

**Master DEV Review** (2024-11-27):
- ✅ All tasks completed with excellent quality
- ✅ No missing deliverables found
- ✅ Code quality excellent
- ✅ Responsive design excellent
- ✅ SEO implementation comprehensive
- ✅ Preview functionality fully implemented
- **Status**: Approved — Sprint 3 Complete

**Detailed Review**: See `project-docs/v2/sprints/sprint_3/reviews/sprint_3_review_devc.md`

##### Dev C (Frontend/UI) — ✅ **COMPLETE**

**Tasks Completed** (2024-11-27):
- [x] **Task C1: Theme Block Views (Styling)** (2024-11-27)
  - Hero block: Responsive images with srcset, CTA styling, overlay effects
  - Text block: Typography styling, alignment support, responsive spacing
  - Gallery block: Responsive grid, aspect ratios, lightbox ready
- [x] **Task C2: SEO & Meta Tags** (2024-11-27)
  - Title, description, keywords from content
  - OG image from media
  - Canonical URL, OG tags, Twitter Card
  - Dynamic per content
- [x] **Task C3: Content Preview (Optional)** (2024-11-27)
  - Preview banner implemented
  - Preview route and controller implemented
  - Admin-only access with authorization
  - Fully functional preview system

**Code Quality**:
- ✅ Clean, well-structured Blade templates
- ✅ Excellent responsive design
- ✅ Proper image optimization (srcset)
- ✅ Comprehensive SEO implementation
- ✅ Good use of TailwindCSS
- ✅ Smooth transitions and hover effects
- ✅ No linting errors

**Sprint 3 Final Status** (2024-11-27):
- ✅ All developers completed tasks (100% completion)
- ✅ All deliverables met (10/10)
- ✅ All critical issues resolved (7 issues fixed)
- ✅ 2 enhancements applied (eager loading, variants support)
- ✅ Code quality excellent across all developers
- ✅ System fully functional and ready for production
- ✅ Ready for Sprint 4

**Detailed Reviews**:
- Dev A: `project-docs/v2/sprints/sprint_3/reviews/sprint_3_review_deva.md`
- Dev B: `project-docs/v2/sprints/sprint_3/reviews/sprint_3_review_devb.md`
- Dev C: `project-docs/v2/sprints/sprint_3/reviews/sprint_3_review_devc.md`
- Final Review: `project-docs/v2/sprints/sprint_3/reviews/sprint_3_final_check.md`

#### Sprint 2 — Media Library (Core) — ✅ **COMPLETE**

**Sprint 2 Final Review** (2024-11-27):
- ✅ All developers completed tasks with excellent quality
- ✅ **Issues Found & Fixed**:
  - Dev B: 1 issue (missing creator relationship in Media model) — Fixed
  - Dev C: 5 issues (1 critical + 4 minor) — All Fixed
    - Critical: Hero block data flow mismatch
    - Minor: Move Modal, File Details Modal, Folder Loading, Upload Button
- ✅ All deliverables met
- ✅ Media Library fully functional and integrated
- ✅ Content Editor integration complete
- ✅ Services fully integrated in controllers
- ✅ Ready for Sprint 3

**Completion Status**: **100%** (excluding optional Task C4 - Drag & Drop Upload)

**Detailed Reviews**: 
- Dev A: `project-docs/v2/sprints/sprint_2/reviews/sprint_2_review_deva.md`
- Dev B: `project-docs/v2/sprints/sprint_2/reviews/sprint_2_review_devb.md`
- Dev C: `project-docs/v2/sprints/sprint_2/reviews/sprint_2_review_devc.md`

##### Dev A (Backend/Infrastructure) — ✅ **REVIEWED & APPROVED**

**Master DEV Review** (2024-11-27):
- ✅ All tasks completed with excellent quality
- ✅ No missing deliverables found
- ✅ Code quality excellent
- ✅ Controllers ready for service integration (pending Dev B)
- ✅ All routes registered and working
- ✅ API Resources for consistent JSON format
- ✅ Comprehensive validation with Greek messages
- **Status**: Approved — Ready for Dev B & Dev C

**Detailed Review**: See `project-docs/v2/sprints/sprint_2/reviews/sprint_2_review_deva.md`

##### Dev A (Backend/Infrastructure) — ✅ **COMPLETE**

**Tasks Completed** (2024-11-27):
- [x] **Task A1: Admin Media Controllers** (2024-11-27)
  - Created `Admin/MediaController` with index, store, update, destroy methods
  - Created `Admin/MediaFolderController` with index, store, update, destroy methods
  - Filters: folder_id, type, search
  - Pagination support
  - Routes: `/admin/media/*` and `/admin/media/folders/*`
  - Services integrated: UploadMediaService, DeleteMediaService, GetMediaService
- [x] **Task A2: API Endpoints (Headless)** (2024-11-27)
  - Created `Api/V1/MediaController` with index, store, show, destroy methods
  - Created `MediaResource` for consistent JSON format
  - Created `MediaFolderResource` for folder structure
  - All methods use Resources for API consistency
  - Filters: folder_id, type, search
  - Pagination support
  - Routes: `/api/v1/businesses/{id}/media/*`
  - All methods functional with services integrated
- [x] **Task A3: Form Requests & Validation** (2024-11-27)
  - Created `UploadMediaRequest` with validation:
    - file/files (required, supports single or multiple files)
    - Extended file types: images (jpeg, png, gif, webp), videos (mp4, mpeg), PDFs
    - Max file size: 10MB
    - folder_id (optional)
  - Created `CreateFolderRequest` with validation:
    - name (required, unique per business/parent)
    - business_id (required)
    - parent_id (optional)
  - Created `UpdateMediaRequest` with validation:
    - name (optional, string, max 255)
    - folder_id (optional)
  - Created `UpdateFolderRequest` with validation:
    - name (required, unique per business/parent, ignore current)
  - Greek error messages for all validation rules

**Code Quality**:
- ✅ All controllers use `declare(strict_types=1);`
- ✅ Type hints & return types everywhere
- ✅ Services fully integrated from Dev B (UploadMediaService, DeleteMediaService, GetMediaService)
- ✅ Follows Service Layer Pattern
- ✅ API Resources for consistent JSON format
- ✅ Multiple file upload support
- ✅ Folders passed to views for UI integration
- ✅ No linting errors

##### Dev B (Architecture/Domain) — ✅ **REVIEWED & APPROVED**

**Master DEV Review** (2024-11-27):
- ✅ All tasks completed with excellent quality
- ✅ No missing deliverables found
- ✅ Code quality excellent
- ✅ All models, services, and policies properly implemented
- ✅ Native PHP GD implementation for image variants (no external dependencies)
- **Status**: Approved — Ready for Dev C

**Detailed Review**: See `project-docs/v2/sprints/sprint_2/reviews/sprint_2_review_devb.md`

##### Dev B (Architecture/Domain) — ✅ **COMPLETE**

**Tasks Completed** (2024-11-27):
- [x] **Task B1: Media Database Migrations** (2024-11-27)
  - Verified migrations already created in Sprint 0 (correct structure)
  - Foreign keys and indexes properly configured
  - Nested folder structure supported
- [x] **Task B2: Media Models** (2024-11-27)
  - **Media Model**:
    - Added scopes: `ofBusiness()`, `inFolder()`, `ofType()`, `search()`
    - Added accessors: `url`, `thumbnail_url`
    - All relationships: `business()`, `folder()`
    - Casts: `metadata` → array, `size` → integer
  - **MediaFolder Model**:
    - Added scopes: `ofBusiness()`, `root()`
    - Added helper: `getPath()`
    - All relationships: `children()`, `parent()`, `files()`, `business()`, `creator()`
- [x] **Task B3: Media Services** (2024-11-27)
  - Created `UploadMediaService`: File upload, unique filename generation, automatic variant generation, Media record creation
  - Created `DeleteMediaService`: File deletion, variant cleanup, empty folder cleanup
  - Created `GenerateVariantsService`: Image variants (thumb, small, medium, large) using native PHP GD
  - Created `GetMediaService`: byBusiness, byFolder, search, byType methods
- [x] **Task B4: Media Policies** (2024-11-27)
  - Created `MediaPolicy`: viewAny, view, create, update, delete with RBAC support
  - Created `MediaFolderPolicy`: viewAny, create, update, delete with RBAC support

**Code Quality**:
- ✅ All models use `declare(strict_types=1);`
- ✅ Type hints & return types everywhere
- ✅ Proper relationships and scopes
- ✅ Useful accessors for URL generation
- ✅ DB transactions for multi-step operations
- ✅ Proper error handling
- ✅ Follows existing patterns from other domains
- ✅ No linting errors

**Architecture Decisions**:
- ✅ Native PHP GD for image variants (no external dependencies)
- ✅ `asset('storage/...')` for public URLs
- ✅ Automatic empty folder cleanup
- ✅ File type determination from MIME type

**Bugs Fixed During Review**:
- ✅ **Missing creator() relationship in Media model** (2024-11-27)
  - Issue: Media model missing `creator()` relationship (discovered during review)
  - Fix: Added `creator()` relationship to Media model: `belongsTo(User::class, 'created_by')`
  - Status: Fixed and verified
  - Impact: Media records can now access creator user information

##### Dev C (Frontend/UI) — ✅ **REVIEWED & APPROVED**

**Master DEV Review** (2024-11-27):
- ✅ All tasks completed with excellent quality
- ✅ 1 critical issue found and fixed:
  1. Hero Block Data Flow Mismatch — Field names corrected with `getFieldName()` method
- ✅ 4 minor issues found and fixed:
  1. Move Modal Missing — Complete modal implementation added
  2. File Details Modal Placeholder — Full implementation with preview and copy functionality
  3. Media Picker Folder Loading — Enhanced to use parent context
  4. Upload Button in Media Picker Modal — Quick upload button added
- ✅ Code quality excellent
- ✅ All deliverables met
- **Status**: Approved — Sprint 2 Complete, Ready for Sprint 3

**Detailed Review**: See `project-docs/v2/sprints/sprint_2/reviews/sprint_2_review_devc.md`

##### Dev C (Frontend/UI) — ✅ **COMPLETE**

**Tasks Completed** (2024-11-27):
- [x] **Task C1: Media Library Admin UI** (2024-11-27)
  - Created `admin/media/index.blade.php` with full media library interface
  - Grid view with image previews and thumbnails
  - Left sidebar with folder tree
  - Top bar with upload button, search, filters (type, folder)
  - Bulk actions bar (Move, Delete, Clear)
  - View toggle (Grid/List)
  - Empty state
  - Create folder modal
  - File upload functionality (multiple files support)
  - Delete functionality (single & bulk)
  - **Move Modal** — Complete with folder selection and bulk file movement
  - **File Details Modal** — Complete with file info, preview, and copy URL functionality
- [x] **Task C2: Media Picker Component** (2024-11-27)
  - Created `components/admin/media-picker.blade.php` — Reusable media picker component
  - Modal-based picker interface
  - Thumbnail grid (responsive)
  - Search bar
  - Folder navigation (breadcrumb)
  - Multiple select mode (for galleries)
  - Single select mode (for hero image)
  - **Upload Button** — Quick upload in modal empty state
  - **Folder Loading** — Enhanced to load from parent context
- [x] **Task C3: Content Editor Integration** (2024-11-27)
  - Updated `components/admin/blocks/hero.blade.php` to use media-picker
  - Updated `components/admin/blocks/gallery.blade.php` to use media-picker
  - Image preview in hero block
  - Gallery preview in gallery block
  - **Fixed**: Hero block data flow issue — Added `getFieldName()` method to convert field names correctly
  - Backend integration: `ContentController` updated to handle media picker data format
- [ ] **Task C4: Drag & Drop Upload** (Optional)
  - Not implemented (optional enhancement for future sprint)

**Controller Enhancements** (2024-11-27):
- `MediaController` — Integrated services from Dev B:
  - `UploadMediaService` for file uploads
  - `DeleteMediaService` for file deletion
  - `GetMediaService` for data retrieval
  - Multiple file upload support added
  - Folders passed to index view for sidebar
- `UploadMediaRequest` — Extended for multiple files and extended file types:
  - Support for `files[]` array and single `file` input
  - Extended file types: images, videos, PDFs
  - Improved validation messages

**Code Quality**:
- ✅ Clean, well-structured Blade templates
- ✅ Good use of Alpine.js for interactivity
- ✅ Responsive design
- ✅ Proper error handling
- ✅ Consistent code style
- ✅ Server-side rendering with progressive enhancement
- ✅ Component-based architecture for reusability

**Bugs Fixed During Review**:
1. ✅ **Hero Block Data Flow Mismatch** (Critical)
   - Issue: Field names `blocks[X][props][image]_id` instead of `blocks[X][props][image_id]`
   - Fix: Added `getFieldName()` method to media-picker component
   - Status: Fixed and tested
2. ✅ **Move Modal Missing** (High Priority)
   - Issue: Button existed but modal not implemented
   - Fix: Complete modal with folder selection and `moveFiles()` method
   - Status: Complete
3. ✅ **File Details Modal Placeholder** (Medium Priority)
   - Issue: Modal existed but was empty
   - Fix: Full implementation with preview, metadata, and copy URL
   - Status: Complete
4. ✅ **Media Picker Folder Loading** (Low Priority)
   - Issue: `loadFolders()` set empty array
   - Fix: Enhanced to use folders from parent view context
   - Status: Complete
5. ✅ **Upload Button in Media Picker Modal** (Low Priority)
   - Issue: Spec mentioned upload button but not implemented
   - Fix: Added "Quick Upload" button with `handleQuickUpload()` method
   - Status: Complete

**Sprint 2 Final Status** (2024-11-27):
- ✅ All tasks completed by all developers (100% completion)
- ✅ All critical issues fixed
- ✅ All minor issues resolved
- ✅ All deliverables met
- ✅ Code quality excellent
- ✅ Services fully integrated
- ✅ Media Library fully functional and integrated with Content Editor
- ✅ Ready for Sprint 3

#### Sprint 1 — Content Module (Core) — ✅ **COMPLETE**

##### Dev A (Backend/Infrastructure) — ✅ **REVIEWED & APPROVED**

**Master DEV Review** (2024-11-27):
- ✅ All tasks completed with excellent quality
- ✅ 2 missing deliverables found and fixed:
  1. ContentResource (Task A2) — explicit deliverable
  2. Error Codes Documentation (Task A4) — explicit deliverable
- ✅ Code quality excellent
- ✅ Lessons learned documented
- **Status**: Approved — Ready for Dev B & Dev C

**Detailed Review**: See `project-docs/v2/sprints/sprint_1/reviews/sprint_1_review_deva.md`

##### Dev A (Backend/Infrastructure) — ✅ **COMPLETE**

**Tasks Completed** (2024-11-27):
- [x] **Task A1: Admin Content Controllers** (2024-11-27)
  - Created `Admin/ContentController` with full CRUD (index, create, store, show, edit, update, destroy)
  - Added `publish()` method for content publishing
  - Added `preview()` method for content preview
  - Filters: type, status, search (title/slug)
  - Pagination support
  - Routes: `/admin/content/*` (resource + publish route)
- [x] **Task A2: API Content Controllers** (2024-11-27)
  - Created `Api/V1/ContentController` with show, index, byType methods
  - Created `ContentResource` for consistent JSON format (explicit deliverable)
  - All methods use ContentResource for API consistency
  - Only published content accessible via API
  - Filters: type, search
  - Pagination support
  - Routes: `/api/v1/businesses/{id}/content/*`
- [x] **Task A3: Form Requests & Validation** (2024-11-27)
  - Created `StoreContentRequest` with validation rules:
    - title (required), slug (unique per business), type (required, in: page/article/block)
    - body_json (required, array), block structure validation
    - meta (nullable, array), status (nullable, in: draft/published/archived)
  - Created `UpdateContentRequest` with same rules + unique slug check (ignore current)
  - Greek error messages for all validation rules
- [x] **Task A4: API Error Handling Enhancement** (2024-11-27)
  - Enhanced exception handling in `bootstrap/app.php`
  - Standardized API response format: `{ success, message, errors, data }`
  - Handles: Validation (422), Authentication (401), Authorization (403), NotFound (404), MethodNotAllowed (405), Throttle (429), General (500)
  - Debug mode: Shows exception details in development, generic messages in production
  - Created `project-docs/v2/api_error_codes.md` with complete error codes documentation (explicit deliverable)
  - Error codes documentation includes: HTTP status codes, examples, solutions, rate limiting, success response format

**Services Created** (2024-11-27):
- `GetContentService`: bySlug(), byType(), withRevisions()
- `CreateContentService`: Creates content + initial revision in transaction
- `UpdateContentService`: Auto-creates revision before update in transaction
- `DeleteContentService`: Soft delete support
- `PublishContentService`: Updates status to published + sets published_at timestamp

**Policies Created** (2024-11-27):
- `ContentPolicy`: viewAny, view, create, update, delete with RBAC support (fallback to is_admin)

**Code Quality**:
- ✅ All services use `declare(strict_types=1);`
- ✅ Type hints & return types everywhere
- ✅ Constructor injection for dependencies
- ✅ DB transactions for multi-step operations
- ✅ Follows Service Layer Pattern
- ✅ No linting errors

##### Dev B (Architecture/Domain) — ✅ **REVIEWED & APPROVED**

**Master DEV Review** (2024-11-27):
- ✅ All tasks completed with excellent quality
- ✅ No missing deliverables found
- ✅ Code quality excellent
- ✅ All models, scopes, and helpers properly implemented
- **Status**: Approved — Ready for Dev C

**Detailed Review**: See `project-docs/v2/sprints/sprint_1/reviews/sprint_1_review_devb.md`

##### Dev B (Architecture/Domain) — ✅ **COMPLETE**

**Tasks Completed** (2024-11-27):
- [x] **Task B1: Content Migrations** (2024-11-27)
  - Created `ContentTypeSeeder` with default content types (page, article, block)
  - Added seeder to `DatabaseSeeder`
  - Migrations already created by Dev A (verified correct)
- [x] **Task B2: Content Models** (2024-11-27)
  - **Content Model**:
    - Added scopes: `forBusiness()`, `ofType()`, `archived()`
    - Added helper methods: `isPublished()`, `isDraft()`, `publish()`, `archive()`
    - Added relationship: `contentType()` (belongsTo ContentType via type → slug)
    - All relationships: `business()`, `contentType()`, `revisions()`, `creator()`
    - All casts: `body_json` → array, `meta` → array, `published_at` → datetime
  - **ContentType Model**:
    - Added relationship: `contents()`
    - Added helper: `getFieldDefinitions()`
  - **ContentRevision Model**:
    - Added helper: `restore()` method
- [x] **Task B3: Content Services** (2024-11-27)
  - Verified existing services meet requirements (GetContentService, CreateContentService, UpdateContentService, DeleteContentService, PublishContentService)
  - Created `CreateRevisionService` for manual revision creation
  - Created `RenderContentService` skeleton/placeholder (full implementation in Sprint 3)

**Code Quality**:
- ✅ All models use `declare(strict_types=1);`
- ✅ Type hints & return types everywhere
- ✅ Proper relationships and scopes
- ✅ Useful helper methods
- ✅ Follows existing patterns from other domains
- ✅ No linting errors

**Next Steps** (Dev C):
- Admin UI views for content management
- Block editor UI components

##### Dev C (Frontend/UI) — ✅ **REVIEWED & APPROVED**

**Master DEV Review** (2024-11-27):
- ✅ All tasks completed with excellent quality
- ✅ 3 bugs found and fixed:
  1. Body JSON Data Flow Issue — Frontend/backend data format misalignment
  2. ContentType Dropdown Not Populated — Missing variable in controller
  3. Content Type Selection Not Preserving — Incorrect comparison in dropdown
- ✅ Code quality excellent
- ✅ Dynamic block editor UI working perfectly
- ✅ All acceptance criteria met
- **Status**: Approved — Sprint 1 Complete, Ready for Sprint 2

**Detailed Review**: See `project-docs/v2/sprints/sprint_1/reviews/sprint_1_review_devc.md`

##### Dev C (Frontend/UI) — ✅ **COMPLETE**

**Tasks Completed** (2024-11-27):
- [x] **Task C1: Content List Page** (2024-11-27)
  - Created `admin/content/index.blade.php` with table/list view
  - Columns: title, type, status, updated_at, actions
  - Filters: type dropdown, status dropdown, search (title/slug)
  - Status badges (draft, published, archived) — color-coded
  - Quick actions: edit, view, delete
  - Pagination support
  - Empty state handling
  - Flash messages (success/error)
  - Responsive design
- [x] **Task C2: Content Editor (Create/Edit)** (2024-11-27)
  - Created `admin/content/create.blade.php` with block-based editor
  - Created `admin/content/edit.blade.php` with block loading
  - Created `admin/content/show.blade.php` for content details (bonus)
  - Basic fields form:
    - Title (required) with auto-slug generation
    - Slug (editable, auto-populated from title)
    - Content Type (dropdown from database)
    - Status (draft/published/archived)
  - Block builder UI:
    - Add block button with dropdown (text, hero, gallery)
    - Block list (dynamic rendering with Alpine.js)
    - Block config forms (dynamic based on block type)
    - Remove block button
  - Save/Publish functionality
  - Form validation errors display (inline)
  - Form data persistence on validation errors
  - Gallery images handling (newline-separated URLs converted to array)
- [x] **Task C3: Block Components (Admin)** (2024-11-27)
  - Created `components/admin/blocks/text.blade.php`:
    - Content field (textarea, ready for WYSIWYG integration)
    - Alignment selector (left/center/right)
  - Created `components/admin/blocks/hero.blade.php`:
    - Fields: title, subtitle, image URL (media picker → Sprint 2)
    - CTA text and CTA link
  - Created `components/admin/blocks/gallery.blade.php`:
    - Images array (newline-separated URLs, media picker → Sprint 2)
    - Columns selector (1-4)
  - All blocks support props configuration
- [x] **Task C4: Navigation Link** (2024-11-27)
  - Added Content link to admin sidebar
  - Positioned in navigation structure
  - Consistent styling with other navigation items

**Controller Enhancements** (2024-11-27):
- Updated `ContentController` to pass `$contentTypes` to views
- Enhanced `store()` and `update()` methods to handle `blocks` array input
- Added gallery image conversion logic (newline-separated to array)

**Form Request Updates** (2024-11-27):
- Modified `StoreContentRequest` to accept `blocks` array as alternative input
- Modified `UpdateContentRequest` to accept `blocks` array as alternative input
- Made `body_json` conditionally required (`required_without:blocks`)

**Code Quality**:
- ✅ Clean, well-structured Blade views
- ✅ Proper use of Alpine.js for dynamic UI
- ✅ Reusable Blade components for blocks
- ✅ Consistent styling with existing admin UI
- ✅ Proper error handling and validation feedback
- ✅ Responsive design
- ✅ No linting errors

**Bugs Fixed** (2024-11-27):
- ✅ Body JSON data flow issue (frontend/backend format alignment)
- ✅ ContentType dropdown not populated (missing controller variable)
- ✅ Content type selection not preserving on validation error (incorrect comparison)

**Minor Observations**:
- ⚠️ WYSIWYG editor not integrated (within Sprint 1 scope — textarea used)
- ⚠️ Block drag & drop reordering not implemented (can be added in Sprint 2)
- ⚠️ Preview functionality not yet implemented (can use show page for now)

---

### Sprint 1 Final Check (2024-11-27)

**Status**: ✅ **COMPLETE** — All issues resolved

**Final Summary**:
- ✅ **Dev A**: All tasks complete, 2 missing deliverables fixed
- ✅ **Dev B**: All tasks complete, no issues found
- ✅ **Dev C**: All tasks complete, 3 bugs fixed during review

**Total Issues Found & Fixed**: **5**
- Dev A: 2 missing deliverables (ContentResource, Error Codes Documentation)
- Dev C: 3 bugs (body_json data flow, ContentType dropdown, form state preservation)

**Content Management Features**:
- ✅ Create Content → `/admin/content/create`
- ✅ Edit Content → `/admin/content/{id}/edit`
- ✅ View Content → `/admin/content/{id}`
- ✅ List Content → `/admin/content` (with filters)
- ✅ Publish Content → `POST /admin/content/{id}/publish`

**API Endpoints**:
- ✅ `GET /api/v1/businesses/{id}/content` — List published content
- ✅ `GET /api/v1/businesses/{id}/content/{slug}` — Get content by slug
- ✅ `GET /api/v1/businesses/{id}/content/type/{type}` — Get content by type

**Block System**:
- ✅ Text block (textarea with alignment)
- ✅ Hero block (title, subtitle, image URL, CTA)
- ✅ Gallery block (image URLs, columns)

**Content Versioning**:
- ✅ Initial revision created on content creation
- ✅ Auto-revision created before content update
- ✅ Manual revision creation via CreateRevisionService
- ✅ Revision restore functionality

**Ready for Sprint 2**: ✅ **YES**

**See**: `project-docs/v2/sprints/sprint_1/reviews/sprint_1_final_check.md` for detailed final check

---

#### Sprint 0 — Infrastructure & Foundation ✅ **COMPLETE**

##### Dev A (Backend/Infrastructure) — ✅ REVIEWED & APPROVED

**Master DEV Review** (2024-11-27):
- ✅ All tasks completed with excellent quality
- ✅ 5 bugs found and fixed:
  1. SettingsController method name mismatch
  2. Setting model incorrect cast
  3. Cache tags inconsistency
  4. Value casting improvement
  5. API routes Sanctum handling
- ⚠️ Minor: Migration naming inconsistency (documented for cleanup)
- **Status**: Approved — Ready for next sprint

**Detailed Review**: See `project-docs/v2/sprints/sprint_0_review.md` and `project-docs/v2/sprints/sprint_0_review_deva.md`

##### Dev A (Backend/Infrastructure)
- [x] **Task A1: Architecture Documentation** (2024-11-27)
  - Updated `project-docs/architecture.md` with v2 specifics
  - Created `project-docs/v2/domain-structure.md` — Complete domain structure
  - Created `project-docs/v2/cms-core-concepts.md` — CMS core concepts & blocks
- [x] **Task A2: Laravel Project Setup & Configuration** (2024-11-27)
  - Created `config/cms.php` with `CMS_ENABLED` feature flag
  - Added exception handling (validation, auth, authorization, 404)
  - Added response macros (success, error, paginated) in AppServiceProvider
- [x] **Task A3: RBAC Implementation** (2024-11-27)
  - Migrations: `roles`, `permissions`, `role_user`, `permission_role`
  - Data migration: `migrate_is_admin_to_roles`
  - Models: `Role`, `Permission` with relationships
  - Services: `AssignRoleService`, `RevokeRoleService`, `CheckPermissionService`, `MigrateAdminToRolesService`
  - Middleware: `CheckPermission` for route protection
  - User model: Added RBAC methods (`hasRole()`, `hasPermission()`, etc.)
  - Seeders: `RoleSeeder`, `PermissionSeeder` with default roles/permissions
- [x] **Task A4: Settings Module** (2024-11-27)
  - Migration: `settings` table (key-value storage)
  - Model: `Setting` with type casting
  - Services: `GetSettingsService`, `UpdateSettingsService`, `ClearSettingsCacheService`
  - Caching: 1 hour TTL with proper invalidation
  - Seeder: `SettingsSeeder` with default settings
- [x] **Task A5: API Foundation** (2024-11-27)
  - Created `routes/api.php` with `/api/v1/` prefix
  - Base controller: `Api\BaseController` with standard response methods
  - Settings API: `Api\V1\SettingsController` (CRUD)
  - API routes: Public (GET) and Protected (POST/PUT/DELETE)
  - Exception handling: JSON error responses
  - API middleware: Throttling configured
  - ⚠️ Note: Sanctum package needs installation (next phase)

##### Dev C (Frontend/UI) — ✅ REVIEWED & APPROVED

**Master DEV Review** (2024-11-27):
- ✅ All tasks completed with excellent quality
- ✅ 1 bug found and fixed:
  1. Settings view boolean checkbox label logic error
- ⚠️ Minor: Mobile menu overlay content empty (can be completed in Sprint 1)
- **Status**: Approved — Ready for next sprint

**Detailed Review**: See `project-docs/v2/sprints/sprint_0_review_devc.md`

---

### Sprint 0 Final Check (2024-11-27)

**Status**: ✅ **COMPLETE** — All issues resolved

**Final Fixes Applied**:
1. ✅ **AdminMiddleware**: Updated to use RBAC (`hasRole('admin')`) with backward compatibility
2. ✅ **Filament Authorization**: Added `canAccessUsing()` to restrict panel access to admin role
3. ✅ **Route Conflict**: Removed conflicting Blade `/admin/` route (Filament dashboard handles this)

**Admin Panel Access**:
- ✅ `/admin` → Filament Dashboard (requires admin role)
- ✅ `/admin/login` → Filament Login Page
- ✅ `/admin/users` → Filament UserResource
- ✅ `/admin/roles` → Filament RoleResource
- ✅ `/admin/settings` → Blade Settings Page

**Ready for Sprint 1**: ✅ **YES**

**See**: `project-docs/v2/sprints/sprint_0_final_check.md` for detailed final check

##### Dev B (Architecture/Domain) — ✅ REVIEWED & APPROVED

**Master DEV Review** (2024-11-27):
- ✅ All tasks completed with excellent quality
- ✅ 1 bug found and fixed:
  1. MediaFolder migration missing `created_by` field
- **Status**: Approved — Ready for next sprint

**Detailed Review**: See `project-docs/v2/sprints/sprint_0_review_devb.md`

##### Dev C (Frontend/UI)
- [x] **Task C1: Admin Panel Base (Hybrid Filament/Blade)** (2024-11-27)
  - Installed Filament v4.0 with AdminPanelProvider
  - Created `UserResource` (Filament) with role assignment
  - Created `RoleResource` (Filament) with permission management
  - Created Blade admin layout (`resources/views/layouts/admin.blade.php`)
  - Created admin dashboard (`resources/views/admin/dashboard/index.blade.php`)
  - Added navigation links for Users, Roles, Settings
  - Integrated Filament routes with Blade routes
- [x] **Task C2: Settings UI** (2024-11-27)
  - Created `SettingsController` with index & update methods
  - Created settings view with group-based organization
  - Integrated with `UpdateSettingsService`
  - Form validation and success notifications
- [x] **Task C3: User Management UI (Filament)** (2024-11-27)
  - UserResource fully functional (list, create, edit, delete)
  - Role assignment (multi-select) in user form
  - Filters & search functionality
  - RoleResource fully functional with permission assignment
  - Customized forms & tables with badges and icons
- [x] **Task C4: Dev Workflow Setup** (2024-11-27)
  - Created `project-docs/git-workflow.md` — Git branching model (simplified GitFlow)
  - Created `project-docs/pr-template.md` — Pull Request template
  - Created `.git/hooks/pre-commit` — Pre-commit hook for Laravel Pint
  - Updated conventions documentation

##### Dev B (Architecture/Domain)
- [x] **Task B1: CMS Database Structure** (2024-11-27)
  - Migrations (v2_ prefix):
    - `v2_create_content_types_table` — Content type definitions
    - `v2_create_contents_table` — Main content entries (body_json, meta, status)
    - `v2_create_content_revisions_table` — Version history
    - `v2_create_media_folders_table` — Folder hierarchy
    - `v2_create_media_table` — Media files with metadata
  - Foreign keys, indexes configured
  - Multi-business support (business_id on all tables)
- [x] **Task B2: Domain Folder Setup** (2024-11-27)
  - `app/Domain/Content/` — Models (Content, ContentType, ContentRevision) + README
  - `app/Domain/Media/` — Models (Media, MediaFolder) + README
  - `app/Domain/Settings/` — Models (Setting) + Services (GetSettingsService, UpdateSettingsService)
  - Skeleton structure ready for Sprint 1-2 implementation
- [x] **Task B3: Base Content API** (2024-11-27)
  - Placeholder endpoint: `/api/v1/content/test`
  - Returns skeleton response confirming API readiness
  - Full implementation in Sprint 1
- [x] **Task B4: Media Library Skeleton** (2024-11-27)
  - Media & MediaFolder models (skeleton)
  - Domain structure ready
  - Full implementation in Sprint 2
- [x] **Task B5: Services Structure** (2024-11-27)
  - Service Layer Pattern documented in `architecture.md`
  - Constructor injection pattern established
  - Single-responsibility principle enforced

---

## v1.0 — E-Commerce Platform

**Στόχος**: Μετατροπή σε CMS-first πλατφόρμα με block-based content editor, media library, RBAC, και plugin system.

**Overview**: `project-docs/v2/v2_overview.md` — Architecture, strategy & technical specs  
**Migration Guide**: `project-docs/v2/v2_migration_guide.md` — Step-by-step migration  
**Detailed Sprints**: `project-docs/v2/sprints/` — Individual sprint files with detailed tasks

**Documentation Created** (2024-11-27):
- ✅ `project-docs/v2/v2_migration_guide.md` — Step-by-step migration guide
- ✅ `project-docs/v2/v2_api_spec.md` — API specification
- ✅ `project-docs/v2/v2_plugin_guide.md` — Plugin development guide
- ✅ `project-docs/v2/v2_content_model.md` — Content model specification
- ✅ `project-docs/v2/sprints/` — Sprint files (0-6) for detailed tracking
- ✅ `project-docs/v2/dev-responsibilities.md` — Developer guide for quality & best practices

**Documentation Reorganization** (2024-11-27):
- ✅ Created `project-docs/v2/v2_overview.md` — Consolidated overview with architecture, strategy, technical specs, and checklists
- ✅ Moved content from `project-docs/steps_versions/v2_steps.md` → `v2/v2_overview.md` (refactored)
- ✅ Updated all references in README.md, CHANGELOG.md, and sprint files
- ✅ Deleted `project-docs/steps_versions/v2_steps.md` (replaced by v2_overview.md)

**Architecture Decision: Hybrid Admin Panel** (2024-11-27):
- ✅ **Decision**: Hybrid Filament/Blade approach for admin panel
  - **Filament** for standard CRUD (Products, Categories, Orders, Users, Roles)
  - **Blade** for custom features (Content Editor, Media Library, Plugins, Dashboard)
- ✅ Updated `project-docs/conventions.md` — Added Hybrid Admin Panel rules (Section 14)
- ✅ Updated `project-docs/architecture.md` — Added Admin Panel Architecture section
- ✅ Updated `project-docs/v2/v2_overview.md` — Added Core Decision for Hybrid approach
- ✅ Updated `project-docs/v2/sprints/sprint_0.md` — Task C1 & C3 now use Filament
- **Rationale**: Filament = fast CRUD development, Blade = full control for custom features

**Dev B Questions Answered** (2024-11-27):
- ✅ Updated `project-docs/v2/decisions.md` with Dev B answers:
  - **CMS Folder**: Don't fill it — create `Content/` instead, delete CMS in Sprint 1
  - **ImageUploadService**: Refactor in Sprint 2 (use Media model, don't create new)
  - **Migration Naming**: Use `v2_` prefix for all v2 migrations, remove in final cleanup
  - **Feature Flag**: `config/cms.php` + check in middleware/services/controllers
- ✅ Updated `sprint_0.md` with clarifications:
  - Migration naming with `v2_` prefix
  - CMS folder handling (don't fill, create Content/ instead)
  - Feature flag implementation details
- ✅ Updated `sprint_2.md` with ImageUploadService refactor details
- **Purpose**: Clear answers for Dev B before starting Sprint 0 tasks

**Architectural Decisions & Dev A Questions** (2024-11-27):
- ✅ Created `project-docs/v2/decisions.md` — Centralized decisions log
- ✅ Answered Dev A questions for Sprint 0:
  - **Domain Structure**: Keep `app/Domain/` (singular) ✅
  - **RBAC**: Custom implementation (not Spatie) ✅
  - **Settings**: Separate modules (Business Settings vs Global Settings) ✅
  - **Feature Flag**: `config/cms.php` with `CMS_ENABLED` from `.env` ✅
  - **Existing Data**: Keep all existing data (products, orders, customers) ✅
  - **CMS Folder**: Delete in Sprint 1 (not Sprint 0) ✅
- ✅ Updated `sprint_0.md` with clarifications:
  - Settings module = Global Settings (separate from Business Settings)
  - RBAC = Custom implementation (Role & Permission models)
  - Feature flag via config file
- **Purpose**: Clear answers for developers before starting implementation

**Technical Conventions & Architecture Details** (2024-11-27):
- ✅ Added comprehensive technical conventions to `project-docs/conventions.md`:
  - Section 15: Service Layer Pattern (detailed) — naming, structure, error handling, transactions
  - Section 16: API Response Format — standard structure, status codes, pagination
  - Section 17: Caching Conventions — key naming, TTL, invalidation patterns
  - Section 18: File Storage Conventions — disks, naming, path structure
  - Section 19: Database Conventions — soft deletes, timestamps, indexes, foreign keys
  - Section 20: Events & Listeners — when to use, naming, structure, async
  - Section 21: Jobs & Queues — when to use, naming, structure
  - Section 22: Exception Handling — custom exceptions, logging, user-friendly messages
  - Section 23: Block System Conventions — registration, validation, rendering
  - Section 24: Testing Conventions — naming, structure, test data
  - Section 25: Validation Conventions — form requests, custom rules, error messages
- ✅ Enhanced `project-docs/architecture.md` with detailed sections:
  - Service Layer Pattern (detailed) — principles, structure, error handling
  - Caching Strategy (detailed) — keys, TTL, invalidation, tags
  - File Storage Architecture — disks, naming, paths, variants
  - Event-Driven Architecture — when to use, structure, async listeners
- **Purpose**: Provide clear technical guidelines for all developers, prevent inconsistencies, ensure code quality

**Sprint 0 Revised** (2024-11-27):
- ✅ Updated `sprint_0.md` with REVISED hybrid approach
- ✅ Enhanced `v2_steps.md` Sprint 0 with detailed tasks
- ✅ Added: Architecture Documentation, User Management UI, Dev Workflow Setup
- ✅ Corrections: `Domain` (singular), Service Layer Pattern (not Actions/Repositories)

**Sprints**:
- Sprint 0 — Infrastructure & Foundation (pending)
- Sprint 1 — Content Module (pending)
- Sprint 2 — Media Library (pending)
- Sprint 3 — Content Rendering & Theming (pending)
- Sprint 4 — RBAC & Permissions (pending)
- Sprint 5 — API & Headless Support (pending)
- Sprint 6 — Plugins & Polish (pending)

**Cleanup Strategy**: Replace v1 code, don't keep legacy files. See cleanup tasks in [v2_overview.md](project-docs/v2/v2_overview.md) and individual sprint files.

---

## Sprint 0 — Review & Fixes (Master DEV)

### Fixes (2024-11-27)
- [x] **BUG FIX**: Component wrappers (`public-layout.blade.php`, `admin-layout.blade.php`) χρησιμοποιούσαν `@include` αντί για proper Blade component classes
  - Δημιουργήθηκαν `app/View/Components/PublicLayout.php` και `AdminLayout.php`
  - Διαγράφηκαν τα λανθασμένα blade wrapper files
  - Τώρα το `<x-public-layout>` και `<x-admin-layout>` δουλεύουν σωστά με `$slot`

---

## Sprint 0 — Προετοιμασία

### Dev A (Implementer)
- [x] Laravel 12 project setup (2024-11-27)
- [x] Git repository initialization (2024-11-27)
- [x] Laravel Breeze installation (2024-11-27)
- [x] Domain folder structure (2024-11-27)
  - `app/Domain/Catalog/`
  - `app/Domain/Orders/`
  - `app/Domain/Customers/`
  - `app/Domain/Businesses/`
  - `app/Domain/CMS/`
  - `app/Domain/Auth/`

### Dev B (Architect)
- [x] Database schema design (2024-11-27)
  - `project-docs/database-schema.md`
  - Tables: businesses, categories, products, customers, orders, order_items, users, pages
- [x] Conventions document (2024-11-27)
  - `project-docs/conventions.md`
  - Services vs Actions, naming conventions, code style
- [x] Domain boundaries definition (2024-11-27)
  - Catalog, Orders, Customers, Businesses, CMS, Auth

### Dev C (Frontend)
- [x] Base layouts (2024-11-27)
  - `resources/views/layouts/public.blade.php` — Public site layout με header, footer, mobile menu
  - `resources/views/layouts/admin.blade.php` — Admin panel layout με sidebar navigation
- [x] TailwindCSS configuration (2024-11-27)
  - Custom color palette (primary: amber, accent: teal)
  - Outfit font family
  - Surface & content semantic colors
- [x] Demo homepage (2024-11-27)
  - `resources/views/home.blade.php` — Hero section, features, CTA
  - Route updated to serve home view

---

## Sprint 1 — Catalog & Public Menu ✅

> **Status**: COMPLETED (2024-11-27)
> **Review**: Master DEV approved — all deliverables met

### Dev A
- [x] MenuController (2024-11-27)
  - `App\Http\Controllers\MenuController@show`
  - Χρησιμοποιεί `GetMenuForBusinessService`
- [x] CategoryController (2024-11-27)
  - `App\Http\Controllers\CategoryController@show`
  - Χρησιμοποιεί `GetActiveProductsService`
- [x] Routes setup (2024-11-27)
  - `/menu` → MenuController@show
  - `/menu/{slug}` → CategoryController@show
- [x] Basic caching — ήδη υλοποιημένο στο GetMenuForBusinessService (30 min)

### Dev B
- [x] Migrations (2024-11-27)
  - `create_businesses_table` — businesses με type, settings JSON
  - `create_categories_table` — categories με business_id FK
  - `create_products_table` — products με category_id FK
- [x] Models (2024-11-27)
  - `App\Domain\Businesses\Models\Business`
  - `App\Domain\Catalog\Models\Category`
  - `App\Domain\Catalog\Models\Product`
- [x] Seeders (2024-11-27)
  - `BusinessSeeder` — Demo Cafe
  - `CategorySeeder` — Καφέδες, Ροφήματα, Σνακ, Γλυκά
  - `ProductSeeder` — 15 demo products
- [x] Services (2024-11-27)
  - `GetMenuForBusinessService` — Full menu με caching
  - `GetActiveProductsService` — Products by business/category/featured

### Dev C
- [x] menu.blade.php (2024-11-27)
  - Categories grid με hover effects
  - Featured products section
  - Empty state handling
- [x] product-card.blade.php (2024-11-27)
  - Reusable component με props
  - Featured badge, unavailable overlay
  - Add to cart button placeholder
- [x] category.blade.php (2024-11-27)
  - Products grid για single category
  - Breadcrumb navigation
  - Back to menu link
- [x] Responsive grid layout — mobile-first με Tailwind breakpoints

### Sprint 1 Deliverables ✅
- [x] Public menu fully working
- [x] Real categories + products from DB
- [x] Responsive UI (mobile-first)
- [x] Basic caching (30 min TTL)
- [x] Generic naming (products, not coffees)

---

## Sprint 2 — Admin Panel ✅

> **Status**: COMPLETED (2024-11-27)
> **Review**: Master DEV approved — all deliverables met

### Dev A
- [x] AdminMiddleware (2024-11-27)
  - `App\Http\Middleware\AdminMiddleware`
  - Registered as 'admin' alias in bootstrap/app.php
- [x] Migration: add_is_admin_to_users_table (2024-11-27)
  - Added `is_admin` boolean column to users
- [x] Admin routes (2024-11-27)
  - `/admin/products` — full CRUD resource
  - `/admin/categories` — full CRUD resource
  - Protected by `auth` + `admin` middleware
- [x] Admin ProductController (2024-11-27)
  - `App\Http\Controllers\Admin\ProductController`
  - index, create, store, edit, update, destroy
- [x] Admin CategoryController (2024-11-27)
  - `App\Http\Controllers\Admin\CategoryController`
  - index, create, store, edit, update, destroy

### Dev B
- [x] CRUD Services (2024-11-27)
  - `CreateProductService`, `UpdateProductService`, `DeleteProductService`
  - `CreateCategoryService`, `UpdateCategoryService`, `DeleteCategoryService`
  - Auto cache invalidation on update/delete
- [x] Policies (2024-11-27)
  - `App\Domain\Catalog\Policies\ProductPolicy`
  - `App\Domain\Catalog\Policies\CategoryPolicy`
  - RBAC based on `is_admin` flag
- [x] FormRequests (2024-11-27)
  - `StoreProductRequest`, `UpdateProductRequest`
  - `StoreCategoryRequest`, `UpdateCategoryRequest`
  - Greek validation messages

### Dev C
- [x] Admin Products views (2024-11-27)
  - `admin/products/index.blade.php` — List με pagination, status badges
  - `admin/products/create.blade.php` — Form με validation errors
  - `admin/products/edit.blade.php` — Edit form με pre-filled values
- [x] Admin Categories views (2024-11-27)
  - `admin/categories/index.blade.php` — List με product count
  - `admin/categories/create.blade.php` — Create form
  - `admin/categories/edit.blade.php` — Edit form
- [x] UI Features
  - Flash messages (success/error)
  - Breadcrumb navigation
  - Responsive tables
  - Delete confirmation dialogs
- [ ] Image upload form (deferred to Sprint 4)

### Sprint 2 Deliverables ✅
- [x] Full admin catalog management
- [x] CRUD for products & categories
- [x] Safe validation & policies
- [x] Clean admin UI
- [x] Ready for demo to client

### Sprint 2 Review Notes (Master DEV)
- Dev A: Minor fix (added `is_admin` cast to User model)
- Dev B: Bug fix (added missing `use` statement in services)
- Dev C: No issues found

---

## Sprint 3 — Ordering System ✅

> **Status**: COMPLETED (2024-11-27)
> **Review**: Master DEV approved — all deliverables met

### Dev A
- [x] CartController (2024-11-27)
  - `App\Http\Controllers\CartController`
  - Session-based cart (add, update, remove, clear)
  - AJAX endpoints for cart data
  - Tax calculation (24% VAT)
- [x] CheckoutController (2024-11-27)
  - `App\Http\Controllers\CheckoutController`
  - Checkout form with validation
  - Order creation via CreateOrderService
  - Success/confirmation page
- [x] Admin OrderController (2024-11-27)
  - `App\Http\Controllers\Admin\OrderController`
  - Orders list with status filter
  - Order detail view
  - Status update functionality
- [x] Routes setup (2024-11-27)
  - `/cart` — cart page
  - `/cart/add`, `/cart/update`, `/cart/remove`, `/cart/clear` — AJAX
  - `/checkout`, `/checkout/success/{orderNumber}`
  - `/admin/orders`, `/admin/orders/{order}`

### Dev B
- [x] Migrations (2024-11-27)
  - `create_customers_table` — customer data με optional user_id
  - `create_orders_table` — orders με status enum, type enum
  - `create_order_items_table` — product snapshots
- [x] Models (2024-11-27)
  - `App\Domain\Customers\Models\Customer`
  - `App\Domain\Orders\Models\Order` — με scopes (status, pending)
  - `App\Domain\Orders\Models\OrderItem`
- [x] Services (2024-11-27)
  - `CalculateOrderTotalService` — subtotal, tax (24%), total
  - `CreateOrderService` — full order creation με transaction
  - `ValidateBusinessOperatingHoursService` — ώρες λειτουργίας
  - `ValidateOrderService` — business rules validation

### Dev C
- [x] Cart page (2024-11-27)
  - `cart/index.blade.php` — Full cart με quantity controls
  - Order summary sidebar
  - AJAX cart updates
  - Clear cart functionality
- [x] Checkout page (2024-11-27)
  - `checkout/index.blade.php` — Contact form, order type selection
  - Pickup/Delivery toggle με address field
  - Order notes
  - Validation error display
- [x] Order confirmation (2024-11-27)
  - `checkout/success.blade.php` — Order summary, status badge
  - Order items list, totals
- [x] Admin Orders views (2024-11-27)
  - `admin/orders/index.blade.php` — Orders list με status filter
  - `admin/orders/show.blade.php` — Order details, status update

### Sprint 3 Deliverables ✅
- [x] Cart functionality (add, update, remove, clear)
- [x] Checkout flow (form, validation, order creation)
- [x] Order confirmation page
- [x] Admin order management (list, view, status update)
- [x] Customer creation on checkout
- [x] Business rules validation (operating hours, delivery, min order)

### Sprint 3 Review Notes (Master DEV)
- Dev A: Bug fix (ValidateOrderService call signature)
- Dev B: No issues found
- Dev C: 2 bug fixes (route name, property name in view)

---

## Pre-Sprint 4 Fixes (Master DEV)

### Fixes (2024-11-27)
- [x] Cart button στο header → link στο `/cart` με dynamic count
- [x] Mobile menu → added Cart link
- [x] About page → created placeholder (`about.blade.php`)
- [x] Contact page → created placeholder (`contact.blade.php`)
- [x] Routes → added `/about` και `/contact`

---

## Sprint 4 — Multi-Business & Theming

**Status**: ✅ COMPLETED

### Dev A
- [x] SetCurrentBusiness middleware (2024-11-27)
  - `App\Http\Middleware\SetCurrentBusiness`
  - Resolves business from: route param, query param, session, or fallback
  - Shares `$currentBusiness` with all views
  - Registered as 'business' alias
- [x] Additional seeders (2024-11-27)
  - `GasStationSeeder` — QuickFuel με καύσιμα, σνακ, car care (10 products)
  - `BakerySeeder` — Artisan Bakery με ψωμιά, αρτοσκευάσματα, γλυκά (14 products)
  - DatabaseSeeder updated να καλεί όλα τα seeders
- [x] Verified business_id filtering (2024-11-27)
  - Όλες οι queries χρησιμοποιούν `where('business_id', ...)`

### Dev B
- [x] Business model enhancements (2024-11-27)
  - Added helper methods: `getSetting()`, `isDeliveryEnabled()`, `getTheme()`, `getCurrency()`
  - Added `scopeOfType()` for filtering by business type
- [x] GetBusinessSettingsService (2024-11-27)
  - Default settings per business type (cafe, restaurant, bakery, gas_station, salon)
  - Theme colors configuration (default, warm, elegant, modern, industrial)
  - `getThemeColors()` method for dynamic theming
- [x] BusinessSettingsDTO (2024-11-27)
  - Documented settings structure
  - Ordering: delivery, pickup, minimum_order
  - Display: show_images, color_theme
  - Currency & Tax: currency, symbol, tax_rate
  - Contact & Social: phone, email, facebook, instagram

### Dev C
- [x] Dynamic theme colors (2024-11-27)
  - CSS variables injected based on business theme
  - `--color-primary` and `--color-accent` from GetBusinessSettingsService
- [x] AJAX Add to Cart (2024-11-27)
  - `product-card.blade.php` updated με AJAX functionality
  - Visual feedback (checkmark icon, green color)
  - Dynamic cart count update in header

### Additional Tasks (from UI/UX Review)
- [ ] **Dev C**: Hero image — replace placeholder SVG with real image
- [x] **Dev B**: Image upload functionality for products/categories (2024-11-27)
  - `ImageUploadService` — upload, replace, delete, getUrl
  - Storage link created (`php artisan storage:link`)
  - FormRequests updated με image validation rules
  - ProductController integrated με image upload
  - Admin views updated με file input & image preview
- [x] **Dev C**: Add to cart button στο product-card (AJAX integration) (2024-11-27)

### Sprint 4 Review Notes (Master DEV)
- Dev A: No issues found
- Dev B: No issues found  
- Dev C: No issues found
- All tasks completed successfully

---

## Sprint 5 — Testing & Deployment ✅

> **Status**: ✅ COMPLETED (2024-11-27)
> **Review**: ✅ Approved by Master DEV

### Dev A
- [x] Feature Tests (2024-11-27)
  - `tests/Feature/Catalog/ViewMenuTest.php` — 5 tests (menu load, categories, products, unavailable, 404)
  - `tests/Feature/Orders/CreateOrderTest.php` — 12 tests (cart CRUD, checkout, orders)
  - `tests/Feature/Admin/ProductCrudTest.php` — 10 tests (auth, CRUD, validation)
- [x] Performance Optimization (2024-11-27)
  - `app:optimize-production` artisan command
  - Config, route, view caching
  - Composer autoloader optimization

### Dev B
- [x] Refactoring & code review (2024-11-27)
  - Verified all services follow single-responsibility principle
  - Domain boundaries are clean and well-defined
  - No significant code duplication found
- [x] Architecture documentation (2024-11-27)
  - `project-docs/architecture.md` created
  - Domain structure overview
  - Data flow diagrams
  - How to add new business types
  - How to add new modules
  - Deployment checklist

### Dev C
- [x] Mobile menu — slide-in drawer (2024-11-27)
  - Full-screen overlay με backdrop blur
  - Animated drawer από δεξιά
  - Icons για κάθε menu item
  - Cart badge στο mobile menu
- [x] Contact form functionality (2024-11-27)
  - Form με name, email, subject, message
  - Client-side validation
  - Loading state με spinner
  - Success message feedback
- [x] UX polish (2024-11-27)
  - Smooth scroll (`scroll-behavior: smooth`)
  - Fade-in-up animations για page elements
  - Animation delay utilities

### Sprint 5 Review Notes (Master DEV)
- Dev A: No issues found — 27 tests total
- Dev B: No issues found — excellent documentation
- Dev C: No issues found — polished UX

