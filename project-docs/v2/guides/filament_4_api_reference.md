# Filament 4 API Reference Guide

**Last Updated**: 2026-01-08  
**Purpose**: Prevent common Filament 4 API mistakes

---

## ⚠️ Critical API Changes from Filament 3 → 4

### 1. Forms vs Schemas

**❌ WRONG (Filament 3):**
```php
use Filament\Forms\Form;

public function form(Form $form): Form
{
    return $form->schema([...]);
}
```

**✅ CORRECT (Filament 4):**
```php
use Filament\Schemas\Schema;

public function form(Schema $schema): Schema
{
    return $schema->schema([...]);
}
```

**When to use:**
- **Resources**: `form(Schema $schema): Schema`
- **Pages with HasForms**: `form(Schema $schema): Schema`
- **Custom Form Classes**: Use `Schema` not `Form`

---

### 2. Section Component

**❌ WRONG:**
```php
use Filament\Forms\Components\Section;
```

**✅ CORRECT:**
```php
use Filament\Schemas\Components\Section;
```

---

### 3. Navigation Properties

**❌ WRONG:**
```php
protected static ?string $navigationIcon;
protected static ?string $navigationGroup;
```

**✅ CORRECT:**
```php
protected static string|\BackedEnum|null $navigationIcon;
protected static string|\UnitEnum|null $navigationGroup;
```

---

### 4. Actions Namespace

**❌ WRONG:**
```php
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
```

**✅ CORRECT:**
```php
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
```

---

### 5. View Property (Pages)

**❌ WRONG:**
```php
protected static string $view = 'filament.pages.cms.dashboard';
```

**✅ CORRECT:**
```php
protected string $view = 'filament.pages.cms.dashboard'; // Non-static!
```

---

## 📋 Quick Checklist

Before creating a new Filament Resource/Page:

- [ ] Use `Filament\Schemas\Schema` not `Filament\Forms\Form`
- [ ] Use `Filament\Schemas\Components\Section` not `Filament\Forms\Components\Section`
- [ ] Use `Filament\Actions\*` not `Filament\Tables\Actions\*`
- [ ] Navigation properties use union types (`string|\BackedEnum|null`)
- [ ] Page `$view` property is **non-static**

---

## 🔍 How to Verify

1. **Check existing working code:**
   ```bash
   grep -r "use Filament\\Schemas" app/Filament/
   ```

2. **Check for old API usage:**
   ```bash
   grep -r "Filament\\Forms\\Form" app/Filament/
   grep -r "Filament\\Tables\\Actions" app/Filament/
   ```

3. **Run linter:**
   ```bash
   php artisan pint
   ```

---

## 📚 References

- Filament 4 Documentation: https://filamentphp.com/docs/4.x
- Sprint 4.3: Filament 4 Alignment (`project-docs/v2/sprints/sprint_4.3/`)

