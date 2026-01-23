# Dynamic Site Generator - Complete Documentation

## 🎯 Overview

Ένα πλήρως δυναμικό σύστημα που χρησιμοποιεί μεταβλητές από τη βάση δεδομένων για να καθορίζει όλες τις ρυθμίσεις του site, τα themes, και τα features - **χωρίς να χρειάζεται να αγγίξεις κώδικα**.

## 📁 Αρχεία που Δημιουργήθηκαν

### Services
1. **`app/Domain/Variables/Services/VariableService.php`**
   - Φόρτωση και caching μεταβλητών
   - Type casting (string, number, boolean, json)
   - Site configuration builder
   - Cache management

2. **`app/Domain/Variables/Services/ThemeService.php`**
   - CSS generation από theme colors
   - Dynamic CSS variables
   - Color manipulation (darken/lighten)

### Helpers & Components
3. **`app/Support/VariableHelper.php`**
   - Global helper functions: `variable()`, `site_config()`, `theme_css()`

4. **`app/View/Components/DynamicTheme.php`**
   - Blade component για dynamic theme CSS

5. **`resources/views/components/dynamic-theme.blade.php`**
   - Blade view για theme CSS injection

### Middleware
6. **`app/Http/Middleware/InjectVariables.php`**
   - Injects `$siteConfig` και `$variables` σε όλα τα views

### Configuration
7. **`config/variables.php`**
   - Default values
   - Cache settings
   - Category icons

### Layout Example
8. **`resources/views/layouts/app.blade.php`**
   - Example layout που χρησιμοποιεί dynamic variables

## 🚀 Εγκατάσταση

### 1. Register Services

Το `AppServiceProvider` έχει ήδη register τα services. Αν δεν υπάρχει, προσθέστε:

```php
// app/Providers/AppServiceProvider.php
public function register(): void
{
    $this->app->singleton(\App\Domain\Variables\Services\VariableService::class);
    $this->app->singleton(\App\Domain\Variables\Services\ThemeService::class);
}

public function boot(): void
{
    require_once base_path('app/Support/VariableHelper.php');
}
```

### 2. Middleware Registration

Το middleware έχει ήδη προστεθεί στο `bootstrap/app.php`. Ελέγξτε ότι υπάρχει:

```php
$middleware->web(append: [
    \App\Http\Middleware\InjectVariables::class,
]);
```

### 3. Run Migrations & Seeders

```bash
php artisan migrate
php artisan db:seed --class=DynamicVariablesSeeder
```

## 💻 Χρήση

### Στο Blade Templates

#### 1. Χρήση Helper Functions

```blade
{{-- Get single variable --}}
<h1>{{ variable('site_name', 'Default Name') }}</h1>

{{-- Get site config --}}
@php
    $config = site_config();
@endphp
<p>Currency: {{ $config['currency'] }}</p>

{{-- Inject theme CSS --}}
{!! theme_css() !!}
```

#### 2. Χρήση Shared Variables

Το middleware injects `$siteConfig` και `$variables` σε όλα τα views:

```blade
{{-- Site name --}}
<title>{{ $siteConfig['site_name'] ?? 'My Store' }}</title>

{{-- Theme colors --}}
<div style="background-color: var(--color-primary);">
    Primary Color Content
</div>

{{-- Social links --}}
@if(isset($siteConfig['social']['facebook']))
    <a href="{{ $siteConfig['social']['facebook'] }}">Facebook</a>
@endif
```

#### 3. Dynamic Theme Component

```blade
{{-- In your layout head --}}
<x-dynamic-theme />
```

Αυτό θα inject το CSS με τα theme colors ως CSS variables.

### Στο PHP Code

```php
use App\Domain\Variables\Services\VariableService;

// Get service
$variableService = app(VariableService::class);

// Get single variable
$siteName = $variableService->get('site_name', 'Default');

// Get all variables
$allVariables = $variableService->getAllVariables();

// Get site config
$config = $variableService->getSiteConfig();

// Get by category
$appearanceVars = $variableService->getByCategory('appearance');
```

## 🎨 Theme System

### CSS Variables

Το `ThemeService` δημιουργεί CSS variables από JSON colors:

```json
{
  "primary": "#3b82f6",
  "secondary": "#8b5cf6",
  "accent": "#10b981"
}
```

Γίνεται:

```css
:root {
    --color-primary: #3b82f6;
    --color-primary-dark: #2563eb;
    --color-primary-light: #60a5fa;
    --color-secondary: #8b5cf6;
    --color-accent: #10b981;
}
```

### Χρήση στο Tailwind

Μπορείς να χρησιμοποιήσεις τα CSS variables στο Tailwind:

