# Sprint 4 — OpenCart-like Layout System (Regions + Modules + Rows)

**Status**: ⏳ Pending  
**Start Date**: _TBD_  
**End Date**: _TBD_  
**Διάρκεια**: 1 εβδομάδα

---

## 📋 Sprint Goal

Να μπορείς από το admin να:
- Επιλέγεις layout σε κάθε σελίδα
- Γεμίζεις regions (θέσεις) όπως στο OpenCart
- Βάζεις modules με σειρά, enable/disable
- Έχεις 3-level rows παντού
- Ορίζεις full width / contained / full-bg rows
- Χωρίς duplication views
- Χωρίς να σπάσεις το υπάρχον theming

**Μετά το Sprint 4**: 👉 «Φτιάχνω οποιαδήποτε σελίδα χωρίς να γράψω Blade»

---

## 🎯 High-Level Objectives

- Layout & Region system (OpenCart-like)
- Module instances (reusable, business-scoped)
- 3-level rows pattern (row → container → content)
- Width modes (contained, full, full-bg-contained-content)
- Page-level overrides + Theme defaults
- Admin UI για layout/module management
- Backward compatibility με legacy blocks

⚠️ **Δεν υλοποιείται ακόμα:**
- ❌ Theming tokens — Sprint 5
- ❌ Headless API — Sprint 6
- ❌ Plugins — Sprint 6
- ❌ Subdomain routing — Sprint 6+

---

## 🧠 Κεντρικές Αποφάσεις (LOCKED)

- ❌ **Όχι** page builder τύπου Elementor
- ✅ **OpenCart logic**: Layouts → Regions → Modules
- ✅ **Page-level override** + Theme defaults
- ✅ **3 επίπεδα row** παντού
- ✅ **Business-scoped** σε όλα

---

## 📊 Database Schema

### New Tables

#### `layouts` Table
```php
Schema::create('layouts', function (Blueprint $table) {
    $table->id();
    $table->foreignId('business_id')->constrained()->cascadeOnDelete();
    $table->string('name'); // 'Default', 'Full Width', 'Landing'
    $table->string('type')->default('default'); // 'default', 'full-width', 'landing'
    $table->json('regions'); // ['header_top', 'content_top', 'main_content', ...]
    $table->boolean('is_default')->default(false);
    $table->timestamps();
    
    $table->index('business_id');
    $table->index('type');
});
```

#### `module_instances` Table
```php
Schema::create('module_instances', function (Blueprint $table) {
    $table->id();
    $table->foreignId('business_id')->constrained()->cascadeOnDelete();
    $table->string('type'); // 'hero', 'rich_text', 'image', 'menu', 'gallery', etc.
    $table->string('name')->nullable(); // For reusable instances
    $table->json('settings'); // Module-specific settings
    $table->json('style')->nullable(); // Background, padding, etc.
    $table->enum('width_mode', ['contained', 'full', 'full-bg-contained-content'])->default('contained');
    $table->boolean('enabled')->default(true);
    $table->timestamps();
    
    $table->index('business_id');
    $table->index('type');
    $table->index('enabled');
});
```

#### `content_module_assignments` Table (Junction)
```php
Schema::create('content_module_assignments', function (Blueprint $table) {
    $table->id();
    $table->foreignId('content_id')->constrained()->cascadeOnDelete();
    $table->foreignId('module_instance_id')->constrained()->cascadeOnDelete();
    $table->string('region'); // 'header_top', 'content_top', 'main_content', etc.
    $table->integer('sort_order')->default(0);
    $table->timestamps();
    
    $table->unique(['content_id', 'module_instance_id', 'region'], 'unique_assignment');
    $table->index('content_id');
    $table->index('region');
    $table->index('sort_order');
});
```

### Modified Tables

#### `contents` Table — Add `layout_id`
```php
Schema::table('contents', function (Blueprint $table) {
    $table->foreignId('layout_id')->nullable()->after('type')->constrained('layouts')->nullOnDelete();
    $table->index('layout_id');
});
```

