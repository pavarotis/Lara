# Filament 4 API Reference Guide

**Last Updated**: 2026-01-22  
**Purpose**: Prevent common Filament 4 API mistakes

---

## ⚠️ Critical API Changes from Filament 3 → 4

### 1. Forms vs Schemas

**❌ WRONG (Filament 3):**
```php
use Filament\Forms\Form;

public function form(Form $form): Form
{
    return $form->schema([...]);
}
```

**✅ CORRECT (Filament 4):**
```php
use Filament\Schemas\Schema;

public function form(Schema $schema): Schema
{
    return $schema->schema([...]);
}
```

**When to use:**
- **Resources**: `form(Schema $schema): Schema`
- **Pages with HasForms**: `form(Schema $schema): Schema`
- **Custom Form Classes**: Use `Schema` not `Form`

---

### 2. Section Component

**❌ WRONG:**
```php
use Filament\Forms\Components\Section;
```

**✅ CORRECT:**
```php
use Filament\Schemas\Components\Section;
```

---

### 3. Navigation Properties

**❌ WRONG:**
```php
protected static ?string $navigationIcon;
protected static ?string $navigationGroup;
```

**✅ CORRECT:**
```php
protected static string|\BackedEnum|null $navigationIcon;
protected static string|\UnitEnum|null $navigationGroup;
```

---

### 4. Actions Namespace

**❌ WRONG:**
```php
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
```

**✅ CORRECT:**
```php
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
```

---

### 5. View Property (Pages)

**❌ WRONG:**
```php
protected static string $view = 'filament.pages.cms.dashboard';
```

**✅ CORRECT:**
```php
protected string $view = 'filament.pages.cms.dashboard'; // Non-static!
```

---

## 📋 Quick Checklist

Before creating a new Filament Resource/Page:

- [ ] Use `Filament\Schemas\Schema` not `Filament\Forms\Form`
- [ ] Use `Filament\Schemas\Components\Section` not `Filament\Forms\Components\Section`
- [ ] Use `Filament\Actions\*` not `Filament\Tables\Actions\*`
- [ ] Navigation properties use union types (`string|\BackedEnum|null`)
- [ ] Page `$view` property is **non-static**

---

## 🔧 Asset Management & Cache Commands

### Πότε χρησιμοποιώ `php artisan filament:assets` και `php artisan optimize:clear`

**1️⃣ `php artisan filament:assets`**

**ΤΙ ΚΑΝΕΙ:**
- Αντιγράφει / δημοσιεύει τα assets του Filament και τα custom panel assets σου (CSS / JS) στο `public/`
- **ΔΕΝ** κάνει build Tailwind
- **ΔΕΝ** επεξεργάζεται `@apply` directives

**ΠΟΤΕ ΤΟ ΤΡΕΧΕΙΣ:**

✅ **ΤΟ ΤΡΕΧΕΙΣ ΚΑΘΕ ΦΟΡΑ ΠΟΥ:**
- Αλλάζεις ή προσθέτεις `->assets([ Css::make(...) ])` στο PanelProvider
- Προσθέτεις/αλλάζεις custom CSS αρχείο που φορτώνεται μέσω Filament panel
- Προσθέτεις/αλλάζεις custom JS asset για Filament

❌ **ΔΕΝ χρειάζεται όταν:**
- Αλλάζεις μόνο Blade templates
- Αλλάζεις PHP logic
- Αλλάζεις Livewire methods
- Αλλάζεις Filament components config

**Παράδειγμα:**
```php
// AdminPanelProvider.php
->assets([
    Css::make('backup-restore', resource_path('css/backup-restore.css')),
])
```

👉 **Μετά από αυτό ΠΑΝΤΑ:**
```bash
php artisan filament:assets
```

Αλλιώς το νέο CSS δεν θα φορτωθεί ποτέ.

**2️⃣ `php artisan optimize:clear`**

**ΤΙ ΚΑΝΕΙ:**
Καθαρίζει:
- View cache
- Route cache
- Config cache
- Event cache
- Compiled cache
- Blade icons cache
- Filament cache

**ΠΟΤΕ ΤΟ ΤΡΕΧΕΙΣ:**

✅ **Το τρέχεις όταν:**
- Βλέπεις ότι "δεν αλλάζει τίποτα" ενώ άλλαξες Blade / CSS
- Filament δείχνει παλιά έκδοση
- Assets φορτώνονται αλλά styles δεν εφαρμόζονται
- Περίεργα errors "κολλάνε"
- Μετά από `filament:assets` και hard refresh δεν βλέπεις αλλαγές

⚠️ **ΔΕΝ χρειάζεται πάντα**
⚠️ **ΜΗΝ** το τρέχεις σε κάθε αλλαγή "by default"

**🧠 ΤΟ ΣΩΣΤΟ WORKFLOW:**

| Αλλαγή | Τι τρέχω |
|--------|----------|
| 🔁 Αλλάζεις Blade / PHP | → refresh browser |
| 🎨 Αλλάζεις custom Filament CSS | `php artisan filament:assets` → hard refresh (Ctrl + F5) |
| 😵 "Δεν πιάνει τίποτα / βλέπω παλιά styles" | `php artisan filament:assets` + `php artisan optimize:clear` |
| 🚀 Παραγωγή (deploy) | Και τα δύο |

**❌ ΤΙ ΔΕΝ ΠΡΕΠΕΙ ΝΑ ΚΑΝΕΙΣ:**

- ❌ Να τρέχεις `optimize:clear` κάθε φορά (χάνει χρόνο)
- ❌ Να περιμένεις ότι `filament:assets` θα κάνει Tailwind compile
- ❌ Να γράφεις `@apply` σε CSS που φορτώνεται από Filament assets (χρησιμοποίησε pure CSS)

**💡 Pro Tip:**

Αν χρησιμοποιείς custom CSS files με Filament, γράψε **pure CSS** (χωρίς `@apply`). Το `filament:assets` δεν κάνει Tailwind compilation, οπότε τα `@apply` directives θα αγνοηθούν.

**Παράδειγμα - ❌ WRONG:**
```css
/* resources/css/backup-restore.css */
.backup-restore-table {
    @apply rounded-lg border; /* ΔΕΝ θα λειτουργήσει! */
}
```

**Παράδειγμα - ✅ CORRECT:**
```css
/* resources/css/backup-restore.css */
.backup-restore-table {
    border-radius: 0.5rem;
    border: 1px solid rgb(229 231 235);
}
```

---

## 🧠 Filament Dev Cheat Sheet (ΠΡΑΚΤΙΚΟ)

**🔁 Τι άλλαξα → Τι κάνω**

| Τι άλλαξα | Τι κάνω |
|-----------|---------|
| 🧱 **Άλλαξα Blade** (`.blade.php`) | → απλό refresh |
| 🧠 **Άλλαξα PHP logic**<br>• Page class<br>• Livewire methods<br>• Filament config (όχι assets) | → απλό refresh |
| 🎨 **Άλλαξα custom CSS για Filament panel**<br>(π.χ. `resources/css/backup-restore.css`) | `php artisan filament:assets`<br>👉 μετά hard refresh (Ctrl + F5) |
| 🧩 **Πρόσθεσα ή άλλαξα:**<br>• `->assets([ Css::make(...) ])`<br>• νέο CSS / JS asset<br>• path σε asset | `php artisan filament:assets` |
| 😵 **"Δεν αλλάζει τίποτα / βλέπω παλιά styles"** | `php artisan filament:assets`<br>`php artisan optimize:clear`<br>👉 μετά hard refresh |

**❌ Τι ΔΕΝ κάνει το `filament:assets`:**

- ❌ Δεν κάνει Tailwind build
- ❌ Δεν καταλαβαίνει `@apply`
- ❌ Δεν επεξεργάζεται CSS
- ❌ Δεν κάνει minify Tailwind utilities

👉 **Είναι copy/publish tool, όχι compiler.**

**🧩 Tailwind – ο κανόνας:**

Αν ένα CSS αρχείο φορτώνεται μέσω Filament panel assets:

- ✔ γράφεις καθαρό CSS
- ❌ όχι `@apply`
- ❌ όχι Tailwind utilities

Αν θες Tailwind → πρέπει να περάσει από Vite build (άλλο pipeline).

**🛑 Σημάδια ότι κάτι πάει στραβά:**

- `flex`, `gap`, `space-y` δεν δουλεύουν ❌
- SVG γίνονται τεράστια ❌
- layout "σπάει" χωρίς λόγο ❌

➡️ **99% γράφεις `@apply` σε CSS που δεν γίνεται compile**

**🧪 10-second debug checklist:**

1. Inspect element
2. Βλέπω `display: flex;` ή όχι;
3. Αν όχι → CSS δεν εφαρμόζεται
4. Έτρεξα `filament:assets`?
5. Έκανα hard refresh?

**🚀 Production deploy (κανόνας):**

```bash
php artisan filament:assets
php artisan optimize
```

(όχι `optimize:clear`, αλλά `optimize`)

**🧩 Μίνι mantra Filament dev 😄:**

```
Blade → refresh
CSS → filament:assets
Πανικός → optimize:clear
@apply → ΜΗΝ ΤΟ ΚΑΝΕΙΣ
```

---

## 🛡️ Bootstrap Safety & Cache Management

### ⚠️ "Target class [config] does not exist" Error Prevention

**Το πρόβλημα:**
Αυτό το error συμβαίνει όταν:
- Τρέχεις `php artisan optimize` με corrupted cache files
- Service Providers καλούν `config()` ή `Log::` πριν το config service είναι διαθέσιμο
- Cache files (`bootstrap/cache/*.php`) είναι locked ή corrupted

**✅ Προστασία που έχουμε ήδη προσθέσει:**

1. **AppServiceProvider** - Try-catch + config binding check
2. **PluginRegistryService** - Checks πριν κάθε `config()` call
3. **Log calls** - Protected με binding checks

**📋 Service Provider Best Practices (Κανόνες):**

**❌ ΜΗΝ ΚΑΝΕΙΣ:**
```php
// AppServiceProvider.php - WRONG
public function boot(): void
{
    // ❌ config() χωρίς check
    $plugins = config('plugins.providers');
    
    // ❌ Log:: χωρίς check
    Log::info('Booting...');
    
    // ❌ app('config') χωρίς check
    $value = app('config')->get('key');
}
```

**✅ ΚΑΝΕ ΑΥΤΟ:**
```php
// AppServiceProvider.php - CORRECT
public function boot(): void
{
    // ✅ Check αν το config είναι διαθέσιμο
    try {
        if (app()->bound('config')) {
            $plugins = config('plugins.providers');
            // ... rest of code
        }
    } catch (\Exception $e) {
        // Silently fail during bootstrap
        // Will work on next request when config is available
    }
}
```

**✅ Pattern για Services που χρησιμοποιούν config:**
```php
// PluginRegistryService.php - CORRECT
public function discover(): array
{
    // Check if config service is available
    if (!app()->bound('config')) {
        return [];
    }
    
    return config('plugins.providers', []);
}
```

**✅ Pattern για Log calls:**
```php
// CORRECT
if (app()->bound('config') && app()->bound('log')) {
    Log::warning("Plugin class not found: {$pluginClass}");
}
```

### 5️⃣ Table Actions Column Alignment (Center/Right)

**Πρόβλημα:** Το Actions column header δεν συγχρονίζεται με τα κουμπιά από κάτω.

**✅ Λύση: Ίδιο Flex Layout στο Header και Body**

**Blade Template:**
```blade
<thead>
    <tr>
        <th>Filename</th>
        <th>Type</th>
        <th>Size</th>
        <th>Created</th>
        <th>
            <div class="backup-restore-actions-header">Actions</div>
        </th>
    </tr>
</thead>
<tbody>
    @foreach($items as $item)
        <tr>
            <td>...</td>
            <td>
                <div class="backup-restore-actions">
                    <x-filament::button>Download</x-filament::button>
                    <x-filament::button>Delete</x-filament::button>
                </div>
            </td>
        </tr>
    @endforeach
</tbody>
```

