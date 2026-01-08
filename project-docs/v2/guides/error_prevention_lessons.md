# Error Prevention Lessons - Sprint 5

**Date**: 2026-01-08  
**Sprint**: Sprint 5 - Theming 2.0

---

## 🐛 Errors Encountered

### 1. Missing Database Tables

**Error:**
```
SQLSTATE[42S02]: Base table or view not found: 1146 Table 'larashop.theme_tokens' doesn't exist
```

**Root Cause:**
- Migrations were created but never run
- No verification step after creating migrations

**Solution:**
```bash
php artisan migrate
php artisan db:seed --class=ThemePresetsSeeder
```

**Prevention:**
- ✅ Always run `php artisan migrate:status` after creating migrations
- ✅ Add to deployment checklist (see `deployment_checklist.md`)
- ✅ Run migrations immediately after creating them during development

---

### 2. Missing Seeder

**Error:**
```
Target class [Database\Seeders\ThemePresetsSeeder] does not exist.
```

**Root Cause:**
- Seeder was mentioned in sprint docs but never created
- No verification that seeder file exists

**Solution:**
- Created `ThemePresetsSeeder` with 3 presets (Cafe, Restaurant, Retail)

**Prevention:**
- ✅ Create seeders immediately when mentioned in sprint
- ✅ Add seeder creation to task deliverables
- ✅ Verify seeder exists before marking task complete

---

### 3. Filament 4 API Mismatch

**Error:**
```
TypeError: Argument #1 ($form) must be of type Filament\Forms\Form, Filament\Schemas\Schema given
```

**Root Cause:**
- Used Filament 3 API (`Filament\Forms\Form`) instead of Filament 4 API (`Filament\Schemas\Schema`)
- No reference guide for Filament 4 API changes

**Solution:**
```php
// Changed from:
use Filament\Forms\Form;
public function form(Form $form): Form

// To:
use Filament\Schemas\Schema;
public function form(Schema $schema): Schema
```

**Prevention:**
- ✅ Created `filament_4_api_reference.md` guide
- ✅ Always check existing working Filament code before writing new
- ✅ Reference Sprint 4.3 (Filament 4 Alignment) documentation

---

## 📋 Prevention Strategies

### 1. Immediate Verification

**After creating migrations:**
```bash
php artisan migrate:status  # Check if pending
php artisan migrate         # Run if needed
```

**After creating seeders:**
```bash
php artisan db:seed --class=SeederName  # Test immediately
```

**After creating Filament code:**
- Check existing Resources/Pages for API patterns
- Reference `filament_4_api_reference.md`
- Run linter: `php artisan pint`

---

### 2. Documentation

**Created guides:**
- ✅ `filament_4_api_reference.md` - Filament 4 API changes
- ✅ `deployment_checklist.md` - Pre-deployment verification
- ✅ `error_prevention_lessons.md` - This document

---

### 3. Code Review Checklist

Before marking a sprint task complete:

- [ ] Migrations run successfully
- [ ] Seeders exist and run successfully
- [ ] Filament API matches Filament 4 (check reference guide)
- [ ] Routes registered
- [ ] Middleware registered
- [ ] Config files exist
- [ ] No linter errors
- [ ] Browser test passes (no runtime errors)

---

### 4. Development Workflow

**Recommended order:**
1. Create migrations → Run immediately
2. Create models → Test in tinker
3. Create seeders → Run immediately
4. Create services → Test in tinker
5. Create controllers → Test routes
6. Create views → Test in browser
7. Create Filament Resources/Pages → Test in browser

**Never:**
- ❌ Create migrations without running them
- ❌ Create seeders without running them
- ❌ Use Filament API without checking reference
- ❌ Mark task complete without browser testing

---

## 🎯 Key Takeaways

1. **Always verify immediately**: Run migrations/seeders right after creating them
2. **Reference existing code**: Check working Filament code before writing new
3. **Use documentation**: Create and maintain API reference guides
4. **Test in browser**: Don't assume code works without testing
5. **Follow checklists**: Use deployment checklist before marking complete

---

## 🔗 Related Documents

- `filament_4_api_reference.md` - Filament 4 API guide
- `deployment_checklist.md` - Pre-deployment verification
- Sprint 4.3 Review - Filament 4 alignment details