**Note**: `body_json` field remains for backward compatibility (legacy blocks).

---

## 🏗️ Service Layer Architecture

### New Services

1. **RenderLayoutService** — Renders layout with regions & modules
2. **GetLayoutService** — Loads layout with regions
3. **GetModulesForRegionService** — Loads modules per region
4. **CreateModuleInstanceService** — Creates reusable module instances
5. **UpdateModuleInstanceService** — Updates module instances
6. **AssignModuleToContentService** — Assigns modules to content regions
7. **GetThemeDefaultModulesService** — Loads theme default modules

### Enhanced Services

1. **RenderContentService** — Enhanced with dual mode:
   - If `layout_id` exists → use `RenderLayoutService`
   - If `layout_id` is NULL → render legacy `body_json` blocks

---

## 👥 Tasks by Developer

---

## Dev A — Backend/Infrastructure

### Task A1 — Database Migrations

**Περιγραφή**: Create migrations για layouts, module_instances, content_module_assignments, και add layout_id στο contents.

**Deliverables:**
- `v2_2024_XX_XX_000001_create_layouts_table.php`
- `v2_2024_XX_XX_000002_create_module_instances_table.php`
- `v2_2024_XX_XX_000003_create_content_module_assignments_table.php`
- `v2_2024_XX_XX_000004_add_layout_id_to_contents_table.php`

**Technical Details:**
- Foreign keys με `cascadeOnDelete()` για business isolation
- Indexes για performance (business_id, type, region, sort_order)
- Unique constraint στο junction table
- `layout_id` nullable για backward compatibility

**Acceptance Criteria:**
- [x] Migrations run without errors
- [x] Foreign keys & indexes correct
- [x] Unique constraints working
- [x] Backward compatibility maintained (layout_id nullable)

---

### Task A2 — Layout & Module Models

**Περιγραφή**: Create Eloquent models για Layout, ModuleInstance, ContentModuleAssignment.

**Deliverables:**
- `app/Domain/Layouts/Models/Layout.php`
- `app/Domain/Modules/Models/ModuleInstance.php`
- `app/Domain/Modules/Models/ContentModuleAssignment.php`

**Technical Details:**

#### Layout Model
```php
class Layout extends Model {
    protected $fillable = ['business_id', 'name', 'type', 'regions', 'is_default'];
    protected $casts = ['regions' => 'array', 'is_default' => 'boolean'];
    
    // Relationships
    public function business(): BelongsTo
    public function contents(): HasMany
    public function defaultModules(): HasMany // ModuleInstance where name is null
    
    // Scopes
    public function scopeForBusiness($query, int $businessId)
    public function scopeOfType($query, string $type)
    public function scopeDefault($query)
    
    // Helpers
    public function hasRegion(string $region): bool
    public function getRegions(): array
}
```

#### ModuleInstance Model
```php
class ModuleInstance extends Model {
    protected $fillable = ['business_id', 'type', 'name', 'settings', 'style', 'width_mode', 'enabled'];
    protected $casts = ['settings' => 'array', 'style' => 'array', 'enabled' => 'boolean'];
    
    // Relationships
    public function business(): BelongsTo
    public function assignments(): HasMany // ContentModuleAssignment
    
    // Scopes
    public function scopeForBusiness($query, int $businessId)
    public function scopeOfType($query, string $type)
    public function scopeEnabled($query)
    public function scopeReusable($query) // name is not null
    
    // Helpers
    public function isReusable(): bool
    public function getSetting(string $key, mixed $default = null): mixed
}
```

#### ContentModuleAssignment Model
```php
class ContentModuleAssignment extends Model {
    protected $fillable = ['content_id', 'module_instance_id', 'region', 'sort_order'];
    
    // Relationships
    public function content(): BelongsTo
    public function moduleInstance(): BelongsTo
    
    // Scopes
    public function scopeForContent($query, int $contentId)
    public function scopeForRegion($query, string $region)
    public function scopeOrdered($query)
}
```

