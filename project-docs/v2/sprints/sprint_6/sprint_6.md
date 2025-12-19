# Sprint 6 — Platform Hardening, Routing Strategy, API, Release

**Status**: ⏳ Pending  
**Start Date**: _TBD_  
**End Date**: _TBD_  
**Διάρκεια**: 1 εβδομάδα  
**Filament Version**: Χρησιμοποιούμε μόνο **Filament 4.x** στο admin panel (δεν επιτρέπονται Filament v2/v3 packages ή APIs).

---

## 📋 Sprint Goal

Να μετατραπεί το CMS από "δουλεύει" σε πλατφόρμα παραγωγής:
- Καθαρή στρατηγική routing / tenancy
- Σταθερό isolation ανά business
- Headless-ready API
- Release-ready (tests, docs, deployment)
- Foundation για plugins & future growth

**Μετά το Sprint 6**: 👉 «Μπορώ να το δώσω σε πελάτη ή να το πουλήσω σαν product»

---

## 🎯 High-Level Objectives

- Canonical routing strategy (path-based v1)
- Business resolution hardening
- Headless API (read-only v1)
- Plugin foundation (spec + demo)
- Performance & stability audit
- Testing & QA
- Deployment & documentation

⚠️ **Δεν υλοποιείται ακόμα:**
- ❌ Full plugin marketplace — Future
- ❌ Multi-domain routing — Sprint 7+
- ❌ Frontend rewrite (React/Vue) — Future

---

## 🧠 Κεντρικές Αποφάσεις (LOCKED)

- ✅ **Κρατάμε** manual business_id tenancy (όχι package)
- ✅ **Canonical routing**: path-based για v1
- ✅ **Headless API** = read-only (v1)
- ❌ **Όχι** feature creep
- ❌ **Όχι** νέα CMS features

---

## 📊 Database Schema

### New Tables

#### `api_keys` Table
```php
Schema::create('api_keys', function (Blueprint $table) {
    $table->id();
    $table->foreignId('business_id')->constrained()->cascadeOnDelete();
    $table->string('name'); // User-friendly name
    $table->string('key')->unique(); // API key
    $table->string('secret'); // API secret (hashed)
    $table->json('scopes')->nullable(); // ['read:menu', 'read:products', ...]
    $table->timestamp('last_used_at')->nullable();
    $table->timestamp('expires_at')->nullable();
    $table->boolean('is_active')->default(true);
    $table->timestamps();
    
    $table->index('business_id');
    $table->index('key');
    $table->index('is_active');
});
```

#### `plugin_registry` Table (Optional, ή config-based)
```php
Schema::create('plugin_registry', function (Blueprint $table) {
    $table->id();
    $table->string('name')->unique();
    $table->string('version');
    $table->string('class'); // Plugin class name
    $table->json('modules')->nullable(); // Registered modules
    $table->boolean('is_active')->default(true);
    $table->timestamps();
});
```

---

## 🏗️ Service Layer Architecture

### New Services

1. **ResolveBusinessService** — Enhanced business resolution
2. **ApiAuthService** — API key authentication
3. **ApiRateLimitService** — Rate limiting per business
4. **PluginRegistryService** — Plugin registration & discovery
5. **CacheInvalidationService** — Unified cache invalidation

### Enhanced Services

1. **SetCurrentBusiness** middleware — Hardened with guards
2. **ContentController** — Enhanced with canonical routing

---

## 👥 Tasks by Developer

---

## Dev A — Backend/Infrastructure

### Task A1 — Canonical Routing Strategy

**Περιγραφή**: Implement canonical URL strategy: `/{business:slug}/{page:slug?}`

**Deliverables:**
- Update `routes/web.php`
- Update `app/Http/Controllers/ContentController.php`
- Update `app/Http/Middleware/SetCurrentBusiness.php`

**Technical Details:**

