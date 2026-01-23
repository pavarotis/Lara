# 🎯 CURSOR IDE PROMPT - Laravel + Filament v4 Dynamic Site Generator

## 📋 Complete System Description

Δημιούργησε ένα **Laravel 11 + Filament v4 Dynamic Site Generator** όπου **ΟΛΟ** το site (config, theme, features, behavior) καθορίζεται από τον πίνακα `variables`. Ο πίνακας `variables` είναι η **μοναδική πηγή αλήθειας**.

---

## 🏗️ ΦΑΣΗ 1️⃣ – Variables ως Configuration Layer

### Requirements:
- Φόρτωσε όλα τα records από τον πίνακα `variables` (key, value, type, category, business_id)
- Κάνε cast στο value ανά type:
  - `string` → string
  - `number` → int|float
  - `boolean` → bool
  - `json` → array/object
- Δημιούργησε service layer: `VariableService` που:
  - Φορτώνει variables με caching (1 hour TTL)
  - Provides `get(string $key, mixed $default)` method
  - Provides `getAllVariables()` method
  - Provides `getByCategory(string $category)` method
  - Auto-clears cache on updates

### Deliverables:
- ✅ `app/Domain/Variables/Services/VariableService.php`
- ✅ Helper function: `variable(string $key, mixed $default)` in `app/Support/VariableHelper.php`

### Result:
```php
// Anywhere in code
$siteName = variable('site_name', 'My Store');
$perPage = variable('catalog_products_per_page', 12);
```

**Κανόνας**: Χωρίς hardcoded config files. Όλα από βάση.

---

## 🎨 ΦΑΣΗ 2️⃣ – Dynamic Site Configuration

### Requirements:
Χρησιμοποίησε τα variables για να ορίσεις:

**Site Identity:**
- `site_name` (string)
- `site_description` (string)
- `contact_email` (string)
- `currency` (string)

**Behavior:**
- `items_per_page` (number)
- `catalog_products_per_page` (number)
- Feature flags (boolean): `blog_enabled`, `catalog_wishlist_enabled`, etc.

**Theme:**
- `primary_color` (string)
- `theme_colors` (json): `{"primary": "#3b82f6", "secondary": "#8b5cf6"}`
- `cms_header_variant` (string)
- `cms_footer_variant` (string)

### Deliverables:
- ✅ `ThemeService` για CSS generation από JSON colors
- ✅ `getSiteConfig()` method στο VariableService
- ✅ Middleware `InjectVariables` που injects `$siteConfig` και `$variables` σε όλα τα views

### Result:
```blade
{{ $siteConfig['site_name'] }}
{{ variable('blog_enabled') ? 'Blog Enabled' : 'Blog Disabled' }}
```

**Κανόνας**: Νέο variable → auto διαθέσιμο στο site χωρίς code change.

---

## ⚙️ ΦΑΣΗ 3️⃣ – Filament SettingsPage (Admin Control)

### Requirements:
Δημιούργησε Filament Custom Page: `DynamicSettings`

**Structure:**
- Full-page Livewire component
- Φορτώνει όλα τα variables από βάση
- Ομαδοποιεί UI ανά `category` (Tabs)
- Dynamic form fields ανά `type`:
  - `string` → `TextInput`
  - `number` → `TextInput` (numeric)
  - `boolean` → `Toggle`
  - `json` → `Textarea` (monospace font)

**Dynamic Field Generation:**
```php
protected function createFieldForVariable(Variable $variable) {
    return match ($variable->type) {
        'string' => TextInput::make($variable->key)...,
        'number' => TextInput::make($variable->key)->numeric()...,
        'boolean' => Toggle::make($variable->key)...,
        'json' => Textarea::make($variable->key)...,
    };
}
```

**Category Icons:**
- `general` → `heroicon-o-cog-6-tooth`
- `catalog` → `heroicon-o-shopping-bag`
- `blog` → `heroicon-o-document-text`
- etc. (12 categories total)

### Deliverables:
- ✅ `app/Filament/Pages/CMS/DynamicSettings.php`
- ✅ `resources/views/filament/pages/cms/dynamic-settings.blade.php`

### Result:
- `/admin/dynamic-settings` → Shows all variables in tabs
- New variable in DB → Auto appears in form
- New category → Auto new tab

