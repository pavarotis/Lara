# Sprint 4.2 — Filament 4 Migration & Admin Panel Alignment

**Status**: ⏳ Pending  
**Start Date**: _TBD_  
**End Date**: _TBD_  
**Διάρκεια**: 1–2 εβδομάδες  
**Filament Version**: Χρησιμοποιούμε μόνο **Filament 4.x** στο admin panel (δεν επιτρέπονται Filament v2/v3 packages ή APIs).

---

## 📋 Sprint Goal

Πλήρης μετάβαση του admin panel σε **Filament 4**, ευθυγραμμισμένη με τα v2 docs και τα προηγούμενα sprints (0–4.1), χωρίς να «σπάσουν»:

- το **Content engine** (Sprints 1–3),  
- το **Media Library** (Sprint 2),  
- το **Layout / Module system & Admin Navigation** (Sprint 4 & 4.1).

👉 Μετά το Sprint 4.2: «Το admin panel τρέχει σε Filament 4, όλα τα `App\Filament\*` είναι συμβατά και οι ροές από τα Sprints 0–4.1 παραμένουν λειτουργικές».

---

## 🎯 High‑Level Objectives

- Αναβάθμιση **Filament** σε v4 (composer, vendor, config).
- Ευθυγράμμιση **AdminPanelProvider** / panel config με v4 API.
- Refactor **όλων** των `App\Filament\Pages\**` και `App\Filament\Resources\**` σε Filament 4 style.
- Συντήρηση/βελτίωση **navigation structure** του Sprint 4.1.
- Regression QA για **admin** και **public** ροές (content rendering, media, layouts).

---

## 👥 Tasks by Developer Stream

> Σχεδιασμένο για 1 προγραμματιστή (εσύ + εγώ ως “helper”), αλλά σπασμένο σε Dev A / B / C streams για οργάνωση.

### Dev A — Infrastructure, Composer & Panel Setup

#### Task A1 — Filament Version & Baseline Audit

- **Περιγραφή**:
  - Επιβεβαίωση τρέχουσας έκδοσης Filament από `composer.json` / `composer.lock`.
  - Καταγραφή Laravel & PHP version, Livewire/Tailwind dependencies.
- **Deliverables**:
  - `project-docs/v2/filament/filament_version_baseline.md` με:
    - Filament/Laravel/PHP versions,
    - Known incompatibilities / notes.
- **Acceptance Criteria**:
  - Τεκμηριωμένη baseline κατάσταση πριν το migration.

---

#### Task A2 — Composer Upgrade σε Filament 4

- **Περιγραφή**:
  - Ενημέρωση `composer.json` ώστε `filament/filament` → `^4.0`.
  - Εκτέλεση `composer update filament/*` και επίλυση conflicts.
- **Deliverables**:
  - Updated `composer.json`, `composer.lock`.
  - Σημειώσεις για breaking changes (σύντομη λίστα στο baseline doc).
- **Acceptance Criteria**:
  - `php artisan about` αναφέρει Filament 4.
  - `php artisan serve` τρέχει χωρίς vendor fatals (πριν τα app‑level refactors).

---

#### Task A3 — Admin Panel & Routing Alignment

- **Περιγραφή**:
  - Έλεγχος / ρύθμιση `AdminPanelProvider` σύμφωνα με Filament 4:
    - Panel name, path `/admin`, auth/guards, middleware.
    - `navigationGroups()` σύμφωνα με Sprint 4.1.
  - Καθαρισμός παλιών configs (`config/filament.php` κ.λπ. αν χρειαστεί).
- **Deliverables**:
  - Σταθερό `AdminPanelProvider` που φορτώνει admin skeleton σε v4.
- **Acceptance Criteria**:
  - Το `/admin` ανοίγει χωρίς σφάλματα routing / panel configuration.

---

#### Task A4 — Feature Flag & Rollback Strategy

