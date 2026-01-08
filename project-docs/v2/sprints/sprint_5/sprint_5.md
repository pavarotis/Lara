# Sprint 5 — Theming 2.0 + Information Pages + Media Integration

**Status**: ⏳ Pending  
**Start Date**: _TBD_  
**End Date**: _TBD_  
**Διάρκεια**: 1 εβδομάδα  
**Filament Version**: Χρησιμοποιούμε μόνο **Filament 4.x** στο admin panel (δεν επιτρέπονται Filament v2/v3 packages ή APIs).

---

## 📋 Sprint Goal

Να μπορείς χωρίς κώδικα να:
- Αλλάζεις εμφάνιση ολόκληρου site (χρώματα, fonts, spacing)
- Επιλέγεις header / footer variants
- Έχεις Information Pages (About, Terms κλπ) που κουμπώνουν σε layout
- Χρησιμοποιείς Media Library παντού
- Έχεις draft → publish → revisions
- Είσαι SEO-ready

**Μετά το Sprint 5**: 👉 «Το CMS είναι πλήρως διαχειρίσιμο από admin και οπτικά παραμετροποιήσιμο»

---

## 🎯 High-Level Objectives

- Design tokens system (JSON-based)
- Theme presets (Cafe, Restaurant, Retail)
- Header/Footer variants
- Information Pages system
- Media Library integration (παντού)
- Enhanced SEO (meta tags, sitemap, JSON-LD)
- Publishing workflow (draft, publish, revisions)

⚠️ **Δεν υλοποιείται ακόμα:**
- ❌ Headless API — Sprint 6
- ❌ Plugins — Sprint 6
- ❌ Multi-domain routing — Sprint 6+

---

## 🧠 Κεντρικές Αποφάσεις (LOCKED)

- ❌ **Όχι** νέα layouts (χρησιμοποιούμε Sprint 4)
- ❌ **Όχι** page builder
- ✅ **Design tokens** (JSON)
- ✅ **Variants** (header/footer)
- ✅ **Presets** (Cafe / Restaurant / Retail)
- ✅ **Information Pages** = κανονικά Content Pages

---

## 📊 Database Schema

### New Tables

#### `theme_presets` Table
```php
Schema::create('theme_presets', function (Blueprint $table) {
    $table->id();
    $table->string('slug')->unique(); // 'cafe', 'restaurant', 'retail'
    $table->string('name'); // 'Cafe', 'Restaurant', 'Retail'
    $table->json('tokens'); // Design tokens JSON
    $table->json('default_modules'); // Default modules per region
    $table->string('default_header_variant')->default('minimal');
    $table->string('default_footer_variant')->default('simple');
    $table->boolean('is_active')->default(true);
    $table->timestamps();
});
```

#### `theme_tokens` Table (Business Overrides)
```php
Schema::create('theme_tokens', function (Blueprint $table) {
    $table->id();
    $table->foreignId('business_id')->unique()->constrained()->cascadeOnDelete();
    $table->string('preset_slug'); // Reference to theme_presets
    $table->json('token_overrides')->nullable(); // Partial token overrides
    $table->string('header_variant')->default('minimal');
    $table->string('footer_variant')->default('simple');
    $table->timestamps();
    
    $table->index('business_id');
    $table->index('preset_slug');
});
```

### Modified Tables

#### `businesses` Table — Add theme fields (optional, ή use theme_tokens)
```php
// Option: Add to existing businesses.settings JSON
// Or: Use separate theme_tokens table (recommended)
```

---

## 🏗️ Service Layer Architecture

### New Services

1. **GetThemeTokensService** — Loads tokens (preset + business overrides)
2. **ApplyThemeTokensService** — Applies tokens to views
3. **GenerateThemeCssService** — Generates CSS variables from tokens
4. **GetThemePresetService** — Loads theme preset
5. **GetHeaderVariantService** — Loads header variant
6. **GetFooterVariantService** — Loads footer variant
7. **GetSitemapService** — Generates sitemap per business
8. **GenerateJsonLdService** — Generates JSON-LD structured data

### Enhanced Services

1. **RenderContentService** — Enhanced with SEO meta tags
2. **PublishContentService** — Enhanced with audit log

---

## 👥 Tasks by Developer

---

## Dev A — Backend/Infrastructure

### Task A1 — Theme Presets & Tokens Database

**Περιγραφή**: Create migrations για theme_presets και theme_tokens.