```blade
<div class="bg-[var(--color-primary)] text-white">
    Primary Color Background
</div>
```

## 🔄 Workflow

### 1. Προσθήκη Νέας Μεταβλητής

```php
Variable::create([
    'business_id' => $business->id,
    'key' => 'new_feature_enabled',
    'value' => '1',
    'type' => 'boolean',
    'category' => 'general',
    'description' => 'Enable New Feature',
]);
```

### 2. Χρήση στο Site

```blade
@if(variable('new_feature_enabled'))
    <div>New Feature Content</div>
@endif
```

### 3. Ενημέρωση από Admin

- Πήγαινε στο `/admin/dynamic-settings`
- Επεξεργάσου την τιμή
- Save → Cache clears → Site updates automatically

## 📊 Supported Types

### String
```php
'type' => 'string'
// Value: "My Site Name"
// Usage: variable('site_name')
```

### Number
```php
'type' => 'number'
// Value: "12"
// Returns: 12 (int) or 12.5 (float)
// Usage: variable('items_per_page')
```

### Boolean
```php
'type' => 'boolean'
// Value: "1" or "0"
// Returns: true or false
// Usage: variable('enable_feature')
```

### JSON
```php
'type' => 'json'
// Value: '{"key": "value"}'
// Returns: ['key' => 'value']
// Usage: variable('theme_colors')['primary']
```

## 🎯 Site Configuration Structure

Το `getSiteConfig()` επιστρέφει:

```php
[
    'site_name' => 'My Store',
    'items_per_page' => 12,
    'contact_email' => 'contact@example.com',
    'currency' => 'EUR',
    'theme' => [
        'primary' => '#3b82f6',
        'secondary' => '#8b5cf6',
        'accent' => '#10b981',
    ],
    'seo' => [
        'meta_description' => '...',
        'google_analytics_id' => '...',
        'keywords' => [...],
    ],
    'social' => [
        'facebook' => '...',
        'twitter' => '...',
    ],
]
```

## 🚀 Performance

### Caching

- Όλες οι μεταβλητές cache-άρονται για 1 ώρα
- Cache clears αυτόματα όταν save στο DynamicSettings
- Individual variable cache keys για optimal performance

### Cache Keys

```
variables:all:{business_id}
variable:{business_id}:{key}
theme:css:{business_id}
```

## 🔧 Extending

### Προσθήκη Νέου Type

1. Update `VariableService::castValue()`:

```php
protected function castValue(Variable $variable): mixed
{
    return match ($variable->type) {
        'color' => $this->parseColor($variable->value), // New type
        // ...
    };
}
```

2. Update `DynamicSettings::createFieldForVariable()`:

```php
protected function createFieldForVariable(Variable $variable)
{
    return match ($variable->type) {
        'color' => ColorPicker::make($variable->key)... // New field
        // ...
    };
}
```

### Προσθήκη Νέας Category

Απλά προσθέστε μεταβλητή με νέα category:

```php
Variable::create([
    'category' => 'new_category',
    // ...
]);
```

Θα εμφανιστεί αυτόματα ως νέο tab!

## 📝 Examples

### Example 1: Dynamic Site Name

```blade
<title>{{ $siteConfig['site_name'] ?? 'My Store' }}</title>
```

### Example 2: Conditional Feature

```blade
@if(variable('enable_maintenance', false))
    <div class="maintenance-mode">Site is under maintenance</div>
@endif
```

### Example 3: Theme Colors

```blade
<div style="background: var(--color-primary); color: white;">
    Themed Content
</div>
```

### Example 4: Social Links Loop

```blade
@if(isset($siteConfig['social']))
    @foreach($siteConfig['social'] as $platform => $url)
        <a href="{{ $url }}" target="_blank">{{ ucfirst($platform) }}</a>
    @endforeach
@endif
```

## ✅ Checklist

- [x] VariableService για loading & caching
- [x] ThemeService για CSS generation
- [x] Helper functions (variable, site_config, theme_css)
- [x] Middleware για view injection
- [x] Blade components
- [x] DynamicSettings με cache clearing
- [x] Config file
- [x] Example layout
- [x] Documentation

## 🎉 Result

**Ένα πλήρως δυναμικό site όπου:**
- ✅ Όλες οι ρυθμίσεις από τη βάση
- ✅ Themes από JSON colors
- ✅ Auto-update όταν αλλάζουν μεταβλητές
- ✅ Scalable & Extensible
- ✅ Zero hardcoding
- ✅ Performance optimized με caching

---

**Created**: 2026-01-23  
**Version**: 1.0.0  
**Laravel**: 11.x  
**Filament**: 4.x