**CSS - Center Alignment:**
```css
/* Header wrapper - ίδιο flex layout με body */
.backup-restore-actions-header {
    display: flex;
    justify-content: center;  /* ή flex-end για right alignment */
    align-items: center;
    width: 100%;
}

/* Body actions wrapper - ίδιο flex layout */
.backup-restore-actions {
    display: flex;
    justify-content: center;  /* ή flex-end για right alignment */
    align-items: center;
    gap: 0.75rem;
    flex-wrap: nowrap;
    width: 100%;
}

/* Table cells - center alignment */
.backup-restore-table thead th {
    text-align: center;
}

.backup-restore-table tbody td {
    text-align: center;
}
```

**CSS - Right Alignment (Alternative):**
```css
/* Για right alignment, αλλάξε justify-content */
.backup-restore-actions-header {
    justify-content: flex-end;  /* right alignment */
}

.backup-restore-actions {
    justify-content: flex-end;  /* right alignment */
}
```

**✅ Βασικές Αρχές:**

1. **Χρησιμοποίησε wrapper div στο header** - Ίδιο class pattern με το body
2. **Ίδιο flex engine** - `display: flex; justify-content: center/flex-end` και στα δύο
3. **Μην χρησιμοποιείς `display: flex` στο `<td>`** - Κράτα το `<td>` ως table-cell
4. **Χρησιμοποίησε `inline-flex` ή `flex` στο wrapper** - Όχι στο cell

**❌ Common Mistakes:**

```css
/* WRONG - flex στο td σπάει table layout */
.backup-restore-table tbody td:last-child {
    display: flex;  /* ❌ */
    justify-content: flex-end;
}

/* WRONG - text-align χωρίς wrapper */
.backup-restore-table thead th:last-child {
    text-align: right;  /* ❌ Δεν συγχρονίζεται με body */
}
```

**✅ Correct Pattern:**
```css
/* CORRECT - wrapper με flex */
.backup-restore-actions-header {
    display: flex;
    justify-content: center;  /* ή flex-end */
}

.backup-restore-actions {
    display: flex;
    justify-content: center;  /* ή flex-end */
}
```

**🔒 Cache Management Rules:**

**Local Development:**
- ✅ Χρησιμοποίησε `optimize:clear` όταν κάτι κολλάει
- ❌ ΜΗΝ τρέχεις `optimize` στο local (δεν είναι απαραίτητο)
- ✅ Μετά από `filament:assets`, δοκίμασε πρώτα το site πριν τρέξεις `optimize`

**Production:**
- ✅ Τρέξε `optimize` μόνο μετά από deploy
- ✅ Πάντα τρέξε `optimize:clear` πριν από `optimize` σε production

**🚨 Αν ξανασυμβεί (Cache Corrupted):**

**Συμπτώματα:**
- `php artisan` commands δεν δουλεύουν
- Error: "Target class [config] does not exist"
- Το site δεν φορτώνει

**Λύση (Step-by-step):**

1. **Κλείσε το Laragon** (Stop All) - ΚΡΙΣΙΜΟ!
2. **Διέγραψε cache files χειροκίνητα:**
   - Άνοιξε `bootstrap/cache/`
   - Διέγραψε: `config.php`, `packages.php`, `services.php` (αν υπάρχουν)
   - Ή τρέξε: `.\clear-cache.ps1` (αν το Laragon είναι κλειστό)
3. **Ξαναάνοιξε το Laragon**
4. **Δοκίμασε το site** - θα πρέπει να δουλεύει
5. **Μετά τρέξε:** `php artisan optimize:clear` (για να καθαρίσεις τα υπόλοιπα caches)

**⚠️ ΣΗΜΑΝΤΙΚΟ:**
- Αν τα cache files είναι **locked**, πρέπει να κλείσεις το Laragon ΠΡΙΝ τα διαγράψεις
- Το `optimize:clear` **ΔΕΝ** θα δουλέψει αν τα cache files είναι corrupted
- Πρέπει να τα διαγράψεις **χειροκίνητα** πρώτα

**📝 Checklist για νέους Service Providers:**

Όταν γράφεις νέο Service Provider, βεβαιώσου ότι:

- [ ] Δεν καλείς `config()` στο `register()` method
- [ ] Αν χρειάζεται `config()` στο `boot()`, το τυλίγεις σε `if (app()->bound('config'))`
- [ ] Αν χρειάζεται `Log::`, ελέγχεις `app()->bound('log')` πρώτα
- [ ] Χρησιμοποιείς try-catch για critical operations στο `boot()`
- [ ] Δεν κάνεις heavy operations στο `register()` (μόνο bindings)

**💡 Pro Tip:**

Αν δημιουργείς service που χρειάζεται config, χρησιμοποίησε **deferred loading**:

```php
// CORRECT - Deferred loading
public function boot(): void
{
    // Register only if config is available
    $this->app->booted(function () {
        if (app()->bound('config')) {
            $this->registerPlugins();
        }
    });
}
```

---

## 6. Text Colors and Hover States in Blade Templates

**❌ WRONG:**
```blade
<!-- Tailwind hover classes don't work well with inline styles -->
<a href="#" style="color: #d4a574;" class="hover:text-amber-500">
    Link text
</a>
```

**✅ CORRECT:**
```blade
<!-- Use onmouseover/onmouseout for hover with inline styles -->
<a href="#" 
   class="font-medium transition-colors duration-200" 
   style="color: #d4a574;" 
   onmouseover="this.style.color='#f59e0b';" 
   onmouseout="this.style.color='#d4a574';">
    Link text
</a>
```

**Alternative (using Tailwind classes only):**
```blade
<!-- If using Tailwind colors, use classes without inline styles -->
<a href="#" class="text-amber-600 hover:text-amber-500 font-medium transition-colors">
    Link text
</a>
```

**Key Points:**
- When using **inline styles** (`style="color: ..."`), use `onmouseover`/`onmouseout` for hover
- When using **Tailwind classes**, use `hover:` prefix (e.g., `hover:text-amber-500`)
- Don't mix inline styles with Tailwind hover classes - they conflict
- For Filament's primary amber color, use `#f59e0b` (amber-500) or Tailwind `text-amber-500`

---

## 7. Padding and Spacing in Blade Templates

**❌ WRONG:**
```blade
<!-- Tailwind padding classes may not work as expected in some contexts -->
<div class="p-4 mb-6">
    Content
</div>
```

**✅ CORRECT (Inline Styles):**
```blade
<!-- Use inline styles for reliable padding/spacing -->
<div class="mb-6" style="padding: 1.5rem;">
    Content
</div>

<!-- For vertical padding only -->
<div style="padding: 1rem 0;">
    Content
</div>
```

**✅ CORRECT (Tailwind with explicit values):**
```blade
<!-- Use explicit Tailwind spacing classes -->
<div class="p-6 mb-8">
    Content
</div>
```

**Key Points:**
- When Tailwind classes don't work, use **inline styles** with `style="padding: X;"` or `style="padding: Y X;"`
- For spacing between elements, use `mb-*` (margin-bottom) or `mt-*` (margin-top) classes
- Common padding values:
  - `1rem` = 16px (small padding)
  - `1.5rem` = 24px (medium padding)
  - `2rem` = 32px (large padding)
- For vertical-only padding: `style="padding: 1rem 0;"` (top/bottom: 1rem, left/right: 0)
- For horizontal-only padding: `style="padding: 0 1rem;"` (top/bottom: 0, left/right: 1rem)

**Example:**
```blade
<!-- Buttons container with vertical padding -->
<div class="flex justify-end gap-2 mb-8" style="padding: 1rem 0;">
    <x-filament::button>Button</x-filament::button>
</div>

<!-- Info box with all-around padding -->
<div class="bg-gray-50 rounded-lg mb-6" style="padding: 1.5rem;">
    <p>Info text</p>
</div>
```

---

## 8. Horizontal Tabs Menu

**When to use**: Pages with 3+ categories/functions that need organization.

**✅ CORRECT:**
```blade
<x-filament::tabs>
    <x-filament::tabs.item
        :active="$activeTab === 'tab1'"
        wire:click="$set('activeTab', 'tab1')"
    >
        Tab Label
    </x-filament::tabs.item>
</x-filament::tabs>
```

**❌ WRONG:**
```blade
<x-filament::tabs.item label="Tab Label" />
```

**See**: [Horizontal Tabs Menu Guide](./horizontal_tabs_menu.md) for complete implementation.

---

## 9. Log Viewer / Readonly Textarea (Χωρίς Tailwind)

### ❌ Το Πρόβλημα με Filament Textarea

Το Filament 4 Textarea component:
- Έχει **autosize enabled by default**
- Δεν σέβεται `height` στο `extraAttributes` όταν χρησιμοποιείς Schema forms
- Το wrapper του Filament προσπαθεί να κάνει inline autosize
- Ακόμα κι αν βάλεις `height: 500px`, το component render-άρεται μέσα σε wrapper div με `display: flex` / auto resize
- Η ύψος του textarea **αγνοείται**

**Παράδειγμα που ΔΕΝ δουλεύει:**
```php
Textarea::make('logContent')
    ->label(false)
    ->disabled()
    ->autosize(false)  // ❌ Αγνοείται
    ->extraAttributes([
        'style' => 'height: 500px;',  // ❌ Αγνοείται
    ])
```

### ✅ Η Σωστή Λύση: HTML Textarea στο Blade

**Μην χρησιμοποιείς Filament Form schema για readonly log viewers.**

Αντί για `Textarea::make()`, βάλτο απευθείας σε Blade:

**Blade Template:**
```blade
<div class="log-viewer-body">
    <textarea
        readonly
        style="
            width: 100%;
            height: 500px;
            font-family: monospace;
            white-space: pre;
            overflow: auto;
            resize: none;
            padding: 0;
            border: none;
            background: #0f172a;
            color: #4ade80;
        "
    >{{ $this->logContent }}</textarea>
</div>
```

**PHP Class (απλοποιημένο):**
```php
class ErrorLogs extends Page  // ❌ ΔΕΝ implements HasForms
{
    // ❌ ΔΕΝ use InteractsWithForms;
    
    public ?string $logContent = null;
    
    // ❌ ΔΕΝ χρειάζεται form() method
    
    public function loadLogFile(string $filename): void
    {
        $this->selectedLogFile = $filename;
        $logPath = storage_path('logs/'.$filename);
        
        if (File::exists($logPath)) {
            $lines = file($logPath);
            $totalLines = count($lines);
            $startLine = max(0, $totalLines - 1000);
            $this->logContent = implode('', array_slice($lines, $startLine));
            // ❌ ΔΕΝ χρειάζεται $this->form->fill()
        }
    }
}
```

### 🎨 CSS Styling (Χωρίς Tailwind)

**CSS File (`resources/css/error-logs.css`):**
```css
.log-viewer-body {
    padding: 0;
}

.log-viewer-body textarea {
    width: 100%;
    height: 500px;
    white-space: pre;
    font-family: monospace;
    overflow: auto;
    resize: none;
    padding: 0;
    margin: 0;
    border: none;
    border-radius: 0;
    background: #0f172a;
    color: #4ade80;
}
```

**Panel Provider (`app/Providers/Filament/AdminPanelProvider.php`):**
```php
use Filament\Support\Assets\Css;

public function panel(Panel $panel): Panel
{
    return $panel
        ->assets([
            Css::make('error-logs', base_path('resources/css/error-logs.css')),
        ])
        // ... rest of config
}
```

### 📋 Checklist για Log Viewer