**Deliverables:**
- `v2_2024_XX_XX_000001_create_theme_presets_table.php`
- `v2_2024_XX_XX_000002_create_theme_tokens_table.php`
- Seeders για 3 presets (Cafe, Restaurant, Retail)

**Technical Details:**

#### Theme Presets Seeder
```php
class ThemePresetsSeeder extends Seeder {
    public function run() {
        $presets = [
            'cafe' => [
                'name' => 'Cafe',
                'tokens' => [
                    'colors' => [
                        'primary' => '#8B4513',
                        'secondary' => '#D2691E',
                        'background' => '#FFF8DC',
                        'text' => '#2F2F2F',
                        'accent' => '#FFD700',
                    ],
                    'fonts' => [
                        'heading' => ['family' => 'Playfair Display', 'weight' => '700'],
                        'body' => ['family' => 'Lato', 'weight' => '400'],
                    ],
                    'spacing' => ['section' => '4rem', 'gap' => '2rem'],
                    'radius' => ['small' => '0.5rem', 'medium' => '1rem', 'large' => '1.5rem'],
                ],
                'default_modules' => [
                    'header_top' => ['logo', 'menu'],
                    'content_top' => ['hero'],
                    'footer_top' => ['opening_hours', 'contact_card'],
                ],
            ],
            // ... restaurant, retail
        ];
        
        foreach ($presets as $slug => $data) {
            ThemePreset::create([
                'slug' => $slug,
                'name' => $data['name'],
                'tokens' => $data['tokens'],
                'default_modules' => $data['default_modules'],
            ]);
        }
    }
}
```

**Acceptance Criteria:**
- [x] Migrations run without errors
- [x] 3 presets seeded
- [x] Tokens structure validated

---

### Task A2 — Theme Tokens Models & Services

**Περιγραφή**: Create models και services για theme tokens.

**Deliverables:**
- `app/Domain/Themes/Models/ThemePreset.php`
- `app/Domain/Themes/Models/ThemeToken.php`
- `app/Domain/Themes/Services/GetThemeTokensService.php`
- `app/Domain/Themes/Services/GetThemePresetService.php`

**Technical Details:**

#### ThemePreset Model
```php
class ThemePreset extends Model {
    protected $fillable = ['slug', 'name', 'tokens', 'default_modules', 'default_header_variant', 'default_footer_variant'];
    protected $casts = ['tokens' => 'array', 'default_modules' => 'array'];
    
    public function businesses(): HasMany // Through theme_tokens
}
```

#### GetThemeTokensService
```php
class GetThemeTokensService {
    public function getTokens(Business $business): array
    {
        // 1. Get business theme token (if exists)
        $themeToken = ThemeToken::where('business_id', $business->id)->first();
        
        // 2. Get preset
        $presetSlug = $themeToken ? $themeToken->preset_slug : 'default';
        $preset = ThemePreset::where('slug', $presetSlug)->firstOrFail();
        
        // 3. Merge: preset tokens + business overrides
        $tokens = $preset->tokens;
        if ($themeToken && $themeToken->token_overrides) {
            $tokens = array_merge_recursive($tokens, $themeToken->token_overrides);
        }
        
        return $tokens;
    }
}
```

**Acceptance Criteria:**
- [x] Models with relationships
- [x] Services load tokens correctly
- [x] Override merging works
- [x] Fallback to default preset

---

### Task A3 — GenerateThemeCssService

**Περιγραφή**: Service που generate-άρει CSS variables από tokens.

**Deliverables:**
- `app/Domain/Themes/Services/GenerateThemeCssService.php`

**Technical Details:**

```php
class GenerateThemeCssService {
    public function generate(Business $business): string
    {
        $tokens = app(GetThemeTokensService::class)->getTokens($business);
        
        $css = ":root {\n";
        
        // Colors
        foreach ($tokens['colors'] as $key => $value) {
            $css .= "    --color-{$key}: {$value};\n";
        }
        
        // Fonts
        foreach ($tokens['fonts'] as $key => $font) {
            $css .= "    --font-{$key}-family: {$font['family']};\n";
            $css .= "    --font-{$key}-weight: {$font['weight']};\n";
        }
        
        // Spacing
        foreach ($tokens['spacing'] as $key => $value) {
            $css .= "    --spacing-{$key}: {$value};\n";
        }
        
        // Radius
        foreach ($tokens['radius'] as $key => $value) {
            $css .= "    --radius-{$key}: {$value};\n";
        }
        
        $css .= "}\n";
        
        return $css;
    }
}
```