- **Περιγραφή**:
  - Επέκταση `config/cms.php` / Settings για:
    - Flag ενεργοποίησης/απενεργοποίησης του νέου admin (π.χ. maintenance mode για panel).
  - Τεκμηρίωση rollback procedure (πώς γυρνάμε σε προηγούμενο Filament build αν χρειαστεί).
- **Deliverables**:
  - `project-docs/v2/filament/filament4_rollback_plan.md` με:
    - Βήματα rollback (composer, git, env flags),
    - Known risks.
- **Acceptance Criteria**:
  - Υπάρχει ξεκάθαρο documented rollback plan.

---

### Dev B — Filament Integration (Pages, Resources, Widgets)

#### Task B1 — Inventory όλων των `App\Filament\*`

- **Περιγραφή**:
  - Σκανάρισμα του `app/Filament`:
    - `Pages\**`,
    - `Resources\**` (π.χ. `ModuleInstanceResource`, `UserResource`, `RoleResource`),
    - Widgets / custom pages αν υπάρχουν.
  - Mapping κάθε class σε σχετικό sprint (0–4.1) και domain (Content, Media, Layouts, RBAC κ.λπ.).
- **Deliverables**:
  - `project-docs/v2/filament/filament_objects_map.md` με πίνακα:
    - Class | Τύπος (Page/Resource/Widget) | Sprint | Notes.
- **Acceptance Criteria**:
  - Καμία Filament class δεν μένει εκτός λίστας.

---

#### Task B2 — Refactor όλων των Filament Pages σε Filament 4 API

- **Περιγραφή**:
  - Για **όλα** τα `App\Filament\Pages\**`:
    - Επιβεβαίωση base class: `Filament\Pages\Page` (v4).
    - Ευθυγράμμιση properties:
      - `protected string $view` (όχι static, χωρίς conflicts με base page),
      - σωστά union types για navigation fields (group/icon) όπως απαιτεί η v4.
    - Σύνδεση με τα `navigationGroups` του `AdminPanelProvider`.
  - Re‑sync με file structure & στόχους του Sprint 4.1 (όλα τα placeholder pages).
- **Deliverables**:
  - Refactored `Pages` (CMS, Catalog, Extensions, Sales, Customers, Marketing, System, Reports, Vqmod).
- **Acceptance Criteria**:
  - Κανένα `Cannot redeclare ... $view` ή type mismatch fatal.
  - Όλες οι σελίδες εμφανίζονται στο navigation στις σωστές groups/θέσεις.

---

#### Task B3 — Refactor Filament Resources σε Filament 4

- **Περιγραφή**:
  - Προσαρμογή όλων των `Resources` στο v4 API:
    - Forms, Tables, Actions, Filters, `->navigationGroup()`, `->navigationIcon()`.
  - Έμφαση σε:
    - `ModuleInstanceResource` (Sprint 4),
    - `UserResource`, `RoleResource` (Sprint 0),
    - τυχόν άλλα resources που χρησιμοποιούν Content/Media.
- **Deliverables**:
  - Όλα τα Resources fully λειτουργικά σε Filament 4.
- **Acceptance Criteria**:
  - CRUD για Users, Roles, ModuleInstances κ.λπ. δουλεύει κανονικά.
  - Policies / RBAC συνεχίζουν να λειτουργούν.

---

#### Task B4 — Widgets / Dashboard & Custom Filament Integration

- **Περιγραφή**:
  - Audit τυχόν Filament widgets, dashboard cards, metrics.
  - Refactor σε v4 widget API (ή explicit deprecation, αν δεν χρειάζονται).
- **Deliverables**:
  - Επικαιροποιημένα widgets (ή documented removal).
- **Acceptance Criteria**:
  - Το Filament dashboard φορτώνει χωρίς σφάλματα.
  - Όλα τα ενεργά widgets είναι Filament 4 συμβατά.

---

### Dev C — Navigation, UX & Regression QA

#### Task C1 — Navigation Groups & Menu Structure (Filament 4 Style)

