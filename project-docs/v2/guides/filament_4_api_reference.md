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

## 10. Custom Pages (Χωρίς Forms) - Complete Guide

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

### 4️⃣ Interactivity & Behavior

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
- ❌ **Μην ξεχνάς fallback values** σε CSS variables

---

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