#### Routes Structure
```php
// routes/web.php

// Static routes first (menu, cart, checkout, etc.)
Route::get('/menu', [MenuController::class, 'index'])->name('menu');
// ... other static routes

// Canonical business routes
Route::prefix('{business:slug}')->group(function () {
    // Business home page
    Route::get('/', [ContentController::class, 'showBusinessHome'])
        ->where('business', '[a-z0-9-]+')
        ->name('business.home');
    
    // Content pages
    Route::get('/{page:slug}', [ContentController::class, 'show'])
        ->where('business', '[a-z0-9-]+')
        ->where('page', '[a-z0-9-/]+')
        ->name('content.show');
})->middleware(['business']);

// Fallback: Legacy routes (query param, session)
Route::get('/', function (Request $request) {
    // Fallback logic
    $business = app(ResolveBusinessService::class)->resolve($request);
    if ($business) {
        return redirect()->route('business.home', ['business' => $business->slug]);
    }
    abort(404);
});
```

#### ContentController Updates
```php
class ContentController extends Controller {
    public function showBusinessHome(Business $business) {
        // Get home page (slug: '/')
        $content = app(GetContentService::class)->bySlug($business->id, '/');
        
        if (!$content) {
            abort(404);
        }
        
        return $this->renderContent($content);
    }
    
    public function show(Business $business, string $page) {
        $content = app(GetContentService::class)->bySlug($business->id, $page);
        
        if (!$content) {
            abort(404);
        }
        
        return $this->renderContent($content);
    }
}
```

**Acceptance Criteria:**
- [x] Canonical routes work: `/{business}/{page}`
- [x] Business home works: `/{business}`
- [x] Fallback routes work (backward compatibility)
- [x] 404 for invalid business/page

---

### Task A2 — Business Resolution Hardening

**Περιγραφή**: Enhance SetCurrentBusiness middleware με guards & validation.

**Deliverables:**
- Update `app/Http/Middleware/SetCurrentBusiness.php`
- Create `app/Domain/Businesses/Services/ResolveBusinessService.php`

**Technical Details:**

#### ResolveBusinessService
```php
class ResolveBusinessService {
    public function resolve(Request $request): ?Business
    {
        // 1. Route parameter (canonical)
        if ($business = $request->route('business')) {
            $business = Business::where('slug', $business)->active()->first();
            if ($business) {
                return $business;
            }
            abort(404, 'Business not found');
        }
        
        // 2. Query parameter (fallback)
        if ($slug = $request->query('business')) {
            $business = Business::where('slug', $slug)->active()->first();
            if ($business) {
                return $business;
            }
        }
        
        // 3. Session (fallback)
        if ($businessId = session('current_business_id')) {
            $business = Business::find($businessId);
            if ($business && $business->is_active) {
                return $business;
            }
        }
        
        // 4. No fallback to first business (security)
        return null;
    }
}
```

#### Enhanced Middleware
```php
class SetCurrentBusiness {
    public function handle(Request $request, Closure $next): Response
    {
        $business = app(ResolveBusinessService::class)->resolve($request);
        
        if (!$business) {
            // Only allow fallback for admin routes
            if ($request->is('admin/*') || $request->is('api/*')) {
                // Admin can work without business context
                return $next($request);
            }
            abort(404, 'Business not found');
        }
        
        // Validate business is active
        if (!$business->is_active) {
            abort(403, 'Business is inactive');
        }
        
        // Share with views & request
        view()->share('currentBusiness', $business);
        $request->attributes->set('business', $business);
        session(['current_business_id' => $business->id]);
        
        return $next($request);
    }
}
```

**Acceptance Criteria:**
- [x] Business resolution works (route → query → session)
- [x] Inactive business returns 403
- [x] Missing business returns 404
- [x] Admin routes work without business

---

### Task A3 — API Authentication & Rate Limiting

**Περιγραφή**: Create API key authentication & rate limiting.