- **Περιγραφή**:
  - Επαλήθευση/ρύθμιση navigation ώστε να ακολουθεί **ακριβώς** το Sprint 4.1:
    - CMS, Catalog, Extensions, Sales, Customers, Marketing, System, Reports, Vqmod.
  - Χρήση Filament 4 navigation API (groups + pages/resources).
- **Deliverables**:
  - Τελικό navigation structure διαμορφωμένο σε `AdminPanelProvider` + Page/Resource configs.
- **Acceptance Criteria**:
  - Όλα τα menu items:
    - ανήκουν στο σωστό Group,
    - έχουν σωστό `navigationSort`,
    - έχουν σωστό `navigationIcon`.

---

#### Task C2 — Theming & Filament 4 UI Consistency

- **Περιγραφή**:
  - Έλεγχος/ρύθμιση branding:
    - Χρώματα, logo, typography,
    - consistency μεταξύ Filament views και Blade admin views (από Sprint 0).
  - Ενημέρωση τυχόν Filament view overrides σε νέα δομή.
- **Deliverables**:
  - Επικαιροποιημένο Filament theme config / overrides.
- **Acceptance Criteria**:
  - Admin UI οπτικά συνεπές (δεν φαίνεται “σπασμένο” μετά τη μετάβαση σε v4).

---

#### Task C3 — Regression QA (Admin & Public)

- **Περιγραφή**:
  - Smoke/Regression tests στις βασικές ροές:
    - **Admin**:
      - Login, navigation, Users/Roles, Settings.
      - Content list/editor (Sprints 1–3).
      - Media Library (Sprint 2).
      - ModuleInstance & σχετικές σελίδες (Sprint 4).
    - **Public**:
      - Content rendering (Sprint 3).
      - Layout/module rendering (όσο έχει ήδη υλοποιηθεί από Sprint 4).
  - Καταγραφή τυχόν regressions ως mini issues.
- **Deliverables**:
  - `project-docs/v2/sprints/sprint_4.2/regression_checklist.md` με status ανά ροή.
- **Acceptance Criteria**:
  - Καμία κρίσιμη ροή (admin ή public) δεν μένει “σπασμένη” μετά το migration.

---

## 📦 Deliverables (Definition of Done)

### 1. Filament 4 Installed & Stable

- [ ] Filament 4 εγκατεστημένο (composer) χωρίς vendor errors.
- [ ] `AdminPanelProvider` fully συμβατό με v4 και φορτώνει στο `/admin`.
- [ ] Documented baseline + rollback plan.

### 2. Filament Classes v4‑Compatible

- [ ] Όλα τα `App\Filament\Pages\**` refactored:
  - Non‑static `$view`,
  - Σωστά union types για navigation fields.
- [ ] Όλα τα `App\Filament\Resources\**` δουλεύουν σε Filament 4.
- [ ] Widgets / dashboard elements χωρίς errors.

### 3. Navigation & UX

- [ ] Navigation structure = Sprint 4.1 spec (groups, sort, icons).
- [ ] Filament + Blade admin views οπτικά συνεπή.

### 4. Regression & Quality

- [ ] Admin & public βασικές ροές δοκιμασμένες (regression checklist).
- [ ] Κανένα blocking bug μετά τη μετάβαση σε Filament 4.

---

## 📝 Notes

- Το Sprint 4.2 είναι **καθαρά τεχνικό/migration sprint**: δεν προσθέτει νέα business features, αλλά σταθεροποιεί την admin layer πάνω σε Filament 4.
- Όλα τα προηγούμενα sprints (0–4.1) θεωρούνται **contract**: το migration δεν πρέπει να αλλάζει τη συμπεριφορά τους, μόνο την υλοποίηση στο admin layer.

---

## 🔄 Related Sprints

- **Sprint 4.1** — Navigation Structure (prerequisite)
- **Sprint 4.3** — Filament 4 Alignment (code-level fixes)
- **Sprint 4.4** — MVC Audit & Completion (MVC flow audit)
- **Sprint 4.5** — Hybrid Admin Panel Guidelines (decision tree & patterns)


