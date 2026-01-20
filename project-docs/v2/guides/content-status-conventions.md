# Content Status Conventions

## Overview

This guide explains how to work with content statuses in LaraShop. We use a **Configuration Pattern** with a **Helper Class** to ensure consistent status handling across the application.

## Configuration

Content statuses are defined in `config/content.php`:

```php
'statuses' => [
    'draft' => 'draft',
    'published' => 'published',
    'archived' => 'archived',
],
'default_status' => 'draft',
```

## Usage

### ✅ DO: Use ContentStatusHelper

```php
use App\Support\ContentStatusHelper;

// Get all valid statuses
$statuses = ContentStatusHelper::all(); // ['draft', 'published', 'archived']

// Get default status
$default = ContentStatusHelper::default(); // 'draft'

// Get specific status
$published = ContentStatusHelper::published(); // 'published'
$draft = ContentStatusHelper::draft(); // 'draft'
$archived = ContentStatusHelper::archived(); // 'archived'

// Validation in FormRequests
'status' => ContentStatusHelper::validationRule()
'status' => ContentStatusHelper::nullableValidationRule()

// Check if status is valid
if (ContentStatusHelper::isValid($status)) {
    // ...
}

// Validate and throw exception if invalid
ContentStatusHelper::validate($status);
```

### ❌ DON'T: Hardcode Status Values

```php
// ❌ BAD
$content->status = 'published';
$query->where('status', 'draft');
'status' => ['sometimes', 'string', Rule::in(['draft', 'published', 'archived'])]

// ✅ GOOD
$content->status = ContentStatusHelper::published();
$query->where('status', ContentStatusHelper::draft());
'status' => ContentStatusHelper::validationRule()
```

## In Models

### Content Model Methods

The `Content` model provides helper methods that use `ContentStatusHelper` internally:

```php
// Scopes
Content::published()->get();
Content::draft()->get();
Content::archived()->get();

// Instance methods
$content->isPublished(); // bool
$content->isDraft(); // bool
$content->publish(); // Sets status to 'published'
$content->archive(); // Sets status to 'archived'
```

## In FormRequests

Always use `ContentStatusHelper` for validation:

```php
use App\Support\ContentStatusHelper;

public function rules(): array
{
    return [
        'status' => ContentStatusHelper::validationRule(), // For 'sometimes' updates
        // OR
        'status' => ContentStatusHelper::nullableValidationRule(), // For nullable fields
    ];
}
```

## In Services

Use `ContentStatusHelper` when setting default values:

```php
use App\Support\ContentStatusHelper;

$data['status'] = $data['status'] ?? ContentStatusHelper::default();
```

## Adding New Statuses

If you need to add a new status:

1. **Update `config/content.php`**:
```php
'statuses' => [
    'draft' => 'draft',
    'published' => 'published',
    'archived' => 'archived',
    'scheduled' => 'scheduled', // New status
],
```

2. **Add method to `ContentStatusHelper`** (optional but recommended):
```php
public static function scheduled(): string
{
    return config('content.statuses.scheduled', 'scheduled');
}
```

3. **Update database migration** if using ENUM:
```php
$table->enum('status', ['draft', 'published', 'archived', 'scheduled'])->default('draft');
```

## Migration Checklist

When migrating existing code:

- [ ] Replace hardcoded `'draft'` with `ContentStatusHelper::draft()`
- [ ] Replace hardcoded `'published'` with `ContentStatusHelper::published()`
- [ ] Replace hardcoded `'archived'` with `ContentStatusHelper::archived()`
- [ ] Update FormRequest validation rules to use `ContentStatusHelper::validationRule()`
- [ ] Update default values to use `ContentStatusHelper::default()`
- [ ] Test all status-related functionality

## Benefits

1. **Single Source of Truth**: Status values defined in one place
2. **Type Safety**: Helper methods prevent typos
3. **Easy Updates**: Change status values in one place
4. **Consistency**: All code uses the same status values
5. **Documentation**: Clear API for status handling