**Acceptance Criteria:**
- [x] Generates valid CSS
- [x] All token types converted
- [x] CSS variables format correct

---

### Task A4 — Header/Footer Variants Configuration

**Περιγραφή**: Create configuration files για header/footer variants.

**Deliverables:**
- `config/header_variants.php`
- `config/footer_variants.php`

**Technical Details:**

#### header_variants.php
```php
return [
    'minimal' => [
        'name' => 'Minimal',
        'sticky' => false,
        'show_phone' => false,
        'show_hours' => false,
        'show_social' => false,
        'layout' => 'minimal',
        'view' => 'themes.default.variants.header-minimal',
    ],
    'centered' => [
        'name' => 'Centered',
        'sticky' => true,
        'show_phone' => true,
        'show_hours' => false,
        'show_social' => true,
        'layout' => 'centered',
        'view' => 'themes.default.variants.header-centered',
    ],
    'with-topbar' => [
        'name' => 'With Top Bar',
        'sticky' => true,
        'show_phone' => true,
        'show_hours' => true,
        'show_social' => true,
        'layout' => 'with-topbar',
        'view' => 'themes.default.variants.header-with-topbar',
    ],
];
```

**Acceptance Criteria:**
- [x] All variants defined
- [x] Settings documented
- [x] View paths correct

---

### Task A5 — SEO Services (Sitemap, JSON-LD)

**Περιγραφή**: Create services για SEO automation.

**Deliverables:**
- `app/Domain/Seo/Services/GetSitemapService.php`
- `app/Domain/Seo/Services/GenerateJsonLdService.php`
- `app/Http/Controllers/SitemapController.php`
- `app/Http/Controllers/RobotsController.php`

**Technical Details:**

#### GetSitemapService
```php
class GetSitemapService {
    public function generate(Business $business): string
    {
        $urls = [];
        
        // Home page
        $urls[] = [
            'loc' => route('content.show', ['business' => $business->slug, 'slug' => '/']),
            'lastmod' => now()->toIso8601String(),
            'priority' => '1.0',
        ];
        
        // Content pages
        $contents = Content::where('business_id', $business->id)
            ->published()
            ->get();
        
        foreach ($contents as $content) {
            $urls[] = [
                'loc' => route('content.show', ['business' => $business->slug, 'slug' => $content->slug]),
                'lastmod' => $content->updated_at->toIso8601String(),
                'priority' => '0.8',
            ];
        }
        
        // Generate XML
        return view('seo.sitemap', ['urls' => $urls])->render();
    }
}
```

#### GenerateJsonLdService
```php
class GenerateJsonLdService {
    public function forBusiness(Business $business): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'LocalBusiness',
            'name' => $business->name,
            'image' => $business->logo ? asset($business->logo) : null,
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => $business->getSetting('address'),
            ],
            'telephone' => $business->getSetting('phone'),
            'openingHours' => $this->formatOpeningHours($business),
        ];
    }
}
```

**Acceptance Criteria:**
- [x] Sitemap generates correctly
- [x] JSON-LD valid schema.org
- [x] Robots.txt per business
- [x] Routes configured

---

## Dev B — Architecture/Domain

### Task B1 — ApplyThemeTokensService

**Περιγραφή**: Service που apply-άρει tokens σε views.

**Deliverables:**
- `app/Domain/Themes/Services/ApplyThemeTokensService.php`

**Technical Details:**

```php
class ApplyThemeTokensService {
    public function apply(Business $business): void
    {
        // 1. Generate CSS
        $css = app(GenerateThemeCssService::class)->generate($business);
        
        // 2. Share with views
        view()->share('themeCss', $css);
        view()->share('themeTokens', app(GetThemeTokensService::class)->getTokens($business));
    }
}
```

**Middleware Integration:**
```php
// app/Http/Middleware/ApplyThemeMiddleware.php
class ApplyThemeMiddleware {
    public function handle(Request $request, Closure $next) {
        $business = $request->attributes->get('business');
        if ($business) {
            app(ApplyThemeTokensService::class)->apply($business);
        }
        return $next($request);
    }
}
```

**Acceptance Criteria:**
- [x] CSS generated and shared
- [x] Tokens available in views
- [x] Middleware applied to public routes

---

### Task B2 — Header/Footer Variant Services

**Περιγραφή**: Services για header/footer variant resolution.