**Acceptance Criteria:**
- [x] All relationships tested and correct
- [x] Scopes working
- [x] Casts working (JSON → array)
- [x] Helper methods functional

---

### Task A3 — Layout & Module Services (Core)

**Περιγραφή**: Create services για layout & module management.

**Deliverables:**
- `app/Domain/Layouts/Services/GetLayoutService.php`
- `app/Domain/Layouts/Services/CreateLayoutService.php`
- `app/Domain/Modules/Services/GetModulesForRegionService.php`
- `app/Domain/Modules/Services/CreateModuleInstanceService.php`
- `app/Domain/Modules/Services/UpdateModuleInstanceService.php`
- `app/Domain/Modules/Services/AssignModuleToContentService.php`

**Technical Details:**

#### GetLayoutService
```php
class GetLayoutService {
    public function forBusiness(int $businessId, ?int $layoutId = null): ?Layout
    public function defaultForBusiness(int $businessId): ?Layout
    public function withRegions(int $layoutId): Layout
}
```

#### GetModulesForRegionService
```php
class GetModulesForRegionService {
    public function forContentRegion(Content $content, string $region): Collection
    // Returns: Collection of ModuleInstance ordered by sort_order
    // Logic:
    // 1. Load assignments for content + region
    // 2. Eager load module instances
    // 3. Filter enabled modules
    // 4. Order by sort_order
}
```

#### CreateModuleInstanceService
```php
class CreateModuleInstanceService {
    public function create(array $data): ModuleInstance
    // Validates:
    // - business_id exists
    // - type is in allowed modules list (config/modules.php)
    // - settings match module schema
    // - width_mode is valid enum
}
```

#### AssignModuleToContentService
```php
class AssignModuleToContentService {
    public function assign(Content $content, ModuleInstance $module, string $region, int $sortOrder = 0): ContentModuleAssignment
    // Validates:
    // - Content has layout
    // - Layout has region
    // - Module belongs to same business
    // - No duplicate assignment
}
```

**Acceptance Criteria:**
- [x] All services use `declare(strict_types=1);`
- [x] Type hints & return types everywhere
- [x] Constructor injection for dependencies
- [x] Proper validation & error handling
- [x] Business isolation enforced

---

### Task A4 — RenderLayoutService (Core Rendering)

**Περιγραφή**: Service που render-άρει layout με regions & modules.

**Deliverables:**
- `app/Domain/Layouts/Services/RenderLayoutService.php`

**Technical Details:**

```php
class RenderLayoutService {
    public function __construct(
        private GetLayoutService $getLayoutService,
        private GetModulesForRegionService $getModulesService,
        private RenderModuleService $renderModuleService
    ) {}
    
    public function render(Content $content): string
    {
        // 1. Get layout (from content or default)
        $layout = $this->getLayout($content);
        
        // 2. Get regions
        $regions = $layout->getRegions();
        
        // 3. Render each region
        $renderedRegions = [];
        foreach ($regions as $region) {
            $modules = $this->getModulesService->forContentRegion($content, $region);
            $renderedRegions[$region] = $this->renderRegion($modules, $region);
        }
        
        // 4. Return rendered HTML
        return $this->buildLayoutHtml($layout, $renderedRegions);
    }
    
    private function renderRegion(Collection $modules, string $region): string
    {
        $html = [];
        foreach ($modules as $module) {
            $html[] = $this->renderModuleService->render($module);
        }
        return implode("\n", $html);
    }
}
```

**Integration with RenderContentService:**
```php
// Enhanced RenderContentService
public function render(Content $content): string {
    // New mode: Layout-based
    if ($content->layout_id) {
        return app(RenderLayoutService::class)->render($content);
    }
    
    // Legacy mode: Sequential blocks
    return $this->renderLegacyBlocks($content);
}
```

