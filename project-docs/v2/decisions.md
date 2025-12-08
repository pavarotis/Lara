# 🎯 v2 Architectural Decisions & Answers

Αυτό το έγγραφο περιέχει αποφάσεις και απαντήσεις σε τεχνικές ερωτήσεις από τους developers.

---

## 📋 Decisions Log

### 2024-11-27 — Dev A Questions (Sprint 0)

#### 1. Domain Structure — Singular vs Plural

**Question**: Το conventions λέει Domain (singular), και ήδη υπάρχει `app/Domain/`. Να το κρατήσουμε ή να το αλλάξουμε;

**Decision**: ✅ **Κρατάμε `app/Domain/` (singular)**

**Rationale**:
- Το υπάρχον project χρησιμοποιεί `app/Domain/` (singular)
- Consistency με υπάρχον code
- Domain-Driven Design convention (Domain = singular concept)

**Action**: Κρατάμε `app/Domain/` όπως είναι.

---

#### 2. RBAC Package — Spatie vs Custom

**Question**: Θα χρησιμοποιήσουμε Spatie Laravel Permission ή custom implementation;

**Decision**: ✅ **Custom Implementation**

**Rationale**:
- Full control over structure
- No external dependency
- Aligns with our Domain structure
- Simpler for our use case
- Already planned in Sprint 0 (Role & Permission models)

**Structure**:
```
app/Domain/Auth/
├── Models/
│   ├── Role.php
│   └── Permission.php
├── Services/
│   ├── AssignRoleService.php
│   └── CheckPermissionService.php
└── Policies/
```

**Action**: Custom implementation με `Role` & `Permission` models.

---

#### 3. Settings Module — Global vs Business-Specific

**Question**: Υπάρχει ήδη `GetBusinessSettingsService` με business-specific settings. Το Settings module (global settings) θα είναι ξεχωριστό, ή θα ενσωματωθεί;

**Decision**: ✅ **Χωριστά Modules**

**Structure**:
- **Business Settings** (existing): `app/Domain/Businesses/Services/GetBusinessSettingsService.php`
  - Per-business settings (theme, delivery, currency, etc.)
  - Stored in `businesses.settings` JSON column
  
- **Global Settings** (new): `app/Domain/Settings/`
  - System-wide settings (site name, email, maintenance mode, etc.)
  - Stored in `settings` table (key-value)

**Rationale**:
- Clear separation of concerns
- Business settings = per business
- Global settings = system-wide
- Different use cases, different storage

**Action**: 
- Keep `GetBusinessSettingsService` as is
- Create new `app/Domain/Settings/` for global settings
- Document the difference clearly

---

#### 4. Feature Flag — .env vs Config

**Question**: Το `CMS_ENABLED` θα πάει στο `.env` ή σε config file;

**Decision**: ✅ **Config File** (`config/cms.php`)

**Rationale**:
- Type safety (boolean casting)
- Default values
- Easier to test
- Better for production

**Implementation**:
```php
// config/cms.php
return [
    'enabled' => env('CMS_ENABLED', false),
];
```

**Usage**:
```php
if (config('cms.enabled')) {
    // CMS features
}
```

**Action**: Create `config/cms.php` with `CMS_ENABLED` from `.env`.

---

#### 5. Existing Data — Keep or Fresh Start

**Question**: Τα υπάρχοντα data (products, orders, customers) θα παραμείνουν ή θα κάνουμε fresh start για το v2;

**Decision**: ✅ **Keep Existing Data**

**Rationale**:
- Real data is valuable
- Migration strategy supports it
- No need to lose existing work
- Feature flag allows gradual migration

**Migration Strategy**:
1. Run v2 migrations (add new tables, columns)
2. Migrate `is_admin` → roles (data migration)
3. Convert static pages → CMS content (data migration)
4. Keep all existing products, orders, customers
5. Test with existing data

**Action**: Keep all existing data. Use migrations to upgrade schema.

---

#### 6. CMS Folder — Delete or Replace

**Question**: Το `app/Domain/CMS/` είναι άδειο. Να το διαγράψω ή να το αντικαταστήσω με `Content/`;

**Decision**: ✅ **Delete in Sprint 1 (Cleanup Task)**

**Rationale**:
- Folder is empty (skeleton only)
- Will be replaced by `app/Domain/Content/`
- Cleanup task already planned in Sprint 1

**Action**:
- **Sprint 0**: Create `app/Domain/Content/` structure (skeleton)
- **Sprint 1**: Delete `app/Domain/CMS/` (cleanup task)

**Note**: Don't delete in Sprint 0 — wait for Sprint 1 when Content module is fully implemented.

---

## 📝 Summary for Dev A

