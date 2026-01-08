# Hybrid Admin Panel Developer Guide

**Last Updated**: 2025-01-27  
**Status**: ✅ Complete

---

## 📋 Overview

Complete step-by-step guide για developers για το Hybrid Admin Panel (Filament + Blade). Includes quick start, common scenarios, best practices, και troubleshooting.

---

## 🚀 Quick Start

### Creating a Filament Resource

**Step 1**: Generate Resource
```bash
php artisan make:filament-resource Product
```

**Step 2**: Configure Form
```php
// app/Filament/Resources/ProductResource.php
public static function form(Schema $schema): Schema {
    return $schema->components([
        TextInput::make('name')->required(),
        TextInput::make('price')->numeric(),
        // ...
    ]);
}
```

**Step 3**: Configure Table
```php
public static function table(Table $table): Table {
    return $table
        ->columns([
            TextColumn::make('name'),
            TextColumn::make('price'),
            // ...
        ]);
}
```

**Step 4**: Navigation (Auto)
- Filament automatically adds to navigation
- No manual configuration needed

**Result**: ✅ Full CRUD with auto-generated UI

---

### Creating a Blade Controller

**Step 1**: Create Controller
```bash
php artisan make:controller Admin/CustomController
```

**Step 2**: Create Views
```bash
mkdir -p resources/views/admin/custom
touch resources/views/admin/custom/index.blade.php
touch resources/views/admin/custom/create.blade.php
touch resources/views/admin/custom/edit.blade.php
touch resources/views/admin/custom/show.blade.php
```

**Step 3**: Add Routes
```php
// routes/web.php
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('custom', Admin\CustomController::class);
});
```

**Step 4**: Add Navigation Link
```php
// app/Providers/Filament/AdminPanelProvider.php
->navigationItems([
    NavigationItem::make('Custom')
        ->url(route('admin.custom.index'))
        ->icon('heroicon-o-cog')
        ->group('CMS')
        ->sort(10),
])
```

**Result**: ✅ Custom UI with full control

---

## 📚 Common Scenarios

### Scenario 1: Standard CRUD

**Decision**: Use Filament Resource

**Steps**:
1. Generate Resource: `php artisan make:filament-resource Model`
2. Configure form/table
3. Done! (Navigation auto-added)

**Example**: User management, Role management

---

### Scenario 2: Custom Editor

**Decision**: Use Blade Controller

**Steps**:
1. Create Controller: `php artisan make:controller Admin/EditorController`
2. Create views with custom UI
3. Add routes
4. Add navigation link

**Example**: Content Editor, Media Library

---

### Scenario 3: Dashboard

**Decision**: Use Blade Controller (or Filament Dashboard)

**Steps**:
1. Create Controller: `php artisan make:controller Admin/DashboardController`
2. Create dashboard view
3. Add route
4. Add navigation link

**Example**: Custom dashboard with widgets

---

### Scenario 4: Hybrid (List + Custom Action)

**Decision**: Use Filament Resource + Blade Controller

**Steps**:
1. Create Filament Resource for list/edit
2. Create Blade Controller for custom action
3. Add custom action in Filament table
4. Link to Blade route

**Example**: Module management (list in Filament, assignment in Blade)

---

## 🎯 Best Practices

### Use Services for Business Logic

**✅ Good**:
```php
// Controller
public function store(StoreRequest $request): RedirectResponse {
    $service = app(CreateService::class);
    $model = $service->execute($request->validated());
    return redirect()->route('admin.model.show', $model);
}
```

**❌ Bad**:
```php
// Controller with business logic
public function store(StoreRequest $request): RedirectResponse {
    // Complex business logic here
    $model = Model::create([...]);
    // More logic...
    return redirect()->back();
}
```

---

### Share Services Between Filament and Blade

**✅ Good**:
```php
// Service used by both
class GetContentService {
    public function bySlug(string $slug): ?Content {
        return Content::where('slug', $slug)->first();
    }
}

// Filament Resource
$service = app(GetContentService::class);
$content = $service->bySlug($slug);

// Blade Controller
public function show(GetContentService $service, string $slug): View {
    $content = $service->bySlug($slug);
    return view('admin.content.show', compact('content'));
}
```

