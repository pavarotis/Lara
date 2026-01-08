# Filament-Blade Integration Guide

**Last Updated**: 2025-01-27  
**Status**: ✅ Complete

---

## 📋 Overview

Comprehensive guide για πώς να συνδέσεις Filament με Blade features. Includes navigation, data sharing, component reuse, και best practices.

---

## 🔗 Linking from Filament to Blade

### Method 1: Custom Action URL

**Use Case**: Table action που opens Blade page.

**Example**: Module Instance → Assign to Content

```php
// app/Filament/Resources/ModuleInstanceResource.php
use Filament\Tables\Actions\Action;

public static function table(Table $table): Table {
    return $table
        ->actions([
            Action::make('assign_to_content')
                ->label('Assign to Content')
                ->url(fn ($record) => route('admin.content.modules.index', [
                    'content' => $record->id
                ]))
                ->icon('heroicon-o-link')
                ->openUrlInNewTab(false),
        ]);
}
```

**Blade Route**:
```php
// routes/web.php
Route::get('/admin/content/{content}/modules', [ContentModuleController::class, 'index'])
    ->name('admin.content.modules.index');
```

---

### Method 2: Navigation Link

**Use Case**: Add Blade page to Filament navigation.

**Example**: Content Editor in navigation

```php
// app/Providers/Filament/AdminPanelProvider.php
use Filament\Navigation\NavigationItem;

public function panel(Panel $panel): Panel {
    return $panel
        ->navigationItems([
            NavigationItem::make('Content Editor')
                ->url(route('admin.content.index'))
                ->icon('heroicon-o-document-text')
                ->group('CMS')
                ->sort(10),
        ]);
}
```

---

### Method 3: Button in Filament Page

**Use Case**: Filament page με link to Blade page.

**Example**: Settings page με link to advanced settings

```php
// app/Filament/Pages/CMS/Settings.php
use Filament\Actions\Action;

protected function getHeaderActions(): array {
    return [
        Action::make('advanced_settings')
            ->label('Advanced Settings')
            ->url(route('admin.settings.advanced'))
            ->icon('heroicon-o-cog-6-tooth'),
    ];
}
```

---

## 🔗 Linking from Blade to Filament

### Method 1: Direct Link

**Use Case**: Blade view με link to Filament Resource.

**Example**: Content show page με link to Users

```blade
{{-- resources/views/admin/content/show.blade.php --}}
<a href="{{ route('filament.admin.resources.users.index') }}" 
   class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-600">
    Manage Users
</a>
```

---

### Method 2: Filament Button Component

**Use Case**: Use Filament button styling in Blade.

**Example**: Filament-style button in Blade view

```blade
{{-- resources/views/admin/custom/page.blade.php --}}
<x-filament::button 
    href="{{ route('filament.admin.resources.products.index') }}"
    color="primary">
    View Products
</x-filament::button>
```

---

### Method 3: Navigation Breadcrumb

**Use Case**: Breadcrumb navigation to Filament pages.

**Example**: Content show page με breadcrumb

```blade
{{-- resources/views/admin/content/show.blade.php --}}
<nav class="flex items-center gap-2 text-sm text-gray-500 mb-2">
    <a href="{{ route('filament.admin.pages.cms.dashboard') }}" 
       class="hover:text-primary">CMS</a>
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
    </svg>
    <a href="{{ route('admin.content.index') }}" 
       class="hover:text-primary">Content</a>
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
    </svg>
    <span class="text-gray-900">{{ $content->title }}</span>
</nav>
```

---

## 🔄 Shared Data & Services

### Using Same Services

**Use Case**: Both Filament and Blade use same business logic.

**Example**: Content creation service

```php
// app/Domain/Content/Services/CreateContentService.php
class CreateContentService {
    public function execute(array $data): Content {
        // Business logic
        $content = Content::create($data);
        $content->revisions()->create([...]);
        return $content;
    }
}
```

**Filament Resource Usage**:
```php
// app/Filament/Resources/ContentResource.php (if exists)
protected function mutateFormDataBeforeCreate(array $data): array {
    $service = app(CreateContentService::class);
    // Use service if needed
    return $data;
}
```

**Blade Controller Usage**:
```php
// app/Http/Controllers/Admin/ContentController.php
public function store(StoreContentRequest $request): RedirectResponse {
    $service = app(CreateContentService::class);
    $content = $service->execute($request->validated());
    return redirect()->route('admin.content.show', $content);
}
```

