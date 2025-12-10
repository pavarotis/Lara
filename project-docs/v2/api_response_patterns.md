# API Response Patterns — Best Practices

## 📋 Overview

Όλα τα API controllers πρέπει να ακολουθούν consistent patterns για responses. Αυτό εξασφαλίζει:
- Consistent JSON format
- DRY principle (Don't Repeat Yourself)
- Easy maintenance
- Better error handling

---

## ✅ Correct Patterns

### 1. **Paginated Responses**

**Πάντα** χρησιμοποίησε το `BaseController::paginated()` helper για paginated responses.

**✅ Correct Pattern:**
```php
public function index(Request $request, int $businessId): JsonResponse
{
    $query = Media::where('business_id', $businessId)
        ->with(['folder', 'creator'])
        ->orderBy('created_at', 'desc');

    // Filters...
    
    $media = $query->paginate($request->get('per_page', 24));

    // Transform paginated items using Resource
    $media->setCollection(
        MediaResource::collection($media->items())->collection
    );

    return $this->paginated($media, 'Media retrieved successfully');
}
```

**❌ Wrong Pattern (Manual JSON):**
```php
// DON'T DO THIS!
$media = $query->paginate($request->get('per_page', 24));
$transformedItems = MediaResource::collection($media->items());

return response()->json([
    'success' => true,
    'data' => $transformedItems,
    'message' => 'Media retrieved successfully',
    'meta' => [
        'current_page' => $media->currentPage(),
        'per_page' => $media->perPage(),
        // ... manual pagination data
    ],
    'links' => [
        // ... manual links
    ],
]);
```

**Why?**
- ❌ Duplicates code (pagination logic)
- ❌ Inconsistent format (if BaseController changes, manual code doesn't)
- ❌ More code to maintain
- ❌ Easy to make mistakes (forget fields, wrong structure)

---

### 2. **Single Resource Responses**

**✅ Correct Pattern:**
```php
public function show(int $businessId, int $id): JsonResponse
{
    $media = Media::where('business_id', $businessId)
        ->with(['folder', 'creator'])
        ->find($id);

    if (! $media) {
        return $this->error('Media not found', [], 404);
    }

    return $this->success(new MediaResource($media), 'Media retrieved successfully');
}
```

**❌ Wrong Pattern:**
```php
// DON'T DO THIS!
return response()->json([
    'success' => true,
    'data' => [
        'id' => $media->id,
        'name' => $media->name,
        // ... manual transformation
    ],
    'message' => 'Media retrieved successfully',
]);
```

---

### 3. **Collection Responses (Non-Paginated)**

**✅ Correct Pattern:**
```php
public function byType(int $businessId, string $type): JsonResponse
{
    $contents = $this->getContentService->byType($businessId, $type);

    return $this->success(
        ContentResource::collection($contents), 
        'Content retrieved successfully'
    );
}
```

---

### 4. **Error Responses**

**✅ Correct Pattern:**
```php
if (! $media) {
    return $this->error('Media not found', [], 404);
}

// Validation errors
return $this->error('Validation failed', $errors, 422);
```

**❌ Wrong Pattern:**
```php
// DON'T DO THIS!
return response()->json([
    'success' => false,
    'message' => 'Media not found',
], 404);
```

---

## 🔍 Pattern Checklist

**Before committing API controller code:**

- [ ] **Paginated responses** use `$this->paginated($paginator, $message)`
- [ ] **Single resource** uses `$this->success(new Resource($model), $message)`
- [ ] **Collections** use `$this->success(Resource::collection($items), $message)`
- [ ] **Errors** use `$this->error($message, $errors, $statusCode)`
- [ ] **Resources** are used for all data transformation
- [ ] **No manual JSON** responses (except special cases)

---

## 📚 Reference Implementation

**See existing correct implementations:**
- `app/Http/Controllers/Api/V1/ContentController.php` — Paginated response pattern
- `app/Http/Controllers/Api/V1/MediaController.php` — Paginated response pattern (after fix)
- `app/Http/Controllers/Api/BaseController.php` — Helper methods

---

## 🎯 Key Rules

1. **Always use BaseController helpers**: `success()`, `error()`, `paginated()`
2. **Always use Resources**: Transform data with Resource classes
3. **Never manual JSON**: Don't create manual `response()->json()` for standard responses
4. **Consistent format**: All responses follow the same structure
5. **Check existing code**: Before writing new API endpoint, check similar existing endpoints

---

## ⚠️ Common Mistakes

### Mistake 1: Manual Pagination JSON
**Problem**: Creating manual JSON for paginated responses
**Solution**: Use `$this->paginated($paginator, $message)`

### Mistake 2: Not Using Resources
**Problem**: Manual array transformation in controller
**Solution**: Create Resource class and use it

### Mistake 3: Inconsistent Error Format
**Problem**: Manual error JSON responses
**Solution**: Use `$this->error($message, $errors, $statusCode)`

### Mistake 4: Forgetting to Transform Paginated Items
**Problem**: Returning raw models in paginated response
**Solution**: Use `$paginator->setCollection(Resource::collection($items)->collection)`

---

## 📝 Example: Complete API Controller

```php
<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Domain\Media\Models\Media;
use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\MediaResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MediaController extends BaseController
{
    public function index(Request $request, int $businessId): JsonResponse
    {
        $query = Media::where('business_id', $businessId)
            ->with(['folder', 'creator'])
            ->orderBy('created_at', 'desc');

        // Filters
        if ($request->filled('folder_id')) {
            $query->where('folder_id', $request->folder_id);
        }

        $media = $query->paginate($request->get('per_page', 24));

        // Transform using Resource
        $media->setCollection(
            MediaResource::collection($media->items())->collection
        );

        // Use helper method
        return $this->paginated($media, 'Media retrieved successfully');
    }

    public function show(int $businessId, int $id): JsonResponse
    {
        $media = Media::where('business_id', $businessId)
            ->with(['folder', 'creator'])
            ->find($id);

        if (! $media) {
            return $this->error('Media not found', [], 404);
        }

        return $this->success(new MediaResource($media), 'Media retrieved successfully');
    }
}
```

---

**Last Updated**: 2024-11-27  
**Created by**: Dev A (after Sprint 2 review feedback)

