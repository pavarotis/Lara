# Routing Conventions & Best Practices

## Overview

This document defines routing conventions and best practices to ensure routes remain maintainable and don't conflict with system routes.

## Route Loading Order

Routes are loaded in the following order (defined in `routes/web.php`):

1. **System Routes** (explicit routes like `/menu`, `/cart`, etc.)
2. **Auth Routes** (`routes/auth.php`) - **MUST be loaded before catch-all routes**
3. **Business Routes** (`/{business:slug}`) - Canonical business routes
4. **Legacy Routes** (`/{slug}`) - Fallback for backward compatibility

## Protected System Routes

The following routes are **protected** and must NEVER be used as business slugs or content slugs:

### Authentication Routes
- `login`, `register`, `logout`
- `password`, `forgot-password`, `reset-password`
- `email-verification`, `verify-email`, `confirm-password`

### Admin Routes
- `admin` (and all sub-routes under `/admin`)

### API Routes
- `api` (and all sub-routes under `/api`)

### Public System Routes
- `cart`, `checkout`, `menu`
- `dashboard`, `profile`, `preview`

### SEO Routes
- `sitemap.xml`, `robots.txt`

**Full list:** See `config/routes.php`

## Adding New Routes

### ✅ DO: Add Explicit Routes First

```php
// Add explicit routes BEFORE catch-all routes
Route::get('/my-new-feature', [MyController::class, 'index'])->name('my-feature');
```

### ✅ DO: Add to Config if It's a System Route

If you're adding a new system route that should be protected:

1. Add it to `config/routes.php` in the appropriate category
2. The exclusion patterns will automatically update

```php
// config/routes.php
'public' => [
    'cart',
    'checkout',
    'menu',
    'my-new-feature', // ← Add here
],
```

### ❌ DON'T: Use System Route Names for Business/Content

```php
// ❌ BAD: This will conflict
$business->slug = 'login'; // Will break /login route
$content->slug = 'admin';  // Will break /admin route

// ✅ GOOD: Use descriptive, unique slugs
$business->slug = 'my-cafe';
$content->slug = 'about-us';
```

### ❌ DON'T: Add Routes After Catch-All Routes

```php
// ❌ BAD: This route will never be reached
Route::get('/{slug}', [ContentController::class, 'show']); // Catch-all
Route::get('/my-feature', [MyController::class, 'index']); // Too late!

// ✅ GOOD: Add before catch-all
Route::get('/my-feature', [MyController::class, 'index']);
Route::get('/{slug}', [ContentController::class, 'show']); // Catch-all last
```

## Route Validation

### Using RouteHelper

```php
use App\Support\RouteHelper;

// Validate a route before using it
RouteHelper::validateRoute('my-new-route'); // Throws exception if conflict

// Get exclusion pattern for catch-all routes
Route::get('/{slug}', [Controller::class, 'show'])
    ->where('slug', RouteHelper::exclusionPattern());

// Get business slug pattern
Route::prefix('{business:slug}')
    ->where('business', RouteHelper::businessSlugPattern());
```

## Testing Routes

### Check Route Conflicts

```bash
# List all routes
php artisan route:list

# Check specific route
php artisan route:list --name=login

# Check route path
php artisan route:list --path=login
```

### Validate in Code

```php
// In tests or before creating business/content
use App\Support\RouteHelper;

RouteHelper::validateRoute($proposedSlug);
```

## Common Pitfalls

### 1. Route Order Matters

Laravel matches routes in the order they're defined. More specific routes must come before catch-all routes.

### 2. Regex Constraints

Always use `->where()` constraints on catch-all routes to exclude system routes:

```php
// ✅ GOOD
Route::get('/{slug}', [Controller::class, 'show'])
    ->where('slug', RouteHelper::exclusionPattern());

// ❌ BAD: No constraint - will match /login, /admin, etc.
Route::get('/{slug}', [Controller::class, 'show']);
```

### 3. Business Slug Validation

When creating businesses, validate the slug:

```php
use App\Support\RouteHelper;

$slug = 'my-business';
RouteHelper::validateRoute($slug); // Throws if conflict

$business = Business::create(['slug' => $slug]);
```

## Route Structure

```
routes/
├── web.php          # Main web routes (loads auth.php)
├── auth.php         # Authentication routes (loaded by web.php)
├── api.php          # API routes
└── console.php      # Artisan commands
```

## Migration Checklist

When adding new routes:

- [ ] Is it a system route? → Add to `config/routes.php`
- [ ] Is it explicit? → Add before catch-all routes
- [ ] Does it need protection? → Use `RouteHelper::validateRoute()`
- [ ] Test route doesn't conflict → Run `php artisan route:list`
- [ ] Update this documentation if adding new route category

## Examples

### Adding a New Public Feature

```php
// routes/web.php

// 1. Add explicit route BEFORE catch-all
Route::get('/reviews', [ReviewController::class, 'index'])->name('reviews.index');

// 2. If it's a system route, add to config/routes.php
// config/routes.php
'public' => [
    'cart',
    'checkout',
    'menu',
    'reviews', // ← Add here
],
```

### Adding a New Business Route

```php
// routes/web.php

// Business routes are already protected by businessSlugPattern()
// Just ensure the route is added before catch-all routes
Route::prefix('{business:slug}')->group(function () {
    Route::get('/reviews', [ReviewController::class, 'index'])
        ->where('business', RouteHelper::businessSlugPattern())
        ->name('business.reviews');
});
```

## Questions?

If you're unsure about route placement or conflicts:
1. Check `config/routes.php` for excluded routes
2. Use `RouteHelper::validateRoute()` to test
3. Run `php artisan route:list` to see route order
4. Ask in team chat or create an issue
