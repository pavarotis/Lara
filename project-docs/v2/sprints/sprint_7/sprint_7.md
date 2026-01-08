# Sprint 7 — Lightweight Public Site & Performance Optimization

**Status**: ✅ Completed  
**Start Date**: 2026-01-08  
**End Date**: 2026-01-08  
**Διάρκεια**: 1-2 εβδομάδες  
**Filament Version**: Χρησιμοποιούμε μόνο **Filament 4.x** στο admin panel (δεν επιτρέπονται Filament v2/v3 packages ή APIs).

---

## 📋 Sprint Goal

Μετατροπή του public site από "φορτώνει τα πάντα" σε "φορτώνει μόνο αυτό που χρειάζεται":
- Widget Contract με asset declaration
- Layout compilation pipeline (JSON → compiled HTML)
- Per-widget asset loading
- Zero-JS default policy
- Image optimization pipeline
- Aggressive caching strategy

**Μετά το Sprint 7**: 👉 «Public site Lighthouse 90+ από την αρχή, με Journal-like editing experience στο admin».

---

## 🎯 High-Level Objectives

1. **Widget Contract Interface** — Modules δηλώνουν assets (CSS/JS)
2. **Layout Compilation** — Publish-time compilation σε compiled HTML
3. **Per-Widget Assets** — Vite chunks per widget
4. **Zero-JS Default** — Alpine μόνο όπου χρειάζεται
5. **Image Pipeline** — WebP/AVIF, srcset, lazy loading
6. **Caching Strategy** — Full page + fragment cache

---

## 🔗 Integration Points

### Dependencies
- **Sprint 3** (Content Rendering & Theming) — Θα βελτιώσουμε το `RenderModuleService`
- **Sprint 4.1** (Navigation Structure) — Δεν επηρεάζεται
- **Sprint 4.3** (Filament 4 Alignment) — Δεν επηρεάζεται
- **Sprint 4.4** (MVC Audit) — Services follow MVC patterns
- **Sprint 4.5** (Hybrid Admin Panel) — Δεν επηρεάζεται (public site only)
- **Sprint 6** (Plugins) — Plugins πρέπει να υποστηρίζουν Widget Contract
- **Module System** — `ModuleInstance`, `RenderModuleService`, `config/modules.php`

### Backward Compatibility
- Modules χωρίς assets → fallback σε global assets
- Layouts χωρίς compilation → on-the-fly rendering (current behavior)
- Feature flag: `PERFORMANCE_OPTIMIZATIONS_ENABLED`

---

## 👥 Tasks by Developer Stream

### Dev A — Widget Contract & Asset System

#### Task A1 — Widget Contract Interface

**Περιγραφή**: Δημιουργία interface για widgets που δηλώνουν assets.

**Deliverables**:
- `app/Domain/Modules/Contracts/WidgetContract.php`:
  ```php
  interface WidgetContract {
      /**
       * Render widget HTML
       */
      public function render($config, $context): string;
      
      /**
       * Get required assets (CSS/JS)
       * Returns: ['css' => [...], 'js' => [...]]
       */
      public function assets(): array;
      
      /**
       * Get critical CSS (inline)
       */
      public function criticalCss(): ?string;
      
      /**
       * Cache TTL in seconds
       */
      public function cacheTtl(): int;
      
      /**
       * Generate cache key
       */
      public function cacheKey($config): string;
  }
  ```
- `app/Domain/Modules/AbstractWidget.php` — Base class με default implementations
- Update `config/modules.php` να περιλαμβάνει asset declarations:
  ```php
  'hero' => [
      'name' => 'Hero',
      'assets' => [
          'css' => ['widgets/hero.css'],
          'js' => [], // No JS needed
      ],
      'critical_css' => '.hero { ... }',
      // ... existing config
  ],
  ```

**Acceptance Criteria**:
- Όλα τα modules έχουν `assets()` declaration στο config
- Widget Contract interface implemented
- Backward compatible: modules χωρίς assets → fallback

