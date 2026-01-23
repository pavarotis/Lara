# Filament 4 FileUpload Component - Customization Guide

## Overview

Αυτός ο οδηγός εξηγεί πώς να προσαρμόσεις το `FileUpload` component στο Filament 4, ειδικά για μείωση του μεγέθους των εικονιδίων/previews.

---

## ⚠️ Σημαντικές Αρχές

### 1. Μην χρησιμοποιείς Inline `<style>` στο Blade Template

**❌ Λάθος Προσέγγιση:**
```blade
<x-filament-panels::page>
    <style>
        .filepond--root { ... }
    </style>
    ...
</x-filament-panels::page>
```

**Προβλήματα:**
- Το inline CSS μπορεί να φορτώνεται νωρίς ή να χάνει σε specificity
- Δεν είναι "Filament-friendly" και μπορεί να "χαθεί" από ordering/scoping
- Δύσκολο να maintain

**✅ Σωστή Προσέγγιση:**
Φόρτωσε το CSS ως Panel Asset (βλέπε παρακάτω).

---

### 2. Χρησιμοποίησε Filament Semantic Classes / CSS Hooks

**❌ Λάθος Προσέγγιση:**
```css
/* Τυφλά FilePond selectors - μπορεί να αλλάξουν */
.filepond--file-icon-wrapper svg {
    width: 1.25rem;
}
```

**Προβλήματα:**
- Τα FilePond class names μπορεί να αλλάξουν
- Δεν είναι "semantic" - δεν σχετίζονται με Filament structure
- Δεν είναι maintainable

**✅ Σωστή Προσέγγιση:**
Χρησιμοποίησε Filament hook classes / semantic classes που είναι σταθερές για overrides.

---

### 3. Scoped Overrides (όχι Global)

**Στόχος:** Να επηρεάζεις μόνο το συγκεκριμένο FileUpload field ή τη συγκεκριμένη σελίδα, όχι όλο το panel.

**Πώς:**
1. Εντόπισε το Filament wrapper / hook class γύρω από το FileUpload στο rendered HTML
2. Γράψε CSS overrides "κάτω" από αυτό το scope

---

## 📋 Βήματα Εφαρμογής

### Βήμα 1: Δημιούργησε το CSS File

Δημιούργησε `resources/css/filament-fileupload.css`:

```css
/* 
 * Filament FileUpload Customization
 * 
 * ΣΗΜΑΝΤΙΚΟ: Πρέπει να κάνεις inspect στο DOM για να βρεις τα σωστά
 * Filament hook classes / semantic classes πριν γράψεις τους selectors.
 * 
 * Δες παρακάτω το "Βήμα 4: Inspect Element" για λεπτομέρειες.
 */

/* 
 * Παράδειγμα (θα αλλάξεις μετά το inspect):
 * 
 * .fi-fo-field-wrp[data-field-wrapper="files"] .filepond--root {
 *     --filepond-icon-size: 1.25rem;
 * }
 * 
 * .fi-fo-field-wrp[data-field-wrapper="files"] .filepond--file-icon-wrapper svg {
 *     width: 1.25rem;
 *     height: 1.25rem;
 * }
 * 
 * .fi-fo-field-wrp[data-field-wrapper="files"] .filepond--image-preview-wrapper {
 *     max-height: 120px;
 * }
 */
```

**Σημείωση:** Αυτό είναι placeholder. Θα το συμπληρώσεις μετά το inspect element.

---

### Βήμα 2: Φόρτωσε το CSS ως Panel Asset

Ενημέρωσε `app/Providers/Filament/AdminPanelProvider.php`:

```php
use Filament\Support\Assets\Css;

public function panel(Panel $panel): Panel
{
    return $panel
        ->default()
        ->id('admin')
        ->path('admin')
        // ... other config ...
        ->assets([
            Css::make('error-logs', base_path('resources/css/error-logs.css')),
            Css::make('fileupload-overrides', base_path('resources/css/filament-fileupload.css')), // ← Προσθήκη
        ]);
}
```

---

### Βήμα 3: Compile τα Assets

Τρέξε:

```bash
php artisan filament:assets
```

Αυτό θα compile τα CSS assets και θα τα κάνει διαθέσιμα στο panel.

---

### Βήμα 4: Inspect Element - Βρες τα Filament Hook Classes

**Στόχος:** Να βρεις το HTML structure του FileUpload component με τα Filament semantic classes.

**Βήματα:**

1. Άνοιξε το admin panel στο browser
2. Πήγαινε στη σελίδα με το FileUpload (π.χ. `/admin/uploads`)
3. Κάνε **Right Click → Inspect Element** στο FileUpload field
4. Βρες το **outermost wrapper** που έχει Filament classes (π.χ. `fi-fo-field-wrp`, `fi-section-content`, etc.)
5. Κάνε **Copy → Copy Outer HTML** για το wrapper element

**Τι να ψάχνεις:**