**Acceptance Criteria:**
- [x] Renders layout with all regions
- [x] Loads modules per region correctly
- [x] Respects sort_order
- [x] Only renders enabled modules
- [x] Handles missing regions gracefully
- [x] Backward compatible (legacy blocks still work)

---

### Task A5 — Module Registry Configuration

**Περιγραφή**: Create configuration file για module registry.

**Deliverables:**
- `config/modules.php`

**Technical Details:**

```php
return [
    'hero' => [
        'name' => 'Hero',
        'icon' => 'hero',
        'category' => 'content',
        'settings_form' => \App\Http\Requests\Modules\HeroModuleRequest::class,
        'view' => 'modules.hero',
        'description' => 'Hero section with title, subtitle, image, and CTA',
    ],
    'rich_text' => [
        'name' => 'Rich Text',
        'icon' => 'text',
        'category' => 'content',
        'settings_form' => \App\Http\Requests\Modules\RichTextModuleRequest::class,
        'view' => 'modules.rich-text',
        'description' => 'WYSIWYG content block',
    ],
    'image' => [
        'name' => 'Image',
        'icon' => 'image',
        'category' => 'media',
        'settings_form' => \App\Http\Requests\Modules\ImageModuleRequest::class,
        'view' => 'modules.image',
        'description' => 'Single image with optional caption',
    ],
    'banner' => [
        'name' => 'Banner',
        'icon' => 'banner',
        'category' => 'marketing',
        'settings_form' => \App\Http\Requests\Modules\BannerModuleRequest::class,
        'view' => 'modules.banner',
        'description' => 'Promotional banner',
    ],
    'menu' => [
        'name' => 'Menu',
        'icon' => 'menu',
        'category' => 'catalog',
        'settings_form' => \App\Http\Requests\Modules\MenuModuleRequest::class,
        'view' => 'modules.menu',
        'description' => 'Product menu/catalog',
    ],
    'products_grid' => [
        'name' => 'Products Grid',
        'icon' => 'grid',
        'category' => 'catalog',
        'settings_form' => \App\Http\Requests\Modules\ProductsGridModuleRequest::class,
        'view' => 'modules.products-grid',
        'description' => 'Grid of products',
    ],
    'categories_list' => [
        'name' => 'Categories List',
        'icon' => 'list',
        'category' => 'catalog',
        'settings_form' => \App\Http\Requests\Modules\CategoriesListModuleRequest::class,
        'view' => 'modules.categories-list',
        'description' => 'List of categories',
    ],
    'gallery' => [
        'name' => 'Gallery',
        'icon' => 'gallery',
        'category' => 'media',
        'settings_form' => \App\Http\Requests\Modules\GalleryModuleRequest::class,
        'view' => 'modules.gallery',
        'description' => 'Image gallery',
    ],
    'cta' => [
        'name' => 'Call to Action',
        'icon' => 'cta',
        'category' => 'marketing',
        'settings_form' => \App\Http\Requests\Modules\CtaModuleRequest::class,
        'view' => 'modules.cta',
        'description' => 'Call to action button/link',
    ],
    'map' => [
        'name' => 'Map',
        'icon' => 'map',
        'category' => 'contact',
        'settings_form' => \App\Http\Requests\Modules\MapModuleRequest::class,
        'view' => 'modules.map',
        'description' => 'Google Maps embed',
    ],
    'opening_hours' => [
        'name' => 'Opening Hours',
        'icon' => 'clock',
        'category' => 'contact',
        'settings_form' => \App\Http\Requests\Modules\OpeningHoursModuleRequest::class,
        'view' => 'modules.opening-hours',
        'description' => 'Business hours display',
    ],
    'contact_card' => [
        'name' => 'Contact Card',
        'icon' => 'contact',
        'category' => 'contact',
        'settings_form' => \App\Http\Requests\Modules\ContactCardModuleRequest::class,
        'view' => 'modules.contact-card',
        'description' => 'Contact information card',
    ],
    'faq' => [
        'name' => 'FAQ',
        'icon' => 'faq',
        'category' => 'content',
        'settings_form' => \App\Http\Requests\Modules\FaqModuleRequest::class,
        'view' => 'modules.faq',
        'description' => 'Frequently asked questions',
    ],
    'testimonials' => [
        'name' => 'Testimonials',
        'icon' => 'testimonials',
        'category' => 'marketing',
        'settings_form' => \App\Http\Requests\Modules\TestimonialsModuleRequest::class,
        'view' => 'modules.testimonials',
        'description' => 'Customer testimonials',
    ],
];
```

