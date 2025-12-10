# 🚀 Dev A — Sprint 1 Starting Guide

**Welcome!** Ακολούθησε αυτά τα βήματα **με τη σειρά**.

---

## 📋 Step-by-Step Plan

### ✅ Step 1: Documentation Reading (2-3 hours) — **DO THIS FIRST**

**Σειρά ανάγνωσης**:

1. **`project-docs/conventions.md`** (30-45 min) ⭐ **MUST READ**
   - Coding conventions
   - Service Layer Pattern
   - Naming conventions
   - File structure

2. **`project-docs/architecture.md`** (30-45 min)
   - Project architecture
   - Domain structure
   - Key decisions

3. **`project-docs/v2/v2_overview.md`** (20-30 min)
   - v2 strategy
   - Migration approach
   - Module overview

4. **`project-docs/v2/dev-responsibilities.md`** (20-30 min)
   - Best practices
   - Error prevention
   - Quality checks

5. **`project-docs/v2/sprints/sprint_1.md`** (15-20 min)
   - Your specific tasks
   - Acceptance criteria
   - Deliverables

6. **`project-docs/v2/v2_content_model.md`** (20-30 min)
   - Content model structure
   - Block system
   - JSON structure

**⏱️ Total Time**: ~2-3 hours

**Why?** Θα σε βοηθήσει να:
- Καταλάβεις την αρχιτεκτονική
- Αποφύγεις common mistakes
- Γράψεις code που follows conventions
- Εξοικονομήσεις χρόνο (λιγότερα bugs, λιγότερες αναθεωρήσεις)

---

### ✅ Step 2: Environment Verification (15 minutes)

**Ελέγξε ότι όλα δουλεύουν**:

```bash
# 1. Check Laravel
php artisan --version

# 2. Check migrations
php artisan migrate:status

# 3. Check routes
php artisan route:list --path=admin

# 4. Check database connection
php artisan tinker
# In tinker: \App\Models\User::count()
# Exit: exit
```

**Expected Results**:
- ✅ Laravel 12.x
- ✅ All migrations run (no pending)
- ✅ Admin routes exist
- ✅ Database connection works

**If something fails**: Fix it before proceeding.

---

### ✅ Step 3: Explore Codebase (30-60 minutes)

**Κάνε μια "tour" του codebase**:

1. **Domain Structure**:
   ```bash
   # Explore existing domains
   ls app/Domain/
   ```
   - Δες πώς είναι structured τα existing domains (Catalog, Orders, etc.)
   - Δες πώς είναι τα Services (π.χ. `app/Domain/Catalog/Services/`)

2. **Content Domain** (Sprint 0 skeleton):
   ```bash
   # Check what exists
   ls app/Domain/Content/
   ```
   - Models exist (Content, ContentType, ContentRevision)
   - Services folder exists (empty)
   - Policies folder exists (empty)

3. **Existing Services Examples**:
   - Read `app/Domain/Catalog/Services/CreateProductService.php`
   - Read `app/Domain/Orders/Services/CreateOrderService.php`
   - **Study the pattern**: Constructor injection, `execute()` method, transactions

4. **Existing Controllers**:
   - Read `app/Http/Controllers/Admin/ProductController.php`
   - **Study the pattern**: Thin controllers, call Services

5. **Form Requests**:
   - Read `app/Http/Requests/Catalog/StoreProductRequest.php`
   - **Study the pattern**: Validation rules, location

6. **Policies**:
   - Read `app/Domain/Catalog/Policies/ProductPolicy.php`
   - **Study the pattern**: Authorization checks

**Goal**: Να καταλάβεις τα patterns που χρησιμοποιούμε.

---

### ✅ Step 4: Start with First Task (Recommended Order)

**Σύμφωνα με το `sprint_1.md`, τα tasks σου είναι**:

#### Task A1 — Admin Content Controllers

**Recommended Starting Point**: Ξεκίνα με **Services πρώτα**, μετά Controllers.

**Why?** Services contain business logic. Controllers depend on Services.

---

## 🎯 Recommended Task Order

### Phase 1: Services (Business Logic) — **START HERE**

**1. `GetContentService`** (Easiest — good starting point)
   - Read existing `GetMenuForBusinessService.php` as reference
   - Implement `bySlug()`, `byType()`, `withRevisions()`
   - Test manually with tinker

**2. `CreateContentService`**
   - Read existing `CreateProductService.php` as reference
   - Implement content creation + initial revision
   - Use DB transactions
   - Test manually

**3. `UpdateContentService`**
   - Similar to CreateContentService
   - Auto-create revision before update
   - Test manually

**4. `DeleteContentService`**
   - Read existing `DeleteProductService.php` as reference
   - Implement soft delete (if needed)
   - Test manually

**5. `PublishContentService`**
   - Update status to 'published'
   - Set `published_at` timestamp
   - Test manually

### Phase 2: Form Requests (Validation)

**6. `StoreContentRequest`**
   - Read existing `StoreProductRequest.php` as reference
   - Validate: title, slug, type, body_json
   - Block validation (simple for Sprint 1)

**7. `UpdateContentRequest`**
   - Similar to StoreContentRequest
   - Allow slug update with unique check

