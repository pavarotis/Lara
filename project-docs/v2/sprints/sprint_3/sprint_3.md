# Sprint 3 — Content Rendering & Theming — REVISED

**Status**: ⏳ Pending  
**Start Date**: _TBD_  
**End Date**: _TBD_  
**Διάρκεια**: 1 εβδομάδα

---

## 📋 Sprint Goal

Public site renders from CMS content. Block-based rendering system με theme support και migration από static pages → CMS content.

---

## 🎯 High-Level Objectives

- Block renderer service (theme-aware)
- Theme structure & block views
- Public content controller
- Migration: Static pages → CMS content
- Theme block views (hero, text, gallery)
- Page layout wrapper
- SEO meta tags from content

⚠️ **Δεν υλοποιείται ακόμα:**
- ❌ Advanced blocks (products-list) — Sprint 4+
- ❌ Multiple themes per business — Future
- ❌ Theme customization UI — Future
- ❌ Block preview in editor — Sprint 4

---

## 👥 Tasks by Developer

### Dev B — Renderer & Theme System

#### Task B1 — Block Renderer Service

**Περιγραφή**: Service που render-άρει blocks σε HTML based on theme.

**Deliverables:**
- `RenderContentService` enhancement:
  - `render($content)` — render full content (array of blocks)
  - `renderBlock($block)` — render single block
  - Theme resolution:
    - Load theme from business settings
    - Fallback to default theme
    - View path: `themes/{theme}/blocks/{type}.blade.php`
  - Block props injection to views
  - Error handling (missing block view → fallback message)

**Acceptance Criteria:**
- Blocks render correctly
- Theme fallback working
- Missing block views handled gracefully

---

#### Task B2 — Theme Structure

**Περιγραφή**: Folder structure για themes.

**Deliverables:**
- Create `resources/views/themes/default/`:
  - `blocks/hero.blade.php`
  - `blocks/text.blade.php`
  - `blocks/gallery.blade.php`
  - `layouts/page.blade.php` (wrapper for CMS pages)
- Theme config file `themes/default/theme.json` (optional):
  ```json
  {
    "name": "Default",
    "version": "1.0.0",
    "blocks": ["hero", "text", "gallery"]
  }
  ```

**Acceptance Criteria:**
- Theme folder structure ready
- All block views exist
- Page layout wrapper ready

---

#### Task B3 — Block Views Implementation

**Περιγραφή**: Public-facing block views για κάθε block type.

**Deliverables:**
- `themes/default/blocks/hero.blade.php`:
  - Render hero section
  - Props: title, subtitle, image (from media ID), cta_text, cta_link
  - Responsive image (use media variants)
  - CTA button (if provided)
- `themes/default/blocks/text.blade.php`:
  - Render WYSIWYG content
  - Props: content (HTML), alignment
  - Safe HTML rendering
- `themes/default/blocks/gallery.blade.php`:
  - Render image gallery
  - Props: images (array of media IDs), columns
  - Responsive grid
  - Lightbox (optional, basic)

**Acceptance Criteria:**
- All blocks render correctly
- Responsive design
- Images load from media IDs

---

#### Task B4 — Page Layout Wrapper

**Περιγραφή**: Layout που wrap-άρει CMS pages.

**Deliverables:**
- `themes/default/layouts/page.blade.php`:
  - Extends public layout
  - Renders content blocks in order
  - SEO meta tags from content meta
  - Breadcrumbs (optional)
  - Title from content

**Acceptance Criteria:**
- Page layout working
- SEO meta tags correct
- Blocks render in order

---

### Dev A — Public Controllers & Migration

#### Task A1 — Content Controller (Public)

**Περιγραφή**: Controller που render-άρει CMS content για public site.

**Deliverables:**
- `ContentController@show`:
  - Get content by slug & business
  - Check if published
  - Render via `RenderContentService`
  - 404 if not found
- Route: `/{slug}` (dynamic, after static routes)
- Route priority: static routes first, then dynamic content

