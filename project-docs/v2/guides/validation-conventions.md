# Validation Conventions

## Overview

This guide explains how to use validation rules in LaraShop. We use a **Configuration Pattern** with a **Helper Class** to ensure consistent validation across FormRequests.

## Configuration

Common validation rules are defined in `config/validation.php`:

```php
'rules' => [
    'email' => ['required', 'string', 'email', 'max:255'],
    'name' => ['required', 'string', 'max:255'],
    'password' => ['required', 'confirmed', Rules\Password::defaults()],
    'slug' => ['required', 'string', 'max:255', 'regex:/^[a-z0-9-]+$/'],
    // ...
],
```

## Usage

### ✅ DO: Use ValidationHelper

```php
use App\Support\ValidationHelper;

public function rules(): array
{
    return [
        'name' => ValidationHelper::name(),
        'email' => ValidationHelper::email(),
        'email' => ValidationHelper::emailUnique($userId), // With unique constraint
        'password' => ValidationHelper::password(),
        'slug' => ValidationHelper::slug(),
        'status' => ValidationHelper::status(), // Uses ContentStatusHelper
        'price' => ValidationHelper::price(),
        'is_active' => ValidationHelper::boolean(),
    ];
}
```

### ❌ DON'T: Duplicate Validation Rules

```php
// ❌ BAD
'name' => ['required', 'string', 'max:255'],
'email' => ['required', 'string', 'email', 'max:255'],
'password' => ['required', 'confirmed', Rules\Password::defaults()],

// ✅ GOOD
'name' => ValidationHelper::name(),
'email' => ValidationHelper::email(),
'password' => ValidationHelper::password(),
```

## Available Methods

### Basic Fields

- `ValidationHelper::name()` - Name field (required, string, max:255)
- `ValidationHelper::email()` - Email field (required, string, email, max:255)
- `ValidationHelper::emailUnique(?int $ignoreUserId = null)` - Email with unique constraint
- `ValidationHelper::password()` - Password field (required, confirmed, Password rules)
- `ValidationHelper::passwordCurrent()` - Current password validation
- `ValidationHelper::slug()` - Slug field (required, string, max:255, regex)
- `ValidationHelper::slugOptional()` - Optional slug field
- `ValidationHelper::description()` - Optional description field
- `ValidationHelper::descriptionRequired()` - Required description field

### Status & Content

- `ValidationHelper::status()` - Content status (uses ContentStatusHelper)
- `ValidationHelper::statusOptional()` - Optional content status

### Numbers & Booleans

- `ValidationHelper::price()` - Price field (required, numeric, min:0)
- `ValidationHelper::priceOptional()` - Optional price field
- `ValidationHelper::sortOrder()` - Sort order field (nullable, integer, min:0)
- `ValidationHelper::boolean()` - Boolean field (sometimes, boolean)
- `ValidationHelper::booleanRequired()` - Required boolean field

## Combining Rules

You can combine `ValidationHelper` rules with additional rules:

```php
use App\Support\ValidationHelper;
use Illuminate\Validation\Rule;

public function rules(): array
{
    return [
        'title' => array_merge(['sometimes'], ValidationHelper::name()),
        'slug' => [
            'sometimes',
            'string',
            'max:255',
            Rule::unique('contents', 'slug')->ignore($this->route('content')),
        ],
    ];
}
```

## Custom Rules

For custom validation rules not covered by `ValidationHelper`, define them in your FormRequest:

```php
public function rules(): array
{
    return [
        'custom_field' => ['required', 'string', 'custom_rule'],
    ];
}
```

## Migration Checklist

When migrating existing code:

- [ ] Replace duplicate `'name'` rules with `ValidationHelper::name()`
- [ ] Replace duplicate `'email'` rules with `ValidationHelper::email()`
- [ ] Replace duplicate `'password'` rules with `ValidationHelper::password()`
- [ ] Replace duplicate `'slug'` rules with `ValidationHelper::slug()`
- [ ] Replace duplicate `'status'` rules with `ValidationHelper::status()`
- [ ] Replace duplicate `'price'` rules with `ValidationHelper::price()`
- [ ] Replace duplicate `'boolean'` rules with `ValidationHelper::boolean()`
- [ ] Test all validation functionality

## Benefits

1. **Consistency**: All validation uses the same rules
2. **Maintainability**: Change validation rules in one place
3. **DRY Principle**: No duplicate validation code
4. **Type Safety**: Helper methods prevent errors
5. **Documentation**: Clear API for validation