**Deliverables:**
- `database/migrations/v2_2024_XX_XX_000001_create_api_keys_table.php`
- `app/Http/Middleware/ApiAuthMiddleware.php`
- `app/Http/Middleware/ApiRateLimitMiddleware.php`
- `app/Domain/Api/Services/ApiAuthService.php`
- `app/Domain/Api/Services/ApiRateLimitService.php`

**Technical Details:**

#### ApiAuthService
```php
class ApiAuthService {
    public function authenticate(string $key, string $secret): ?ApiKey
    {
        $apiKey = ApiKey::where('key', $key)
            ->where('is_active', true)
            ->first();
        
        if (!$apiKey) {
            return null;
        }
        
        // Verify secret
        if (!Hash::check($secret, $apiKey->secret)) {
            return null;
        }
        
        // Check expiration
        if ($apiKey->expires_at && $apiKey->expires_at->isPast()) {
            return null;
        }
        
        // Update last used
        $apiKey->update(['last_used_at' => now()]);
        
        return $apiKey;
    }
    
    public function hasScope(ApiKey $apiKey, string $scope): bool
    {
        $scopes = $apiKey->scopes ?? [];
        return in_array($scope, $scopes) || in_array('*', $scopes);
    }
}
```

#### ApiRateLimitService
```php
class ApiRateLimitService {
    public function check(Business $business, string $endpoint): bool
    {
        $key = "api:rate_limit:{$business->id}:{$endpoint}";
        $limit = config('api.rate_limit', 100); // per minute
        
        $count = Cache::get($key, 0);
        
        if ($count >= $limit) {
            return false;
        }
        
        Cache::put($key, $count + 1, now()->addMinute());
        return true;
    }
}
```

**Acceptance Criteria:**
- [x] API key authentication works
- [x] Secret verification works
- [x] Scope checking works
- [x] Rate limiting works
- [x] Expiration checking works

---

### Task A4 — API Endpoints (Read-only v1)

**Περιγραφή**: Create read-only API endpoints.

**Deliverables:**
- `app/Http/Controllers/Api/V2/BusinessController.php`
- `app/Http/Controllers/Api/V2/MenuController.php`
- `app/Http/Controllers/Api/V2/ProductsController.php`
- `app/Http/Controllers/Api/V2/CategoriesController.php`
- `app/Http/Controllers/Api/V2/PagesController.php`
- `app/Http/Resources/Api/V2/*` (Resources)

**Technical Details:**

#### API Routes
```php
// routes/api.php
Route::prefix('v2')->middleware(['api.auth', 'api.rate_limit'])->group(function () {
    Route::get('/business', [BusinessController::class, 'show']);
    Route::get('/menu', [MenuController::class, 'index']);
    Route::get('/categories', [CategoriesController::class, 'index']);
    Route::get('/categories/{category}', [CategoriesController::class, 'show']);
    Route::get('/products', [ProductsController::class, 'index']);
    Route::get('/products/{product}', [ProductsController::class, 'show']);
    Route::get('/pages', [PagesController::class, 'index']);
    Route::get('/pages/{slug}', [PagesController::class, 'show']);
});
```

#### Example Controller
```php
class MenuController extends Controller {
    public function index(Request $request) {
        $business = $request->attributes->get('business');
        
        $menu = app(GetMenuForBusinessService::class)->get($business);
        
        return MenuResource::collection($menu);
    }
}
```

**Acceptance Criteria:**
- [x] All endpoints return JSON
- [x] Business isolation enforced
- [x] Resources format data correctly
- [x] Pagination works
- [x] Error handling works

---

### Task A5 — Plugin Foundation (Spec + Demo)

**Περιγραφή**: Create plugin contract & demo plugin.

**Deliverables:**
- `app/Contracts/PluginInterface.php`
- `app/Domain/Plugins/Services/PluginRegistryService.php`
- `app/Plugins/Demo/NewsletterPlugin.php` (Demo)
- `config/plugins.php`

**Technical Details:**