**Κανόνας**: Η σελίδα δεν γνωρίζει συγκεκριμένα keys. Διαβάζει μόνο τη δομή.

---

## 💾 ΦΑΣΗ 4️⃣ – Save Mechanism

### Requirements:
- Save method ενημερώνει **ΜΑΖΙΚΑ** όλα τα variables
- Κάθε field ενημερώνει το αντίστοιχο record στη βάση
- Type casting πριν save:
  - `number` → string
  - `boolean` → '1' or '0'
  - `json` → json_encode()
- Auto cache clearing μετά save:
  - Clear individual variable cache
  - Clear all variables cache
  - Clear theme CSS cache

### Deliverables:
- ✅ `save()` method στο DynamicSettings
- ✅ Cache clearing integration με VariableService & ThemeService

### Result:
```php
public function save(): void {
    $data = $this->form->getState();
    foreach ($data as $key => $value) {
        $variable = Variable::where('key', $key)->first();
        $variable->update(['value' => $castedValue]);
    }
    // Clear all caches
}
```

**Κανόνας**: Ένα Save → όλο το site αλλάζει behavior.

---

## 🎨 ΦΑΣΗ 5️⃣ – Blade View

### Requirements:
Η Blade view περιέχει **ΜΟΝΟ**:
- `{{ $this->form }}` (Filament auto-renders)
- Ένα κουμπί Save → `wire:click="save"`

### Deliverables:
- ✅ `resources/views/filament/pages/cms/dynamic-settings.blade.php`

### Template:
```blade
<x-filament-panels::page>
    <div class="space-y-6">
        {{ $this->form }}
        <div class="flex justify-end">
            <x-filament::button wire:click="save">
                Save All Settings
            </x-filament::button>
        </div>
    </div>
</x-filament-panels::page>
```

**Κανόνας**: Καμία λογική στο Blade. Όλα από Livewire + Filament.

---

## 🚀 ΦΑΣΗ 6️⃣ – Dynamic Site Behavior

### Requirements:
Κάθε φορά που αλλάζει variable:

**Theme Colors:**
- JSON colors → CSS variables
- `ThemeService` generates CSS:
  ```css
  :root {
      --color-primary: #3b82f6;
      --color-primary-dark: #2563eb;
      --color-primary-light: #60a5fa;
  }
  ```
- Blade component: `<x-dynamic-theme />` injects CSS

**Feature Flags:**
- `blog_enabled = false` → Blog section disappears
- `catalog_wishlist_enabled = true` → Wishlist button appears

**Runtime Updates:**
- Cache clears on save
- Next page load → new values active

### Deliverables:
- ✅ `app/Domain/Variables/Services/ThemeService.php`
- ✅ `app/View/Components/DynamicTheme.php`
- ✅ `resources/views/components/dynamic-theme.blade.php`
- ✅ Middleware `InjectVariables` (injects to all views)

### Result:
```blade
@if(variable('blog_enabled'))
    <div>Blog Content</div>
@endif

<div style="background: var(--color-primary);">
    Themed Content
</div>
```

**Κανόνας**: Theme colors & features → auto apply στο UI.

---

## 📈 ΦΑΣΗ 7️⃣ – Scalability Rules

### Requirements:

**Auto-Discovery:**
- Νέο variable στη βάση → auto εμφανίζεται στο SettingsPage
- Νέο category → auto νέο tab
- Νέος type (future) → προσθήκη renderer στο `createFieldForVariable()`, όχι αλλαγή core

**Extensibility:**
- Προσθήκη νέου helper: `site_config()` → returns structured config
- Προσθήκη category icons: Update `getCategoryIcon()` method
- Προσθήκη field types: Update `createFieldForVariable()` match expression

### Deliverables:
- ✅ Extensible architecture
- ✅ No hardcoded variable lists
- ✅ Dynamic form generation

### Result:
```php
// Add new variable type
protected function createFieldForVariable(Variable $variable) {
    return match ($variable->type) {
        'color' => ColorPicker::make($variable->key)..., // NEW
        // existing types...
    };
}
```

**Κανόνας**: Το σύστημα να ΜΗΝ χρειάζεται refactor όσο μεγαλώνει.

---

## 📦 ΦΑΣΗ 8️⃣ – Deliverables