1. ✅ **Domain Structure**: Keep `app/Domain/` (singular)
2. ✅ **RBAC**: Custom implementation (Role & Permission models)
3. ✅ **Settings**: Separate modules (Business Settings vs Global Settings)
4. ✅ **Feature Flag**: `config/cms.php` with `CMS_ENABLED` from `.env`
5. ✅ **Existing Data**: Keep all existing data (products, orders, customers)
6. ✅ **CMS Folder**: Delete in Sprint 1 (not Sprint 0)

**You can proceed with Sprint 0 - Task A1 (Architecture Documentation)!** 🚀

---

---

### 2024-11-27 — Dev B Questions (Sprint 0)

#### 1. CMS Folder — Fill or Create New?

**Question**: Το `app/Domain/CMS/` είναι άδειο. Θα το γεμίσουμε στο Sprint 0 με skeleton structure (Models, Services, Policies);

**Decision**: ❌ **ΔΕΝ γεμίζουμε το CMS folder. Δημιουργούμε `app/Domain/Content/`**

**Rationale**:
- Το `CMS/` folder είναι legacy placeholder
- Θα διαγραφεί στο Sprint 1 (cleanup task)
- Δημιουργούμε `Content/` ως το νέο structure
- Clear separation: old (CMS) vs new (Content)

**Action**:
- **Sprint 0**: Create `app/Domain/Content/` structure (skeleton)
- **Sprint 1**: Delete `app/Domain/CMS/` (cleanup task)
- **Don't** fill the CMS folder — it's deprecated

---

#### 2. ImageUploadService — Refactor or New Service?

**Question**: Υπάρχει ήδη `ImageUploadService` στο Catalog. Θα το refactor στο Sprint 2 για Media model ή θα δημιουργήσουμε νέο service;

**Decision**: ✅ **Refactor existing `ImageUploadService`**

**Rationale**:
- Avoid code duplication
- Single source of truth for image uploads
- Refactor to use Media model (Sprint 2)
- Update Product/Category controllers to use Media

**Action**:
- **Sprint 0**: Keep `ImageUploadService` as is (in Catalog)
- **Sprint 2**: Refactor `ImageUploadService` to use Media model
- **Sprint 2**: Update Product/Category controllers to use Media
- **Sprint 2 Cleanup**: Document the refactor

**Location**: `app/Domain/Catalog/Services/ImageUploadService.php` → refactor to use `Media` model

---

#### 3. Migration Naming — v2_ Prefix?

**Question**: Τα migrations θα έχουν `v2_` prefix και θα αφαιρεθεί στο cleanup, ή να ξεκινήσουμε χωρίς prefix;

**Decision**: ✅ **Use `v2_` prefix, remove in final cleanup**

**Rationale**:
- Clear identification of v2 migrations
- Easy to track during migration
- Remove prefix in final cleanup (after all sprints)
- Prevents conflicts with v1 migrations

**Naming Pattern**:
```php
// Sprint 0
v2_create_roles_table
v2_create_permissions_table
v2_create_settings_table

// Sprint 1
v2_create_content_types_table
v2_create_contents_table

// Final Cleanup (after all sprints)
// Rename: v2_create_roles_table → create_roles_table
```

**Action**:
- Use `v2_` prefix for all v2 migrations
- Document in migration guide
- Remove prefix in final cleanup (after Sprint 6)

---

#### 4. Feature Flag — Where to Check?

**Question**: Το `CMS_ENABLED` θα το ορίσουμε στο `.env` ή ως config value? Πού θα το ελέγχουμε (middleware, service, config);

**Decision**: ✅ **Config file + Check in middleware/services**

**Implementation**:
```php
// config/cms.php
return [
    'enabled' => env('CMS_ENABLED', false),
];
```

**Where to Check**:
1. **Middleware** (for routes):
   ```php
   // app/Http/Middleware/CheckCmsEnabled.php
   if (!config('cms.enabled')) {
       abort(404); // or redirect to v1 routes
   }
   ```

2. **Services** (for business logic):
   ```php
   // In services
   if (!config('cms.enabled')) {
       throw new CmsNotEnabledException();
   }
   ```

3. **Controllers** (for conditional features):
   ```php
   // In controllers
   if (config('cms.enabled')) {
       // CMS features
   }
   ```

**Action**:
- Create `config/cms.php` with `CMS_ENABLED` from `.env`
- Create `CheckCmsEnabled` middleware (optional, for route protection)
- Check in services/controllers as needed
- Default: `false` (v1 active)

---

## 📝 Summary for Dev B

1. ❌ **CMS Folder**: Don't fill it — create `Content/` instead, delete CMS in Sprint 1
2. ✅ **ImageUploadService**: Refactor in Sprint 2 (use Media model)
3. ✅ **Migration Naming**: Use `v2_` prefix, remove in final cleanup
4. ✅ **Feature Flag**: `config/cms.php` + check in middleware/services/controllers

**You can proceed with Sprint 0 - Dev B tasks!** 🚀

---

**Last Updated**: 2024-11-27