#### PluginInterface
```php
interface PluginInterface {
    public function getName(): string;
    public function getVersion(): string;
    public function getDescription(): string;
    public function registerModules(): array; // ['newsletter' => NewsletterModule::class]
    public function boot(): void;
    public function getSettings(): array;
}
```

#### PluginRegistryService
```php
class PluginRegistryService {
    public function register(PluginInterface $plugin): void
    {
        // 1. Validate plugin
        $this->validatePlugin($plugin);
        
        // 2. Register modules
        $modules = $plugin->registerModules();
        foreach ($modules as $name => $class) {
            config(['modules.' . $name => [
                'name' => $name,
                'class' => $class,
                'plugin' => get_class($plugin),
            ]]);
        }
        
        // 3. Boot plugin
        $plugin->boot();
    }
    
    public function discover(): array
    {
        // Discover plugins from config/plugins.php
        $plugins = config('plugins', []);
        return array_map(fn($class) => app($class), $plugins);
    }
}
```

#### Demo Plugin
```php
class NewsletterPlugin implements PluginInterface {
    public function getName(): string { return 'Newsletter Signup'; }
    public function getVersion(): string { return '1.0.0'; }
    public function getDescription(): string { return 'Newsletter signup module'; }
    
    public function registerModules(): array {
        return [
            'newsletter' => NewsletterModule::class,
        ];
    }
    
    public function boot(): void {
        // Register routes, listeners, etc.
    }
    
    public function getSettings(): array {
        return [];
    }
}
```

**Acceptance Criteria:**
- [x] Plugin interface defined
- [x] Plugin registry service works
- [x] Demo plugin works
- [x] Modules registered from plugin

---

## Dev B — Architecture/Domain

### Task B1 — Isolation Tests

**Περιγραφή**: Write tests που εγγυώνται business isolation.

**Deliverables:**
- `tests/Feature/BusinessIsolationTest.php`

**Technical Details:**

```php
class BusinessIsolationTest extends TestCase {
    public function test_business_a_cannot_see_business_b_content(): void
    {
        $businessA = Business::factory()->create(['slug' => 'business-a']);
        $businessB = Business::factory()->create(['slug' => 'business-b']);
        
        $contentA = Content::factory()->create(['business_id' => $businessA->id, 'slug' => 'page-a']);
        $contentB = Content::factory()->create(['business_id' => $businessB->id, 'slug' => 'page-b']);
        
        // Access business A content
        $response = $this->get("/business-a/page-a");
        $response->assertStatus(200);
        $response->assertSee($contentA->title);
        
        // Cannot access business B content from business A context
        $response = $this->get("/business-a/page-b");
        $response->assertStatus(404);
    }
    
    public function test_modules_are_scoped_to_business(): void
    {
        // Test module isolation
    }
    
    public function test_media_is_scoped_to_business(): void
    {
        // Test media isolation
    }
}
```

**Acceptance Criteria:**
- [x] All isolation tests pass
- [x] No data leakage between businesses
- [x] 404 for cross-business access

---

### Task B2 — Cache Invalidation Service

**Περιγραφή**: Create unified cache invalidation service.

**Deliverables:**
- `app/Domain/Cache/Services/CacheInvalidationService.php`

**Technical Details:**

```php
class CacheInvalidationService {
    public function invalidateContent(Content $content): void
    {
        Cache::tags([
            'content',
            "content:{$content->id}",
            "business:{$content->business_id}",
            "layout:{$content->layout_id}",
        ])->flush();
    }
    
    public function invalidateModule(ModuleInstance $module): void
    {
        Cache::tags([
            'module',
            "module:{$module->id}",
            "business:{$module->business_id}",
        ])->flush();
    }
    
    public function invalidateBusiness(Business $business): void
    {
        Cache::tags(["business:{$business->id}"])->flush();
    }
    
    public function invalidateTheme(Business $business): void
    {
        Cache::tags([
            "business:{$business->id}",
            "theme:{$business->getTheme()}",
        ])->flush();
    }
}
```

