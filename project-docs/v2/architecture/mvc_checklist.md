# MVC Checklist Template

**Last Updated**: 2025-01-27  
**Status**: ✅ Complete

---

## 📋 Overview

Checklist template για να βεβαιωθείς ότι κάθε νέο Model έχει πλήρη MVC flow.

---

## ✅ MVC Checklist

### For Every New Model

#### Model Layer
- [ ] Model created in `app/Domain/{Domain}/Models/`
- [ ] Migration created (`database/migrations/`)
- [ ] Relationships defined (`belongsTo`, `hasMany`, etc.)
- [ ] Scopes defined (if needed) (`scopeForBusiness`, etc.)
- [ ] Accessors/Mutators (if needed)
- [ ] Model casts defined (`$casts` array)

#### Authorization
- [ ] Policy created (`app/Domain/{Domain}/Policies/`)
- [ ] Policy methods defined (`viewAny`, `view`, `create`, `update`, `delete`)
- [ ] Policy registered in `AuthServiceProvider`

#### Business Logic
- [ ] Service created (if business logic needed) (`app/Domain/{Domain}/Services/`)
- [ ] Service methods defined
- [ ] Service tested

#### Controller Layer
- [ ] Decision: Filament Resource or Blade Controller?
  - [ ] Standard CRUD? → Filament Resource
  - [ ] Custom UI? → Blade Controller
- [ ] Controller created:
  - [ ] Filament Resource: `app/Filament/Resources/{Model}Resource.php`
  - [ ] Blade Controller: `app/Http/Controllers/Admin/{Model}Controller.php`
- [ ] Controller methods implemented:
  - [ ] `index()` — List
  - [ ] `create()` — Show form
  - [ ] `store()` — Save new
  - [ ] `show()` — View single
  - [ ] `edit()` — Show edit form
  - [ ] `update()` — Save changes
  - [ ] `destroy()` — Delete

#### Validation
- [ ] Form Request created (`app/Http/Requests/{Domain}/`)
- [ ] Validation rules defined
- [ ] Authorization in Form Request (if needed)

#### View Layer (Blade Controllers only)
- [ ] Views directory created (`resources/views/admin/{resource}/`)
- [ ] Views created:
  - [ ] `index.blade.php` — List view
  - [ ] `create.blade.php` — Create form
  - [ ] `edit.blade.php` — Edit form
  - [ ] `show.blade.php` — Single view (optional)
- [ ] Views use admin layout (`<x-admin-layout>`)
- [ ] Views follow UI consistency guidelines

#### Routes
- [ ] Routes defined in `routes/web.php`
- [ ] Route names follow convention (`admin.{resource}.{action}`)
- [ ] Middleware applied (`auth`, `admin`, etc.)
- [ ] Route model binding (if needed)

#### Navigation (Blade Controllers only)
- [ ] Navigation link added (if needed)
- [ ] Navigation group correct
- [ ] Navigation sort order set

#### Testing
- [ ] Unit tests for Model
- [ ] Feature tests for Controller
- [ ] Policy tests
- [ ] Service tests (if applicable)

#### Documentation
- [ ] Model documented in `mvc_inventory.md`
- [ ] Flow documented in `mvc_flow.md` (if complex)
- [ ] API documented (if applicable)

---

## 🔀 Decision Tree

```
New Model?
├─ Needs CRUD?
│  ├─ Standard CRUD (simple forms/tables)?
│  │  └─ → Filament Resource
│  │     - Create Resource
│  │     - Define form() and table()
│  │     - Add to navigation (auto)
│  │
│  └─ Custom UI needed?
│     └─ → Blade Controller
│        - Create Controller
│        - Create Views
│        - Add Routes
│        - Add Navigation link
│
├─ Supporting/Junction Model?
│  └─ → Document only
│     - Add to supporting_models.md
│     - No Controller/View needed
│
└─ Configuration Model?
   └─ → Service-based
      - Create Service
      - Access via Services
      - No Controller/View needed
```

---

## 📝 Examples

### Example 1: Standard CRUD → Filament Resource

**Model**: `Product`

**Checklist**:
- ✅ Model: `app/Domain/Catalog/Models/Product.php`
- ✅ Migration: Created
- ✅ Policy: `ProductPolicy`
- ✅ Resource: `ProductResource` (Filament)
- ✅ Form/Table: Defined in Resource
- ✅ Navigation: Auto (Filament)
- ✅ Tests: Created

**Result**: ✅ Complete

---

### Example 2: Custom UI → Blade Controller

**Model**: `Content`

**Checklist**:
- ✅ Model: `app/Domain/Content/Models/Content.php`
- ✅ Migration: Created
- ✅ Policy: `ContentPolicy`
- ✅ Service: `CreateContentService`, `UpdateContentService`
- ✅ Controller: `Admin\ContentController`
- ✅ Views: `resources/views/admin/content/*`
- ✅ Routes: Defined
- ✅ Navigation: Link added
- ✅ Tests: Created

**Result**: ✅ Complete

---

### Example 3: Supporting Model → Document Only

**Model**: `ContentModuleAssignment`

**Checklist**:
- ✅ Model: `app/Domain/Modules/Models/ContentModuleAssignment.php`
- ✅ Migration: Created
- ❌ Controller: Not needed (junction model)
- ❌ Views: Not needed (embedded)
- ✅ Documentation: Added to `supporting_models.md`

**Result**: ✅ Complete (documented, not implemented)

---

## 🎯 Best Practices

### Do's
- ✅ Always create Policy (even if simple)
- ✅ Use Services for business logic
- ✅ Use Form Requests for validation
- ✅ Follow naming conventions
- ✅ Document in `mvc_inventory.md`

### Don'ts
- ❌ Don't put business logic in Controllers
- ❌ Don't skip authorization
- ❌ Don't create Controllers for junction models
- ❌ Don't create Views for Filament Resources
- ❌ Don't skip documentation

---

## 📚 Related Documentation

- [MVC Inventory](./mvc_inventory.md) — Current status
- [MVC Flow](./mvc_flow.md) — Flow examples
- [MVC Best Practices](./mvc_best_practices.md) — Guidelines
- [Supporting Models](./supporting_models.md) — When not to create Controller/View

---

**Last Updated**: 2025-01-27

