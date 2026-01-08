# MVC Flow Documentation

**Last Updated**: 2025-01-27  
**Status**: ✅ Complete

---

## 📋 Overview

Documentation του MVC flow για κάθε domain στο project. Includes standard flow, examples, και special cases.

---

## 🔄 Standard MVC Flow

```
Request
  ↓
Route (routes/web.php)
  ↓
Controller (app/Http/Controllers/)
  ↓
Service (app/Domain/{Domain}/Services/) [Optional]
  ↓
Model (app/Domain/{Domain}/Models/)
  ↓
Database
  ↓
View (resources/views/)
```

---

## 📚 Domain-Specific Flows

### Content Domain

#### Standard Flow: List Content

```
1. Request: GET /admin/content
   ↓
2. Route: Route::get('/admin/content', [ContentController::class, 'index'])
   ↓
3. Controller: Admin\ContentController@index
   - Authorization: $this->authorize('viewAny', Content::class)
   - Service: GetContentService (optional, can query directly)
   ↓
4. Model: Content::where('business_id', $business->id)->get()
   ↓
5. View: resources/views/admin/content/index.blade.php
   - Receives: $contents (Collection), $business
   - Renders: List of content items
```

#### Standard Flow: Create Content

```
1. Request: POST /admin/content
   ↓
2. Route: Route::post('/admin/content', [ContentController::class, 'store'])
   ↓
3. Controller: Admin\ContentController@store
   - Validation: StoreContentRequest
   - Authorization: $this->authorize('create', Content::class)
   - Service: CreateContentService::execute($validated)
   ↓
4. Service: CreateContentService
   - Business logic: Process blocks, set defaults
   - Model: Content::create($data)
   ↓
5. Redirect: route('admin.content.show', $content)
```

#### Revision Flow: Restore Revision

```
1. Request: POST /admin/content/{content}/revisions/{revision}/restore
   ↓
2. Route: Route::post('/admin/content/{content}/revisions/{revision}/restore', ...)
   ↓
3. Controller: Admin\ContentRevisionController@restore
   - Authorization: $this->authorize('update', $content)
   - Service: CreateRevisionService (create backup)
   - Model: $revision->restore() (updates Content)
   ↓
4. Redirect: route('admin.content.show', $content)
```

---

### Media Domain

#### Standard Flow: Upload Media

```
1. Request: POST /admin/media
   ↓
2. Route: Route::post('/admin/media', [MediaController::class, 'store'])
   ↓
3. Controller: Admin\MediaController@store
   - Validation: StoreMediaRequest
   - Authorization: $this->authorize('create', Media::class)
   - Service: UploadMediaService::execute($file, $folder)
   ↓
4. Service: UploadMediaService
   - Business logic: Generate variants, store file
   - Model: Media::create($data)
   ↓
5. Redirect: route('admin.media.index')
```

---

### Catalog Domain

#### Standard Flow: List Products

```
1. Request: GET /admin/products
   ↓
2. Route: Route::get('/admin/products', [ProductController::class, 'index'])
   ↓
3. Controller: Admin\ProductController@index
   - Service: GetActiveProductsService (optional)
   - Model: Product::where('business_id', $business->id)->get()
   ↓
4. View: resources/views/admin/products/index.blade.php
```

---

### Filament Resources Flow

#### Filament Resource: User Management

```
1. Request: GET /admin/users
   ↓
2. Filament Auto-Route: filament.admin.resources.users.index
   ↓
3. Resource: UserResource
   - Table: UsersTable::configure($table)
   - Model: User::query()
   ↓
4. Filament View: Auto-generated table view
   - No manual view needed
```

#### Filament Resource: Module Instance

```
1. Request: GET /admin/module-instances
   ↓
2. Filament Auto-Route: filament.admin.resources.module-instances.index
   ↓
3. Resource: ModuleInstanceResource
   - Form: form() method with Schema
   - Table: table() method with Table
   - Model: ModuleInstance::query()
   ↓
4. Filament View: Auto-generated
```

---

## 🔀 Special Cases

### Service-Based Models

**Example: Layout**

```
1. Request: Internal (via Service)
   ↓
2. Service: GetLayoutService::forBusiness($businessId, $type)
   - Model: Layout::forBusiness($businessId)->ofType($type)->first()
   ↓
3. Usage: Used in ContentController, not direct route
```

**Example: Business Settings**

```
1. Request: Internal (via Service)
   ↓
2. Service: GetBusinessSettingsService::getThemeColors($business)
   - Model: Business::find($id)
   - Settings: Access via relationships
   ↓
3. Usage: Used in views, not direct CRUD
```

---

### Embedded/Junction Models

**Example: ContentModuleAssignment**

```
1. Request: POST /admin/content/{content}/modules
   ↓
2. Controller: Admin\ContentModuleController@addModule
   - Service: AssignModuleToContentService
   - Model: ContentModuleAssignment::create([...])
   ↓
3. View: Embedded in resources/views/admin/content/modules.blade.php
   - No standalone view
```

---

### Hybrid Flow (Filament + Blade)

**Example: Module Management**

```
1. List/Edit: Filament Resource
   - Route: /admin/module-instances (Filament)
   - Resource: ModuleInstanceResource
   
2. Assignment UI: Blade Controller
   - Route: /admin/content/{content}/modules
   - Controller: Admin\ContentModuleController
   - View: resources/views/admin/content/modules.blade.php
```

---

## 📝 Flow Patterns

### Pattern 1: Standard CRUD (Blade)

```php
// Route
Route::get('/admin/resource', [ResourceController::class, 'index']);

// Controller
public function index(): View {
    $this->authorize('viewAny', Resource::class);
    $resources = Resource::where(...)->get();
    return view('admin.resource.index', compact('resources'));
}
```

### Pattern 2: Service-Based (Blade)

```php
// Route
Route::post('/admin/resource', [ResourceController::class, 'store']);

// Controller
public function store(StoreRequest $request): RedirectResponse {
    $this->authorize('create', Resource::class);
    $resource = $this->createService->execute($request->validated());
    return redirect()->route('admin.resource.show', $resource);
}
```

### Pattern 3: Filament Resource

```php
// Auto-routed by Filament
// Resource
class ResourceResource extends Resource {
    public static function form(Schema $schema): Schema { ... }
    public static function table(Table $table): Table { ... }
}
```

---

## 🎯 Best Practices

### Controller Guidelines
- ✅ Keep controllers thin (delegate to Services)
- ✅ Use Form Requests for validation
- ✅ Use Policies for authorization
- ✅ Return appropriate responses

### Service Guidelines
- ✅ Business logic in Services
- ✅ Services are reusable
- ✅ Services can be used by both Blade and Filament

### Model Guidelines
- ✅ Use relationships, not manual joins
- ✅ Use scopes for common queries
- ✅ Keep models focused

### View Guidelines
- ✅ Use Blade components for reusability
- ✅ Keep views simple (no complex logic)
- ✅ Use layouts consistently

---

**Last Updated**: 2025-01-27