**Acceptance Criteria:**
- CMS pages accessible via slug
- 404 for non-existent content
- Only published content shown

---

#### Task A2 — Migration: Static Pages → CMS

**Περιγραφή**: Μετατροπή existing static pages σε CMS content.

**Deliverables:**
- Migration script/command:
  - Convert `home.blade.php` → CMS content entry (slug: `/`)
  - Convert `about.blade.php` → CMS content entry (slug: `about`)
  - Convert `contact.blade.php` → CMS content entry (slug: `contact`)
- Content structure:
  - Home: Hero block + Text blocks
  - About: Text blocks
  - Contact: Text block + Contact form (keep form, migrate content)
- Update routes:
  - Remove static route closures
  - Use `ContentController@show` for all

**Acceptance Criteria:**
- Static pages migrated to CMS
- Routes updated
- Content accessible via CMS

---

#### Task A3 — Route Priority & Fallback

**Deliverables:**
- Route ordering:
  1. Static routes (auth, cart, checkout, admin)
  2. Dynamic content routes (`/{slug}`)
- Fallback handling:
  - 404 for non-existent content
  - Redirect old static routes (if needed)

**Acceptance Criteria:**
- Route priority correct
- No conflicts
- 404 working

---

### Dev C — Public Theme & Integration

#### Task C1 — Theme Block Views (Styling)

**Περιγραφή**: Styling & responsive design για block views.

**Deliverables:**
- Hero block:
  - Responsive image (srcset with variants)
  - CTA button styling
  - Overlay effects (optional)
- Text block:
  - Typography styling
  - Alignment support
  - Responsive spacing
- Gallery block:
  - Responsive grid (columns adapt to screen)
  - Image aspect ratios
  - Lightbox (basic, optional)

**Acceptance Criteria:**
- All blocks responsive
- Styling consistent with theme
- Images optimized (variants)

---

#### Task C2 — SEO & Meta Tags

**Deliverables:**
- SEO meta tags from content:
  - Title (from content title)
  - Description (from content.meta.description)
  - Keywords (from content.meta.keywords)
  - OG image (from content.meta.og_image)
- Meta tags in page layout
- Dynamic title per page

**Acceptance Criteria:**
- SEO meta tags working
- Dynamic per content
- OG tags correct

---

#### Task C3 — Content Preview (Optional)

**Περιγραφή**: Preview functionality για draft content (admin only).

**Deliverables:**
- Preview route: `/preview/{contentId}?token=...`
- Token-based access (admin only)
- Render draft content
- Warning banner ("Preview Mode")

**Acceptance Criteria:**
- Preview working
- Only admins can preview
- Draft content renderable

---

## ✅ Deliverables (End of Sprint 3)

- [ ] Block renderer service working
- [ ] Theme structure ready
- [ ] All block views implemented (hero, text, gallery)
- [ ] Page layout wrapper
- [ ] Public content controller
- [ ] Static pages migrated to CMS
- [ ] Routes updated
- [ ] SEO meta tags working
- [ ] Responsive design
- [ ] Public site renders from CMS

---

## ❌ Δεν θα υπάρχουν ακόμα

- Advanced blocks (products-list) — Sprint 4+
- Multiple themes per business
- Theme customization UI
- Block preview in editor
- Full lightbox gallery
- Video blocks

**Αυτά θα έρθουν στα Sprint 4+.**

---

## 🧹 Cleanup Tasks

- [ ] Delete static views replaced by CMS:
  - [ ] `resources/views/home.blade.php`
  - [ ] `resources/views/about.blade.php`
  - [ ] `resources/views/contact.blade.php` (keep form logic, migrate content)
- [ ] Update routes (remove static closures, use ContentController)

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
- [Content Model](../v2_content_model.md)
- [Migration Guide](../v2_migration_guide.md)
- [**Developer Responsibilities**](../dev-responsibilities.md) ⭐ **Read this for quality checks & best practices**

---

**Last Updated**: _TBD_

