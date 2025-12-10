# Sprint 4 — RBAC & Permissions — REVISED

**Status**: ⏳ Pending  
**Start Date**: _TBD_  
**End Date**: _TBD_  
**Διάρκεια**: 1 εβδομάδα

---

## 📋 Sprint Goal

Fine-grained access control. Upgrade από `is_admin` flag σε full RBAC system με roles, permissions, και UI για management.

---

## 🎯 High-Level Objectives

- Permissions system (define all permissions)
- Policies update (use permissions instead of `is_admin`)
- Permission middleware
- Role management UI
- User role assignment UI
- Remove `is_admin` logic (cleanup)

⚠️ **Σημείωση**: Το RBAC foundation έχει ήδη δημιουργηθεί στο Sprint 0. Στο Sprint 4 κάνουμε:
- Full implementation
- UI για management
- Integration σε όλα τα modules
- Cleanup deprecated `is_admin` code

---

## 👥 Tasks by Developer

### Dev B — Domain Logic & Permissions

#### Task B1 — Permissions System Definition

**Περιγραφή**: Ορισμός όλων των permissions για όλα τα modules.

**Deliverables:**
- Define permission structure:
  - **Content**: `content.view`, `content.create`, `content.edit`, `content.delete`, `content.publish`
  - **Media**: `media.view`, `media.upload`, `media.edit`, `media.delete`
  - **Products**: `products.view`, `products.create`, `products.edit`, `products.delete`
  - **Categories**: `categories.view`, `categories.create`, `categories.edit`, `categories.delete`
  - **Orders**: `orders.view`, `orders.update`, `orders.delete`
  - **Settings**: `settings.view`, `settings.edit`
  - **Users**: `users.view`, `users.create`, `users.edit`, `users.delete`
  - **Roles**: `roles.view`, `roles.create`, `roles.edit`, `roles.delete`
- Permission seeder:
  - Create all permissions in database
  - Group by module
- `Permission` model enhancements:
  - `scopeByModule($module)` — filter by module
  - Helper: `getModule()` — extract module from name

**Acceptance Criteria:**
- All permissions defined
- Permissions seeded
- Model helpers working

---

#### Task B2 — Default Roles & Permissions

**Περιγραφή**: Ορισμός default roles με permissions.

**Deliverables:**
- Role seeder:
  - **Super Admin**: All permissions
  - **Admin**: All except roles management
  - **Editor**: Content & Media (view, create, edit, delete)
  - **Manager**: View all, edit orders, view settings
  - **Customer**: None (public user, optional)
- Assign permissions to roles
- Default role assignment (new users → Customer)

**Acceptance Criteria:**
- Default roles created
- Permissions assigned correctly
- New users get default role

---

#### Task B3 — Policies Update

**Περιγραφή**: Update όλων των policies να χρησιμοποιούν permissions αντί για `is_admin`.

**Deliverables:**
- Update `ContentPolicy`:
  - `viewAny()` → check `content.view`
  - `view()` → check `content.view`
  - `create()` → check `content.create`
  - `update()` → check `content.edit`
  - `delete()` → check `content.delete`
- Update `MediaPolicy`:
  - `viewAny()` → check `media.view`
  - `upload()` → check `media.upload`
  - `delete()` → check `media.delete`
- Update `ProductPolicy`:
  - All methods → check respective permissions
- Update `CategoryPolicy`:
  - All methods → check respective permissions
- Update `OrderPolicy`:
  - All methods → check respective permissions
- Update `SettingsPolicy`:
  - All methods → check respective permissions

**Acceptance Criteria:**
- All policies updated
- No `is_admin` checks remaining
- Permissions enforced correctly

---

### Dev A — Controllers & Middleware

#### Task A1 — Permission Middleware

**Περιγραφή**: Middleware για permission checking.

**Deliverables:**
- `CheckPermission` middleware:
  - Check if user has permission
  - Redirect/403 if not
  - Support multiple permissions (any/all)
- Register in `bootstrap/app.php`:
  ```php
  $middleware->alias([
      'permission' => \App\Http\Middleware\CheckPermission::class,
  ]);
  ```
- Usage in routes:
  ```php
  Route::middleware(['auth', 'permission:content.create'])->group(...);
  ```

**Acceptance Criteria:**
- Middleware working
- Can check single/multiple permissions
- 403 response for unauthorized

---

#### Task A2 — Role Management Controllers

**Deliverables:**
- `Admin/RoleController`:
  - `index()` — list roles
  - `create()` — show create form
  - `store()` — create role with permissions
  - `edit()` — show edit form
  - `update()` — update role & permissions
  - `destroy()` — delete role (with checks)