- Classes που ξεκινάνε με `fi-` (Filament semantic classes)
- Data attributes (π.χ. `data-field-wrapper="files"`)
- Wrapper structure γύρω από το `.filepond--root`

**Παράδειγμα HTML Structure (υποθετικό - θα διαφέρει):**

```html
<div class="fi-fo-field-wrp" data-field-wrapper="files">
    <div class="fi-fo-field-wrp-label">
        <label>Files</label>
    </div>
    <div class="fi-fo-field-wrp-input">
        <div class="filepond--root">
            <!-- FilePond content -->
            <div class="filepond--file-icon-wrapper">
                <svg>...</svg>
            </div>
        </div>
    </div>
</div>
```

**Σημείωση:** Το πραγματικό HTML structure μπορεί να διαφέρει. Κάνε inspect για να δεις το ακριβές structure.

---

### Βήμα 5: Γράψε Scoped CSS Selectors

Με βάση το HTML structure που βρήκες στο inspect, γράψε scoped selectors:

**Παράδειγμα (αν το wrapper είναι `.fi-fo-field-wrp[data-field-wrapper="files"]`):**

```css
/* Scoped στο συγκεκριμένο field */
.fi-fo-field-wrp[data-field-wrapper="files"] .filepond--root {
    --filepond-icon-size: 1.25rem;
}

.fi-fo-field-wrp[data-field-wrapper="files"] .filepond--file-icon-wrapper svg {
    width: 1.25rem;
    height: 1.25rem;
}

.fi-fo-field-wrp[data-field-wrapper="files"] .filepond--image-preview-wrapper {
    max-height: 120px;
}

/* Αν θες να επηρεάσεις μόνο αυτή τη σελίδα, προσθέστε page-specific class */
.fi-page[data-page="uploads"] .fi-fo-field-wrp[data-field-wrapper="files"] .filepond--root {
    /* ... */
}
```

**Σημαντικό:**
- Χρησιμοποίησε Filament hook classes (π.χ. `fi-fo-field-wrp`) ως base selector
- Προσθήκη data attributes ή page-specific classes για scoping
- Μην βασιστείς μόνο σε FilePond classes (`.filepond--*`)

---

### Βήμα 6: Αφαίρεσε Inline Styles από το Blade

Αφαίρεσε το `<style>` block από `resources/views/filament/pages/system/maintenance/uploads.blade.php`:

```blade
<x-filament-panels::page>
    {{-- ❌ Αφαίρεσε αυτό: --}}
    {{-- <style>...</style> --}}
    
    <div class="space-y-6">
        <!-- ... rest of template ... -->
    </div>
</x-filament-panels::page>
```

---

## 🔧 Global Defaults (Optional)

Αν θες global default συμπεριφορά για όλα τα FileUpload components, μπορείς να χρησιμοποιήσεις `configureUsing()` (αν υποστηρίζεται):

**Στο `AppServiceProvider`:**

```php
use Filament\Forms\Components\FileUpload;

public function boot(): void
{
    // Αν το FileUpload υποστηρίζει configureUsing
    FileUpload::configureUsing(function (FileUpload $component) {
        $component->imagePreviewHeight('120');
    });
}
```

**Σημείωση:** Δεν υπάρχει τεκμηρίωση ότι το `FileUpload` υποστηρίζει `configureUsing()`, οπότε αυτό είναι optional και μπορεί να μην λειτουργήσει.

---

## 🐛 Troubleshooting

### Το CSS δεν "πιάνει"

**Πιθανές αιτίες:**

1. **Δεν έτρεξες `php artisan filament:assets`**
   - **Λύση:** Τρέξε `php artisan filament:assets` μετά από κάθε αλλαγή στο CSS

2. **CSS Specificity Issues**
   - **Λύση:** Χρησιμοποίησε πιο specific selectors (π.χ. προσθήκη data attributes)

3. **CSS Ordering Issues**
   - **Λύση:** Το CSS που φορτώνεται ως panel asset θα έχει σωστό ordering

4. **Browser Cache**
   - **Λύση:** Hard refresh (Ctrl+Shift+R / Cmd+Shift+R)

---

## 📝 Checklist

- [ ] Δημιούργησα `resources/css/filament-fileupload.css`
- [ ] Πρόσθεσα το CSS στο `->assets([...])` στο `AdminPanelProvider`
- [ ] Έτρεξα `php artisan filament:assets`
- [ ] Έκανα inspect element στο FileUpload
- [ ] Εντόπισα τα Filament hook classes / semantic classes
- [ ] Έγραψα scoped CSS selectors με βάση τα hook classes
- [ ] Αφαίρεσα inline `<style>` από το Blade template
- [ ] Έκανα hard refresh στο browser
- [ ] Επαλήθευσα ότι το CSS "πιάνει"

---

## 📚 Πηγές

