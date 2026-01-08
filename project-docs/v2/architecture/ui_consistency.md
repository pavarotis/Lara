# UI/UX Consistency Guidelines

**Last Updated**: 2025-01-27  
**Status**: ✅ Complete

---

## 📋 Overview

Guidelines για consistent UI/UX μεταξύ Filament και Blade στο admin panel. Includes design tokens, component library, spacing/typography rules.

---

## 🎨 Design System

### Colors

**Primary Color**: Filament Amber
- Primary: `#f59e0b` (amber-500)
- Primary Dark: `#d97706` (amber-600)
- Primary Light: `#fbbf24` (amber-400)

**Usage in Tailwind**:
```blade
{{-- Use Filament color classes --}}
<button class="bg-primary text-white hover:bg-primary-600">
    Button
</button>

{{-- Or use Tailwind amber directly --}}
<div class="bg-amber-500 text-white">
    Content
</div>
```

**Color Palette**:
- Primary: `bg-primary`, `text-primary`
- Success: `bg-green-500`, `text-green-600`
- Danger: `bg-red-500`, `text-red-600`
- Warning: `bg-yellow-500`, `text-yellow-600`
- Info: `bg-blue-500`, `text-blue-600`

---

### Typography

**Headings**:
- H1: `text-3xl font-bold text-gray-900`
- H2: `text-2xl font-bold text-gray-900`
- H3: `text-xl font-semibold text-gray-900`
- H4: `text-lg font-semibold text-gray-900`

**Body Text**:
- Default: `text-base text-gray-700`
- Small: `text-sm text-gray-600`
- Muted: `text-sm text-gray-500`

**Font Stack**:
```css
font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
```

---

### Spacing

**Consistent Spacing Scale**:
- xs: `0.5rem` (8px)
- sm: `0.75rem` (12px)
- md: `1rem` (16px)
- lg: `1.5rem` (24px)
- xl: `2rem` (32px)
- 2xl: `3rem` (48px)

**Common Patterns**:
```blade
{{-- Card padding --}}
<div class="p-6">

{{-- Section spacing --}}
<div class="space-y-6">

{{-- Grid gap --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
```

---

## 🧩 Components

### Buttons

**Primary Button**:
```blade
<button class="px-4 py-2 bg-primary text-white font-medium rounded-lg hover:bg-primary-600 transition-colors">
    Button Text
</button>
```

**Secondary Button**:
```blade
<button class="px-4 py-2 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200 transition-colors">
    Button Text
</button>
```

**Danger Button**:
```blade
<button class="px-4 py-2 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 transition-colors">
    Delete
</button>
```

**Filament Button Component**:
```blade
<x-filament::button color="primary">
    Button Text
</x-filament::button>
```

---

### Cards

**Standard Card**:
```blade
<div class="bg-white rounded-xl shadow-sm p-6">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">Card Title</h3>
    <!-- Card content -->
</div>
```

**Card with Header**:
```blade
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900">Card Title</h3>
    </div>
    <div class="p-6">
        <!-- Card content -->
    </div>
</div>
```

---

### Forms

**Form Input**:
```blade
<div class="mb-4">
    <label class="block text-sm font-medium text-gray-700 mb-1">
        Label
    </label>
    <input type="text" 
           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary">
</div>
```

**Form Select**:
```blade
<div class="mb-4">
    <label class="block text-sm font-medium text-gray-700 mb-1">
        Label
    </label>
    <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary">
        <option>Option 1</option>
    </select>
</div>
```

**Filament Form Components**:
```blade
<x-filament::input.wrapper>
    <x-filament::input 
        type="text" 
        wire:model="name"
        label="Name"
    />
</x-filament::input.wrapper>
```

---

### Tables

**Standard Table**:
```blade
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Header
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        Content
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
```

---

### Navigation

**Breadcrumb Navigation**:
```blade
<nav class="flex items-center gap-2 text-sm text-gray-500 mb-2">
    <a href="{{ route('admin.dashboard') }}" class="hover:text-primary">Dashboard</a>
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
    </svg>
    <span class="text-gray-900">Current Page</span>
</nav>
```

---

## 🎯 Layout Patterns

### Page Layout

**Standard Page**:
```blade
<x-admin-layout>
    <x-slot name="title">Page Title</x-slot>
    
    <!-- Header -->
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Page Title</h2>
        <p class="text-gray-600 text-sm mt-1">Page description</p>
    </div>
    
    <!-- Content -->
    <div class="space-y-6">
        <!-- Content sections -->
    </div>
</x-admin-layout>
```

---

### Grid Layouts

**Two Column Grid**:
```blade
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <!-- Left column -->
    </div>
    <div class="bg-white rounded-xl shadow-sm p-6">
        <!-- Right column -->
    </div>
</div>
```

**Three Column Grid**:
```blade
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <!-- Three columns -->
</div>
```

---

## 📦 Reusable Components

### Admin Button Component

**Create**: `resources/views/components/admin/button.blade.php`

```blade
@props(['type' => 'button', 'variant' => 'primary', 'href' => null])

@if($href)
    <a href="{{ $href }}" 
       class="px-4 py-2 {{ $variant === 'primary' ? 'bg-primary text-white hover:bg-primary-600' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }} font-medium rounded-lg transition-colors">
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" 
            class="px-4 py-2 {{ $variant === 'primary' ? 'bg-primary text-white hover:bg-primary-600' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }} font-medium rounded-lg transition-colors">
        {{ $slot }}
    </button>
@endif
```

**Usage**:
```blade
<x-admin.button variant="primary">Save</x-admin.button>
<x-admin.button href="{{ route('admin.content.index') }}">Back</x-admin.button>
```

---

### Admin Card Component

**Create**: `resources/views/components/admin/card.blade.php`

```blade
@props(['title' => null])

<div class="bg-white rounded-xl shadow-sm p-6">
    @if($title)
        <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ $title }}</h3>
    @endif
    {{ $slot }}
</div>
```

**Usage**:
```blade
<x-admin.card title="Card Title">
    <!-- Card content -->
</x-admin.card>
```

---

## 🎨 Filament-Style Components

### Using Filament CSS Classes

**Filament provides CSS classes** that can be used in Blade views:

```blade
{{-- Filament button style --}}
<button class="fi-btn fi-btn-color-primary">
    Button
</button>

{{-- Filament input style --}}
<input class="fi-input" type="text">
```

**Note**: Check Filament documentation for available classes.

---

## 📝 Best Practices

### Do's
- ✅ Use consistent spacing scale
- ✅ Use Filament color palette
- ✅ Match Filament button styles
- ✅ Use consistent typography
- ✅ Create reusable components

### Don'ts
- ❌ Don't use custom colors (use Filament palette)
- ❌ Don't mix different spacing scales
- ❌ Don't break Filament consistency
- ❌ Don't create one-off components

---

## 🔍 Consistency Checklist

### Before Creating New View
- [ ] Uses Filament color palette
- [ ] Uses consistent spacing
- [ ] Uses standard typography
- [ ] Matches Filament button styles
- [ ] Uses reusable components
- [ ] Follows layout patterns

---

## 📚 Related Documentation

- [Decision Tree](./hybrid_admin_decision_tree.md) — When to use Filament vs Blade
- [Hybrid Patterns](./hybrid_patterns.md) — Reusable patterns
- [Integration Guide](./filament_blade_integration.md) — Integration details
- [Developer Guide](../guides/hybrid_admin_developer_guide.md) — Step-by-step

---

**Last Updated**: 2025-01-27