**Deliverables:**
- `app/Domain/Themes/Services/GetHeaderVariantService.php`
- `app/Domain/Themes/Services/GetFooterVariantService.php`

**Technical Details:**

```php
class GetHeaderVariantService {
    public function getVariant(Business $business): array
    {
        // 1. Get from theme_tokens
        $themeToken = ThemeToken::where('business_id', $business->id)->first();
        $variantSlug = $themeToken ? $themeToken->header_variant : 'minimal';
        
        // 2. Get variant config
        $variants = config('header_variants');
        return $variants[$variantSlug] ?? $variants['minimal'];
    }
}
```

**Acceptance Criteria:**
- [x] Variant resolution works
- [x] Fallback to minimal
- [x] Settings loaded correctly

---

### Task B3 — Header/Footer Variant Views

**Περιγραφή**: Create variant views για header/footer.

**Deliverables:**
- `resources/views/themes/default/variants/header-minimal.blade.php`
- `resources/views/themes/default/variants/header-centered.blade.php`
- `resources/views/themes/default/variants/header-with-topbar.blade.php`
- `resources/views/themes/default/variants/footer-simple.blade.php`
- `resources/views/themes/default/variants/footer-extended.blade.php`
- `resources/views/themes/default/variants/footer-business-info.blade.php`

**Technical Details:**

#### header-minimal.blade.php
```blade
@php
    $variant = app(\App\Domain\Themes\Services\GetHeaderVariantService::class)->getVariant($currentBusiness);
    $showPhone = $variant['show_phone'] ?? false;
    $showHours = $variant['show_hours'] ?? false;
@endphp

<header class="header-minimal {{ $variant['sticky'] ? 'sticky top-0 z-50' : '' }}">
    <div class="container mx-auto px-4">
        <div class="flex items-center justify-between py-4">
            <div class="logo">
                <img src="{{ $currentBusiness->logo }}" alt="{{ $currentBusiness->name }}">
            </div>
            <nav class="menu">
                {{-- Menu items --}}
            </nav>
        </div>
    </div>
</header>
```

**Acceptance Criteria:**
- [x] All variants created
- [x] Settings applied (sticky, show_phone, etc.)
- [x] Responsive design
- [x] Uses theme tokens (CSS variables)

---

### Task B4 — Enhanced PublishContentService (Audit Log)

**Περιγραφή**: Enhance publish service με audit log.

**Deliverables:**
- Update `app/Domain/Content/Services/PublishContentService.php`

**Technical Details:**

```php
class PublishContentService {
    public function publish(Content $content, User $user): void
    {
        // 1. Create revision before publish (using existing service from Sprint 4.4)
        app(CreateRevisionService::class)->execute($content, $user->id);
        
        // 2. Publish
        $content->update([
            'status' => 'published',
            'published_at' => now(),
        ]);
        
        // 3. Log audit
        \Log::info('Content published', [
            'content_id' => $content->id,
            'user_id' => $user->id,
            'slug' => $content->slug,
            'published_at' => $content->published_at,
        ]);
        
        // 4. Clear cache
        Cache::tags(['content', "content:{$content->id}"])->flush();
    }
}
```

**Integration with Sprint 4.4**:
- Uses existing `CreateRevisionService` (Sprint 4.4)
- Revisions accessible via `ContentRevisionController` (Sprint 4.4)
- Follows MVC patterns (Sprint 4.4)

**Acceptance Criteria:**
- [x] Audit log created
- [x] Revision created before publish (using Sprint 4.4 service)
- [x] Cache cleared

---

## Dev C — Frontend/UI

### Task C1 — Admin UI: Theme Settings Panel (Filament)

**Περιγραφή**: Create Filament page για theme settings.

**Decision**: Use Filament Page (standard CRUD for theme settings) ✅

**Deliverables:**
- `app/Filament/Pages/CMS/Styles.php` (or `ThemeSettings.php`)

**Technical Details:**