---

### Using Same Policies

**Use Case**: Consistent authorization.

**Example**: Content authorization

```php
// app/Domain/Content/Policies/ContentPolicy.php
class ContentPolicy {
    public function viewAny(User $user): bool {
        return $user->hasRole('admin');
    }
    
    public function view(User $user, Content $content): bool {
        return $user->hasRole('admin');
    }
}
```

**Filament Resource**: Auto-uses Policy (no code needed)

**Blade Controller**:
```php
// app/Http/Controllers/Admin/ContentController.php
public function index(): View {
    $this->authorize('viewAny', Content::class);
    // ...
}
```

---

## 🎨 Component Reuse

### Using Filament Components in Blade

**Use Case**: Filament styling in Blade views.

**Example**: Filament input in Blade form

```blade
{{-- resources/views/admin/custom/form.blade.php --}}
<form wire:submit="save">
    <x-filament::input.wrapper>
        <x-filament::input 
            type="text" 
            wire:model="name"
            label="Name"
            required
        />
    </x-filament::input.wrapper>
    
    <x-filament::button type="submit">
        Save
    </x-filament::button>
</form>
```

---

### Using Blade Components in Filament

**Use Case**: Custom Blade component in Filament view.

**Example**: Custom widget in Filament page

```blade
{{-- resources/views/filament/pages/cms/dashboard.blade.php --}}
<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Filament widgets --}}
        @livewire('filament.widgets.stats-overview')
        
        {{-- Custom Blade component --}}
        <x-admin.custom-widget />
    </div>
</x-filament-panels::page>
```

---

## 🔄 Navigation Integration

### Unified Navigation Structure

**Use Case**: Both Filament and Blade pages in same navigation.

**Implementation**:
```php
// app/Providers/Filament/AdminPanelProvider.php
public function panel(Panel $panel): Panel {
    return $panel
        ->navigationGroups([
            NavigationGroup::make('CMS')
                ->items([
                    // Filament Pages auto-added
                    // Blade links added manually
                ]),
        ])
        ->navigationItems([
            // Blade Controllers
            NavigationItem::make('Content Editor')
                ->url(route('admin.content.index'))
                ->icon('heroicon-o-document-text')
                ->group('CMS')
                ->sort(10),
            
            NavigationItem::make('Media Library')
                ->url(route('admin.media.index'))
                ->icon('heroicon-o-photo')
                ->group('CMS')
                ->sort(20),
        ]);
}
```

---

## 📝 Best Practices

### Do's
- ✅ Use Services for shared business logic
- ✅ Use Policies for shared authorization
- ✅ Keep navigation consistent
- ✅ Use Filament components when possible
- ✅ Document integration points

### Don'ts
- ❌ Don't duplicate business logic
- ❌ Don't mix authorization approaches
- ❌ Don't break navigation consistency
- ❌ Don't overcomplicate simple cases

---

## 🔍 Common Scenarios

### Scenario 1: Filament List → Blade Detail

**Use Case**: Filament table με custom detail view.

**Implementation**:
```php
// Filament Resource
Action::make('view_details')
    ->url(fn ($record) => route('admin.orders.show', $record));

// Blade Controller
Route::get('/admin/orders/{order}', [OrderController::class, 'show']);
```

---

### Scenario 2: Blade Form → Filament Resource

**Use Case**: Custom form creates record, redirects to Filament edit.

**Implementation**:
```php
// Blade Controller
public function store(StoreRequest $request): RedirectResponse {
    $record = Model::create($request->validated());
    return redirect()->route('filament.admin.resources.model.edit', $record);
}
```

---

### Scenario 3: Shared Dashboard

**Use Case**: Dashboard με Filament widgets + Blade content.

**Implementation**:
```blade
{{-- Blade view --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    @livewire('filament.widgets.stats-overview')
    <div class="bg-white rounded-xl p-6">
        <!-- Custom Blade content -->
    </div>
</div>
```

---

## 📚 Related Documentation

- [Decision Tree](./hybrid_admin_decision_tree.md) — When to use Filament vs Blade
- [Hybrid Patterns](./hybrid_patterns.md) — Reusable patterns
- [UI Consistency](./ui_consistency.md) — UI/UX guidelines
- [Developer Guide](../guides/hybrid_admin_developer_guide.md) — Step-by-step guide

---

**Last Updated**: 2025-01-27