- ✅ **Χωρίς Filament Forms** - Μην χρησιμοποιείς `HasForms`, `InteractsWithForms`, ή `form()` method
- ✅ **HTML textarea** - Απευθείας `<textarea>` στο Blade
- ✅ **Fixed height** - `height: 500px` (δεν επηρεάζεται από autosize)
- ✅ **CSS Asset** - Προσθήκη στο Panel Provider με `Css::make()`
- ✅ **Semantic classes** - Χρήση CSS classes αντί για Tailwind utilities
- ✅ **Readonly** - `readonly` attribute για προβολή μόνο
- ✅ **Terminal look** - Dark background (#0f172a), green text (#4ade80)

### 🚫 Τι ΝΑ ΜΗΝ Κάνεις

- ❌ **Μην χρησιμοποιείς `Textarea::make()`** για readonly log viewers
- ❌ **Μην προσπαθείς `autosize(false)`** - Δεν δουλεύει με Schema forms
- ❌ **Μην χρησιμοποιείς Tailwind utilities** - Χρησιμοποίησε CSS classes
- ❌ **Μην χρησιμοποιείς `@vite()`** στο Blade - Χρησιμοποίησε Panel assets
- ❌ **Μην χρησιμοποιείς inline `<link>`** - Χρησιμοποίησε `Css::make()`

### 💡 Γιατί Αυτή η Προσέγγιση

1. **Filament autosize** - Το wrapper του Filament προσπαθεί να κάνει auto-resize, αγνοώντας το fixed height
2. **Schema forms** - Όταν χρησιμοποιείς Schema, το wrapper έχει `display: flex` που επηρεάζει το layout
3. **Καθαρός κώδικας** - HTML textarea είναι πιο απλό και προβλέψιμο
4. **CSS control** - Με καθαρό CSS έχεις πλήρη έλεγχο στο styling

---

## 10. Table Actions Column Alignment - Complete Guide

### 🎯 Στόχος

Να συγχρονίζεται το Actions column header με τα κουμπιά από κάτω, είτε με center είτε με right alignment.

### ⚠️ Common Problem

Το Actions header δεν "κάθεται" ακριβώς πάνω από τα κουμπιά:
- Header: απλό κείμενο "Actions"
- Body: group κουμπιών με δικό του πλάτος
- Διαφορετικό alignment engine → δεν συγχρονίζονται

### ✅ Λύση: Ίδιο Flex Layout στο Header και Body

**1️⃣ Blade Template - Προσθήκη Wrapper στο Header:**

```blade
<thead>
    <tr>
        <th>Filename</th>
        <th>Type</th>
        <th>Size</th>
        <th>Created</th>
        <th>
            <div class="backup-restore-actions-header">Actions</div>
        </th>
    </tr>
</thead>
<tbody>
    @foreach($backups as $backup)
        <tr>
            <td>...</td>
            <td>
                <div class="backup-restore-actions">
                    <x-filament::button>Download</x-filament::button>
                    <x-filament::button>Delete</x-filament::button>
                </div>
            </td>
        </tr>
    @endforeach
</tbody>
```

**2️⃣ CSS - Center Alignment (Recommended):**

```css
/* Header wrapper - ίδιο flex layout με body */
.backup-restore-actions-header {
    display: flex;
    justify-content: center;
    align-items: center;
    width: 100%;
}

/* Body actions wrapper - ίδιο flex layout */
.backup-restore-actions {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 0.75rem;
    flex-wrap: nowrap;
    width: 100%;
}

/* Table cells - center alignment */
.backup-restore-table thead th {
    text-align: center;
}

.backup-restore-table tbody td {
    text-align: center;
}
```

**3️⃣ CSS - Right Alignment (Alternative):**

```css
/* Για right alignment, αλλάξε μόνο το justify-content */
.backup-restore-actions-header {
    display: flex;
    justify-content: flex-end;  /* right alignment */
    align-items: center;
    width: 100%;
}

.backup-restore-actions {
    display: flex;
    justify-content: flex-end;  /* right alignment */
    align-items: center;
    gap: 0.75rem;
    flex-wrap: nowrap;
    width: 100%;
}
```

### ✅ Βασικές Αρχές

1. **Χρησιμοποίησε wrapper div στο header** - Ίδιο class pattern με το body
2. **Ίδιο flex engine** - `display: flex; justify-content: center/flex-end` και στα δύο
3. **Μην χρησιμοποιείς `display: flex` στο `<td>`** - Κράτα το `<td>` ως table-cell
4. **Χρησιμοποίησε `flex` στο wrapper** - Όχι στο cell

### ❌ Common Mistakes

**WRONG - Flex στο td σπάει table layout:**
```css
.backup-restore-table tbody td:last-child {
    display: flex;  /* ❌ Σπάει table layout */
    justify-content: flex-end;
}
```

**WRONG - Text-align χωρίς wrapper:**
```css
.backup-restore-table thead th:last-child {
    text-align: right;  /* ❌ Δεν συγχρονίζεται με body */
}
```

**WRONG - Double alignment logic:**
```css
.backup-restore-table tbody td:last-child {
    text-align: right;  /* Method 1 */
}

.backup-restore-actions {
    margin-left: auto;  /* Method 2 - CONFLICT! */
}
```

### ✅ Correct Pattern

```css
/* Header wrapper */
.backup-restore-actions-header {
    display: flex;
    justify-content: center;  /* ή flex-end */
    align-items: center;
    width: 100%;
}

/* Body wrapper */
.backup-restore-actions {
    display: flex;
    justify-content: center;  /* ή flex-end */
    align-items: center;
    gap: 0.75rem;
    flex-wrap: nowrap;
    width: 100%;
}

/* Table cells - normal table-cell behavior */
.backup-restore-table tbody td:last-child {
    padding-right: 1.5rem;
    vertical-align: middle;
    /* NO display: flex here */
}
```

### 💡 Γιατί Αυτή η Προσέγγιση

1. **Ίδιο flex engine** - Header και body χρησιμοποιούν το ίδιο layout system
2. **Table layout intact** - Το `<td>` μένει table-cell, δεν σπάει το layout
3. **Deterministic alignment** - Με `width: 100%` και ίδιο `justify-content`, συγχρονίζονται
4. **Responsive friendly** - Δουλεύει σε όλα τα screen sizes

### 📋 Checklist

- [ ] Wrapper div στο header με class (π.χ. `backup-restore-actions-header`)
- [ ] Wrapper div στο body με class (π.χ. `backup-restore-actions`)
- [ ] Ίδιο `display: flex` και `justify-content` και στα δύο wrappers
- [ ] `width: 100%` και στα δύο wrappers
- [ ] Το `<td>` δεν έχει `display: flex` (μένει table-cell)
- [ ] `text-align: center` στα `th` και `td` για center alignment

---

## 11. Custom Pages (Χωρίς Forms) - Complete Guide

### 🎯 Στόχος

Να φτιάχνεις σελίδες με custom περιεχόμενο και styling χωρίς να περιορίζεσαι σε Filament Forms, με πλήρη έλεγχο στο HTML, CSS, και behavior.

### 1️⃣ Γενική Δομή Page Class

**Δημιουργείς μια κλάση που κληρονομεί από `Filament\Pages\Page`.**

**❌ ΔΕΝ χρησιμοποιείς:**
- `HasForms` ή `InteractsWithForms` αν δεν θες schema forms
- `form()` method αν δεν χρειάζεται

**✅ Κάνεις:**
- Δηλώνεις μεταβλητές για το state της σελίδας (π.χ. επιλεγμένα logs, περιεχόμενο)
- Φορτώνεις δεδομένα με απλές μεθόδους PHP/Laravel (`File::get()`, `DB::table()->get()` κλπ)
- Κρατάς όλα τα δεδομένα σε `public` properties για εύκολο binding στο Blade

**Παράδειγμα:**
```php
class ErrorLogs extends Page  // ❌ ΔΕΝ implements HasForms
{
    // ❌ ΔΕΝ use InteractsWithForms;
    
    public ?string $selectedLogFile = null;
    public ?string $logContent = null;
    
    public function mount(): void
    {
        // Auto-load default data
        $this->loadDefaultData();
    }
    
    public function getLogFiles(): array
    {
        // Simple PHP/Laravel logic
        $logPath = storage_path('logs');
        $files = [];
        
        if (File::exists($logPath)) {
            $allFiles = File::files($logPath);
            foreach ($allFiles as $file) {
                if ($file->getExtension() === 'log') {
                    $files[] = [
                        'name' => $file->getFilename(),
                        'size' => $file->getSize(),
                        'modified' => $file->getMTime(),
                    ];
                }
            }
        }
        
        return $files;
    }
    
    public function loadLogFile(string $filename): void
    {
        $this->selectedLogFile = $filename;
        $logPath = storage_path('logs/'.$filename);
        
        if (File::exists($logPath)) {
            $lines = file($logPath);
            $totalLines = count($lines);
            $startLine = max(0, $totalLines - 1000);
            $this->logContent = implode('', array_slice($lines, $startLine));
        }
    }
}
```

### 2️⃣ Blade Template

**Χρησιμοποίησε `<x-filament-panels::page>` για το wrapper.**

**Χωρίς forms, μπορείς να φτιάξεις οποιοδήποτε HTML:**
- Lists, tables, divs, buttons
- Custom textarea με inline styles και PHP variables
- Livewire interactivity με `wire:click`

**Παράδειγμα με PHP Variables για παραμετροποίηση:**
```blade
<x-filament-panels::page>
    <div class="custom-page">
        @if ($this->selectedLogFile && $this->logContent)
            <div class="log-viewer">
                <div class="log-viewer-header">
                    <span>{{ $this->selectedLogFile }}</span>
                    <button wire:click="$set('selectedLogFile', null)">✕</button>
                </div>
                <div class="log-viewer-body">
                    @php
                        $height = '500px';
                        $padding = '0.5rem 1rem';
                        $margin = '1rem 0';
                        $fontSize = '0.875rem';
                        $lineHeight = '1.5';
                        $bgColor = 'rgb(44 44 44)';
                        $textColor = '#ffffff';
                        $border = '1px solid #333';
                        $borderRadius = '4px';
                    @endphp
                    <textarea
                        readonly
                        style="
                            width: 100%;
                            height: {{ $height }};
                            padding: {{ $padding }};
                            margin: {{ $margin }};
                            font-family: monospace;
                            font-size: {{ $fontSize }};
                            line-height: {{ $lineHeight }};
                            white-space: pre-wrap;
                            word-break: break-word;
                            overflow-y: auto;
                            overflow-x: hidden;
                            resize: none;
                            border: {{ $border }};
                            border-radius: {{ $borderRadius }};
                            background: {{ $bgColor }};
                            color: {{ $textColor }};
                        "
                    >{{ $this->logContent }}</textarea>
                </div>
            </div>
        @endif
    </div>
</x-filament-panels::page>
```

**✅ Καλές πρακτικές:**
- Ορίζεις semantic classes για κάθε section (`.log-viewer`, `.header`, `.body`)
- Χρησιμοποιείς PHP variables για εύκολη παραμετροποίηση (height, padding, colors)
- Ελέγχεις overflow, scrolling, fixed height/width με CSS
- Για δυναμικό περιεχόμενο, χρησιμοποιείς Livewire bindings (`{{ $property }}`)

### 3️⃣ Styling με CSS Assets

**Δημιουργείς αυτόνομο CSS αρχείο και το προσθέτεις στο Panel Provider:**

**CSS File (`resources/css/error-logs.css`):**
```css
.custom-page {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.log-viewer {
    background: var(--filament-bg, #fff);
    border-radius: 8px;
    overflow: hidden;
}

.log-viewer-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem;
    border-bottom: 1px solid #e5e7eb;
}

.log-viewer-body {
    padding: 0;
}
```

**Panel Provider (`app/Providers/Filament/AdminPanelProvider.php`):**
```php
use Filament\Support\Assets\Css;

public function panel(Panel $panel): Panel
{
    return $panel
        ->assets([
            Css::make('error-logs', base_path('resources/css/error-logs.css')),
        ])
        // ... rest of config
}
```

**✅ Καλές πρακτικές:**
- Μην χρησιμοποιείς inline `@vite()` στο Blade
- Μην χρησιμοποιείς Tailwind utilities για βασικό layout
- Χρησιμοποίησε padding, margin, overflow, flex, width/height για πλήρη έλεγχο
- Προσάρμοσε χρώματα, fonts, και sizes στο CSS

**⚠️ Important - CSS Overrides για Third-Party Components:**

Όταν προσπαθείς να override-άρεις styles από third-party components (π.χ. FilePond στο FileUpload), χρησιμοποίησε:

1. **Filament Semantic Classes** ως base selector για scoping
2. **Higher Specificity** με duplicate classes (π.χ. `.fi-fo-field.fi-fo-field`)
3. **Inspect Element** για να βρεις το ακριβές HTML structure

**Παράδειγμα - FileUpload Icon Size Override:**

```css
/* resources/css/filament-fileupload.css */

/* Scoped στο FileUpload field - χρησιμοποιούμε Filament semantic classes */
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
```

**Σημείωση**: Αν το CSS δεν "πιάνει" ακόμα και με scoped selectors, μπορεί να χρειάζεται:
- JavaScript-based solution (FilePond initialization hooks)
- Custom FileUpload view component
- Alternative component approach

**⚠️ Known Limitation**: Το Filament 4 FileUpload component δεν έχει built-in method για icon size control. Το `imagePreviewHeight()` ελέγχει μόνο image preview height, όχι icon sizes.

### 4️⃣ Table Alignment Best Practices

**⚠️ Common Mistake - Double Alignment Logic:**

Όταν έχεις table cells με actions/buttons, μην συνδυάζεις `text-align: right` στο `<td>` με `margin-left: auto` ή `width: fit-content` στο flex container. Αυτό δημιουργεί "διπλή" λογική στοίχισης και συχνά φαίνεται σαν να μην κάθεται ακριβώς στο ίδιο x-position.

**❌ WRONG - Double Alignment:**
```css
.backup-restore-table tbody td:last-child {
    text-align: right;  /* Alignment method 1 */
}

.backup-restore-actions {
    display: flex;
    margin-left: auto;  /* Alignment method 2 - CONFLICT! */
    width: fit-content;
}
```

**✅ CORRECT - Single Alignment Method (Recommended):**

**Επιλογή A: Flex στο `<td>` (Recommended)**

Κάνε το `<td>` flex container και αφαιρέσε `text-align: right`:

```css
.backup-restore-table tbody td:last-child {
    padding-right: 1.5rem;
    vertical-align: middle;
    display: flex;
    justify-content: flex-end;  /* Single alignment method */
}

.backup-restore-actions {
    display: inline-flex;
    align-items: center;
    justify-content: flex-end;
    gap: 0.75rem;
    flex-wrap: nowrap;
    /* NO margin-left: auto */
    /* NO width: fit-content */
}
```

**Επιλογή B: Text-align στο `<td>` (Alternative)**

Αν προτιμάς `text-align: right`:

```css
.backup-restore-table tbody td:last-child {
    padding-right: 1.5rem;
    text-align: right;  /* Single alignment method */
    vertical-align: middle;
}

.backup-restore-actions {
    display: inline-flex;
    align-items: center;
    gap: 0.75rem;
    flex-wrap: nowrap;
    /* NO margin-left: auto */
    /* NO width: fit-content */
    /* NO justify-content: flex-end */
}
```

**✅ Καλές πρακτικές:**
- Χρησιμοποίησε **ΜΟΝΟ ένα** σύστημα στοίχισης (flex ή text-align)
- Με Livewire/Filament dynamic rendering, το flex στο `<td>` είναι πιο σταθερό
- Το `inline-flex` στο actions container είναι καλύτερο από `flex` για να μην πιάνει όλο το πλάτος
- Αποφύγετε `width: fit-content` + `margin-left: auto` μαζί - δημιουργεί layout shifts

### 5️⃣ Interactivity & Behavior

**Livewire hooks για actions:**
```blade
<!-- Refresh button -->
<button wire:click="refresh">Refresh</button>

<!-- Load data -->
<div wire:click="loadLogFile('{{ $file['name'] }}')">
    {{ $file['name'] }}
</div>

<!-- Close/Reset -->
<button wire:click="$set('selectedLogFile', null)">✕</button>
```

**Auto-load στο mount:**
```php
public function mount(): void
{
    // Auto-load default data
    $defaultFile = 'laravel.log';
    if (File::exists(storage_path('logs/'.$defaultFile))) {
        $this->loadLogFile($defaultFile);
    }
}
```

### 5️⃣ Textarea Word Wrap (Χωρίς Οριζόντιο Scroll)

**Για να κάνει wrap το κείμενο και να μην εμφανίζει οριζόντιο scrollbar:**

```blade
<textarea
    readonly
    style="
        white-space: pre-wrap;      /* wrap και διατηρεί format */
        word-break: break-word;     /* σπάει μεγάλες λέξεις */
        overflow-y: auto;            /* μόνο κάθετο scroll */
        overflow-x: hidden;          /* κρύβει οριζόντιο scroll */
    "
>{{ $this->logContent }}</textarea>
```

**Key Properties:**
- `white-space: pre-wrap` - Διατηρεί spaces/line breaks και κάνει wrap
- `word-break: break-word` - Σπάει μεγάλες λέξεις/URLs αν χρειάζεται
- `overflow-y: auto` - Μόνο κάθετο scrollbar
- `overflow-x: hidden` - Κρύβει οριζόντιο scrollbar

### 📋 Checklist για Custom Σελίδα

- ✅ **Χωρίς Forms** - Μην χρησιμοποιείς `HasForms` αν δεν χρειάζεται input
- ✅ **Public properties** - Κράτα δεδομένα και state σε public properties
- ✅ **Semantic CSS classes** - Χρήση CSS classes για styling
- ✅ **PHP Variables** - Χρήση PHP variables στο Blade για εύκολη παραμετροποίηση
- ✅ **CSS Assets** - Προσθήκη CSS στο Panel Provider με `Css::make()`
- ✅ **Fixed size/scrollable** - Χρήση fixed height με scroll για προβολή logs/tables
- ✅ **Livewire actions** - Χρήση `wire:click` για refresh, load, delete actions
- ✅ **Auto-load** - Φόρτωση default data στο `mount()`

### 🚫 Τι ΝΑ ΜΗΝ Κάνεις

- ❌ **Μην χρησιμοποιείς Forms** αν δεν χρειάζεται user input
- ❌ **Μην χρησιμοποιείς Tailwind utilities** για βασικό layout - χρησιμοποίησε CSS
- ❌ **Μην χρησιμοποιείς `@vite()`** στο Blade - χρησιμοποίησε Panel assets
- ❌ **Μην χρησιμοποιείς inline `<link>`** - χρησιμοποίησε `Css::make()`
- ❌ **Μην χρησιμοποιείς `white-space: pre`** αν θες word wrap - χρησιμοποίησε `pre-wrap`

### 💡 Γιατί Αυτή η Προσέγγιση

1. **Πλήρης έλεγχος** - Έχεις πλήρη έλεγχο στο HTML, CSS, και behavior
2. **Απλότητα** - Χωρίς Forms overhead, ο κώδικας είναι πιο απλός
3. **Παραμετροποίηση** - PHP variables στο Blade για εύκολη αλλαγή styling
4. **Performance** - Λιγότερο JavaScript overhead από Filament Forms
5. **Flexibility** - Μπορείς να φτιάξεις οποιοδήποτε UI pattern

---

## 11. Filament 4 Design Tokens & Global Variables

### 🎯 Στόχος

Να χρησιμοποιείς τις ίδιες τιμές (χρώματα, spacing, border-radius, typography) που χρησιμοποιεί το Filament 4, ώστε τα custom UI elements να έχουν συνεπή εμφάνιση με το υπόλοιπο admin panel.

### 🎨 1. Color Palette (Semantic Colors)

Το Filament 4 χρησιμοποιεί 6 semantic colors που αντιστοιχούν σε Tailwind palettes:

| Semantic Name | Tailwind Palette | Default Value | CSS Variable | Usage |
|---------------|------------------|---------------|--------------|-------|
| `primary` | amber | `#f59e0b` (amber-500) | `var(--color-primary-500)` | Κύριο UI color, buttons, links |
| `success` | green | `#10b981` (green-500) | `var(--color-success-500)` | Επιτυχία, θετικό feedback |
| `warning` | amber | `#f59e0b` (amber-500) | `var(--color-warning-500)` | Προειδοποίηση |
| `danger` | red | `#ef4444` (red-500) | `var(--color-danger-500)` | Σφάλμα, επικίνδυνο |
| `info` | blue | `#3b82f6` (blue-500) | `var(--color-info-500)` | Πληροφοριακό |
| `gray` | zinc | `#71717a` (zinc-500) | `var(--color-gray-500)` | Neutral, backgrounds, borders |

**Κάθε color έχει 11 shades: 50, 100, 200, 300, 400, 500, 600, 700, 800, 900, 950**

**Παράδειγμα χρήσης:**
```css
.custom-button {
    background: var(--color-primary-500);  /* amber-500 */
    color: var(--color-white);
    border-color: var(--color-primary-600); /* amber-600 για hover */
}

.custom-text {
    color: var(--color-gray-900);  /* Dark text */
}

.custom-border {
    border-color: var(--color-gray-300);  /* Light border */
}
```

**Direct Values (αν δεν υπάρχουν CSS variables):**
```css
/* Primary (amber) */
--primary-50: #fffbeb;
--primary-500: #f59e0b;
--primary-600: #d97706;
--primary-700: #b45309;

/* Success (green) */
--success-500: #10b981;
--success-600: #059669;

/* Danger (red) */
--danger-500: #ef4444;
--danger-600: #dc2626;

/* Gray (zinc) */
--gray-50: #fafafa;
--gray-100: #f4f4f5;
--gray-200: #e4e4e7;
--gray-300: #d4d4d8;
--gray-400: #a1a1aa;
--gray-500: #71717a;
--gray-600: #52525b;
--gray-700: #3f3f46;
--gray-800: #27272a;
--gray-900: #18181b;
--gray-950: #09090b;
```

### 📏 2. Spacing Scale (Tailwind Defaults)

Το Filament χρησιμοποιεί το Tailwind spacing scale για padding, margin, gap, width, height:

| Tailwind Class | Value | Rem | Pixels | Usage |
|----------------|-------|-----|--------|-------|
| `spacing-0` | `0` | `0` | `0px` | No spacing |
| `spacing-1` | `0.25rem` | `0.25rem` | `4px` | Very small spacing |
| `spacing-2` | `0.5rem` | `0.5rem` | `8px` | Small spacing |
| `spacing-3` | `0.75rem` | `0.75rem` | `12px` | Small-medium spacing |
| `spacing-4` | `1rem` | `1rem` | `16px` | **Default spacing** (πιο συχνό) |
| `spacing-5` | `1.25rem` | `1.25rem` | `20px` | Medium spacing |
| `spacing-6` | `1.5rem` | `1.5rem` | `24px` | Medium-large spacing |
| `spacing-8` | `2rem` | `2rem` | `32px` | Large spacing |
| `spacing-10` | `2.5rem` | `2.5rem` | `40px` | Very large spacing |
| `spacing-12` | `3rem` | `3rem` | `48px` | Extra large spacing |

**Παράδειγμα χρήσης:**
```css
.custom-container {
    padding: 1rem;        /* spacing-4 - default */
    margin: 1.5rem 0;     /* spacing-6 vertical */
    gap: 0.5rem;          /* spacing-2 */
}

.custom-section {
    padding: 1.5rem;      /* spacing-6 */
    margin-bottom: 2rem;  /* spacing-8 */
}
```

**CSS Variables (αν υπάρχουν):**
```css
.custom-element {
    padding: var(--spacing-4);  /* 1rem */
    margin: var(--spacing-6);   /* 1.5rem */
    gap: var(--spacing-2);      /* 0.5rem */
}
```

### 🟠 3. Border Radius Scale

Το Filament χρησιμοποιεί Tailwind border-radius values:

| Tailwind Class | Value | Rem | Pixels | Usage |
|----------------|-------|-----|--------|-------|
| `rounded-none` | `0` | `0` | `0px` | No radius |
| `rounded-sm` | `0.125rem` | `0.125rem` | `2px` | Very small radius |
| `rounded` | `0.25rem` | `0.25rem` | `4px` | Small radius |
| `rounded-md` | `0.375rem` | `0.375rem` | `6px` | **Default radius** (πιο συχνό) |
| `rounded-lg` | `0.5rem` | `0.5rem` | `8px` | Medium radius |
| `rounded-xl` | `0.75rem` | `0.75rem` | `12px` | Large radius |
| `rounded-2xl` | `1rem` | `1rem` | `16px` | Extra large radius |
| `rounded-full` | `9999px` | - | - | Full circle |

**Παράδειγμα χρήσης:**
```css
.custom-card {
    border-radius: 0.375rem;  /* rounded-md - default για cards */
}

.custom-button {
    border-radius: 0.5rem;    /* rounded-lg - για buttons */
}

.custom-badge {
    border-radius: 9999px;    /* rounded-full - για badges */
}
```

### 🅰️ 4. Typography

**Font Family:**
```css
/* Default Filament font stack */
font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, 
             "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", 
             sans-serif, "Apple Color Emoji", "Segoe UI Emoji", 
             "Segoe UI Symbol", "Noto Color Emoji";

/* Monospace για logs/code */
font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, 
             "Liberation Mono", "Courier New", monospace;
```

**Font Sizes (Tailwind scale):**
| Tailwind Class | Value | Rem | Pixels | Usage |
|----------------|-------|-----|--------|-------|
| `text-xs` | `0.75rem` | `0.75rem` | `12px` | Extra small text |
| `text-sm` | `0.875rem` | `0.875rem` | `14px` | **Small text** (πιο συχνό) |
| `text-base` | `1rem` | `1rem` | `16px` | **Base text** (default) |
| `text-lg` | `1.125rem` | `1.125rem` | `18px` | Large text |
| `text-xl` | `1.25rem` | `1.25rem` | `20px` | Extra large text |
| `text-2xl` | `1.5rem` | `1.5rem` | `24px` | 2X large text |

**Line Height:**
| Tailwind Class | Value | Usage |
|----------------|-------|-------|
| `leading-none` | `1` | Tight line height |
| `leading-tight` | `1.25` | Tight line height |
| `leading-snug` | `1.375` | Snug line height |
| `leading-normal` | `1.5` | **Default line height** |
| `leading-relaxed` | `1.625` | Relaxed line height |
| `leading-loose` | `2` | Loose line height |

**Font Weights:**
| Tailwind Class | Value | Usage |
|----------------|-------|-------|
| `font-normal` | `400` | Normal weight |
| `font-medium` | `500` | **Medium weight** (πιο συχνό) |
| `font-semibold` | `600` | Semi-bold |
| `font-bold` | `700` | Bold |

**Παράδειγμα χρήσης:**
```css
.custom-heading {
    font-size: 1.125rem;      /* text-lg */
    font-weight: 600;         /* font-semibold */
    line-height: 1.5;         /* leading-normal */
}

.custom-body {
    font-size: 0.875rem;      /* text-sm */
    line-height: 1.5;         /* leading-normal */
    color: var(--color-gray-900);
}
```

### 🌑 5. Dark Mode Colors

Το Filament υποστηρίζει dark mode. Χρησιμοποίησε CSS variables ή Tailwind dark: classes:

**Background Colors:**
```css
/* Light mode */
background: var(--color-white);        /* #ffffff */
background: var(--color-gray-50);     /* #fafafa */

/* Dark mode */
background: var(--color-gray-800);    /* #27272a */
background: var(--color-gray-900);    /* #18181b */
```

**Text Colors:**
```css
/* Light mode */
color: var(--color-gray-900);         /* #18181b */

/* Dark mode */
color: var(--color-gray-100);         /* #f4f4f5 */
color: var(--color-white);            /* #ffffff */
```

**Border Colors:**
```css
/* Light mode */
border-color: var(--color-gray-300);  /* #d4d4d8 */

/* Dark mode */
border-color: var(--color-gray-700);  /* #3f3f46 */
```

### 📋 6. Complete CSS Variables Reference

**Colors:**
```css
/* Primary (amber) */
--color-primary-50: #fffbeb;
--color-primary-100: #fef3c7;
--color-primary-200: #fde68a;
--color-primary-300: #fcd34d;
--color-primary-400: #fbbf24;
--color-primary-500: #f59e0b;  /* Default primary */
--color-primary-600: #d97706;
--color-primary-700: #b45309;
--color-primary-800: #92400e;
--color-primary-900: #78350f;
--color-primary-950: #451a03;

/* Success (green) */
--color-success-500: #10b981;
--color-success-600: #059669;

/* Danger (red) */
--color-danger-500: #ef4444;
--color-danger-600: #dc2626;

/* Gray (zinc) */
--color-gray-50: #fafafa;
--color-gray-100: #f4f4f5;
--color-gray-200: #e4e4e7;
--color-gray-300: #d4d4d8;
--color-gray-400: #a1a1aa;
--color-gray-500: #71717a;
--color-gray-600: #52525b;
--color-gray-700: #3f3f46;
--color-gray-800: #27272a;
--color-gray-900: #18181b;
--color-gray-950: #09090b;
```

**Spacing:**
```css
--spacing-0: 0;
--spacing-1: 0.25rem;   /* 4px */
--spacing-2: 0.5rem;    /* 8px */
--spacing-3: 0.75rem;   /* 12px */
--spacing-4: 1rem;      /* 16px - default */
--spacing-5: 1.25rem;   /* 20px */
--spacing-6: 1.5rem;    /* 24px */
--spacing-8: 2rem;      /* 32px */
--spacing-10: 2.5rem;   /* 40px */
--spacing-12: 3rem;     /* 48px */
```

**Border Radius:**
```css
--rounded-none: 0;
--rounded-sm: 0.125rem;   /* 2px */
--rounded: 0.25rem;       /* 4px */
--rounded-md: 0.375rem;   /* 6px - default */
--rounded-lg: 0.5rem;     /* 8px */
--rounded-xl: 0.75rem;    /* 12px */
--rounded-2xl: 1rem;      /* 16px */
--rounded-full: 9999px;
```

**Typography:**
```css
--font-size-xs: 0.75rem;     /* 12px */
--font-size-sm: 0.875rem;    /* 14px */
--font-size-base: 1rem;      /* 16px - default */
--font-size-lg: 1.125rem;    /* 18px */
--font-size-xl: 1.25rem;     /* 20px */
--font-size-2xl: 1.5rem;      /* 24px */

--line-height-none: 1;
--line-height-tight: 1.25;
--line-height-normal: 1.5;   /* default */
--line-height-relaxed: 1.625;
--line-height-loose: 2;

--font-weight-normal: 400;
--font-weight-medium: 500;
--font-weight-semibold: 600;
--font-weight-bold: 700;
```

### 💡 7. Practical Examples για Custom CSS

**Custom Card (Filament-style):**
```css
.custom-card {
    background: var(--color-white);
    border: 1px solid var(--color-gray-300);
    border-radius: var(--rounded-md);  /* 0.375rem */
    padding: var(--spacing-6);          /* 1.5rem */
    margin-bottom: var(--spacing-4);    /* 1rem */
}

/* Dark mode */
@media (prefers-color-scheme: dark) {
    .custom-card {
        background: var(--color-gray-800);
        border-color: var(--color-gray-700);
        color: var(--color-gray-100);
    }
}
```

**Custom Button (Filament-style):**
```css
.custom-button {
    background: var(--color-primary-500);  /* amber-500 */
    color: var(--color-white);
    border: none;
    border-radius: var(--rounded-lg);     /* 0.5rem */
    padding: var(--spacing-2) var(--spacing-4);  /* 0.5rem 1rem */
    font-size: var(--font-size-sm);       /* 0.875rem */
    font-weight: var(--font-weight-medium); /* 500 */
    cursor: pointer;
    transition: background-color 0.2s;
}

.custom-button:hover {
    background: var(--color-primary-600);  /* amber-600 */
}
```

**Custom Textarea (Filament-style):**
```css
.custom-textarea {
    width: 100%;
    padding: var(--spacing-3);           /* 0.75rem */
    border: 1px solid var(--color-gray-300);
    border-radius: var(--rounded-md);    /* 0.375rem */
    font-size: var(--font-size-sm);      /* 0.875rem */
    font-family: ui-sans-serif, system-ui;
    line-height: var(--line-height-normal); /* 1.5 */
    background: var(--color-white);
    color: var(--color-gray-900);
}

.custom-textarea:focus {
    border-color: var(--color-primary-500);
    outline: none;
    box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.1);  /* primary-500 with opacity */
}
```

**Custom Badge (Filament-style):**
```css
.custom-badge {
    display: inline-flex;
    align-items: center;
    padding: var(--spacing-1) var(--spacing-2);  /* 0.25rem 0.5rem */
    border-radius: var(--rounded-full);  /* 9999px */
    font-size: var(--font-size-xs);      /* 0.75rem */
    font-weight: var(--font-weight-medium); /* 500 */
    background: var(--color-primary-100);
    color: var(--color-primary-800);
}
```

### 📋 Quick Reference Table

| UI Aspect | Filament Default | CSS Value | Usage |
|-----------|------------------|-----------|-------|
| **Primary Color** | amber-500 | `#f59e0b` | Buttons, links, primary actions |
| **Success Color** | green-500 | `#10b981` | Success messages, positive feedback |
| **Danger Color** | red-500 | `#ef4444` | Errors, delete actions |
| **Text Color (light)** | gray-900 | `#18181b` | Default text in light mode |
| **Text Color (dark)** | gray-100 | `#f4f4f5` | Default text in dark mode |
| **Border Color (light)** | gray-300 | `#d4d4d8` | Default borders in light mode |
| **Border Color (dark)** | gray-700 | `#3f3f46` | Default borders in dark mode |
| **Background (light)** | white | `#ffffff` | Card/panel backgrounds |
| **Background (dark)** | gray-800 | `#27272a` | Card/panel backgrounds in dark mode |
| **Default Padding** | spacing-4 | `1rem` (16px) | Most containers |
| **Default Margin** | spacing-4 | `1rem` (16px) | Between elements |
| **Card Border Radius** | rounded-md | `0.375rem` (6px) | Cards, panels |
| **Button Border Radius** | rounded-lg | `0.5rem` (8px) | Buttons |
| **Default Font Size** | text-base | `1rem` (16px) | Body text |
| **Small Font Size** | text-sm | `0.875rem` (14px) | Secondary text |
| **Default Line Height** | leading-normal | `1.5` | Body text |

### ✅ Best Practices

1. **Χρησιμοποίησε CSS variables** όταν είναι διαθέσιμες για consistency
2. **Χρησιμοποίησε direct values** (rem/px) όταν δεν υπάρχουν variables
3. **Υποστηρίξε dark mode** με media queries ή CSS variables
4. **Κράτα spacing consistent** - χρησιμοποίησε spacing-4 (1rem) ως default
5. **Χρησιμοποίησε rounded-md** (0.375rem) για cards/panels
6. **Χρησιμοποίησε rounded-lg** (0.5rem) για buttons
7. **Χρησιμοποίησε text-sm** (0.875rem) για secondary text
8. **Χρησιμοποίησε text-base** (1rem) για primary text

---

## 12. PHP Variables για Παραμετροποίηση (Best Practice)

### 🎯 Στόχος

Να έχεις όλες τις UI παραμέτρους (colors, spacing, sizes) σε ένα σημείο (PHP variables) στο top του Blade template, ώστε να μπορείς να αλλάζεις styling εύκολα χωρίς να ψάχνεις σε όλο το template.

### ✅ Πάντα Βάζεις PHP Variables

**Για κάθε custom page, ορίζεις όλες τις παραμέτρους στο top του `@php` block:**

```blade
<x-filament-panels::page>
    @php
        // Global UI Variables - Customize all elements here
        
        // Card/Container Variables
        $cardBorderRadius = '0.5rem';
        $cardPadding = '1.5rem';
        $cardBorderColor = 'var(--color-gray-200, #e4e4e7)';
        $cardBackground = 'var(--filament-bg, #fff)';
        
        // List Item Variables
        $itemPadding = '1rem 1.25rem';
        $itemBorderRadius = '0.5rem';
        $itemBorderColor = 'var(--color-gray-300, #d4d4d8)';
        $itemHoverBorderColor = 'var(--color-primary-300, #fcd34d)';
        
        // Typography Variables
        $fontSizeSmall = '0.75rem';
        $fontSizeBase = '0.875rem';
        $fontSizeLarge = '1rem';
        $fontWeightNormal = '400';
        $fontWeightMedium = '500';
        $fontWeightSemibold = '600';
        
        // Color Variables
        $textColorPrimary = 'var(--color-gray-900, #18181b)';
        $textColorSecondary = 'var(--color-gray-600, #52525b)';
        $textColorMuted = 'var(--color-gray-500, #71717a)';
        
        // Header Variables
        $headerPadding = '1rem 1.25rem';
        $headerBackground = 'var(--color-gray-50, #fafafa)';
        $headerBorderColor = 'var(--color-gray-200, #e4e4e7)';
        
        // Button Variables
        $buttonSize = '2rem';
        $buttonBorderRadius = '0.375rem';
        
        // Component-specific variables (π.χ. textarea)
        $textareaHeight = '500px';
        $textareaPadding = '0.5rem 1rem';
        $textareaMargin = '1rem 0';
        $textareaBgColor = 'rgb(44 44 44)';
        $textareaTextColor = '#ffffff';
    @endphp
    
    <div class="custom-page">
        <!-- Χρησιμοποίησε τις variables με inline styles -->
        <div class="custom-card" style="
            background: {{ $cardBackground }};
            border-radius: {{ $cardBorderRadius }};
            padding: {{ $cardPadding }};
            border: 1px solid {{ $cardBorderColor }};
        ">
            <!-- Content -->
        </div>
    </div>
</x-filament-panels::page>
```

### 📋 Οργάνωση Variables

**Οργάνωσε τις variables σε λογικές ομάδες:**

1. **Global/Container Variables** - Για cards, panels, containers
2. **List/Item Variables** - Για list items, table rows
3. **Typography Variables** - Font sizes, weights, line heights
4. **Color Variables** - Text colors, backgrounds, borders
5. **Component Variables** - Για συγκεκριμένα components (buttons, inputs, textareas)

**Παράδειγμα οργανωμένου `@php` block:**
```blade
@php
    // ============================================
    // GLOBAL UI VARIABLES
    // ============================================
    // Card/Container
    $cardBorderRadius = '0.5rem';
    $cardPadding = '1.5rem';
    $cardBorderColor = 'var(--color-gray-200, #e4e4e7)';
    $cardBackground = 'var(--filament-bg, #fff)';
    
    // ============================================
    // LIST ITEM VARIABLES
    // ============================================
    $itemPadding = '1rem 1.25rem';
    $itemBorderRadius = '0.5rem';
    $itemBorderColor = 'var(--color-gray-300, #d4d4d8)';
    $itemHoverBorderColor = 'var(--color-primary-300, #fcd34d)';
    
    // ============================================
    // TYPOGRAPHY VARIABLES
    // ============================================
    $fontSizeSmall = '0.75rem';
    $fontSizeBase = '0.875rem';
    $fontSizeLarge = '1rem';
    $fontWeightNormal = '400';
    $fontWeightMedium = '500';
    $fontWeightSemibold = '600';
    
    // ============================================
    // COLOR VARIABLES
    // ============================================
    $textColorPrimary = 'var(--color-gray-900, #18181b)';
    $textColorSecondary = 'var(--color-gray-600, #52525b)';
    $textColorMuted = 'var(--color-gray-500, #71717a)';
    
    // ============================================
    // COMPONENT-SPECIFIC VARIABLES
    // ============================================
    // Textarea
    $textareaHeight = '500px';
    $textareaPadding = '0.5rem 1rem';
    $textareaMargin = '1rem 0';
    $textareaBgColor = 'rgb(44 44 44)';
    $textareaTextColor = '#ffffff';
    
    // Data
    $logFiles = $this->getLogFiles();
@endphp
```

### 💡 Πλεονεκτήματα

1. **Εύκολη παραμετροποίηση** - Όλες οι τιμές σε ένα σημείο
2. **Consistency** - Ίδιες τιμές σε όλα τα στοιχεία
3. **Maintainability** - Εύκολη αλλαγή styling
4. **Reusability** - Μπορείς να χρησιμοποιήσεις τις ίδιες variables σε πολλά στοιχεία
5. **Documentation** - Οι variables λειτουργούν ως documentation

### 📝 Παράδειγμα Χρήσης

**Blade Template:**
```blade
<div class="custom-card" style="
    background: {{ $cardBackground }};
    border-radius: {{ $cardBorderRadius }};
    padding: {{ $cardPadding }};
    border: 1px solid {{ $cardBorderColor }};
">
    <div class="custom-item" style="
        padding: {{ $itemPadding }};
        border-radius: {{ $itemBorderRadius }};
        border-color: {{ $itemBorderColor }};
    "
    onmouseover="this.style.borderColor='{{ $itemHoverBorderColor }}';"
    onmouseout="this.style.borderColor='{{ $itemBorderColor }}';"
    >
        <p style="
            font-size: {{ $fontSizeBase }};
            font-weight: {{ $fontWeightSemibold }};
            color: {{ $textColorPrimary }};
        ">
            Item Title
        </p>
        <p style="
            font-size: {{ $fontSizeSmall }};
            color: {{ $textColorSecondary }};
        ">
            Item Description
        </p>
    </div>
</div>
```

### ✅ Checklist

- ✅ **Ορίζεις όλες τις variables** στο top του `@php` block
- ✅ **Οργανώνεις σε ομάδες** (Global, Typography, Colors, Components)
- ✅ **Χρησιμοποιείς CSS variables** όπου είναι δυνατό (Filament design tokens)
- ✅ **Προσθέτεις fallback values** σε CSS variables (π.χ. `var(--color-gray-900, #18181b)`)
- ✅ **Χρησιμοποιείς inline styles** με PHP variables στο HTML
- ✅ **Κρατάς consistent naming** (π.χ. `$cardPadding`, `$itemPadding`)

### 🚫 Τι ΝΑ ΜΗΝ Κάνεις

- ❌ **Μην βάζεις hardcoded values** στο HTML - χρησιμοποίησε variables
- ❌ **Μην σκορπίζεις variables** σε διάφορα `@php` blocks - όλες στο top
- ❌ **Μην χρησιμοποιείς μόνο CSS classes** - συνδύασε με PHP variables για flexibility

---

## 12. Custom CSS Overrides για Third-Party Components

### 🎯 Στόχος

Να μπορείς να override-άρεις styles από third-party components (π.χ. FilePond στο FileUpload) χρησιμοποιώντας Filament semantic classes και scoped selectors.

### ⚠️ Known Limitations

**FileUpload Component Icon Sizes**: Το Filament 4 FileUpload component δεν έχει built-in method για icon size control. Το `imagePreviewHeight()` ελέγχει μόνο image preview height, όχι icon sizes. Αν χρειάζεσαι μικρότερα icons, χρησιμοποίησε CSS overrides.

### 📋 Best Practices για CSS Overrides

**1. Χρησιμοποίησε Filament Semantic Classes ως Base Selector**

```css
/* ✅ CORRECT - Scoped με Filament classes */
.fi-fo-field.fi-fo-field .fi-fo-file-upload .filepond--root {
    --filepond-icon-size: 1.25rem;
}

/* ❌ WRONG - Global selector, επηρεάζει όλα τα FilePond instances */
.filepond--root {
    --filepond-icon-size: 1.25rem;
}
```

**2. Higher Specificity με Duplicate Classes**

```css
/* ✅ CORRECT - Higher specificity */
.fi-fo-field.fi-fo-field .fi-fo-file-upload .filepond--file-icon-wrapper svg {
    width: 1.25rem;
    height: 1.25rem;
}

/* ❌ WRONG - Lower specificity, μπορεί να override-αριστεί */
.fi-fo-file-upload .filepond--file-icon-wrapper svg {
    width: 1.25rem;
}
```

**3. Inspect Element για Ακριβές HTML Structure**

**Βήματα:**
1. Κάνε inspect στο component στο browser
2. Βρες το Filament wrapper class (π.χ. `.fi-fo-field`, `.fi-section-content`)
3. Χρησιμοποίησε αυτό το class ως base selector
4. Προσθήκη third-party classes (π.χ. `.filepond--root`) για scoping

**Παράδειγμα HTML Structure:**
```html
<div class="fi-fo-field" data-field-wrapper="">
    <div class="fi-fo-field-content-col">
        <div class="fi-fo-file-upload">
            <div class="filepond--root">
                <!-- Third-party component content -->
            </div>
        </div>
    </div>
</div>
```

**CSS Selector:**
```css
.fi-fo-field.fi-fo-field .fi-fo-file-upload .filepond--root {
    /* Overrides here */
}
```

### 🔧 Complete Example - FileUpload Icon Size Override

**Problem**: FileUpload component displays excessively large icons.

**Solution**: CSS override με scoped selectors.

**Step 1: Create CSS File** (`resources/css/filament-fileupload.css`):

```css
/*
 * Filament FileUpload Customization
 * Scoped CSS selectors based on Filament 4 semantic classes.
 */

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

/* File item panel - horizontal layout με εικονίδια δίπλα στα κείμενα */
.fi-fo-field.fi-fo-field .fi-fo-file-upload .filepond--file {
    min-height: 60px;
    display: flex;
    flex-direction: row;
    align-items: center;
    gap: 0.75rem;
    padding: 0.5rem;
}

/* File icon wrapper - horizontal layout */
.fi-fo-field.fi-fo-field .fi-fo-file-upload .filepond--file-icon-wrapper {
    flex-shrink: 0;
    width: 2rem;
    height: 2rem;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* File info container - horizontal layout */
.fi-fo-field.fi-fo-field .fi-fo-file-upload .filepond--file-info {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
    font-size: 0.75rem;
    min-width: 0; /* Allows text truncation */
}

/* File name - truncate αν είναι μεγάλο */
.fi-fo-field.fi-fo-field .fi-fo-file-upload .filepond--file-info-main {
    font-weight: 500;
    color: var(--color-gray-900, #18181b);
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

/* File size - secondary text */
.fi-fo-field.fi-fo-field .fi-fo-file-upload .filepond--file-info-sub {
    font-size: 0.6875rem;
    color: var(--color-gray-600, #52525b);
}

/* File status - horizontal layout */
.fi-fo-field.fi-fo-field .fi-fo-file-upload .filepond--file-status {
    font-size: 0.75rem;
    flex-shrink: 0;
}

/* File actions - horizontal layout */
.fi-fo-field.fi-fo-field .fi-fo-file-upload .filepond--file-action-button {
    flex-shrink: 0;
    width: 1.5rem;
    height: 1.5rem;
}

/* Dark mode support */
@media (prefers-color-scheme: dark) {
    .fi-fo-field.fi-fo-field .fi-fo-file-upload .filepond--file-info-main {
        color: var(--color-gray-100, #f4f4f5);
    }
    
    .fi-fo-field.fi-fo-field .fi-fo-file-upload .filepond--file-info-sub {
        color: var(--color-gray-400, #a1a1aa);
    }
}
```

**Step 2: Add to Panel Config** (`app/Providers/Filament/AdminPanelProvider.php`):

```php
use Filament\Support\Assets\Css;

public function panel(Panel $panel): Panel
{
    return $panel
        ->assets([
            Css::make('fileupload-overrides', base_path('resources/css/filament-fileupload.css')),
        ]);
}
```

**Step 3: Compile Assets**:

```bash
php artisan filament:assets
```

**Step 4: Hard Refresh Browser** (Ctrl+Shift+R / Cmd+Shift+R)

### ⚠️ Troubleshooting

**Αν το CSS δεν "πιάνει":**

1. **Verify CSS is loaded**: Check browser Network tab - CSS file should be loaded
2. **Check specificity**: Use `.fi-fo-field.fi-fo-field` for higher specificity
3. **Inspect element**: Verify actual HTML structure matches your selectors
4. **Clear cache**: Hard refresh browser (Ctrl+Shift+R)
5. **Check for inline styles**: Third-party components may apply inline styles via JavaScript

**Αν ακόμα δεν δουλεύει:**

- Το component μπορεί να χρειάζεται JavaScript-based solution
- Μπορεί να χρειάζεται custom view component
- Μπορεί να χρειάζεται alternative component approach

### 📝 Checklist

- ✅ **Χρησιμοποίησε Filament semantic classes** ως base selector
- ✅ **Higher specificity** με duplicate classes (`.fi-fo-field.fi-fo-field`)
- ✅ **Scoped selectors** - επηρεάζουν μόνο το συγκεκριμένο component
- ✅ **Inspect element** για ακριβές HTML structure
- ✅ **Panel Asset** - φόρτωσε CSS ως Panel Asset, όχι inline
- ✅ **Compile assets** - τρέξε `php artisan filament:assets`
- ✅ **Hard refresh** - clear browser cache

### 🚫 Τι ΝΑ ΜΗΝ Κάνεις

- ❌ **Μην χρησιμοποιείς global selectors** (π.χ. `.filepond--root`) - χρησιμοποίησε scoped
- ❌ **Μην βασίζεσαι μόνο σε third-party classes** - συνδύασε με Filament classes
- ❌ **Μην χρησιμοποιείς inline styles** στο Blade - χρησιμοποίησε Panel Assets
- ❌ **Μην χρησιμοποιείς `!important`** - χρησιμοποίησε higher specificity
- ❌ **Μην ξεχνάς fallback values** σε CSS variables

---

## 13. SVG Icons Pattern - Filament-Native Style

### 🎯 Στόχος
Να χρησιμοποιείς SVG icons με **σωστή στοίχιση** και **σταθερό layout** που μοιάζει με Filament's internal components (hints, alerts, info sections).

### ⚠️ Common Problem

**❌ WRONG:**
```blade
<div class="flex items-start gap-2">
    <svg class="w-4 h-4 text-primary-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path ... />
    </svg>
    <div>Κείμενο</div>
</div>
```

**Προβλήματα:**
- SVG δεν έχει fixed container
- `items-start` + multiline text "τραβάει" το icon
- Όταν το κείμενο μεγαλώνει, το icon δείχνει πιο "ψηλό" απ' όσο πρέπει
- Δεν είναι consistent με Filament's internal styling

### ✅ CORRECT: Bulletproof Filament-Native Pattern (Custom CSS)

**⚠️ Critical Issues to Prevent:**
1. **Tailwind classes δεν "τρέχουν"** - τα utility classes που περιμένεις δεν εφαρμόζονται
2. **Global CSS rules** που κάνουν `svg { width: 100%; height: auto; }` ή `display: block;`
3. **Typography plugins** (π.χ. prose) που επηρεάζουν SVG μέσα σε content
4. **Flex layout breaking** από parent overrides

**🎯 Root Cause:**
Το πρόβλημα δεν είναι το markup (είναι σωστό). Το πρόβλημα είναι ότι τα Tailwind utility classes που περιμένεις δεν "υπάρχουν/τρέχουν", άρα δεν εφαρμόζονται, οπότε το SVG μένει "default huge" και το layout "default block".

**✅ Σωστή Λύση: Custom CSS Classes (Filament Way)**

Μην βασίζεσαι σε Tailwind classes για sizing/layout εδώ. Κλείδωσε το layout με **custom CSS classes** στο Panel Asset (όπως ήδη κάνεις για FileUpload).

**Blade Template:**
```blade
<div class="my-info-list">
    <div class="my-info-row">
        <span class="my-info-icon">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                {!! $folderIcon !!}
            </svg>
        </span>
        <div class="my-info-text">
            <strong>Organize with Folders:</strong>
            Create folders to organize your files by category, project, or date.
        </div>
    </div>
</div>
```

**CSS (resources/css/filament-fileupload.css):**
```css
/* Info list (About Media Library) */
.my-info-list {
    display: grid;
    gap: 0.75rem; /* 12px */
}

.my-info-row {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem; /* 12px */
}

.my-info-icon {
    flex: 0 0 auto;
    width: 20px;
    height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-top: 2px;
    color: rgb(99 102 241); /* περίπου primary-500 */
}

.my-info-icon svg {
    display: inline-block;
    width: 16px;
    height: 16px;
}

.my-info-text {
    font-size: 0.875rem;  /* 14px */
    line-height: 1.25rem; /* 20px */
    color: rgb(75 85 99); /* gray-600 */
}

.my-info-text strong {
    font-weight: 600;
    color: rgb(17 24 39); /* gray-900 */
}

/* Optional: dark mode */
.dark .my-info-text { color: rgb(156 163 175); } /* gray-400 */
.dark .my-info-text strong { color: white; }
```

**⚠️ ΣΗΜΑΝΤΙΚΟ: Explicit SVG Size**
Το `width: 16px; height: 16px;` στο `.my-info-icon svg` είναι **bulletproof** - αν υπάρχει global CSS που μεγαλώνει SVG, αυτό το κλείδωσε.

**Γιατί αυτό είναι το σωστό root fix:**
- ✅ Με `width="16" height="16"` attributes + `width: 16px; height: 16px;` στο CSS, το SVG δεν μπορεί να γίνει τεράστιο, ό,τι CSS κι αν υπάρχει
- ✅ Με `display: flex` στο CSS, το κείμενο δεν μπορεί να πέσει από κάτω
- ✅ Καθαρό & maintainable (Filament way)
- ✅ Δεν γεμίζεις inline styles
- ✅ Reusable classes για όλα τα info rows

**📋 Setup Steps:**

**1. Προσθήκη CSS στο Panel Asset:**

Στο `app/Providers/Filament/AdminPanelProvider.php`:
```php
use Filament\Support\Assets\Css;

public function panel(Panel $panel): Panel
{
    return $panel
        ->assets([
            Css::make('fileupload-overrides', resource_path('css/filament-fileupload.css')),
        ]);
}
```

**⚠️ ΣΗΜΑΝΤΙΚΟ:** Χρησιμοποίησε `resource_path()` όχι `base_path()` - το Filament περιμένει path από `resources/` directory.

**2. Compile Assets & Clear Cache:**
```bash
php artisan filament:assets
php artisan optimize:clear
```

**3. Hard Refresh Browser:**
- Windows/Linux: `Ctrl + F5` ή `Ctrl + Shift + R`
- Mac: `Cmd + Shift + R`

**4. Verify στο DevTools:**
- Inspect το `<div class="my-info-row">`
- Computed → `display` → πρέπει να γράφει `display: flex` ✅
- Αν γράφει `display: block` ❌, το CSS δεν φορτώνεται (ελέγξε asset path, cache, ή αν είσαι στο σωστό panel)

### 🎯 Τι κερδίζεις με αυτό

- ✔ SVG **πάντα 16px** (width/height attributes - bulletproof)
- ✔ **ΣΤΑΘΕΡΗ** στοίχιση αριστερά (CSS κλειδώνει το layout)
- ✔ Κείμενο δίπλα, όχι κάτω (`display: flex` στο CSS)
- ✔ Δεν επηρεάζεται από Tailwind classes που δεν "τρέχουν"
- ✔ Δεν επηρεάζεται από global CSS rules
- ✔ Works perfect με multiline text
- ✔ Bulletproof σε οποιοδήποτε CSS environment
- ✔ Καθαρό & maintainable (Filament way)

### 🧠 Γιατί δουλεύει αυτό (σημαντικό)

| Element | Ρόλος |
|---------|-------|
| `width="16" height="16"` | **Bulletproof** - δεν αλλάζει από CSS rules |
| `.my-info-icon svg { display: inline-block; }` | Prevents `display: block` από global CSS |
| `.my-info-row { display: flex; }` | Ensures horizontal layout, δεν "σπάει" |
| `.my-info-icon { flex: 0 0 auto; }` | Δεν αφήνει το icon να μικρύνει |
| `.my-info-icon { margin-top: 2px; }` | Subtle vertical alignment με text |
| Fixed `width: 20px; height: 20px;` | Σταθερό icon container |
| `gap: 0.75rem;` | Consistent spacing |

**Αυτό είναι bulletproof pattern που δουλεύει ακόμα και αν τα Tailwind classes δεν "τρέχουν".**

### 🧩 Alternative: Inline Styles (100% σίγουρο)

Αν θες 100% bulletproof χωρίς custom CSS:

```blade
<div style="display:flex; align-items:flex-start; gap:0.75rem;">
    <div style="flex:0 0 auto; width:20px; height:20px; display:flex; align-items:center; justify-content:center; margin-top:2px; color: var(--primary-500, #6366f1);">
        <svg
            width="16"
            height="16"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            style="display:inline-block;"
        >
            {!! $folderIcon !!}
        </svg>
    </div>

    <div style="font-size:0.875rem; line-height:1.25rem; color:#4b5563;">
        <strong style="font-weight:600; color:#111827;">Organize with Folders:</strong>
        Create folders to organize your files by category, project, or date.
    </div>
</div>
```

**Χρήση:**
- ✅ 100% σίγουρο - δεν επηρεάζεται από κανένα CSS
- ❌ Λιγότερο maintainable - inline styles
- ❌ Δεν είναι "Filament way"

**Συνιστάται:** Custom CSS classes (πιο maintainable, Filament way)

### 📝 Mini Checklist (για ΟΛΑ τα SVG στο admin)

**✔ SVG πάντα:**
- ✅ `width="16" height="16"` **attributes** στο SVG element (bulletproof)
- ✅ `width: 16px; height: 16px;` **στο CSS** για το `.my-info-icon svg` (extra bulletproof)
- ✅ Custom CSS classes για layout (Filament way) - **χρησιμοποίησε `resource_path()` στο AdminPanelProvider**
- ✅ `display: flex` στο CSS για horizontal layout
- ✅ `flex: 0 0 auto` στο icon wrapper (δεν μικρύνει)
- ✅ Fixed `width: 20px; height: 20px;` στο icon container
- ✅ `display: inline-block` στο SVG (prevents `display: block`)
- ✅ **Compile assets:** `php artisan filament:assets`
- ✅ **Clear cache:** `php artisan optimize:clear`
- ✅ **Hard refresh browser:** `Ctrl + F5`

**❌ ΠΟΤΕ:**
- ❌ SVG μόνο του (χωρίς wrapper)
- ❌ Χωρίς `width/height` attributes (θα επηρεαστεί από global CSS)
- ❌ Χωρίς `width: 16px; height: 16px;` στο CSS (αν υπάρχει global rule)
- ❌ Βασίζεσαι μόνο σε Tailwind classes (μπορεί να μην "τρέχουν")
- ❌ Χωρίς `display: flex` στο CSS (το layout θα "σπάσει")
- ❌ Χωρίς fixed icon container size (θα επηρεαστεί από content)
- ❌ `base_path()` αντί για `resource_path()` στο AdminPanelProvider

### 🔍 Complete Example (Custom CSS - Filament Way)

**Blade Template:**
```blade
@php
    // SVG Icons stored in PHP variables
    $folderIcon = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />';
    $imageIcon = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />';
    $variantIcon = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />';
@endphp

<div class="my-info-list">
    <div class="my-info-row">
        <span class="my-info-icon">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                {!! $folderIcon !!}
            </svg>
        </span>
        <div class="my-info-text">
            <strong>Organize with Folders:</strong>
            Create folders to organize your files by category, project, or date.
        </div>
    </div>
    
    <div class="my-info-row">
        <span class="my-info-icon">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                {!! $imageIcon !!}
            </svg>
        </span>
        <div class="my-info-text">
            <strong>Use in Content:</strong>
            Uploaded images can be used in content blocks, products, categories, and more.
        </div>
    </div>
    
    <div class="my-info-row">
        <span class="my-info-icon">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                {!! $variantIcon !!}
            </svg>
        </span>
        <div class="my-info-text">
            <strong>Automatic Variants:</strong>
            Images are automatically resized into thumbnails and different sizes for optimal performance.
        </div>
    </div>
</div>
```

**CSS (resources/css/filament-fileupload.css):**
```css
/* Info list (About Media Library) */
.my-info-list {
    display: grid;
    gap: 0.75rem; /* 12px */
}

.my-info-row {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem; /* 12px */
}

.my-info-icon {
    flex: 0 0 auto;
    width: 20px;
    height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-top: 2px;
    color: rgb(99 102 241); /* περίπου primary-500 */
}

.my-info-icon svg {
    display: inline-block;
    width: 16px;
    height: 16px;
}

.my-info-text {
    font-size: 0.875rem;  /* 14px */
    line-height: 1.25rem; /* 20px */
    color: rgb(75 85 99); /* gray-600 */
}

.my-info-text strong {
    font-weight: 600;
    color: rgb(17 24 39); /* gray-900 */
}

/* Optional: dark mode */
.dark .my-info-text { color: rgb(156 163 175); } /* gray-400 */
.dark .my-info-text strong { color: white; }
```

**⚠️ ΣΗΜΑΝΤΙΚΟ: Explicit SVG Size**
Το `width: 16px; height: 16px;` στο `.my-info-icon svg` είναι **bulletproof** - αν υπάρχει global CSS που μεγαλώνει SVG, αυτό το κλείδωσε.

### 🚫 Τι ΝΑ ΜΗΝ Κάνεις

- ❌ **Μην βάζεις SVG μόνο του** - πάντα μέσα σε wrapper
- ❌ **Μην ξεχνάς `width/height` attributes** - χωρίς αυτά, global CSS θα το αλλάξει
- ❌ **Μην ξεχνάς `width: 16px; height: 16px;` στο CSS** - extra bulletproof για global rules
- ❌ **Μην βασίζεσαι μόνο σε Tailwind classes** - μπορεί να μην "τρέχουν", χρησιμοποίησε custom CSS
- ❌ **Μην ξεχνάς `display: flex` στο CSS** - χωρίς αυτό, το layout θα "σπάσει"
- ❌ **Μην ξεχνάς fixed icon container size** - χωρίς αυτό, θα επηρεαστεί από content
- ❌ **Μην χρησιμοποιείς `display: block` στο SVG** - χρησιμοποίησε `inline-block`
- ❌ **Μην χρησιμοποιείς `base_path()`** - χρησιμοποίησε `resource_path()` στο AdminPanelProvider
- ❌ **Μην ξεχνάς να compile assets** - `php artisan filament:assets` μετά από κάθε CSS change
- ❌ **Μην ξεχνάς hard refresh** - `Ctrl + F5` για να δεις τις αλλαγές

### 🔧 Development & Extension Guide

#### 1. Προσθήκη Νέου Row

**Step 1:** Προσθήκη SVG icon στο `@php` block:
```blade
@php
    // Existing icons...
    $folderIcon = '<path ... />';
    $imageIcon = '<path ... />';
    
    // New icon
    $newIcon = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M..."/>';
@endphp
```

**Step 2:** Προσθήκη row στο template:
```blade
<div class="my-info-list">
    <!-- Existing rows... -->
    
    <!-- New row -->
    <div class="my-info-row">
        <span class="my-info-icon">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                {!! $newIcon !!}
            </svg>
        </span>
        <div class="my-info-text">
            <strong>New Feature:</strong>
            Description of the new feature goes here.
        </div>
    </div>
</div>
```

**Step 3:** Compile assets:
```bash
php artisan filament:assets
```

**✅ Done!** Δεν χρειάζεται CSS change - οι classes είναι reusable.

---

#### 2. Αλλαγή Icon Colors

**Option A: Global Color Change (όλα τα icons)**

Στο CSS (`resources/css/filament-fileupload.css`):
```css
.my-info-icon {
    /* Change from primary-500 to success-500 */
    color: rgb(16 185 129); /* green-500 */
}
```

**Option B: Specific Icon Color (μόνο ένα icon)**

**Step 1:** Προσθήκη custom class στο Blade:
```blade
<div class="my-info-row">
    <span class="my-info-icon my-info-icon-success">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            {!! $successIcon !!}
        </svg>
    </span>
    <div class="my-info-text">
        <strong>Success Message:</strong>
        This icon will be green.
    </div>
</div>
```

**Step 2:** Προσθήκη CSS:
```css
.my-info-icon-success {
    color: rgb(16 185 129); /* green-500 */
}
```

**Step 3:** Compile assets:
```bash
php artisan filament:assets
```

---

#### 3. Αλλαγή Text Styling

**Option A: Global Text Change**

Στο CSS:
```css
.my-info-text {
    font-size: 1rem; /* Change from 0.875rem to 1rem */
    line-height: 1.5rem; /* Change from 1.25rem to 1.5rem */
    color: rgb(55 65 81); /* Change from gray-600 to gray-700 */
}
```

**Option B: Specific Row Text**

**Step 1:** Προσθήκη custom class:
```blade
<div class="my-info-row">
    <span class="my-info-icon">...</span>
    <div class="my-info-text my-info-text-large">
        <strong>Large Text:</strong>
        This text will be larger.
    </div>
</div>
```

**Step 2:** Προσθήκη CSS:
```css
.my-info-text-large {
    font-size: 1rem; /* 16px */
    line-height: 1.5rem; /* 24px */
}
```

---

#### 4. Αλλαγή Spacing/Gap

**Global Gap Change:**

Στο CSS:
```css
.my-info-list {
    gap: 1rem; /* Change from 0.75rem (12px) to 1rem (16px) */
}

.my-info-row {
    gap: 1rem; /* Change from 0.75rem (12px) to 1rem (16px) */
}
```

**Row-Specific Gap:**

**Step 1:** Προσθήκη inline style (bulletproof):
```blade
<div class="my-info-row" style="gap: 1rem;">
    <!-- content -->
</div>
```

---

#### 5. Reusable Component (Advanced)

**Step 1:** Δημιουργία component (`resources/views/components/info-row.blade.php`):
```blade
@props(['icon', 'title', 'description', 'iconColor' => 'rgb(99 102 241)'])

<div class="my-info-row">
    <span class="my-info-icon" style="color: {{ $iconColor }};">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            {!! $icon !!}
        </svg>
    </span>
    <div class="my-info-text">
        <strong>{{ $title }}</strong>
        {{ $description }}
    </div>
</div>
```

**Step 2:** Χρήση στο template:
```blade
@php
    $folderIcon = '<path ... />';
@endphp

<div class="my-info-list">
    <x-info-row 
        :icon="$folderIcon"
        title="Organize with Folders:"
        description="Create folders to organize your files by category, project, or date."
        icon-color="rgb(99 102 241)"
    />
    
    <x-info-row 
        :icon="$imageIcon"
        title="Use in Content:"
        description="Uploaded images can be used in content blocks, products, categories, and more."
        icon-color="rgb(16 185 129)"
    />
</div>
```

**✅ Benefits:**
- DRY (Don't Repeat Yourself)
- Easy to maintain
- Consistent structure
- Parameterizable

---

#### 6. Επέκταση σε Άλλα Pages

**Step 1:** Αντιγραφή CSS classes στο `filament-fileupload.css` (ή δημιουργία νέου CSS file)

**Step 2:** Προσθήκη στο AdminPanelProvider (αν χρησιμοποιείς νέο file):
```php
->assets([
    Css::make('fileupload-overrides', resource_path('css/filament-fileupload.css')),
    Css::make('custom-info-list', resource_path('css/custom-info-list.css')), // New file
])
```

**Step 3:** Χρήση στο νέο page:
```blade
<!-- Same structure, same classes -->
<div class="my-info-list">
    <div class="my-info-row">
        <!-- ... -->
    </div>
</div>
```

**✅ Classes are reusable across all pages!**

---

#### 7. Troubleshooting

**Problem: CSS changes δεν εφαρμόζονται**

**Solution:**
1. Verify CSS file path στο AdminPanelProvider (`resource_path()`)
2. Compile assets: `php artisan filament:assets`
3. Clear cache: `php artisan optimize:clear`
4. Hard refresh browser: `Ctrl + F5`
5. Check DevTools → Network tab → verify CSS file loads
6. Check DevTools → Computed → verify `display: flex` applies

**Problem: Icon color δεν αλλάζει**

**Solution:**
1. Verify CSS specificity - χρησιμοποίησε `.my-info-icon { color: ... }` (not nested)
2. Check for dark mode overrides - verify `.dark .my-info-icon` rules
3. Use inline style for testing: `style="color: rgb(16 185 129);"`

**Problem: Layout "σπάει" (text κάτω από icon)**

**Solution:**
1. Verify `.my-info-row { display: flex; }` exists στο CSS
2. Check DevTools → Computed → `display` should be `flex`
3. Verify `flex: 0 0 auto;` στο `.my-info-icon`
4. Check for parent CSS overrides

**Problem: SVG size είναι μεγάλο**

**Solution:**
1. Verify `width="16" height="16"` attributes στο SVG
2. Verify `.my-info-icon svg { width: 16px; height: 16px; }` στο CSS
3. Check for global CSS rules: `svg { width: 100%; }` - το explicit CSS θα το override

---

#### 8. Best Practices για Maintenance

**✅ DO:**
- Κράτα όλα τα SVG icons στο `@php` block (centralized)
- Χρησιμοποίησε consistent naming: `$folderIcon`, `$imageIcon`, etc.
- Document custom CSS classes με comments
- Test σε light & dark mode
- Verify με DevTools μετά από κάθε change

**❌ DON'T:**
- Μην βάζεις inline styles εκτός αν είναι absolutely necessary
- Μην duplicate CSS classes - reuse existing
- Μην ξεχνάς να compile assets μετά από CSS changes
- Μην χρησιμοποιείς Tailwind classes για critical layout

---

#### 9. Quick Reference: Common Customizations

| Customization | CSS Change | Blade Change |
|---------------|------------|--------------|
| **Icon color (global)** | `.my-info-icon { color: ... }` | None |
| **Icon color (specific)** | `.my-info-icon-custom { color: ... }` | Add class to `<span>` |
| **Text size (global)** | `.my-info-text { font-size: ... }` | None |
| **Text size (specific)** | `.my-info-text-large { font-size: ... }` | Add class to `<div>` |
| **Gap (global)** | `.my-info-list { gap: ... }` | None |
| **Gap (row-specific)** | None | `style="gap: ..."` on row |
| **Add new row** | None | Copy existing row structure |
| **Dark mode color** | `.dark .my-info-text { color: ... }` | None |

---

## 🔍 How to Verify

## 🔍 How to Verify

1. **Check existing working code:**
   ```bash
   grep -r "use Filament\\Schemas" app/Filament/
   ```

2. **Check for old API usage:**
   ```bash
   grep -r "Filament\\Forms\\Form" app/Filament/
   grep -r "Filament\\Tables\\Actions" app/Filament/
   ```

3. **Run linter:**
   ```bash
   php artisan pint
   ```

---

## 📚 References

- Filament 4 Documentation: https://filamentphp.com/docs/4.x
- Sprint 4.3: Filament 4 Alignment (`project-docs/v2/sprints/sprint_4.3/`)

