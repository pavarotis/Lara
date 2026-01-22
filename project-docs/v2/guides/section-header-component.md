# Section Header Component Guide

**Last Updated**: 2025-01-27  
**Purpose**: Prevent padding issues when creating custom section header components

---

## 🎯 Overview

When you need a Filament-style section that only displays heading and description (without content), use the custom `section-header` component instead of `<x-filament::section>` to avoid empty content divs.

---

## ✅ Correct Usage

### Using the Custom Component

```blade
<x-section-header 
    heading="Warning"
    description="Your description text here"
/>
```

### Component Location

The component is located at:
- `resources/views/components/section-header.blade.php`

---

## ⚠️ Common Mistakes

### ❌ WRONG: Using Filament Section Without Content

```blade
<x-filament::section>
    <x-slot name="heading">Warning</x-slot>
    <x-slot name="description">Description</x-slot>
    <!-- No content = empty fi-section-content div -->
</x-filament::section>
```

**Problem**: Creates an empty `<div class="fi-section-content"></div>` that looks broken.

### ✅ CORRECT: Use Custom Component

```blade
<x-section-header 
    heading="Warning"
    description="Description"
/>
```

**Result**: Clean section with only header, no empty divs.

---

## 🔧 Component Structure

The `section-header` component uses **inline styles** for padding and spacing to ensure consistency:

```blade
<div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
    <div class="fi-section-header-actions-container flex items-center gap-4 sm:px-6" 
         style="padding-top: 1rem; padding-bottom: 1.5rem; padding-left: 1.5rem; padding-right: 1.5rem;">
        <div class="grid gap-y-4">
            <h3 class="fi-section-header-heading text-base font-semibold leading-6 text-gray-950 dark:text-white" 
                style="margin-bottom: 0.25rem;">
                {{ $heading }}
            </h3>
            <p class="fi-section-header-description text-sm text-gray-500 dark:text-gray-400">
                {{ $description }}
            </p>
        </div>
    </div>
</div>
```

### Critical Padding Rules

**⚠️ IMPORTANT**: Always use **inline styles** for padding, not Tailwind classes:

- ❌ **DON'T**: `px-6 py-4 pb-6` (Tailwind classes may be overridden)
- ✅ **DO**: `style="padding-top: 1rem; padding-bottom: 1.5rem; padding-left: 1.5rem; padding-right: 1.5rem;"`

**Why**: Inline styles have higher specificity and won't be overridden by Filament's CSS.

### Spacing Between Heading and Description

**⚠️ IMPORTANT**: Use **inline styles** for margin-bottom on heading to control spacing:

- ❌ **DON'T**: `gap-y-1` or `gap-y-2` alone (may not work due to CSS conflicts)
- ✅ **DO**: Combine `gap-y-4` on grid container + `style="margin-bottom: 0.25rem;"` on heading

**Why**: Inline styles ensure the spacing is applied correctly and won't be overridden.

**Example**:
```blade
<div class="grid gap-y-4">
    <h3 style="margin-bottom: 0.25rem;">Heading</h3>
    <p>Description</p>
</div>
```

### Text Color Styling (For Description Text)

**⚠️ IMPORTANT**: When styling description text in other contexts (not in component), use **inline styles** for reliable color application:

- ❌ **DON'T**: `text-gray-400 dark:text-gray-500` (Tailwind classes may be overridden)
- ✅ **DO**: `style="color: rgb(156 163 175);"` + `class="dark:!text-gray-500"` (inline style + !important for dark mode)

**Why**: Inline styles have higher specificity. For dark mode, use `!important` with Tailwind classes.

**Example**:
```blade
<span style="color: rgb(156 163 175);" class="dark:!text-gray-500">
    Description text that needs to be lighter
</span>
```

---

## 📋 When to Use

### Use `<x-section-header>` when:
- ✅ You only need heading + description
- ✅ No content/buttons/forms inside
- ✅ You want to avoid empty content divs

### Use `<x-filament::section>` when:
- ✅ You have content (buttons, forms, grids, etc.)
- ✅ You need the full section structure

---

## 🔍 Verification Checklist

Before using the component, verify:

- [ ] Component exists at `resources/views/components/section-header.blade.php`
- [ ] Padding uses inline styles (not Tailwind classes)
- [ ] All 4 padding sides are specified (top, bottom, left, right)
- [ ] Heading has inline style for margin-bottom: `style="margin-bottom: 0.25rem;"`
- [ ] Grid container uses `gap-y-4` for spacing
- [ ] Component matches Filament section styling

---

## 🛠️ Creating a New Section Header Component

If you need to create a similar component:

1. **Copy the structure** from `section-header.blade.php`
2. **Always use inline styles** for padding:
   ```blade
   style="padding-top: 1rem; padding-bottom: 1.5rem; padding-left: 1.5rem; padding-right: 1.5rem;"
   ```
3. **Keep Filament classes** for consistency:
   - `fi-section`
   - `fi-section-header-actions-container`
   - `fi-section-header-heading`
   - `fi-section-header-description`
4. **Test visually** to ensure padding matches other sections

---

## 📝 Example Usage

```blade
<x-filament-panels::page>
    <!-- Regular section with content -->
    <x-filament::section>
        <x-slot name="heading">Cache Actions</x-slot>
        <x-slot name="description">Clear specific cache types</x-slot>
        <div class="grid grid-cols-2 gap-4">
            <x-filament::button>Clear Cache</x-filament::button>
        </div>
    </x-filament::section>

    <!-- Header-only section (no content) -->
    <x-section-header 
        heading="Warning"
        description="Clearing cache will temporarily slow down the site."
    />
</x-filament-panels::page>
```

---

## 🐛 Troubleshooting

### Padding Not Working?

1. **Check inline styles**: Ensure padding is in `style` attribute, not Tailwind classes
2. **Hard refresh**: Clear browser cache (Ctrl+F5)
3. **Verify structure**: Compare with working `section-header.blade.php`

### Spacing Between Heading and Description Not Working?

1. **Check inline styles**: Ensure heading has `style="margin-bottom: 0.25rem;"`
2. **Verify grid gap**: Ensure grid container has `gap-y-4` class
3. **Don't rely on Tailwind alone**: Use inline styles for critical spacing

### Text Color Not Changing?

1. **Use inline styles**: For light mode, use `style="color: rgb(156 163 175);"`
2. **Use !important for dark mode**: Add `class="dark:!text-gray-500"` with `!important`
3. **Hard refresh**: Clear browser cache (Ctrl+F5)

### Empty Div Still Appears?

- You're using `<x-filament::section>` instead of `<x-section-header>`
- Switch to the custom component

---

## 📚 Related Documentation

- [Filament 4 API Reference](./filament_4_api_reference.md)
- [UI Consistency Guidelines](../architecture/ui_consistency.md)