### Database:
- ✅ Migration: `create_variables_table` (id, business_id, key, value, type, category, description, timestamps)
- ✅ Migration: `add_category_to_variables_table` (adds category column)
- ✅ Seeder: `CompleteVariablesSeeder` (85+ variables in 12 categories)

### Models:
- ✅ `app/Domain/Variables/Models/Variable.php`
  - Fillable: business_id, key, value, type, category, description
  - Scopes: `forBusiness()`
  - Methods: `getTypedValue()`, `setTypedValue()`

### Services:
- ✅ `app/Domain/Variables/Services/VariableService.php`
  - Methods: `get()`, `getAllVariables()`, `getByCategory()`, `getSiteConfig()`, `clearCache()`
- ✅ `app/Domain/Variables/Services/ThemeService.php`
  - Methods: `generateCssVariables()`, `getCssStyleTag()`, `clearCache()`

### Helpers:
- ✅ `app/Support/VariableHelper.php`
  - Functions: `variable()`, `site_config()`, `theme_css()`

### Middleware:
- ✅ `app/Http/Middleware/InjectVariables.php`
  - Injects `$siteConfig` and `$variables` to all views

### Filament Pages:
- ✅ `app/Filament/Pages/CMS/DynamicSettings.php`
  - Dynamic form generation
  - Category-based tabs
  - Save with cache clearing

### Views:
- ✅ `resources/views/filament/pages/cms/dynamic-settings.blade.php`
- ✅ `resources/views/components/dynamic-theme.blade.php`
- ✅ `app/View/Components/DynamicTheme.php`

### Config:
- ✅ `config/variables.php` (defaults, cache settings, category icons)

### Registration:
- ✅ `app/Providers/AppServiceProvider.php` (register services, load helpers)
- ✅ `bootstrap/app.php` (register middleware)

---

## 🎯 ΤΕΛΙΚΟΣ ΣΤΟΧΟΣ

**Ένα site όπου:**
- ✅ **config** → από `variables` table
- ✅ **theme** → από JSON colors → CSS variables
- ✅ **features** → από boolean flags
- ✅ **behavior** → από number/string settings

**Ελέγχονται 100% από Filament** (`/admin/dynamic-settings`) **χωρίς αλλαγή κώδικα**.

---

## 📊 System Architecture

```
┌─────────────────────────────────────────┐
│         Database (variables)            │
│  ┌───────────────────────────────────┐  │
│  │ key | value | type | category    │  │
│  └───────────────────────────────────┘  │
└─────────────────────────────────────────┘
              │
              ▼
┌─────────────────────────────────────────┐
│      VariableService (Cached)           │
│  - get(key, default)                    │
│  - getAllVariables()                    │
│  - getSiteConfig()                      │
└─────────────────────────────────────────┘
              │
        ┌─────┴─────┐
        ▼           ▼
┌──────────────┐  ┌──────────────────┐
│ ThemeService │  │ InjectVariables   │
│ (CSS Gen)    │  │ Middleware        │
└──────────────┘  └──────────────────┘
        │                 │
        ▼                 ▼
┌──────────────┐  ┌──────────────────┐
│ Blade Views  │  │ DynamicSettings  │
│ (Frontend)   │  │ (Admin Panel)    │
└──────────────┘  └──────────────────┘
```

---

## ✅ Acceptance Criteria

1. ✅ Όλα τα settings από βάση (zero hardcoding)
2. ✅ Dynamic form generation (no hardcoded fields)
3. ✅ Auto-discovery (new variables → auto appear)
4. ✅ Theme system (JSON → CSS variables)
5. ✅ Cache management (auto clear on save)
6. ✅ Scalable architecture (extend without refactor)
7. ✅ Full Filament v4 compatibility
8. ✅ Laravel 11 compatible

---

## 🚀 Quick Start

```bash
# 1. Run migrations
php artisan migrate

# 2. Seed variables
php artisan db:seed --class=CompleteVariablesSeeder

# 3. Access admin
/admin/dynamic-settings

# 4. Use in code
variable('site_name')
site_config()
theme_css()
```

---

**Version**: 1.0.0  
**Laravel**: 11.x  
**Filament**: 4.x  
**Status**: ✅ Production Ready