```php
use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;

class Styles extends Page implements HasForms
{
    use InteractsWithForms;
    
    protected static ?string $navigationGroup = 'CMS';
    protected static ?int $navigationSort = 3;
    protected static string $view = 'filament.pages.cms.styles';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-paint-brush';
    protected static ?string $navigationLabel = 'Styles';
    
    public ?array $data = [];
    
    public function mount(): void
    {
        $business = Business::active()->first();
        $this->form->fill([
            'preset' => $this->getBusinessThemePreset($business),
            'header_variant' => $this->getBusinessHeaderVariant($business),
            'footer_variant' => $this->getBusinessFooterVariant($business),
            'token_overrides' => $this->getBusinessTokenOverrides($business),
        ]);
    }
    
    protected function getFormSchema(): array
    {
        return [
            Select::make('preset')
                ->label('Theme Preset')
                ->options(ThemePreset::pluck('name', 'slug'))
                ->required()
                ->live(),
            Select::make('header_variant')
                ->label('Header Variant')
                ->options(array_column(config('header_variants'), 'name', 'key'))
                ->required(),
            Select::make('footer_variant')
                ->label('Footer Variant')
                ->options(array_column(config('footer_variants'), 'name', 'key'))
                ->required(),
            ColorPicker::make('token_overrides.colors.primary')
                ->label('Primary Color'),
            // ... more token overrides
        ];
    }
    
    public function save(): void
    {
        $data = $this->form->getState();
        $business = Business::active()->first();
        
        // Save theme settings
        app(UpdateThemeTokensService::class)->execute($business, $data);
        
        $this->notify('success', 'Theme settings saved successfully.');
    }
}
```

**Note**: Following Hybrid Admin Panel guidelines (Sprint 4.5) — Filament Page for standard settings UI.

**Acceptance Criteria:**
- [x] Preset selection
- [x] Variant selection
- [x] Token overrides (colors, fonts)
- [x] Live preview (optional)

---

### Task C2 — Information Pages Seeder

**Περιγραφή**: Create seeder για Information Pages (About, Terms, etc.).

**Deliverables:**
- `database/seeders/InformationPagesSeeder.php`

**Technical Details:**

```php
class InformationPagesSeeder extends Seeder {
    public function run() {
        $business = Business::first();
        $admin = User::where('is_admin', true)->first();
        
        $pages = [
            [
                'slug' => 'about',
                'title' => 'About Us',
                'type' => 'page',
                'layout_id' => Layout::where('type', 'default')->first()->id,
            ],
            [
                'slug' => 'terms',
                'title' => 'Terms & Conditions',
                'type' => 'page',
                'layout_id' => Layout::where('type', 'default')->first()->id,
            ],
            // ... privacy, contact, delivery
        ];
        
        foreach ($pages as $pageData) {
            Content::create(array_merge($pageData, [
                'business_id' => $business->id,
                'created_by' => $admin->id,
                'status' => 'published',
                'published_at' => now(),
                'body_json' => [], // Will use layout modules
            ]));
        }
    }
}
```

**Acceptance Criteria:**
- [x] All information pages created
- [x] Assigned to default layout
- [x] Published status

---

### Task C3 — Enhanced SEO Fields (Content Editor)

**Περιγραφή**: Add SEO fields στο Content editor.

**Decision**: Use Blade Controller (Content Editor is custom UI) ✅

**Note**: Content Editor uses Blade Controller (Sprint 4.5 guidelines), not Filament Resource.

**Deliverables:**
- Update `resources/views/admin/content/edit.blade.php` (add SEO section)
- Update `app/Http/Controllers/Admin/ContentController.php` (handle SEO fields)

**Technical Details:**

```blade
{{-- In resources/views/admin/content/edit.blade.php --}}
<div class="bg-white rounded-xl shadow-sm p-6 mb-6">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">SEO Settings</h3>
    <div class="space-y-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Meta Title
                <span class="text-xs text-gray-500">(Recommended: 50-60 characters)</span>
            </label>
            <input type="text" 
                   name="meta[title]" 
                   value="{{ old('meta.title', $content->meta['title'] ?? '') }}"
                   maxlength="60"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Meta Description
                <span class="text-xs text-gray-500">(Recommended: 150-160 characters)</span>
            </label>
            <textarea name="meta[description]" 
                      maxlength="160"
                      rows="3"
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary">{{ old('meta.description', $content->meta['description'] ?? '') }}</textarea>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Keywords</label>
            <input type="text" 
                   name="meta[keywords]" 
                   value="{{ old('meta.keywords', $content->meta['keywords'] ?? '') }}"
                   placeholder="Comma-separated"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">OG Image</label>
            <input type="file" 
                   name="meta[og_image]" 
                   accept="image/*"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary">
        </div>
        <div>
            <label class="flex items-center gap-2">
                <input type="checkbox" 
                       name="meta[noindex]" 
                       value="1"
                       {{ old('meta.noindex', $content->meta['noindex'] ?? false) ? 'checked' : '' }}
                       class="rounded border-gray-300 text-primary focus:ring-primary">
                <span class="text-sm text-gray-700">No Index (Prevent search engines from indexing this page)</span>
            </label>
        </div>
    </div>
</div>
```