**Files to Create/Modify**:
- `app/Domain/Modules/Contracts/WidgetContract.php` (new)
- `app/Domain/Modules/AbstractWidget.php` (new)
- `config/modules.php` (modify)

---

#### Task A2 — Asset Manifest Service

**Περιγραφή**: Service που συλλέγει assets από modules σε μια σελίδα.

**Deliverables**:
- `app/Domain/Modules/Services/CollectWidgetAssetsService.php`:
  ```php
  class CollectWidgetAssetsService {
      /**
       * Collect assets from module instances
       * @param Collection<ModuleInstance> $modules
       * @return array ['css' => [...], 'js' => [...]]
       */
      public function collect(Collection $modules): array;
      
      /**
       * Get critical CSS from modules
       */
      public function getCriticalCss(Collection $modules): string;
  }
  ```
- Integration με `RenderModuleService` για asset collection

**Acceptance Criteria**:
- Asset collection works για multiple modules
- Deduplication (same asset loaded once)
- Critical CSS aggregation

**Files to Create/Modify**:
- `app/Domain/Modules/Services/CollectWidgetAssetsService.php` (new)
- `app/Domain/Modules/Services/RenderModuleService.php` (modify)

---

#### Task A3 — Vite Per-Widget Chunks

**Περιγραφή**: Configuration του Vite για per-widget asset chunks.

**Deliverables**:
- Update `vite.config.js`:
  ```js
  export default defineConfig({
      plugins: [
          laravel({
              input: [
                  'resources/css/app.css', // Base CSS
                  'resources/js/app.js', // Base JS (conditional)
                  // Widget CSS
                  'resources/css/widgets/hero.css',
                  'resources/css/widgets/gallery.css',
                  'resources/css/widgets/slider.css',
                  // Widget JS (only where needed)
                  'resources/js/widgets/slider.js',
                  'resources/js/widgets/map.js',
              ],
              refresh: true,
          }),
      ],
      build: {
          rollupOptions: {
              output: {
                  // Per-widget chunks
                  chunkFileNames: 'widgets/[name]-[hash].js',
                  entryFileNames: 'widgets/[name]-[hash].js',
                  assetFileNames: 'widgets/[name]-[hash].[ext]',
              },
          },
      },
  });
  ```
- Widget CSS/JS files structure:
  ```
  resources/
  ├── css/
  │   ├── app.css (base)
  │   └── widgets/
  │       ├── hero.css
  │       ├── gallery.css
  │       └── slider.css
  └── js/
      ├── app.js (base - conditional)
      └── widgets/
          ├── slider.js
          └── map.js
  ```

**Acceptance Criteria**:
- Vite build generates per-widget chunks
- Base CSS/JS separate from widget assets
- Build works correctly

**Files to Create/Modify**:
- `vite.config.js` (modify)
- `resources/css/widgets/` (new directory)
- `resources/js/widgets/` (new directory)

---

### Dev B — Layout Compilation & Caching

#### Task B1 — Layout Compilation Pipeline

**Περιγραφή**: Compile layout JSON σε compiled HTML + assets manifest στο publish time.

**Deliverables**:
- `app/Domain/Layouts/Services/CompileLayoutService.php`:
  ```php
  class CompileLayoutService {
      /**
       * Compile layout JSON to HTML + assets
       * @param Layout $layout
       * @return array ['compiled_html', 'assets_manifest', 'critical_css']
       */
      public function compile(Layout $layout): array;
      
      /**
       * Get modules for layout
       */
      private function getModulesForLayout(Layout $layout): Collection;
      
      /**
       * Render modules to HTML
       */
      private function renderModules(Collection $modules): string;
      
      /**
       * Collect assets from modules
       */
      private function collectAssets(Collection $modules): array;
  }
  ```
- Database migration: Add columns to `layouts` table:
  ```php
  Schema::table('layouts', function (Blueprint $table) {
      $table->text('compiled_html')->nullable()->after('regions');
      $table->json('assets_manifest')->nullable()->after('compiled_html');
      $table->text('critical_css')->nullable()->after('assets_manifest');
      $table->timestamp('compiled_at')->nullable()->after('critical_css');
  });
  ```