- [Filament 4 Assets Documentation](https://filamentphp.com/docs/4.x/assets)
- [Filament 4 CSS Hooks Documentation](https://filamentphp.com/docs/4.x/styling/css-hooks)
- [Filament 4 FileUpload Documentation](https://filamentphp.com/docs/4.x/forms/file-upload)

---

## 🎯 Next Steps

### ✅ Assets Compiled Successfully

Το CSS έχει compile-αριστεί επιτυχώς:
```
⇂ C:\laragon\www\lara\public\css\app\fileupload-overrides.css
```

---

### 🔍 Inspect Element - Βρες το HTML Structure

**Τώρα χρειάζεται να κάνεις inspect element για να βρούμε τα σωστά Filament hook classes.**

**Βήματα:**

1. **Άνοιξε το admin panel** στο browser:
   ```
   http://lara.test/admin/uploads
   ```

2. **Κάνε Right Click → Inspect Element** στο FileUpload field (στο "Files" input)

3. **Βρες το outermost wrapper** που έχει Filament classes:
   - Ψάξε για classes που ξεκινάνε με `fi-` (π.χ. `fi-fo-field-wrp`, `fi-section-content`)
   - Ψάξε για data attributes (π.χ. `data-field-wrapper="files"`)
   - Το wrapper θα είναι γύρω από το `.filepond--root` element

4. **Copy το HTML:**
   - Right Click στο wrapper element → **Copy → Copy Outer HTML**
   - Ή απλά σημείωσε τις κλάσεις που βλέπεις

5. **Στείλε μου:**
   - Το HTML snippet (ή τουλάχιστον τις κλάσεις του wrapper)
   - Θα σου δώσω ακριβώς τους scoped selectors που χρειάζεσαι

---

### 📝 Παράδειγμα Τι Να Ψάχνεις

**Ψάξε για structure σαν αυτό (υποθετικό):**

```html
<div class="fi-fo-field-wrp" data-field-wrapper="files">
    <div class="fi-fo-field-wrp-label">
        <label>Files</label>
    </div>
    <div class="fi-fo-field-wrp-input">
        <div class="filepond--root">
            <!-- FilePond content -->
        </div>
    </div>
</div>
```

**Σημαντικό:** Το πραγματικό structure μπορεί να διαφέρει. Κάνε inspect για να δεις το ακριβές.

---

**Status:** ✅ **COMPLETED** - Οι scoped selectors έχουν γραφτεί με βάση το HTML structure.

---

## ✅ Final Solution - Scoped CSS Selectors

Με βάση το HTML structure που βρέθηκε από το inspect element, οι σωστοί scoped selectors είναι:

### HTML Structure (από inspect):

```html
<div class="fi-fo-field" data-field-wrapper="">
    <div class="fi-fo-field-content-col">
        <div class="fi-fo-file-upload">
            <div class="fi-fo-file-upload-input-ctn">
                <div class="filepond--root">
                    <!-- FilePond content -->
                </div>
            </div>
        </div>
    </div>
</div>
```

### CSS Selectors (στο `resources/css/filament-fileupload.css`):

```css
/* Scoped στο FileUpload field - μειώνει το μέγεθος των εικονιδίων */
.fi-fo-field.fi-fo-field .fi-fo-file-upload .filepond--root {
    --filepond-icon-size: 1.25rem;
}

/* File icon wrapper - μικρότερα εικονίδια */
.fi-fo-field.fi-fo-field .fi-fo-file-upload .filepond--file-icon-wrapper svg {
    width: 1.25rem;
    height: 1.25rem;
}

/* Image preview wrapper - μικρότερα previews */
.fi-fo-field.fi-fo-field .fi-fo-file-upload .filepond--image-preview-wrapper {
    max-height: 120px;
}

/* File item panel - μικρότερο panel */
.fi-fo-field.fi-fo-field .fi-fo-file-upload .filepond--file {
    min-height: 60px;
}

/* File info - μικρότερο text */
.fi-fo-field.fi-fo-field .fi-fo-file-upload .filepond--file-info {
    font-size: 0.75rem;
}

/* File status - μικρότερο status indicator */
.fi-fo-field.fi-fo-field .fi-fo-file-upload .filepond--file-status {
    font-size: 0.75rem;
}
```

### Γιατί `.fi-fo-field.fi-fo-field` (duplicate class);

Χρησιμοποιούμε `.fi-fo-field.fi-fo-field` για **higher specificity** - αυτό εξασφαλίζει ότι τα styles μας θα override-άρουν τα default Filament styles χωρίς να χρειάζεται `!important`.

### Επόμενα Βήματα:

1. ✅ CSS file δημιουργήθηκε
2. ✅ Panel config ενημερώθηκε
3. ✅ Assets compiled
4. ✅ Scoped selectors γράφτηκαν
5. ⏳ **Hard refresh στο browser** (Ctrl+Shift+R / Cmd+Shift+R)
6. ⏳ **Επαλήθευση** ότι τα εικονίδια είναι μικρότερα

---

**Status:** ✅ **COMPLETED** - Οι scoped selectors έχουν γραφτεί και είναι έτοιμοι για χρήση.