**Acceptance Criteria:**
- [x] All v1 modules defined
- [x] Each module has: name, icon, category, settings_form, view
- [x] Configuration is extensible

---

## Dev B — Architecture/Domain

### Task B1 — RenderModuleService (3-Level Rows)

**Περιγραφή**: Service που render-άρει module με 3-level row pattern.

**Deliverables:**
- `app/Domain/Modules/Services/RenderModuleService.php`
- `resources/views/components/module-row.blade.php`

**Technical Details:**

#### RenderModuleService
```php
class RenderModuleService {
    public function __construct(
        private GetModuleViewService $getModuleViewService
    ) {}
    
    public function render(ModuleInstance $module): string
    {
        // 1. Get module view
        $viewPath = $this->getModuleViewService->getViewPath($module->type);
        
        // 2. Get module settings
        $settings = $module->settings;
        
        // 3. Render module content (inner content)
        $moduleContent = View::make($viewPath, [
            'module' => $module,
            'settings' => $settings,
        ])->render();
        
        // 4. Wrap with module-row component (3-level pattern)
        return View::make('components.module-row', [
            'module' => $module,
            'widthMode' => $module->width_mode,
            'style' => $module->style ?? [],
        ])->with('slot', $moduleContent)->render();
    }
}
```

#### module-row.blade.php Component
```blade
@props(['module', 'widthMode' => 'contained', 'style' => []])

@php
    $background = $style['background'] ?? null;
    $backgroundImage = $style['background_image'] ?? null;
    $padding = $style['padding'] ?? null;
@endphp

<div class="module-row" 
     data-module-type="{{ $module->type }}"
     data-width-mode="{{ $widthMode }}"
     @if($background) style="background-color: {{ $background }};" @endif
     @if($backgroundImage) style="background-image: url('{{ $backgroundImage }}'); background-size: cover; background-position: center;" @endif
     @if($padding) style="padding: {{ $padding }};" @endif>
    
    @if($widthMode === 'full-bg-contained-content')
        {{-- Full background, contained content --}}
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            {{ $slot }}
        </div>
    @elseif($widthMode === 'full')
        {{-- Full width, no container --}}
        {{ $slot }}
    @else
        {{-- Contained (default) --}}
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            {{ $slot }}
        </div>
    @endif
</div>
```

**Acceptance Criteria:**
- [x] 3-level pattern works: row → container → content
- [x] Width modes work correctly
- [x] Background styles applied
- [x] Responsive (container has padding)

---

### Task B2 — GetModuleViewService (Theme Resolution)

**Περιγραφή**: Service που resolve-άρει module view path με theme fallback.

**Deliverables:**
- `app/Domain/Modules/Services/GetModuleViewService.php`

**Technical Details:**

```php
class GetModuleViewService {
    public function getViewPath(string $moduleType, ?Business $business = null): string
    {
        // 1. Get theme from business
        $theme = $business ? $business->getTheme() : 'default';
        
        // 2. Try theme-specific view
        $themeViewPath = "themes.{$theme}.modules.{$moduleType}";
        if (View::exists($themeViewPath)) {
            return $themeViewPath;
        }
        
        // 3. Fallback to default theme
        $defaultViewPath = "themes.default.modules.{$moduleType}";
        if (View::exists($defaultViewPath)) {
            return $defaultViewPath;
        }
        
        // 4. Fallback to generic module view
        return "modules.{$moduleType}";
    }
}
```

