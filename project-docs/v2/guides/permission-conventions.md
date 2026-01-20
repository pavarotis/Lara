# Permission Conventions

## Overview

This guide explains how to check permissions in LaraShop. We use a **Helper Class Pattern** to ensure consistent permission checking across the application.

## Configuration

Permission checking is handled by `App\Support\PermissionHelper`. This class provides a unified interface for checking user permissions, handling both RBAC (`hasRole()`) and legacy (`isAdmin()`) methods.

## Usage

### ✅ DO: Use PermissionHelper

```php
use App\Support\PermissionHelper;

// Check if user is admin
if (PermissionHelper::isAdmin($user)) {
    // Admin-only code
}

// Check if user can view unpublished content
if (PermissionHelper::canViewUnpublished()) {
    // Show unpublished content
}

// Require admin (throws 403 if not admin)
PermissionHelper::requireAdmin();

// Require authentication (throws 401 if not authenticated)
PermissionHelper::requireAuth();

// Check specific role
if (PermissionHelper::hasRole('editor', $user)) {
    // Editor-only code
}

// Check if user has any of the specified roles
if (PermissionHelper::hasAnyRole(['admin', 'editor'], $user)) {
    // Admin or editor code
}
```

### ❌ DON'T: Duplicate Permission Logic

```php
// ❌ BAD
if ($user->hasRole('admin') || $user->isAdmin()) {
    // ...
}

if ($user && (method_exists($user, 'hasRole') ? $user->hasRole('admin') : ($user->isAdmin ?? false))) {
    // ...
}

// ✅ GOOD
if (PermissionHelper::isAdmin($user)) {
    // ...
}
```

## In Controllers

### Authorization Checks

```php
use App\Support\PermissionHelper;

public function show(Content $content)
{
    // Only show published content to non-admin users
    if (! $content->isPublished() && ! PermissionHelper::canViewUnpublished()) {
        abort(404, 'Content not found');
    }
    
    // ...
}
```

### Require Admin Access

```php
use App\Support\PermissionHelper;

public function preview(int $contentId)
{
    PermissionHelper::requireAdmin(); // Throws 403 if not admin
    
    // Admin-only code
}
```

## In FormRequests

Use `PermissionHelper` in the `authorize()` method:

```php
use App\Support\PermissionHelper;

public function authorize(): bool
{
    return PermissionHelper::isAdmin($this->user());
}
```

## In Middleware

Use `PermissionHelper` for consistent checking:

```php
use App\Support\PermissionHelper;

public function handle(Request $request, Closure $next): Response
{
    if (! PermissionHelper::isAdmin($request->user())) {
        abort(403, 'Access denied. Admin privileges required.');
    }
    
    return $next($request);
}
```

## Available Methods

### `isAdmin(?User $user = null): bool`

Checks if user is admin. Handles both `hasRole('admin')` and `isAdmin()` methods.

- **Parameters**: `$user` (optional, defaults to authenticated user)
- **Returns**: `bool`

### `canViewUnpublished(?User $user = null): bool`

Checks if user can view unpublished content. Currently only admins can.

- **Parameters**: `$user` (optional, defaults to authenticated user)
- **Returns**: `bool`

### `canEditContent(?User $user = null): bool`

Checks if user can edit content. Currently only admins can.

- **Parameters**: `$user` (optional, defaults to authenticated user)
- **Returns**: `bool`

### `canPublishContent(?User $user = null): bool`

Checks if user can publish content. Currently only admins can.

- **Parameters**: `$user` (optional, defaults to authenticated user)
- **Returns**: `bool`

### `requireAdmin(?User $user = null): void`

Requires admin access. Throws 403 if user is not admin.

- **Parameters**: `$user` (optional, defaults to authenticated user)
- **Throws**: `HttpResponseException` (403)

### `requireAuth(): void`

Requires authentication. Throws 401 if user is not authenticated.

- **Throws**: `HttpResponseException` (401)

### `hasRole(string $role, ?User $user = null): bool`

Checks if user has a specific role.

- **Parameters**: 
  - `$role` (required): Role slug
  - `$user` (optional, defaults to authenticated user)
- **Returns**: `bool`

### `hasAnyRole(array $roles, ?User $user = null): bool`

Checks if user has any of the specified roles.

- **Parameters**: 
  - `$roles` (required): Array of role slugs
  - `$user` (optional, defaults to authenticated user)
- **Returns**: `bool`

## Migration Checklist

When migrating existing code:

- [ ] Replace `$user->hasRole('admin') || $user->isAdmin()` with `PermissionHelper::isAdmin($user)`
- [ ] Replace complex admin checks with `PermissionHelper::isAdmin()`
- [ ] Update FormRequest `authorize()` methods to use `PermissionHelper`
- [ ] Update middleware to use `PermissionHelper`
- [ ] Update controllers to use `PermissionHelper::canViewUnpublished()`
- [ ] Replace `abort(403)` with `PermissionHelper::requireAdmin()` where appropriate
- [ ] Test all permission-related functionality

## Benefits

1. **Consistency**: All permission checks use the same logic
2. **Maintainability**: Change permission logic in one place
3. **Readability**: Clear, self-documenting method names
4. **Flexibility**: Easy to add new permission checks
5. **Type Safety**: Helper methods prevent errors