- Integration με publish action:
  - On layout publish → compile
  - Store compiled result in DB
  - Use compiled HTML on public site

**Acceptance Criteria**:
- Layout compilation on publish
- Compiled HTML stored in DB
- Assets manifest per layout
- Public site uses compiled HTML when available

**Files to Create/Modify**:
- `app/Domain/Layouts/Services/CompileLayoutService.php` (new)
- `database/migrations/XXXX_add_compilation_to_layouts.php` (new)
- `app/Domain/Layouts/Models/Layout.php` (modify - add casts)
- Layout publish action (modify)

---

#### Task B2 — Aggressive Caching Strategy

**Περιγραφή**: Full page cache + fragment cache για guest users.

**Deliverables**:
- `app/Domain/Layouts/Services/LayoutCacheService.php`:
  ```php
  class LayoutCacheService {
      /**
       * Get cached layout HTML
       */
      public function get(Layout $layout, ?string $locale = null): ?string;
      
      /**
       * Cache layout HTML
       */
      public function put(Layout $layout, string $html, ?string $locale = null): void;
      
      /**
       * Invalidate cache
       */
      public function forget(Layout $layout): void;
      
      /**
       * Generate cache key
       */
      private function cacheKey(Layout $layout, ?string $locale): string;
  }
  ```
- Full page cache middleware:
  ```php
  class CachePublicPages {
      public function handle($request, Closure $next) {
          if (auth()->check()) {
              return $next($request); // No cache for authenticated
          }
          
          $cacheKey = 'page:' . $request->path() . ':' . app()->getLocale();
          return Cache::remember($cacheKey, 3600, function() use ($next, $request) {
              $response = $next($request);
              $response->header('Cache-Control', 'public, max-age=3600');
              $response->header('ETag', md5($response->getContent()));
              return $response;
          });
      }
  }
  ```
- Widget fragment cache:
  ```php
  // In RenderModuleService
  public function render(ModuleInstance $module): string {
      $cacheKey = "module:{$module->id}:{$module->updated_at->timestamp}";
      return Cache::remember($cacheKey, $module->cacheTtl(), function() use ($module) {
          // Render module
      });
  }
  ```
- Cache invalidation on content update:
  - On `ModuleInstance` update → clear module cache
  - On `Layout` update → clear layout cache
  - On `Content` update → clear page cache

**Acceptance Criteria**:
- Guest users get cached pages
- Cache invalidation works correctly
- HTTP caching headers set
- ETag support
- Lighthouse cache score 100

**Files to Create/Modify**:
- `app/Domain/Layouts/Services/LayoutCacheService.php` (new)
- `app/Http/Middleware/CachePublicPages.php` (new)
- `app/Domain/Modules/Services/RenderModuleService.php` (modify)
- `bootstrap/app.php` (modify - register middleware)

---

### Dev C — Zero-JS & Image Optimization

#### Task C1 — Zero-JS Default Policy

**Περιγραφή**: Remove global Alpine.js, load only where needed.

**Deliverables**:
- Update `resources/views/layouts/public.blade.php`:
  ```blade
  <head>
      <!-- Base CSS always -->
      @vite(['resources/css/app.css'])
      
      <!-- Conditional JS -->
      @if($needsAlpine ?? false)
          @vite(['resources/js/app.js'])
      @endif
      
      <!-- Widget-specific JS -->
      @stack('widget-scripts')
  </head>
  ```
- Update `resources/js/app.js`:
  ```js
  // Only load Alpine if needed
  if (document.querySelector('[x-data]')) {
      import('alpinejs').then(Alpine => {
          window.Alpine = Alpine.default;
          Alpine.default.start();
      });
  }
  ```
- Widget JS loading:
  ```blade
  {{-- In widget view --}}
  @push('widget-scripts')
      @vite(['resources/js/widgets/slider.js'])
  @endpush
  ```