**Acceptance Criteria:**
- [x] Theme resolution works
- [x] Fallback chain works
- [x] Handles missing views gracefully

---

### Task B3 — Module View Structure

**Περιγραφή**: Create module views structure (themes/default/modules/).

**Deliverables:**
- `resources/views/themes/default/modules/hero.blade.php`
- `resources/views/themes/default/modules/rich-text.blade.php`
- `resources/views/themes/default/modules/image.blade.php`
- `resources/views/themes/default/modules/gallery.blade.php`
- (And other v1 modules)

**Technical Details:**

#### Module View Pattern
```blade
{{-- modules/hero.blade.php --}}
@php
    $title = $settings['title'] ?? '';
    $subtitle = $settings['subtitle'] ?? '';
    $imageId = $settings['image_id'] ?? null;
    $ctaText = $settings['cta_text'] ?? null;
    $ctaLink = $settings['cta_link'] ?? null;
    
    $image = $imageId ? \App\Domain\Media\Models\Media::find($imageId) : null;
@endphp

<div class="hero-module">
    @if($image)
        <img src="{{ $image->large_url ?? $image->url }}" alt="{{ $title }}" class="hero-image">
    @endif
    <div class="hero-content">
        <h1>{{ $title }}</h1>
        @if($subtitle)
            <p>{{ $subtitle }}</p>
        @endif
        @if($ctaText && $ctaLink)
            <a href="{{ $ctaLink }}" class="cta-button">{{ $ctaText }}</a>
        @endif
    </div>
</div>
```

**Note**: Module views receive `$module` and `$settings` variables. They don't need to handle row/container (handled by module-row component).

**Acceptance Criteria:**
- [x] All v1 module views created
- [x] Views use `$settings` from module instance
- [x] Views load media from Media Library
- [x] Views are responsive

---

### Task B4 — Layout View Structure

**Περιγραφή**: Create layout views που render-άρουν regions.

**Deliverables:**
- `resources/views/themes/default/layouts/default.blade.php`
- `resources/views/themes/default/layouts/full-width.blade.php`
- `resources/views/themes/default/layouts/landing.blade.php`

**Technical Details:**

#### Default Layout View
```blade
{{-- layouts/default.blade.php --}}
@extends('layouts.public')

@section('content')
    {{-- Header Top Region --}}
    @if(isset($regions['header_top']))
        <div class="layout-region header-top">
            {!! $regions['header_top'] !!}
        </div>
    @endif
    
    {{-- Header Bottom Region --}}
    @if(isset($regions['header_bottom']))
        <div class="layout-region header-bottom">
            {!! $regions['header_bottom'] !!}
        </div>
    @endif
    
    <div class="layout-main">
        <div class="layout-container">
            {{-- Content Top Region --}}
            @if(isset($regions['content_top']))
                <div class="layout-region content-top">
                    {!! $regions['content_top'] !!}
                </div>
            @endif
            
            <div class="layout-content-wrapper">
                {{-- Column Left (optional) --}}
                @if(isset($regions['column_left']))
                    <aside class="layout-sidebar layout-sidebar-left">
                        {!! $regions['column_left'] !!}
                    </aside>
                @endif
                
                {{-- Main Content --}}
                <main class="layout-main-content">
                    {!! $regions['main_content'] ?? '' !!}
                </main>
                
                {{-- Column Right (optional) --}}
                @if(isset($regions['column_right']))
                    <aside class="layout-sidebar layout-sidebar-right">
                        {!! $regions['column_right'] !!}
                    </aside>
                @endif
            </div>
            
            {{-- Content Bottom Region --}}
            @if(isset($regions['content_bottom']))
                <div class="layout-region content-bottom">
                    {!! $regions['content_bottom'] !!}
                </div>
            @endif
        </div>
    </div>
    
    {{-- Footer Top Region --}}
    @if(isset($regions['footer_top']))
        <div class="layout-region footer-top">
            {!! $regions['footer_top'] !!}
        </div>
    @endif
@endsection
```

