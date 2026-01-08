# Hybrid Admin Panel Patterns

**Last Updated**: 2025-01-27  
**Status**: ✅ Complete

---

## 📋 Overview

Library με reusable patterns για hybrid scenarios (Filament + Blade). Includes code examples, use cases, και best practices.

---

## 🔄 Pattern 1: Filament Resource with Custom Action

**Use Case**: Standard CRUD με custom action που opens Blade page.

**Example**: Module Instance list (Filament) → Module assignment (Blade)

### Implementation

```php
// app/Filament/Resources/ModuleInstanceResource.php
use Filament\Tables\Actions\Action;

public static function table(Table $table): Table {
    return $table
        ->actions([
            Action::make('assign_to_content')
                ->label('Assign to Content')
                ->url(fn ($record) => route('admin.content.modules.index', ['content' => $record->id]))
                ->icon('heroicon-o-link')
                ->openUrlInNewTab(false),
        ]);
}
```

### Blade Controller

```php
// app/Http/Controllers/Admin/ContentModuleController.php
public function index(Content $content): View {
    // Custom assignment UI
    return view('admin.content.modules', compact('content'));
}
```

### Benefits
- ✅ Standard CRUD in Filament
- ✅ Custom UI for complex action
- ✅ Seamless integration

---

## 🔄 Pattern 2: Blade Page with Filament Widget

**Use Case**: Custom dashboard με Filament widgets.

**Example**: Custom dashboard με Filament stats widgets.

### Implementation

```blade
{{-- resources/views/admin/dashboard/index.blade.php --}}
<x-admin-layout>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        {{-- Custom Blade content --}}
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold">Custom Widget</h3>
            <!-- Custom content -->
        </div>
        
        {{-- Filament Widget --}}
        @livewire('filament.widgets.stats-overview')
    </div>
</x-admin-layout>
```

### Benefits
- ✅ Custom dashboard layout
- ✅ Reuse Filament widgets
- ✅ Best of both worlds

---

## 🔄 Pattern 3: Filament Custom Page

**Use Case**: Custom functionality με Filament UI.

**Example**: Custom settings page με Filament components.

### Implementation

```php
// app/Filament/Pages/CMS/Settings.php
use Filament\Pages\Page;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;

class Settings extends Page implements HasForms
{
    use InteractsWithForms;
    
    protected static string $view = 'filament.pages.cms.settings';
    
    public ?array $data = [];
    
    public function mount(): void
    {
        $this->form->fill([
            'site_name' => setting('site_name'),
            // ...
        ]);
    }
    
    protected function getFormSchema(): array
    {
        return [
            TextInput::make('site_name')
                ->label('Site Name')
                ->required(),
            // ...
        ];
    }
    
    public function save(): void
    {
        $data = $this->form->getState();
        // Save settings
    }
}
```

### Benefits
- ✅ Filament UI consistency
- ✅ Custom functionality
- ✅ Form handling built-in

---

## 🔄 Pattern 4: Shared Services

**Use Case**: Same business logic για Filament και Blade.

**Example**: Content creation service used by both.

### Implementation

```php
// app/Domain/Content/Services/CreateContentService.php
class CreateContentService {
    public function execute(array $data): Content {
        // Business logic
        $content = Content::create($data);
        // Create revision
        $content->revisions()->create([...]);
        return $content;
    }
}
```

### Filament Resource Usage

```php
// app/Filament/Resources/ContentResource.php (if exists)
public static function form(Schema $schema): Schema {
    return $schema->components([
        // Form fields
    ]);
}

protected function mutateFormDataBeforeCreate(array $data): array {
    $service = app(CreateContentService::class);
    // Use service if needed
    return $data;
}
```

### Blade Controller Usage

```php
// app/Http/Controllers/Admin/ContentController.php
public function store(StoreContentRequest $request): RedirectResponse {
    $service = app(CreateContentService::class);
    $content = $service->execute($request->validated());
    return redirect()->route('admin.content.show', $content);
}
```

### Benefits
- ✅ Reusable business logic
- ✅ Consistent behavior
- ✅ Single source of truth

---

## 🔄 Pattern 5: Navigation Integration

**Use Case**: Both Filament and Blade pages in same navigation.

**Example**: Filament Resources + Blade Controllers in navigation.

### Implementation

```php
// app/Providers/Filament/AdminPanelProvider.php
use Filament\Navigation\NavigationItem;

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
            NavigationItem::make('Content Editor')
                ->url(route('admin.content.index'))
                ->icon('heroicon-o-document-text')
                ->group('CMS')
                ->sort(10),
        ]);
}
```

### Benefits
- ✅ Unified navigation
- ✅ Consistent UX
- ✅ Easy to maintain

---

## 🔄 Pattern 6: Blade View with Filament Components

**Use Case**: Custom view με Filament form components.

**Example**: Custom form με Filament inputs.

### Implementation