### Phase 3: Policies (Authorization)

**8. `ContentPolicy`**
   - Read existing `ProductPolicy.php` as reference
   - Implement: viewAny, view, create, update, delete
   - Use RBAC: `$user->hasPermission('content.*')`

### Phase 4: Controllers

**9. `Admin/ContentController`**
   - Read existing `Admin/ProductController.php` as reference
   - Implement: index, create, store, edit, update, destroy
   - Use Services (don't put business logic here!)
   - Use Form Requests for validation
   - Use Policies for authorization

**10. `Api/V1/ContentController`**
   - Read existing `Api/V1/SettingsController.php` as reference
   - Implement: show, index, byType
   - Use API Resources for consistent format
   - Only published content accessible

### Phase 5: Routes

**11. Add Routes**
   - Admin routes in `routes/web.php`
   - API routes in `routes/api.php`
   - Test all routes

### Phase 6: Tests

**12. Feature Tests**
   - Content CRUD tests
   - Authorization tests
   - API tests

---

## 🔍 How to Start Each Task

### For Each Service/Controller:

1. **Read Reference**:
   - Find similar existing code
   - Study the pattern
   - Understand the structure

2. **Create File**:
   - Follow naming conventions
   - Use correct namespace
   - Add `declare(strict_types=1);`

3. **Implement**:
   - Follow Service Layer Pattern
   - Use type hints & return types
   - Handle errors properly
   - Use transactions for DB operations

4. **Test Manually**:
   ```bash
   php artisan tinker
   # Test your service/controller
   ```

5. **Run Pint**:
   ```bash
   php vendor/bin/pint
   ```

6. **Commit**:
   ```bash
   git add .
   git commit -m "feat(content): add GetContentService"
   ```

---

## 📝 First Task — Detailed Steps

### Task: Create `GetContentService`

**Step 1: Read Reference**
```bash
# Read similar service
cat app/Domain/Catalog/Services/GetMenuForBusinessService.php
```

**Step 2: Create File**
```bash
# Create service file
touch app/Domain/Content/Services/GetContentService.php
```

**Step 3: Implement** (follow the pattern from reference)

```php
<?php

declare(strict_types=1);

namespace App\Domain\Content\Services;

use App\Domain\Content\Models\Content;
use Illuminate\Database\Eloquent\Collection;

class GetContentService
{
    /**
     * Get published content by slug
     */
    public function bySlug(int $businessId, string $slug): ?Content
    {
        return Content::forBusiness($businessId)
            ->published()
            ->where('slug', $slug)
            ->first();
    }

    /**
     * Get all content of a specific type
     */
    public function byType(int $businessId, string $type): Collection
    {
        return Content::forBusiness($businessId)
            ->ofType($type)
            ->published()
            ->get();
    }

    /**
     * Get content with revision history
     */
    public function withRevisions(int $contentId): ?Content
    {
        return Content::with('revisions.user')
            ->find($contentId);
    }
}
```

**Step 4: Test**
```bash
php artisan tinker

# In tinker:
$service = app(\App\Domain\Content\Services\GetContentService::class);
$content = $service->bySlug(1, 'homepage');
# Should return Content or null
```

**Step 5: Run Pint**
```bash
php vendor/bin/pint app/Domain/Content/Services/GetContentService.php
```

**Step 6: Commit**
```bash
git add app/Domain/Content/Services/GetContentService.php
git commit -m "feat(content): add GetContentService"
```

---

## ✅ Daily Checklist

**Every Day**:

- [ ] `git pull origin develop` (morning)
- [ ] Read sprint notes for updates
- [ ] Work on current task
- [ ] Test manually
- [ ] Run Pint before commit
- [ ] Commit & push regularly
- [ ] Update sprint notes (end of day)

---

## 🆘 If You Get Stuck

1. **Check Documentation**:
   - Re-read relevant section in conventions.md
   - Check architecture.md
   - Check dev-responsibilities.md

2. **Check Existing Code**:
   - Find similar implementation
   - Study the pattern
   - Adapt to your needs

3. **Ask for Help**:
   - Update `sprint_1.md` notes section with question
   - Be specific: "I'm trying to X, but Y happens"
   - Show code & error

---

## 🎯 Success Indicators

You're on the right track when:

- ✅ Code follows conventions (checked with existing code)
- ✅ Services are well-structured (similar to existing Services)
- ✅ Controllers are thin (call Services, no business logic)
- ✅ Type hints everywhere
- ✅ Manual testing works
- ✅ Pint passes
- ✅ No errors in browser/API

---

## 🚀 Ready to Start?

**Recommended Path**:

1. ✅ **Read documentation** (2-3 hours) — **DO THIS FIRST**
2. ✅ **Verify environment** (15 minutes)
3. ✅ **Explore codebase** (30-60 minutes)
4. ✅ **Start with `GetContentService`** (first task)

**After Step 1-3, ξεκίνα με `GetContentService`** — είναι το πιο απλό και θα σε βοηθήσει να καταλάβεις τα patterns.

---

**Questions?** Update `sprint_1.md` notes section.

**Good luck! 🎉**