- Identify widgets that need JS:
  - Mobile menu (Alpine)
  - Sliders (custom JS)
  - Maps (Google Maps API)
  - Forms (validation)

**Acceptance Criteria**:
- Public site loads without JS by default
- JS loads only where needed
- Alpine conditional loading
- Lighthouse JS score improved

**Files to Create/Modify**:
- `resources/views/layouts/public.blade.php` (modify)
- `resources/js/app.js` (modify)
- Widget views (modify - add @push for JS)

---

#### Task C2 — Image Optimization Pipeline

**Περιγραφή**: WebP/AVIF generation, srcset, lazy loading.

**Deliverables**:
- `app/Domain/Media/Services/ImageOptimizationService.php`:
  ```php
  class ImageOptimizationService {
      /**
       * Generate optimized variants
       */
      public function generateVariants(string $path): array;
      
      /**
       * Generate WebP/AVIF
       */
      private function generateModernFormats(string $path): void;
      
      /**
       * Generate responsive srcset
       */
      public function generateSrcset(string $path): string;
  }
  ```
- Update Media model to store variants:
  ```php
  // Migration
  Schema::table('media', function (Blueprint $table) {
      $table->json('variants')->nullable()->after('path');
      // variants: {webp: '...', avif: '...', sizes: {...}}
  });
  ```
- Blade component for optimized images:
  ```blade
  {{-- resources/views/components/optimized-image.blade.php --}}
  <picture>
      <source srcset="{{ $avifSrcset }}" type="image/avif">
      <source srcset="{{ $webpSrcset }}" type="image/webp">
      <img 
          src="{{ $src }}" 
          srcset="{{ $srcset }}"
          loading="{{ $lazy ? 'lazy' : 'eager' }}"
          alt="{{ $alt }}"
      >
  </picture>
  ```
- Integration με Media Library:
  - On upload → generate variants
  - Store variants in DB
  - Use optimized-image component in views

**Acceptance Criteria**:
- All images have WebP/AVIF variants
- Responsive images with srcset
- Lazy loading works
- Hero images preloaded
- Lighthouse image score 100

**Files to Create/Modify**:
- `app/Domain/Media/Services/ImageOptimizationService.php` (new)
- `database/migrations/XXXX_add_variants_to_media.php` (new)
- `app/Domain/Media/Models/Media.php` (modify)
- `resources/views/components/optimized-image.blade.php` (new)
- Media upload service (modify)

---

#### Task C3 — CSS Optimization

**Περιγραφή**: Critical CSS extraction, CSS chunks, purge unused.

**Deliverables**:
- Critical CSS extraction:
  - Extract critical CSS from widgets
  - Inline critical CSS in `<head>`
  - Load non-critical CSS async
- Tailwind purge configuration:
  ```js
  // tailwind.config.js
  content: [
      'resources/views/**/*.blade.php',
      'resources/css/**/*.css',
      'resources/js/**/*.js',
      // Widget views
      'resources/views/themes/**/*.blade.php',
      'resources/views/modules/**/*.blade.php',
  ],
  ```
- CSS chunking:
  - Base CSS (layout + typography)
  - Widget CSS per widget
  - Load only needed chunks

**Acceptance Criteria**:
- Critical CSS inline
- Non-critical CSS async
- Tailwind purges unused
- Lighthouse CSS score improved

**Files to Create/Modify**:
- `tailwind.config.js` (modify)
- `resources/views/layouts/public.blade.php` (modify - critical CSS)
- CSS build process (modify)

---

## 📦 Deliverables (Definition of Done)

- [ ] Widget Contract interface implemented
- [ ] All modules have `assets()` declarations in config
- [ ] Vite per-widget chunks working
- [ ] Asset manifest service functional
- [ ] Layout compilation pipeline complete
- [ ] Full page cache + fragment cache working
- [ ] Zero-JS default policy implemented
- [ ] Image optimization pipeline complete
- [ ] CSS optimization complete
- [ ] Cache invalidation working
- [ ] Lighthouse score 90+ (Performance, Best Practices, SEO)
- [ ] Backward compatible (fallback for old modules)
- [ ] Feature flag tested (`PERFORMANCE_OPTIMIZATIONS_ENABLED`)

