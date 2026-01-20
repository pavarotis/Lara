# Sprint 7.5 — Hardening & Performance Closure

**Status**: ✅ Completed  
**Start Date**: 2026-01-20  
**End Date**: 2026-01-20  
**Διάρκεια**: 1 εβδομάδα  
**Filament Version**: Χρησιμοποιούμε μόνο **Filament 4.x** στο admin panel (δεν επιτρέπονται Filament v2/v3 packages ή APIs).

---

## 📋 Sprint Goal

Κλείσιμο των εκκρεμοτήτων από Sprint 6 και Sprint 7:
- Canonical routing
- Plugin foundation
- Cache invalidation & isolation tests
- Image optimization (πλήρες)
- Performance auditing & monitoring

---

## 🎯 High-Level Objectives

1. **Routing Hardening** — Canonical URLs & legacy cleanup
2. **Plugin Foundation** — Βασική υποδομή για plugins
3. **Caching Maturity** — Cache invalidation + hit/miss tracking
4. **Image Optimization** — Πραγματική παραγωγή WebP/AVIF
5. **Performance Validation** — Lighthouse audits + bundle metrics

---

## 🔗 Integration Points

### Dependencies
- **Sprint 6** — Routing strategy, plugin groundwork
- **Sprint 7** — Performance pipeline, caching, media placeholders

### Backward Compatibility
- No breaking changes
- Feature flags όπου χρειάζεται

---

## 👥 Tasks by Developer Stream

### Dev A — Platform Hardening

#### Task A1 — Canonical Routing Completion
**Περιγραφή**: Ολοκλήρωση canonical routing και cleanup legacy paths.

**Deliverables**:
- Ενιαίο canonical URL strategy για public routes
- Redirect rules για legacy URLs
- Documentation update για routing conventions

**Acceptance Criteria**:
- Δεν υπάρχουν duplicate routes που οδηγούν στο ίδιο content
- Redirects αποφεύγουν SEO duplicates

---

#### Task A2 — Plugin Foundation
**Περιγραφή**: Βασικό plugin foundation (structure + loading).

**Deliverables**:
- Plugin loading mechanism
- Minimal plugin manifest validation
- Documentation update στο plugin guide

**Acceptance Criteria**:
- Plugins φορτώνονται με deterministic order
- Plugin errors δεν σπάνε το app (safe failure)

---

#### Task A3 — Cache Invalidation Service
**Περιγραφή**: Κεντρικό service για cache invalidation triggers.

**Deliverables**:
- Service για invalidation ανά Content/Layout/Module
- Integration σε publish/update flows

**Acceptance Criteria**:
- Cache invalidation είναι consistent και predictable
- Δεν αφήνονται stale pages

---

### Dev B — Performance & Testing

#### Task B1 — Cache Hit/Miss Tracking
**Περιγραφή**: Metrics για full-page cache και fragment cache.

**Deliverables**:
- Hit/miss counters per cache layer
- Reporting στο Performance Dashboard

**Acceptance Criteria**:
- Metrics εμφανίζονται σωστά στο admin
- Μετρήσεις ανά business όπου απαιτείται

---

#### Task B2 — Isolation Tests
**Περιγραφή**: Tests για business isolation στα API endpoints.

**Deliverables**:
- Feature tests για v2 API isolation
- Test data setup για multi-business

**Acceptance Criteria**:
- All v2 endpoints enforce business isolation
- Tests περνάνε σε fresh DB

---

#### Task B3 — Performance Audit
**Περιγραφή**: Audit του public site performance.

**Deliverables**:
- Audit report (Lighthouse + runtime observations)
- Follow-up action list (if needed)

**Acceptance Criteria**:
- Documented results με baseline metrics
- Actionable next steps αν υπάρχουν gaps

---

### Dev C — Media & Observability

#### Task C1 — Image Optimization (Full)
**Περιγραφή**: Implement πραγματική παραγωγή WebP/AVIF.

**Deliverables**:
- Install `intervention/image`
- Real implementation για variants
- Update Media pipelines

**Acceptance Criteria**:
- WebP/AVIF παράγονται on upload
- Responsive `srcset` λειτουργεί

---

#### Task C2 — Lighthouse Audits & Dashboard
**Περιγραφή**: Lighthouse runs + καταγραφή αποτελεσμάτων στο admin.

**Deliverables**:
- Manual/CLI audit process documented
- Dashboard section για Lighthouse summaries

**Acceptance Criteria**:
- Lighthouse results recorded per build
- Core metrics visible (Performance/SEO/Best Practices)

---

#### Task C3 — Bundle Size Monitoring
**Περιγραφή**: Παρακολούθηση bundle sizes ανά widget.

**Deliverables**:
- Build-time size report
- Threshold alerts (docs-only ή CI-ready)

**Acceptance Criteria**:
- Report διαθέσιμο μετά από build
- Ανιχνεύονται regressions

---

## 📦 Deliverables (Definition of Done)

- [x] Canonical routing ολοκληρωμένο
- [x] Plugin foundation ολοκληρωμένο
- [x] Cache invalidation service σε χρήση
- [x] Cache hit/miss tracking διαθέσιμο
- [x] Isolation tests για v2 API
- [x] Image optimization πλήρες (WebP/AVIF)
- [x] Lighthouse audit results καταγεγραμμένα
- [x] Bundle size monitoring ενεργό
- [x] Documentation ενημερωμένο

---

## 🧪 Testing Requirements

### Feature Tests
- [x] API business isolation
- [x] Cache invalidation flows

### Performance Tests
- [x] Lighthouse runs (Performance/SEO/Best Practices)
- [x] Bundle size report generation

---

## 📚 Related Documentation

- [Sprint 6 — Plugins & Polish](../sprint_6/sprint_6.md)
- [Sprint 7 — Lightweight Public Site & Performance](../sprint_7/sprint_7.md)
- [v2 Overview](../v2_overview.md)

---

**Last Updated**: 2026-01-20