**Acceptance Criteria:**
- [x] All layout types created
- [x] Regions render correctly
- [x] Responsive layout structure
- [x] Handles missing regions gracefully

---

### Task B5 — GetThemeDefaultModulesService

**Περιγραφή**: Service που load-άρει theme default modules.

**Deliverables:**
- `app/Domain/Themes/Services/GetThemeDefaultModulesService.php`
- `resources/themes/{preset}/default-modules.json` (optional, ή database)

**Technical Details:**

```php
class GetThemeDefaultModulesService {
    public function getDefaultsForTheme(string $theme, int $businessId): array
    {
        // Option 1: Load from JSON file
        $jsonPath = resource_path("themes/{$theme}/default-modules.json");
        if (file_exists($jsonPath)) {
            $defaults = json_decode(file_get_contents($jsonPath), true);
            return $this->createModuleInstances($defaults, $businessId);
        }
        
        // Option 2: Load from database (theme_default_modules table)
        // (Future implementation)
        
        // Option 3: Return empty array
        return [];
    }
    
    private function createModuleInstances(array $defaults, int $businessId): array
    {
        // Create module instances from defaults
        // Used when page has no overrides
    }
}
```

**Acceptance Criteria:**
- [x] Loads theme defaults
- [x] Creates module instances if needed
- [x] Fallback to empty array if no defaults

---

## Dev C — Frontend/UI

### Task C1 — Module Settings Forms (Form Requests)

**Περιγραφή**: Create Form Request classes για κάθε module type.

**Deliverables:**
- `app/Http/Requests/Modules/HeroModuleRequest.php`
- `app/Http/Requests/Modules/RichTextModuleRequest.php`
- `app/Http/Requests/Modules/ImageModuleRequest.php`
- (And other v1 modules)

**Technical Details:**

#### HeroModuleRequest
```php
class HeroModuleRequest extends FormRequest {
    public function rules(): array {
        return [
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:500',
            'image_id' => 'nullable|exists:media,id',
            'cta_text' => 'nullable|string|max:100',
            'cta_link' => 'nullable|url|max:500',
        ];
    }
}
```

**Acceptance Criteria:**
- [x] All module form requests created
- [x] Validation rules match module settings
- [x] Business isolation enforced

---

### Task C2 — Admin UI: Layout Selection (Filament)

**Περιγραφή**: Add layout selection στο Content editor.

**Deliverables:**
- Update `app/Filament/Resources/ContentResource.php`
- Add layout dropdown field

**Technical Details:**

```php
// In ContentResource form
Select::make('layout_id')
    ->label('Layout')
    ->relationship('layout', 'name')
    ->getOptionLabelFromRecordUsing(fn (Layout $record) => "{$record->name} ({$record->type})")
    ->searchable()
    ->preload()
    ->helperText('Select layout for this page. If not set, legacy blocks will be used.')
    ->afterStateUpdated(function ($state, callable $set) {
        // Clear body_json if layout is selected
        if ($state) {
            $set('body_json', null);
        }
    })
```

**Acceptance Criteria:**
- [x] Layout dropdown in content form
- [x] Shows layout name and type
- [x] Searchable & preloaded
- [x] Helper text explains behavior

---

### Task C3 — Admin UI: Region → Modules Management (Blade)

**Περιγραφή**: Create Blade page για region → modules management.

**Deliverables:**
- `resources/views/admin/content/modules.blade.php`
- `app/Http/Controllers/Admin/ContentModuleController.php`

**Technical Details:**