---

## 🔄 Integration with Existing Sprints

### Sprint 3 (Content Rendering & Theming)
- **Enhancement**: `RenderModuleService` now collects assets
- **Enhancement**: Theme views support asset declarations
- **No Breaking Changes**: Existing themes work without assets

### Sprint 4.x (RBAC, MVC, Hybrid Admin Panel)
- **Sprint 4.4 (MVC Audit)**: Services follow MVC patterns
- **Sprint 4.5 (Hybrid Admin Panel)**: No impact (public site only)
- **No Impact**: Performance optimizations don't affect RBAC or admin panel

### Sprint 6 (Plugins & Polish)
- **Requirement**: Plugins must implement `WidgetContract`
- **Enhancement**: Plugin assets automatically collected
- **Documentation**: Update plugin guide with asset declaration

### Module System
- **Enhancement**: `ModuleInstance` supports asset manifest
- **Enhancement**: `config/modules.php` includes asset declarations
- **Backward Compatible**: Old modules work without assets

---

## 📝 Technical Specifications

### Widget Contract Example

```php
// config/modules.php
'hero' => [
    'name' => 'Hero',
    'assets' => [
        'css' => ['widgets/hero.css'],
        'js' => [], // No JS
    ],
    'critical_css' => '.hero { min-height: 400px; }',
    'cache_ttl' => 3600,
    // ... existing config
],

'slider' => [
    'name' => 'Slider',
    'assets' => [
        'css' => ['widgets/slider.css'],
        'js' => ['widgets/slider.js'], // JS needed
    ],
    'critical_css' => '.slider { position: relative; }',
    'cache_ttl' => 1800,
    // ... existing config
],
```

### Layout Compilation Flow

```
1. Admin publishes layout
   ↓
2. CompileLayoutService::compile()
   ↓
3. Get modules for layout
   ↓
4. Render modules → compiled_html
   ↓
5. Collect assets → assets_manifest
   ↓
6. Extract critical CSS → critical_css
   ↓
7. Store in DB (compiled_html, assets_manifest, critical_css)
   ↓
8. Public site uses compiled HTML
```

### Asset Loading Flow

```
1. Public request
   ↓
2. Get layout (with compiled HTML)
   ↓
3. Extract assets_manifest
   ↓
4. Load base CSS
   ↓
5. Load critical CSS (inline)
   ↓
6. Load widget CSS (from manifest)
   ↓
7. Load widget JS (only if needed, async)
```

### Cache Strategy

```
Guest User Request:
1. Check full page cache
2. If cached → return with ETag
3. If not cached → render → cache → return

Authenticated User:
1. Skip cache
2. Render fresh
3. No caching

Cache Invalidation:
- ModuleInstance updated → clear module cache
- Layout updated → clear layout cache
- Content updated → clear page cache
```

---

## 🎯 Success Metrics

### Performance Targets
- **Lighthouse Performance**: 90+
- **Lighthouse Best Practices**: 95+
- **Lighthouse SEO**: 95+
- **First Contentful Paint**: < 1.5s
- **Time to Interactive**: < 3.5s
- **Total Blocking Time**: < 200ms
- **Cumulative Layout Shift**: < 0.1

### Bundle Size Targets
- **Base CSS**: < 50KB (gzipped)
- **Base JS**: < 20KB (gzipped) or 0KB if not needed
- **Per-widget CSS**: < 10KB per widget (gzipped)
- **Per-widget JS**: < 15KB per widget (gzipped)

### Cache Targets
- **Cache Hit Rate**: > 80% (guest users)
- **Cache Invalidation**: < 100ms
- **Cache Storage**: < 100MB per business

---

## ⚠️ Breaking Changes & Migration

### Breaking Changes
- **None** — All changes are backward compatible