**Acceptance Criteria:**
- [x] Cache invalidation works
- [x] Tags used correctly
- [x] No stale cache

---

### Task B3 — Performance Audit (Eager Loading)

**Περιγραφή**: Audit & fix N+1 queries.

**Deliverables:**
- Update services με eager loading
- Document query patterns

**Technical Details:**

```php
// Example: GetModulesForRegionService
public function forContentRegion(Content $content, string $region): Collection
{
    return ContentModuleAssignment::where('content_id', $content->id)
        ->where('region', $region)
        ->with(['moduleInstance.business']) // Eager load
        ->orderBy('sort_order')
        ->get()
        ->map(fn($assignment) => $assignment->moduleInstance)
        ->filter(fn($module) => $module->enabled);
}
```

**Acceptance Criteria:**
- [x] No N+1 queries
- [x] Eager loading used
- [x] Query count optimized

---

## Dev C — Frontend/UI

### Task C1 — API Key Management UI (Filament)

**Περιγραφή**: Create Filament resource για API keys.

**Deliverables:**
- `app/Filament/Resources/ApiKeyResource.php`

**Technical Details:**

```php
class ApiKeyResource extends Resource {
    protected static ?string $model = ApiKey::class;
    
    public static function form(Form $form): Form {
        return $form->schema([
            TextInput::make('name')->required(),
            TextInput::make('key')
                ->default(fn() => Str::random(32))
                ->disabled(),
            TextInput::make('secret')
                ->default(fn() => Str::random(64))
                ->disabled(),
            TagsInput::make('scopes')
                ->suggestions(['read:menu', 'read:products', 'read:pages', '*']),
            DateTimePicker::make('expires_at'),
            Toggle::make('is_active'),
        ]);
    }
}
```

**Acceptance Criteria:**
- [x] CRUD for API keys
- [x] Key generation works
- [x] Scope selection works

---

### Task C2 — API Documentation Page

**Περιγραφή**: Create API documentation page (Blade).

**Deliverables:**
- `resources/views/admin/api-docs.blade.php`
- `app/Http/Controllers/Admin/ApiDocsController.php`

**Technical Details:**
- List all endpoints
- Show request/response examples
- Show authentication method
- Show rate limits

**Acceptance Criteria:**
- [x] Documentation page created
- [x] All endpoints documented
- [x] Examples provided

---

### Task C3 — Testing Dashboard (Optional)

**Περιγραφή**: Create testing dashboard για QA.

**Deliverables:**
- `resources/views/admin/testing.blade.php`

**Technical Details:**
- List all test suites
- Show test coverage
- Run tests button (optional)

**Acceptance Criteria:**
- [x] Testing dashboard created
- [x] Test status visible

---

## 📦 Deliverables (Definition of Done)

### Backend
- [x] Canonical routing implemented
- [x] Business resolution hardened
- [x] API authentication & rate limiting
- [x] API endpoints (read-only)
- [x] Plugin foundation (spec + demo)
- [x] Cache invalidation service
- [x] Performance audit complete

### Frontend
- [x] API key management UI
- [x] API documentation page

### Testing
- [x] Isolation tests pass
- [x] API tests pass
- [x] Performance tests pass

### Documentation
- [x] Deployment guide
- [x] Business onboarding guide
- [x] API documentation

---

## 🧠 Τι ΔΕΝ Κάνει το Sprint 6

- ❌ Full plugin marketplace
- ❌ Multi-domain routing (Sprint 7+)
- ❌ Frontend rewrite (React/Vue)
- ❌ Complex permissions redesign

---

## 📝 Sprint Notes

_Καταγράψε εδώ progress, decisions, issues_

---

## 📚 References

- [v2 Overview](../v2_overview.md) — Architecture & strategy
- [Sprint 4](../sprint_4/sprint_4.md) — Layout System
- [Sprint 5](../sprint_5/sprint_5.md) — Theming
- [Developer Responsibilities](../dev-responsibilities.md) ⭐

---

**Last Updated**: 2024-11-27

