# Content Types Strategy

**Last Updated**: 2025-01-27  
**Status**: ✅ Complete

---

## 📋 Overview

Documentation για Content Types strategy και decision για static vs dynamic management.

---

## 🎯 Decision

**Content Types are Config-Based (Static)**

Content Types are defined in:
- Database (`content_types` table) — seeded from config
- Config files (if needed for defaults)
- Seeders (initial setup)

**No CRUD Interface Needed**

Content Types are:
- Defined during setup/installation
- Not intended for user management
- Configuration, not content

---

## 📊 Current Implementation

### Database Structure

```php
// Migration: content_types table
Schema::create('content_types', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('slug')->unique();
    $table->json('field_definitions')->nullable();
    $table->timestamps();
});
```

### Model

```php
// app/Domain/Content/Models/ContentType.php
class ContentType extends Model {
    protected $fillable = ['name', 'slug', 'field_definitions'];
    protected $casts = ['field_definitions' => 'array'];
}
```

### Usage

```php
// In ContentController
$contentTypes = ContentType::all(); // Used in dropdown

// In Content create/edit forms
<select name="type">
    @foreach($contentTypes as $type)
        <option value="{{ $type->slug }}">{{ $type->name }}</option>
    @endforeach
</select>
```

---

## 🔄 Future Considerations

### If Dynamic Management Needed

**Option 1: Filament Resource** (Recommended)
```php
// app/Filament/Resources/ContentTypeResource.php
class ContentTypeResource extends Resource {
    public static function form(Schema $schema): Schema {
        return $schema->components([
            TextInput::make('name')->required(),
            TextInput::make('slug')->required()->unique(),
            KeyValue::make('field_definitions')->label('Field Definitions'),
        ]);
    }
}
```

**Option 2: Blade Controller** (If custom UI needed)
```php
// app/Http/Controllers/Admin/ContentTypeController.php
// Custom UI for field builder
```

---

## 📝 Rationale

### Why Config-Based?

1. **Stability**: Content types don't change often
2. **Simplicity**: No need for complex UI
3. **Performance**: No overhead of CRUD interface
4. **Control**: Types defined by developers, not users

### When to Switch to Dynamic?

- Users need to create custom content types
- Field definitions need visual builder
- Content types change frequently
- Multi-tenant with different types per business

---

## 🎯 Current Status

- ✅ **ContentType Model**: Exists
- ✅ **Database Table**: Exists
- ✅ **Usage**: Dropdowns in Content forms
- ❌ **CRUD Interface**: Not needed (config-based)
- ✅ **Decision**: Documented (keep as config-based)

---

## 📚 Related Documentation

- [MVC Inventory](./mvc_inventory.md) — ContentType status
- [Supporting Models](./supporting_models.md) — ContentType documentation
- [Content Module](../sprints/sprint_1/sprint_1.md) — Content types in Sprint 1

---

**Last Updated**: 2025-01-27