---

### Keep Controllers Thin

**✅ Good**: Controller delegates to Service
**❌ Bad**: Controller contains business logic

---

### Use Policies for Authorization

**✅ Good**:
```php
// Controller
public function index(): View {
    $this->authorize('viewAny', Model::class);
    // ...
}
```

**❌ Bad**:
```php
// Controller with manual checks
public function index(): View {
    if (!auth()->user()->hasRole('admin')) {
        abort(403);
    }
    // ...
}
```

---

## 🔧 Troubleshooting

### Issue: Filament Resource Not Showing in Navigation

**Solution**:
1. Check `AdminPanelProvider` configuration
2. Ensure Resource has `$navigationGroup` set
3. Clear cache: `php artisan optimize:clear`

---

### Issue: Blade View Not Matching Filament Style

**Solution**:
1. Use Filament color classes (`bg-primary`, `text-primary`)
2. Use consistent spacing (`p-6`, `space-y-6`)
3. Use Filament button components
4. Check [UI Consistency Guide](../architecture/ui_consistency.md)

---

### Issue: Can't Link from Filament to Blade

**Solution**:
1. Ensure route exists: `php artisan route:list`
2. Use correct route name: `route('admin.custom.index')`
3. Check middleware matches

---

### Issue: Shared Service Not Working

**Solution**:
1. Ensure Service is in `app/Domain/{Domain}/Services/`
2. Use dependency injection: `public function __construct(Service $service)`
3. Or use service locator: `app(Service::class)`

---

## 📝 Step-by-Step Examples

### Example 1: Create Filament Resource

```bash
# 1. Generate Resource
php artisan make:filament-resource Product

# 2. Edit Resource
# app/Filament/Resources/ProductResource.php
public static function form(Schema $schema): Schema {
    return $schema->components([
        TextInput::make('name')->required(),
        TextInput::make('price')->numeric()->required(),
    ]);
}

# 3. Done! (Navigation auto-added)
```

---

### Example 2: Create Blade Controller

```bash
# 1. Generate Controller
php artisan make:controller Admin/CustomController

# 2. Create Views
mkdir -p resources/views/admin/custom
# Create index.blade.php, create.blade.php, etc.

# 3. Add Routes
# routes/web.php
Route::resource('custom', Admin\CustomController::class);

# 4. Add Navigation
# app/Providers/Filament/AdminPanelProvider.php
->navigationItems([
    NavigationItem::make('Custom')
        ->url(route('admin.custom.index'))
        ->icon('heroicon-o-cog'),
])
```

---

### Example 3: Hybrid Approach

```bash
# 1. Create Filament Resource (list/edit)
php artisan make:filament-resource ModuleInstance

# 2. Create Blade Controller (custom action)
php artisan make:controller Admin/ContentModuleController

# 3. Add Custom Action in Filament Resource
Action::make('assign')
    ->url(fn ($record) => route('admin.content.modules.index', $record))

# 4. Create Blade Route
Route::get('/admin/content/{content}/modules', [ContentModuleController::class, 'index']);
```

---

## 📚 Related Documentation

- [Decision Tree](../architecture/hybrid_admin_decision_tree.md) — When to use Filament vs Blade
- [Hybrid Patterns](../architecture/hybrid_patterns.md) — Reusable patterns
- [Integration Guide](../architecture/filament_blade_integration.md) — Integration details
- [UI Consistency](../architecture/ui_consistency.md) — UI/UX guidelines
- [Real Examples](./hybrid_admin_examples.md) — More examples

---

## 🎓 Learning Resources

### Filament Documentation
- [Filament v4 Docs](https://filamentphp.com/docs)
- [Filament Resources](https://filamentphp.com/docs/resources)
- [Filament Pages](https://filamentphp.com/docs/pages)

### Laravel Documentation
- [Laravel Controllers](https://laravel.com/docs/controllers)
- [Laravel Views](https://laravel.com/docs/views)
- [Laravel Routing](https://laravel.com/docs/routing)

---

**Last Updated**: 2025-01-27