### Migration Path
1. **Phase 1**: Add Widget Contract (optional)
2. **Phase 2**: Enable compilation (feature flag)
3. **Phase 3**: Enable caching (feature flag)
4. **Phase 4**: Enable image optimization (feature flag)
5. **Phase 5**: Full optimization (all flags on)

### Feature Flags
```env
PERFORMANCE_OPTIMIZATIONS_ENABLED=true
LAYOUT_COMPILATION_ENABLED=true
FULL_PAGE_CACHE_ENABLED=true
IMAGE_OPTIMIZATION_ENABLED=true
ZERO_JS_DEFAULT=true
```

---

## 📚 Documentation Updates

### Required Documentation
- [ ] Widget Contract guide (how to declare assets)
- [ ] Layout compilation guide
- [ ] Caching strategy documentation
- [ ] Image optimization guide
- [ ] Performance best practices
- [ ] Migration guide (from old to new)

### Files to Update
- `project-docs/v2/v2_overview.md` (add Sprint 7)
- `project-docs/v2/plugin_guide.md` (add asset declaration)
- `README.md` (update performance section)

---

## 🧪 Testing Requirements

### Unit Tests
- [ ] `WidgetContract` interface tests
- [ ] `CollectWidgetAssetsService` tests
- [ ] `CompileLayoutService` tests
- [ ] `LayoutCacheService` tests
- [ ] `ImageOptimizationService` tests

### Integration Tests
- [ ] Layout compilation flow
- [ ] Asset collection flow
- [ ] Cache invalidation flow
- [ ] Image optimization flow

### Performance Tests
- [ ] Lighthouse audit (90+)
- [ ] Load time tests
- [ ] Cache hit rate tests
- [ ] Bundle size tests

---

## 📝 Notes

- Το Sprint 7 είναι **performance-focused** — δεν αλλάζει business logic
- Όλα τα changes είναι **backward compatible**
- Feature flags allow **gradual rollout**
- **Zero breaking changes** — existing code continues to work
- Focus on **public site performance** — admin panel unchanged

---

## 🔄 Sprint Dependencies & Order

### Prerequisites
- ✅ **Sprint 3** (Content Rendering & Theming) — Must be complete
- ✅ **Sprint 4.1** (Navigation Structure) — Must be complete
- ✅ **Sprint 4.3** (Filament 4 Alignment) — Must be complete
- ✅ **Sprint 4.4** (MVC Audit) — Recommended (services follow patterns)
- ✅ **Sprint 4.5** (Hybrid Admin Panel) — Must be complete
- ✅ **Sprint 6** (Plugins) — Should be complete (plugins need asset support)

### Execution Order
1. **Sprint 0-6** → Complete core functionality
2. **Sprint 7** → Performance optimization (can be done in parallel with bug fixes)

### Post-Sprint 7
- Future sprints can build on performance foundation
- Plugin developers can use Widget Contract
- New features benefit from lightweight runtime

---

## 📚 Related Documentation

- [Sprint 3 — Content Rendering](./sprint_3/sprint_3.md) — Rendering system
- [Sprint 4.4 — MVC Audit](./sprint_4.4/sprint_4.4.md) — MVC patterns & guidelines
- [Sprint 4.5 — Hybrid Admin Panel](./sprint_4.5/sprint_4.5.md) — Filament vs Blade guidelines
- [Sprint 6 — Plugins](./sprint_6/sprint_6.md) — Plugin system
- [v2 Overview](../v2_overview.md) — Overall architecture
- [MVC Best Practices](../architecture/mvc_best_practices.md) — MVC guidelines

---

## 🔄 Integration with Sprint 4.x

### Sprint 4.4 (MVC Audit)
- **Enhancement**: Performance services follow MVC patterns
- **Integration**: Services in `app/Domain/Modules/Services/`, `app/Domain/Layouts/Services/`
- **Guidelines**: Follow MVC best practices

### Sprint 4.5 (Hybrid Admin Panel)
- **No Impact**: Sprint 7 focuses on public site performance
- **Note**: Admin panel performance not affected (already optimized via Filament)

---

**Last Updated**: 2025-01-27

