# MVC Best Practices

**Last Updated**: 2025-01-27  
**Status**: ✅ Complete

---

## 📋 Overview

Best practices για MVC implementation στο project. Includes guidelines, examples, και anti-patterns.

---

## 🎯 Controller Guidelines

### ✅ Do's

1. **Keep Controllers Thin**
   ```php
   // ✅ Good: Delegate to Service
   public function store(StoreRequest $request): RedirectResponse {
       $this->authorize('create', Resource::class);
       $resource = $this->createService->execute($request->validated());
       return redirect()->route('admin.resource.show', $resource);
   }
   
   // ❌ Bad: Business logic in Controller
   public function store(StoreRequest $request): RedirectResponse {
       // Complex validation, processing, etc. in Controller
   }
   ```

2. **Use Form Requests for Validation**
   ```php
   // ✅ Good: Form Request
   public function store(StoreResourceRequest $request): RedirectResponse {
       $validated = $request->validated(); // Already validated
   }
   
   // ❌ Bad: Manual validation
   public function store(Request $request): RedirectResponse {
       $request->validate([...]); // Validation in Controller
   }
   ```

3. **Use Policies for Authorization**
   ```php
   // ✅ Good: Policy
   public function update(UpdateRequest $request, Resource $resource): RedirectResponse {
       $this->authorize('update', $resource); // Policy handles authorization
   }
   
   // ❌ Bad: Manual checks
   public function update(UpdateRequest $request, Resource $resource): RedirectResponse {
       if (!auth()->user()->can('update', $resource)) { // Manual check
           abort(403);
       }
   }
   ```

4. **Return Appropriate Responses**
   ```php
   // ✅ Good: Type hints
   public function index(): View {
       return view('admin.resource.index', compact('resources'));
   }
   
   public function store(StoreRequest $request): RedirectResponse {
       return redirect()->route('admin.resource.show', $resource);
   }
   ```

### ❌ Anti-Patterns

1. **Fat Controllers**
   - Business logic in Controllers
   - Complex queries in Controllers
   - Multiple responsibilities

2. **Missing Authorization**
   - No `$this->authorize()` calls
   - Manual permission checks
   - Inconsistent authorization

3. **Direct Model Queries**
   - Complex queries in Controllers
   - No scopes used
   - Business logic mixed with queries

---

## 🎯 Service Layer Guidelines

### ✅ Do's

1. **Business Logic in Services**
   ```php
   // ✅ Good: Service handles business logic
   class CreateContentService {
       public function execute(array $data): Content {
           // Process blocks
           // Set defaults
           // Create revision
           return Content::create($processedData);
       }
   }
   ```

2. **Services are Reusable**
   ```php
   // ✅ Good: Service used by both Blade and Filament
   // In Blade Controller
   $content = $this->createService->execute($data);
   
   // In Filament Resource (if needed)
   $content = app(CreateContentService::class)->execute($data);
   ```

3. **Services in Domain Folders**
   ```php
   // ✅ Good: Organized by domain
   app/Domain/Content/Services/CreateContentService.php
   app/Domain/Media/Services/UploadMediaService.php
   ```

### ❌ Anti-Patterns

1. **Services in Controllers**
   - Business logic in Controllers instead of Services
   - Services not used

2. **Services Too Generic**
   - One service doing everything
   - No single responsibility

---

## 🎯 Model Guidelines

### ✅ Do's

1. **Use Relationships**
   ```php
   // ✅ Good: Relationship
   $content->revisions()->create([...]);
   
   // ❌ Bad: Manual join
   ContentRevision::where('content_id', $content->id)->create([...]);
   ```

2. **Use Scopes for Common Queries**
   ```php
   // ✅ Good: Scope
   Content::forBusiness($businessId)->published()->get();
   
   // ❌ Bad: Repeated queries
   Content::where('business_id', $businessId)
       ->where('status', 'published')
       ->get(); // Repeated everywhere
   ```

3. **Keep Models Focused**
   ```php
   // ✅ Good: Single responsibility
   class Content extends Model {
       // Content-specific methods
   }
   
   // ❌ Bad: Multiple responsibilities
   class Content extends Model {
       // Content methods
       // Media methods
       // Order methods
   }
   ```

4. **Use Accessors/Mutators**
   ```php
   // ✅ Good: Accessor
   public function getFullNameAttribute(): string {
       return "{$this->first_name} {$this->last_name}";
   }
   ```

