# Blade Layout vs Component Syntax Guide

## 🎯 Purpose

This guide helps developers understand when to use **Blade Layouts** (`@extends`/`@section`/`@yield`) vs **Blade Components** (`@component`/`$slot`), and how to avoid common mistakes.

---

## 📚 Quick Reference

### Blade Layouts (Traditional)
**Use when**: Creating page layouts that are extended by multiple views

```blade
{{-- layouts/public.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <title>@yield('title', 'Default Title')</title>
</head>
<body>
    <main>
        @yield('content')
    </main>
</body>
</html>

{{-- pages/home.blade.php --}}
@extends('layouts.public')

@section('title', 'Home Page')

@section('content')
    <h1>Welcome</h1>
@endsection
```

### Blade Components (Modern)
**Use when**: Creating reusable UI components (buttons, cards, modals, etc.)

```blade
{{-- components/button.blade.php --}}
<button class="{{ $class ?? 'btn' }}">
    {{ $slot }}
</button>

{{-- Usage --}}
<x-button class="btn-primary">Click Me</x-button>
```

---

## ⚠️ Common Mistakes

### ❌ Mistake 1: Mixing Layout and Component Syntax

**Wrong:**
```blade
{{-- layouts/public.blade.php --}}
<main>
    {{ $slot }}  {{-- Component syntax --}}
</main>

{{-- pages/home.blade.php --}}
@extends('layouts.public')  {{-- Layout syntax --}}
@section('content')
    <h1>Welcome</h1>
@endsection
```

**Error**: `Undefined variable $slot`

**Correct:**
```blade
{{-- layouts/public.blade.php --}}
<main>
    @yield('content')  {{-- Layout syntax --}}
</main>

{{-- pages/home.blade.php --}}
@extends('layouts.public')
@section('content')
    <h1>Welcome</h1>
@endsection
```

### ❌ Mistake 2: Using @yield in Components

**Wrong:**
```blade
{{-- components/card.blade.php --}}
<div class="card">
    @yield('content')  {{-- Layout syntax in component --}}
</div>
```

**Error**: `@yield` doesn't work in components

**Correct:**
```blade
{{-- components/card.blade.php --}}
<div class="card">
    {{ $slot }}  {{-- Component syntax --}}
</div>
```

---

## ✅ Decision Tree

```
Is this a PAGE LAYOUT (header, footer, main structure)?
├─ YES → Use Blade Layout (@extends/@section/@yield)
│   └─ Examples: layouts/public.blade.php, layouts/admin.blade.php
│
└─ NO → Is this a REUSABLE UI COMPONENT?
    ├─ YES → Use Blade Component (@component/$slot or <x-component>)
    │   └─ Examples: components/button.blade.php, components/card.blade.php
    │
    └─ NO → Use regular Blade view
```

---

## 🔍 Verification Checklist

Before committing any Blade view, verify:

### For Layouts (`layouts/*.blade.php`):
- [ ] Uses `@yield('section_name')` for content areas
- [ ] Does NOT use `{{ $slot }}`
- [ ] Can be extended with `@extends('layouts.name')`
- [ ] Child views use `@section('section_name')` to fill content

### For Components (`components/*.blade.php`):
- [ ] Uses `{{ $slot }}` for main content
- [ ] Can use named slots: `{{ $header }}`, `{{ $footer }}`
- [ ] Does NOT use `@yield()`
- [ ] Can be used with `<x-component>` syntax

### For Pages/Views that extend layouts:
- [ ] Uses `@extends('layouts.name')`
- [ ] Uses `@section('section_name')` to fill content
- [ ] Does NOT use `<x-layout>` syntax

---

## 📝 Examples from Codebase

### ✅ Correct: Layout Pattern
```blade
{{-- resources/views/layouts/public.blade.php --}}
<main>
    @yield('content')
</main>

{{-- resources/views/themes/default/layouts/page.blade.php --}}
@extends('layouts.public')
@section('content')
    {!! $renderedContent !!}
@endsection
```

### ✅ Correct: Component Pattern
```blade
{{-- resources/views/components/admin/media-picker.blade.php --}}
<div>
    {{ $slot }}
</div>

{{-- Usage --}}
<x-admin.media-picker>
    <p>Media picker content</p>
</x-admin.media-picker>
```

---

## 🛠️ Pre-Commit Checklist

Add this to your pre-commit checklist:

```markdown
### Blade Views Check
- [ ] Layout files use `@yield()`, not `{{ $slot }}`
- [ ] Component files use `{{ $slot }}`, not `@yield()`
- [ ] Pages that extend layouts use `@section()`, not component syntax
- [ ] No mixing of layout and component syntax in same file
```

---

## 🐛 Debugging Tips

If you see `Undefined variable $slot`:
1. Check if the file is a layout (`layouts/*.blade.php`)
2. If yes, change `{{ $slot }}` to `@yield('content')`
3. Verify child views use `@section('content')`

If you see `@yield() not working in component`:
1. Check if the file is a component (`components/*.blade.php`)
2. If yes, change `@yield()` to `{{ $slot }}`
3. Verify usage uses `<x-component>` syntax

---

## 📚 Additional Resources

- [Laravel Blade Layouts Documentation](https://laravel.com/docs/blade#layouts)
- [Laravel Blade Components Documentation](https://laravel.com/docs/blade#components)

---

## 🎓 Key Takeaways

1. **Layouts** = Page structure (header, footer, main) → Use `@extends`/`@section`/`@yield`
2. **Components** = Reusable UI pieces → Use `{{ $slot }}` or `<x-component>`
3. **Never mix** layout and component syntax in the same file
4. **Always verify** syntax matches the file type (layout vs component)

---

**Last Updated**: 2024-11-27  
**Created by**: Master DEV  
**Purpose**: Prevent layout/component syntax mixing errors