```blade
{{-- resources/views/admin/custom/form.blade.php --}}
<x-admin-layout>
    <form wire:submit="save">
        {{-- Filament Form Components --}}
        <x-filament::input.wrapper>
            <x-filament::input 
                type="text" 
                wire:model="name"
                label="Name"
            />
        </x-filament::input.wrapper>
        
        {{-- Custom Blade content --}}
        <div class="mt-4">
            <!-- Custom content -->
        </div>
    </form>
</x-admin-layout>
```

### Benefits
- ✅ Filament component styling
- ✅ Custom layout
- ✅ Consistent UI

---

## 🔄 Pattern 7: Filament Action → Blade Route

**Use Case**: Filament table action opens Blade page.

**Example**: "View Details" action opens custom view.

### Implementation

```php
// app/Filament/Resources/OrderResource.php
public static function table(Table $table): Table {
    return $table
        ->actions([
            Action::make('view_details')
                ->label('View Details')
                ->url(fn ($record) => route('admin.orders.show', $record))
                ->icon('heroicon-o-eye'),
        ]);
}
```

### Blade Route

```php
// routes/web.php
Route::get('/admin/orders/{order}', [OrderController::class, 'show'])
    ->name('admin.orders.show');
```

### Benefits
- ✅ Filament table consistency
- ✅ Custom detail view
- ✅ Seamless navigation

---

## 🔄 Pattern 8: Shared Policies

**Use Case**: Same authorization για Filament και Blade.

**Example**: Content authorization used by both.

### Implementation

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

### Filament Resource Usage

```php
// Filament auto-uses Policy
// No code needed
```

### Blade Controller Usage

```php
// app/Http/Controllers/Admin/ContentController.php
public function index(): View {
    $this->authorize('viewAny', Content::class);
    // ...
}
```

### Benefits
- ✅ Consistent authorization
- ✅ Single source of truth
- ✅ Easy to maintain

---

## 🔄 Pattern 9: Data Sharing via Services

**Use Case**: Same data source για Filament και Blade.

**Example**: Content data used by both.

### Implementation

```php
// app/Domain/Content/Services/GetContentService.php
class GetContentService {
    public function bySlug(int $businessId, string $slug): ?Content {
        return Content::where('business_id', $businessId)
            ->where('slug', $slug)
            ->first();
    }
}
```

### Usage in Both

```php
// Filament Resource
$service = app(GetContentService::class);
$content = $service->bySlug($businessId, $slug);

// Blade Controller
public function show(GetContentService $service, string $slug): View {
    $content = $service->bySlug($business->id, $slug);
    return view('admin.content.show', compact('content'));
}
```

### Benefits
- ✅ Consistent data access
- ✅ Reusable queries
- ✅ Single source of truth

---

## 🔄 Pattern 10: Hybrid Dashboard

**Use Case**: Dashboard με Filament widgets + custom Blade content.

### Implementation

```blade
{{-- resources/views/admin/dashboard/index.blade.php --}}
<x-admin-layout>
    <div class="space-y-6">
        {{-- Filament Stats Widgets --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            @livewire('filament.widgets.stats-overview')
        </div>
        
        {{-- Custom Blade Content --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold mb-4">Recent Activity</h3>
                <!-- Custom content -->
            </div>
            
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold mb-4">Quick Actions</h3>
                <!-- Custom actions -->
            </div>
        </div>
    </div>
</x-admin-layout>
```

### Benefits
- ✅ Filament widget consistency
- ✅ Custom dashboard layout
- ✅ Best of both worlds

---

## 📊 Pattern Summary

| Pattern | Use Case | Complexity | When to Use |
|---------|----------|------------|-------------|
| **Pattern 1** | Filament + Custom Action | Low | Standard CRUD + custom action |
| **Pattern 2** | Blade + Filament Widget | Medium | Custom dashboard with widgets |
| **Pattern 3** | Filament Custom Page | Medium | Custom functionality, Filament UI |
| **Pattern 4** | Shared Services | Low | Reusable business logic |
| **Pattern 5** | Navigation Integration | Low | Unified navigation |
| **Pattern 6** | Blade + Filament Components | Medium | Custom form with Filament styling |
| **Pattern 7** | Filament Action → Blade | Low | Table action to custom view |
| **Pattern 8** | Shared Policies | Low | Consistent authorization |
| **Pattern 9** | Data Sharing | Low | Consistent data access |
| **Pattern 10** | Hybrid Dashboard | Medium | Custom dashboard with widgets |

---

## 🎯 Best Practices

### Do's
- ✅ Use Services for shared business logic
- ✅ Use Policies for shared authorization
- ✅ Keep navigation consistent
- ✅ Reuse Filament components when possible
- ✅ Document pattern choices

### Don'ts
- ❌ Don't duplicate business logic
- ❌ Don't mix authorization approaches
- ❌ Don't break navigation consistency
- ❌ Don't overcomplicate simple cases

---

## 📚 Related Documentation

- [Decision Tree](./hybrid_admin_decision_tree.md) — When to use each pattern
- [Integration Guide](./filament_blade_integration.md) — Integration details
- [Developer Guide](../guides/hybrid_admin_developer_guide.md) — Step-by-step
- [Real Examples](../guides/hybrid_admin_examples.md) — More examples

---

**Last Updated**: 2025-01-27