#### ContentModuleController
```php
class ContentModuleController extends Controller {
    public function index(Content $content) {
        $layout = $content->layout;
        $regions = $layout ? $layout->getRegions() : [];
        
        // Load modules per region
        $modulesByRegion = [];
        foreach ($regions as $region) {
            $modulesByRegion[$region] = GetModulesForRegionService::forContentRegion($content, $region);
        }
        
        return view('admin.content.modules', [
            'content' => $content,
            'layout' => $layout,
            'regions' => $regions,
            'modulesByRegion' => $modulesByRegion,
        ]);
    }
    
    public function addModule(Request $request, Content $content) {
        // Add module to region
    }
    
    public function reorder(Request $request, Content $content) {
        // Reorder modules in region
    }
    
    public function toggle(Request $request, ContentModuleAssignment $assignment) {
        // Enable/disable module
    }
}
```

#### modules.blade.php View
```blade
{{-- Admin UI για region → modules management --}}
{{-- Similar to OpenCart layout editor --}}
{{-- Drag & drop για reorder --}}
{{-- Enable/disable toggles --}}
{{-- Add module button per region --}}
```

**Acceptance Criteria:**
- [x] Shows all regions
- [x] Lists modules per region
- [x] Drag & drop reorder (Alpine.js)
- [x] Enable/disable toggles
- [x] Add module button per region
- [x] Edit module settings link

---

### Task C4 — Admin UI: Module Instance CRUD (Filament)

**Περιγραφή**: Create Filament resource για ModuleInstance (reusable modules).

**Deliverables:**
- `app/Filament/Resources/ModuleInstanceResource.php`

**Technical Details:**

```php
class ModuleInstanceResource extends Resource {
    // List page: Show reusable modules
    // Create page: Select module type, fill settings
    // Edit page: Edit settings, style, width mode
    // Delete: Only if not assigned to any content
}
```

**Acceptance Criteria:**
- [x] CRUD for module instances
- [x] Module type selection
- [x] Dynamic form based on module type
- [x] Style & width mode settings
- [x] Reusable toggle (name field)

---

### Task C5 — Module Row Component Styling

**Περιγραφή**: Style το module-row component.

**Deliverables:**
- Update `resources/views/components/module-row.blade.php` with TailwindCSS

**Technical Details:**
- Responsive container
- Background image support
- Padding/margin controls
- Full-width vs contained styling

**Acceptance Criteria:**
- [x] Responsive design
- [x] All width modes styled correctly
- [x] Background images work
- [x] Consistent spacing

---

## 📦 Deliverables (Definition of Done)

### Backend
- [x] Database migrations (layouts, module_instances, assignments)
- [x] Models (Layout, ModuleInstance, ContentModuleAssignment)
- [x] Services (RenderLayoutService, GetModulesForRegionService, etc.)
- [x] Module registry configuration
- [x] Backward compatibility (legacy blocks still work)

### Frontend
- [x] Module views (all v1 modules)
- [x] Layout views (default, full-width, landing)
- [x] Module-row component (3-level pattern)
- [x] Theme resolution & fallback

### Admin UI
- [x] Layout selection in content editor
- [x] Region → modules management page
- [x] Module instance CRUD
- [x] Drag & drop reorder
- [x] Enable/disable toggles

### Testing
- [x] Layout rendering works
- [x] Modules render in correct regions
- [x] Width modes work
- [x] Theme defaults work
- [x] Backward compatibility verified

---

## 🧠 Τι ΔΕΝ Κάνει το Sprint 4

- ❌ Theming tokens — Sprint 5
- ❌ Headless API — Sprint 6
- ❌ Plugins — Sprint 6
- ❌ Subdomain routing — Sprint 6+

---

## 📝 Sprint Notes

_Καταγράψε εδώ progress, decisions, issues_

---

## 📚 References

- [v2 Overview](../v2_overview.md) — Architecture & strategy
- [Content Model](../v2_content_model.md)
- [Developer Responsibilities](../dev-responsibilities.md) ⭐

---

**Last Updated**: 2024-11-27