### ❌ Anti-Patterns

1. **Manual Joins**
   - Not using relationships
   - Complex queries in Controllers

2. **No Scopes**
   - Repeated queries
   - Inconsistent filtering

3. **Fat Models**
   - Too many responsibilities
   - Business logic in Models

---

## 🎯 View Guidelines

### ✅ Do's

1. **Use Blade Components**
   ```blade
   {{-- ✅ Good: Reusable component --}}
   <x-admin-layout>
       <x-slot name="title">Page Title</x-slot>
       <!-- Content -->
   </x-admin-layout>
   ```

2. **Keep Views Simple**
   ```blade
   {{-- ✅ Good: Simple view --}}
   @foreach($items as $item)
       <div>{{ $item->name }}</div>
   @endforeach
   
   {{-- ❌ Bad: Complex logic in view --}}
   @php
       $processed = [];
       foreach($items as $item) {
           // Complex processing
       }
   @endphp
   ```

3. **Use Layouts Consistently**
   ```blade
   {{-- ✅ Good: Consistent layout --}}
   <x-admin-layout>
       <!-- Content -->
   </x-admin-layout>
   ```

4. **Extract Complex Logic**
   ```php
   // ✅ Good: Logic in Controller/Service
   $processedItems = $this->processService->process($items);
   
   // View
   @foreach($processedItems as $item)
       <!-- Display -->
   @endforeach
   ```

### ❌ Anti-Patterns

1. **Complex Logic in Views**
   - Business logic in Blade
   - Database queries in views
   - Complex PHP in views

2. **Inconsistent Layouts**
   - Different layouts for similar pages
   - No layout reuse

---

## 📝 Examples from Project

### ✅ Good Example: ContentController

```php
class ContentController extends Controller {
    public function __construct(
        private CreateContentService $createService,
        private UpdateContentService $updateService,
    ) {}
    
    public function store(StoreContentRequest $request): RedirectResponse {
        $this->authorize('create', Content::class);
        $content = $this->createService->execute($request->validated());
        return redirect()->route('admin.content.show', $content);
    }
}
```

**Why Good**:
- Thin controller (delegates to Service)
- Uses Form Request (validation)
- Uses Policy (authorization)
- Clear return type

---

### ✅ Good Example: CreateContentService

```php
class CreateContentService {
    public function execute(array $data): Content {
        // Process blocks
        $blocks = $this->processBlocks($data['blocks'] ?? []);
        
        // Set defaults
        $data['body_json'] = $blocks;
        $data['status'] = $data['status'] ?? 'draft';
        
        // Create content
        $content = Content::create($data);
        
        // Create initial revision
        $content->revisions()->create([...]);
        
        return $content;
    }
}
```

**Why Good**:
- Business logic in Service
- Reusable
- Single responsibility

---

### ❌ Anti-Pattern Example

```php
// ❌ Bad: Fat Controller
class ContentController extends Controller {
    public function store(Request $request): RedirectResponse {
        // Manual validation
        $request->validate([...]);
        
        // Business logic in Controller
        $blocks = [];
        foreach($request->input('blocks') as $block) {
            // Complex processing
        }
        
        // Direct model creation
        $content = Content::create([...]);
        
        // More logic...
        
        return redirect()->back();
    }
}
```

**Why Bad**:
- Business logic in Controller
- Manual validation (should use Form Request)
- No Service layer
- Complex logic in Controller

---

## 🎯 Summary

### Controller Best Practices
- ✅ Thin controllers (delegate to Services)
- ✅ Use Form Requests
- ✅ Use Policies
- ✅ Clear return types

### Service Best Practices
- ✅ Business logic in Services
- ✅ Reusable across Controllers
- ✅ Single responsibility
- ✅ Domain-organized

### Model Best Practices
- ✅ Use relationships
- ✅ Use scopes
- ✅ Keep focused
- ✅ Accessors/Mutators when needed

### View Best Practices
- ✅ Use components
- ✅ Keep simple
- ✅ Consistent layouts
- ✅ Extract complex logic

---

## 📚 Related Documentation

- [MVC Inventory](./mvc_inventory.md) — Current status
- [MVC Flow](./mvc_flow.md) — Flow examples
- [MVC Checklist](./mvc_checklist.md) — Template
- [Hybrid Admin Guidelines](../sprints/sprint_4.5/sprint_4.5.md) — Filament vs Blade

---

**Last Updated**: 2025-01-27