**Note**: Following Hybrid Admin Panel guidelines (Sprint 4.5) — Blade Controller for Content Editor (custom UI).

**Acceptance Criteria:**
- [x] All SEO fields added
- [x] Validation rules (in UpdateContentRequest)
- [x] Helper text
- [x] SEO fields saved in Content meta JSON

---

### Task C4 — Theme CSS Injection (Public Layout)

**Περιγραφή**: Inject theme CSS στο public layout.

**Deliverables:**
- Update `resources/views/layouts/public.blade.php`

**Technical Details:**

```blade
{{-- In public.blade.php <head> --}}
@if(isset($themeCss))
    <style>
        {!! $themeCss !!}
    </style>
@endif
```

**Acceptance Criteria:**
- [x] CSS injected in head
- [x] CSS variables available
- [x] No FOUC (Flash of Unstyled Content)

---

### Task C5 — Header/Footer Variant Integration

**Περιγραφή**: Integrate header/footer variants στο public layout.

**Deliverables:**
- Update `resources/views/layouts/public.blade.php`

**Technical Details:**

```blade
{{-- In public.blade.php --}}
@php
    $headerVariant = app(\App\Domain\Themes\Services\GetHeaderVariantService::class)->getVariant($currentBusiness);
    $footerVariant = app(\App\Domain\Themes\Services\GetFooterVariantService::class)->getVariant($currentBusiness);
@endphp

@include($headerVariant['view'], ['variant' => $headerVariant])

<main>
    @yield('content')
</main>

@include($footerVariant['view'], ['variant' => $footerVariant])
```

**Acceptance Criteria:**
- [x] Header variant renders
- [x] Footer variant renders
- [x] Settings applied (sticky, show_phone, etc.)

---

## 📦 Deliverables (Definition of Done)

### Backend
- [x] Theme presets & tokens database
- [x] Theme tokens models & services
- [x] CSS generation service
- [x] Header/footer variant services
- [x] SEO services (sitemap, JSON-LD)
- [x] Enhanced publish service (audit log)

### Frontend
- [x] Header/footer variant views
- [x] Theme CSS injection
- [x] Variant integration in layout

### Admin UI
- [x] Theme settings panel
- [x] Enhanced SEO fields
- [x] Information pages seeder

### Testing
- [x] Theme tokens apply correctly
- [x] Variants render correctly
- [x] SEO meta tags work
- [x] Sitemap generates correctly

---

## 🧠 Τι ΔΕΝ Κάνει το Sprint 5

- ❌ Headless API — Sprint 6
- ❌ Plugins — Sprint 6
- ❌ Multi-domain routing — Sprint 6+

---

## 📝 Sprint Notes

_Καταγράψε εδώ progress, decisions, issues_

---

## 📚 References

- [v2 Overview](../v2_overview.md) — Architecture & strategy
- [Sprint 4.1 — Navigation Structure](./sprint_4.1/sprint_4.1.md) — Admin panel navigation
- [Sprint 4.3 — Filament 4 Alignment](./sprint_4.3/sprint_4.3.md) — Filament 4 compatibility
- [Sprint 4.4 — MVC Audit](./sprint_4.4/sprint_4.4.md) — MVC patterns & guidelines
- [Sprint 4.5 — Hybrid Admin Panel](./sprint_4.5/sprint_4.5.md) — Filament vs Blade guidelines
- [Hybrid Admin Decision Tree](../architecture/hybrid_admin_decision_tree.md) — When to use Filament vs Blade
- [MVC Best Practices](../architecture/mvc_best_practices.md) — MVC guidelines

---

## 🔄 Integration with Sprint 4.x

### Sprint 4.4 (MVC Audit)
- **Enhancement**: Theme services follow MVC patterns
- **Integration**: Services in `app/Domain/Themes/Services/`
- **Guidelines**: Follow MVC best practices

### Sprint 4.5 (Hybrid Admin Panel)
- **Enhancement**: Theme Settings use Filament Page (standard CRUD)
- **Integration**: Follows Hybrid Admin Panel guidelines
- **Decision**: Filament Page for theme settings (standard forms)

---

**Last Updated**: 2025-01-27

