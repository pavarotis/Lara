# Filament Version Baseline — Sprint 4.2

**Date**: _TBD (Sprint 4.2 start)_  
**Author**: Dev A (assisted by AI)

---

## 🎯 Purpose

Καταγραφή της αρχικής κατάστασης (before Sprint 4.2) για Filament / Laravel / PHP, ώστε:

- να γνωρίζουμε από πού ξεκινάμε για το Filament 4 migration,  
- να μπορούμε να κάνουμε rollback αν χρειαστεί.

---

## 📦 Core Versions

- **PHP**: `^8.2` (from `composer.json`)
- **Laravel Framework**: `^12.0`
- **Filament**: `"filament/filament": "4.0"`
  - Vendor package: `vendor/filament/filament/composer.json`
  - Minimum stability: `dev`, `prefer-stable: true`
  - **Project rule**: Χρησιμοποιούμε μόνο **Filament 4.x** στο admin panel (καμία υποστήριξη/χρήση Filament v2/v3).

---

## 🧩 Filament 4 State

- Filament 4 είναι ήδη εγκατεστημένο (composer).
- Script `@php artisan filament:upgrade` εκτελείται στο `post-autoload-dump`:
  - Ενδείξεις ότι migration σε Filament 4 έχει ήδη ξεκινήσει στη βάση του project.
- Υπάρχουν ακόμη κλάσεις `App\Filament\Pages\**` γραμμένες σε **v2/v3 style**:
  - Static `protected static string $view` properties (conflict με non‑static `$view` του Filament 4 `Page`).
  - Απλά `?string` type hints για `navigationGroup` / `navigationIcon` αντί για τα union types που περιμένει το Filament 4.

---

## ⚠️ Known Issues Before Sprint 4.2

- Fatal errors τύπου:
  - `Cannot redeclare non static Filament\Pages\Page::$view as static App\Filament\Pages\...\$view`
  - `Type of ...::$navigationGroup must be UnitEnum|string|null (as in class Filament\Pages\Page)`
- Μείξη παλιού Filament pattern (v2-style pages) με Filament 4 core classes.

---

## 🧭 Migration Targets (High‑Level)

- **Goal**: Όλες οι Filament classes (`App\Filament\*`) να:
  - είναι πλήρως συμβατές με Filament 4 API,
  - μην έχουν static `$view` conflicts,
  - χρησιμοποιούν σωστά union types για navigation properties.

Λεπτομέρειες migration: 👉 `project-docs/v2/sprints/sprint_4.2/sprint_4.2.md`.