- `Admin/PermissionController` (optional, view only):
  - `index()` — list all permissions (grouped by module)
- Routes:
  - `/admin/roles` (resource)
  - `/admin/permissions` (index only)

**Acceptance Criteria:**
- Role CRUD working
- Permission assignment working
- Cannot delete roles in use

---

#### Task A3 — Update Existing Controllers

**Περιγραφή**: Replace `is_admin` checks με permission checks.

**Deliverables:**
- Update all admin controllers:
  - Remove `is_admin` checks
  - Add permission middleware
  - Update policies usage
- Update routes:
  - Replace `admin` middleware → `permission:*` where needed
  - Keep `auth` middleware

**Acceptance Criteria:**
- All controllers use permissions
- No `is_admin` checks in controllers
- Routes protected correctly

---

### Dev C — RBAC UI

#### Task C1 — Role Management UI

**Περιγραφή**: Admin UI για role management.

**Deliverables:**
- `admin/roles/index.blade.php`:
  - Table: name, description, permissions count, users count
  - Actions: edit, delete
  - Create button
- `admin/roles/create.blade.php`:
  - Form: name, description
  - Permission checkboxes (grouped by module)
  - Save button
- `admin/roles/edit.blade.php`:
  - Same as create, pre-filled
  - Show users with this role
  - Cannot edit Super Admin role (read-only)

**Acceptance Criteria:**
- Role CRUD UI working
- Permission assignment UI working
- Validation working

---

#### Task C2 — User Role Assignment UI

**Περιγραφή**: UI για assign roles σε users.

**Deliverables:**
- Update `admin/users/edit.blade.php`:
  - Role assignment section
  - Checkboxes for roles (multiple roles per user)
  - Show current roles
  - Save roles
- Update `admin/users/create.blade.php`:
  - Role assignment on create
- Update `admin/users/index.blade.php`:
  - Show roles column (badges)
  - Filter by role

**Acceptance Criteria:**
- Can assign roles to users
- Multiple roles per user supported
- Roles displayed in user list

---

#### Task C3 — Permission Matrix UI (Optional)

**Περιγραφή**: Visual matrix showing roles vs permissions (αν προλάβει).

**Deliverables:**
- `admin/permissions/index.blade.php`:
  - Table: Permissions (rows) × Roles (columns)
  - Checkmarks for assigned permissions
  - Read-only view

**Acceptance Criteria:**
- Matrix displays correctly
- Easy to see permission distribution

---

#### Task C4 — Menu Visibility by Permissions

**Περιγραφή**: Update admin sidebar να δείχνει menu items based on permissions.

**Deliverables:**
- Update `components/admin-sidebar.blade.php`:
  - Check permissions before showing menu items
  - Hide items user doesn't have access to
  - Example:
    ```blade
    @can('content.view')
        <a href="/admin/content">Content</a>
    @endcan
    ```

**Acceptance Criteria:**
- Menu items hidden based on permissions
- Users see only what they can access

---

## ✅ Deliverables (End of Sprint 4)

- [ ] Permissions system fully defined
- [ ] Default roles created
- [ ] All policies updated (use permissions)
- [ ] Permission middleware working
- [ ] Role management UI
- [ ] User role assignment UI
- [ ] Menu visibility by permissions
- [ ] All controllers use permissions
- [ ] `is_admin` logic removed

---

## ❌ Δεν θα υπάρχουν ακόμα

- Permission matrix UI (optional, deferred)
- Advanced permission inheritance
- Permission groups
- Audit logs for permission changes

**Αυτά μπορούν να έρθουν σε future sprints.**

---

## 🧹 Cleanup Tasks

- [ ] Remove `is_admin` column from users (new migration: `drop_is_admin_from_users_table`)
- [ ] Remove `AdminMiddleware` (replaced by `CheckPermission`)
- [ ] Update all policies (remove `is_admin` checks)
- [ ] Remove `is_admin` from User model casts
- [ ] Remove `is_admin` from User model methods
- [ ] Update all routes (remove `admin` middleware, use `permission:*`)

---

## 📝 Sprint Notes

_Εδώ μπορείς να γράφεις ελεύθερο κείμενο για το sprint:_
- Progress updates
- Issues encountered
- Decisions made
- Questions for team
- Any other notes

---

## 🐛 Issues & Blockers

_Καταγράψε εδώ οποιαδήποτε issues ή blockers_

---

## 📚 References

- [v2 Overview](../v2_overview.md) — Architecture & strategy
- [Migration Guide](../v2_migration_guide.md)
- [**Developer Responsibilities**](../dev-responsibilities.md) ⭐ **Read this for quality checks & best practices**

---

**Last Updated**: _TBD_

