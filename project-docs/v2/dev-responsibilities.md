# 👥 Developer Responsibilities & Best Practices

## 📋 Overview

Αυτό το έγγραφο περιγράφει τι πρέπει να κάνει **κάθε dev σε κάθε sprint** πέρα από τα tasks που αναγράφονται. Focus σε **quality, communication, και error prevention**.

### 🧱 Tech Stack Guardrails

- Admin panel χρησιμοποιεί **αποκλειστικά Filament 4.x**.  
- Δεν επιτρέπεται χρήση/εγκατάσταση Filament v2/v3 packages ή παλιών Filament v2/v3 API patterns — ακολουθούμε πάντα τα Filament 4 docs & το Sprint 4.2 plan.

---

## 🎯 General Responsibilities (All Devs)

### 1. Code Quality

#### Before Committing
- [ ] **Code Review (Self)**
  - Διάβασε τον κώδικα σου πριν commit
  - Έλεγξε για typos, syntax errors
  - Έλεγξε για unused code, commented code
- [ ] **Linting**
  - Run `./vendor/bin/pint` (Laravel Pint)
  - Fix all linting errors
  - Consistent code style
- [ ] **Static Analysis** (if available)
  - Run PHPStan (if configured)
  - Fix critical issues

#### Code Standards
- [ ] **Naming Conventions**
  - Follow PSR-12
  - Descriptive names (no `$x`, `$temp`)
  - Consistent naming across project
- [ ] **Comments & Documentation**
  - Document complex logic
  - PHPDoc για public methods
  - Inline comments για non-obvious code
- [ ] **No Debug Code**
  - Remove `dd()`, `dump()`, `var_dump()`
  - Remove `console.log()` (frontend)
  - Remove commented code (unless explaining why)

---

### 2. Testing

#### Unit Tests
- [ ] **Write Tests for Services**
  - Test business logic
  - Test edge cases
  - Test error handling
- [ ] **Test Coverage**
  - Aim for 80%+ coverage για critical paths
  - Test happy paths + error cases

#### Feature Tests
- [ ] **Write Feature Tests**
  - Test user flows
  - Test API endpoints
  - Test admin workflows

#### Before Pushing
- [ ] **Run Tests**
  ```bash
  php artisan test
  ```
- [ ] **All Tests Passing**
  - Fix failing tests
  - Don't push if tests fail

---

### 3. Documentation

#### Code Documentation
- [ ] **Update PHPDoc**
  - Document new methods
  - Document parameters & return types
- [ ] **Update README** (if needed)
  - New features
  - Breaking changes

#### Sprint Documentation
- [ ] **Update Sprint Notes**
  - Progress updates
  - Decisions made
  - Issues encountered
  - Questions for team

---

### 4. Communication

#### Daily Updates
- [ ] **Progress Report**
  - What completed today
  - What working on next
  - Any blockers
- [ ] **Ask Questions Early**
  - Don't wait if stuck
  - Clarify requirements
  - Discuss architecture decisions

#### Code Reviews
- [ ] **Review Other Devs' Code**
  - Check for bugs
  - Suggest improvements
  - Approve if good
- [ ] **Respond to Reviews**
  - Address comments
  - Explain decisions
  - Update code if needed

---

### 5. Error Prevention

#### Before Starting Task
- [ ] **Understand Requirements**
  - Read sprint notes
  - Read related documentation
  - Ask if unclear
- [ ] **Check Dependencies**
  - What needs to be done first?
  - What other devs are working on?
  - Any conflicts?

#### During Development
- [ ] **Test Locally**
  - Test your changes
  - Test edge cases
  - Test error scenarios
- [ ] **Check for Breaking Changes**
  - Will this break existing code?
  - Update related code if needed
  - Document breaking changes

#### Before Completing Task
- [ ] **Integration Test**
  - Test with other modules
  - Test with existing features
  - No regressions
- [ ] **Clean Up**
  - Remove debug code
  - Remove unused files
  - Clean git history (if needed)

---

## 👨‍💻 Dev-Specific Responsibilities

### Dev A (Backend/Infrastructure)

#### Additional Tasks
- [ ] **Database Migrations**
  - Test migrations up & down
  - Check foreign keys
  - Check indexes
  - Test with existing data
- [ ] **API Endpoints**
  - Test all endpoints
  - Check response format
  - Test error responses
  - Test rate limiting
- [ ] **Security**
  - Input validation
  - SQL injection prevention
  - XSS prevention
  - CSRF protection
- [ ] **Performance**
  - Check N+1 queries
  - Add eager loading if needed
  - Check query performance
- [ ] **Error Handling**
  - Proper exception handling
  - User-friendly error messages
  - Log errors appropriately

#### Code Review Focus
- [ ] Database queries (efficiency)
- [ ] Security vulnerabilities
- [ ] API consistency
- [ ] Error handling

---

### Dev B (Architecture/Domain)

#### Additional Tasks
- [ ] **Domain Boundaries**
  - Check module boundaries
  - No cross-domain dependencies
  - Services follow single responsibility
- [ ] **Architecture Consistency**
  - Follow project patterns
  - Consistent service structure
  - Document architectural decisions
- [ ] **Data Integrity**
  - Validate business rules
  - Check data consistency
  - Test edge cases
- [ ] **Caching Strategy**
  - Where to cache?
  - Cache invalidation
  - Cache keys consistency
- [ ] **Migration-Model Consistency** ⭐ **CRITICAL**
  - Cross-reference sprint plan specs with migration fields
  - Verify model `$fillable` matches migration columns
  - Check similar entities have consistent audit fields (`created_by`, `updated_by`, etc.)
  - Verify relationships match foreign keys

#### Code Review Focus
- [ ] Architecture patterns
- [ ] Domain boundaries
- [ ] Service design
- [ ] Business logic correctness
- [ ] **Migration-Model consistency** (new)

---

### Dev C (Frontend/UI)

#### Additional Tasks
- [ ] **UI/UX Testing**
  - Test on different browsers
  - Test responsive design
  - Test accessibility (basic)
- [ ] **Performance**
  - Image optimization
  - CSS/JS minification
  - Lazy loading
- [ ] **Cross-Browser Compatibility**
  - Chrome, Firefox, Safari, Edge
  - Mobile browsers
- [ ] **User Feedback**
  - Loading states
  - Error messages
  - Success confirmations
- [ ] **Accessibility**
  - Semantic HTML
  - ARIA labels (if needed)
  - Keyboard navigation

#### Code Review Focus
- [ ] UI consistency
- [ ] Responsive design
- [ ] User experience
- [ ] Performance

---

## 📋 Sprint-Specific Checklists

### Sprint 0 (Infrastructure)
- [ ] **Dev A**: Test all migrations, verify RBAC working
- [ ] **Dev B**: Verify domain structure, document decisions
- [ ] **Dev C**: Test admin UI on all browsers

### Sprint 1 (Content Module)
- [ ] **Dev B**: Test content services, verify revisions
- [ ] **Dev A**: Test API endpoints, verify validation
- [ ] **Dev C**: Test block editor, verify save/load

### Sprint 2 (Media Library)
- [ ] **Dev A**: Test file uploads, verify variants generation
- [ ] **Dev B**: Test media services, verify folder structure
- [ ] **Dev C**: Test media picker, verify integration with blocks

### Sprint 3 (Content Rendering)
- [ ] **Dev B**: Test block renderer, verify theme fallback
- [ ] **Dev A**: Test public routes, verify 404 handling
- [ ] **Dev C**: Test responsive design, verify SEO tags

### Sprint 4 (RBAC)
- [ ] **Dev B**: Test all permissions, verify role assignments
- [ ] **Dev A**: Test middleware, verify route protection
- [ ] **Dev C**: Test UI, verify menu visibility

### Sprint 5 (API)
- [ ] **Dev A**: Test all endpoints, verify rate limiting
- [ ] **Dev B**: Test API services, verify responses
- [ ] **Dev C**: Test documentation, verify examples

### Sprint 6 (Plugins)
- [ ] **Dev B**: Test plugin system, verify hooks
- [ ] **Dev A**: Test dashboard, verify widgets
- [ ] **Dev C**: Test UX improvements, verify polish

---

## 🚨 Common Mistakes to Avoid

### 1. Database
- ❌ **Don't**: Hardcode IDs, use `where('id', 1)`
- ✅ **Do**: Use relationships, scopes, business_id filtering
- ❌ **Don't**: Forget foreign keys
- ✅ **Do**: Add foreign keys, indexes

### 2. Security
- ❌ **Don't**: Trust user input
- ✅ **Do**: Validate all inputs
- ❌ **Don't**: Expose sensitive data in API
- ✅ **Do**: Use API Resources, hide sensitive fields

### 3. Performance
- ❌ **Don't**: N+1 queries
- ✅ **Do**: Eager load relationships
- ❌ **Don't**: Load all data
- ✅ **Do**: Paginate, filter, limit

### 4. Code Quality
- ❌ **Don't**: Copy-paste code
- ✅ **Do**: Extract to services, reuse
- ❌ **Don't**: Leave debug code
- ✅ **Do**: Clean up before commit

### 5. Testing
- ❌ **Don't**: Skip tests
- ✅ **Do**: Write tests for critical paths
- ❌ **Don't**: Test only happy path
- ✅ **Do**: Test error cases too

---

## 📝 Pre-Commit Checklist

### Blade Views Syntax Check (NEW) ⭐ **CRITICAL**

**Before committing ANY Blade view file:**

- [ ] **Layout Files** (`layouts/*.blade.php`):
  - [ ] Uses `@yield('section_name')` for content areas
  - [ ] Does NOT use `{{ $slot }}` (that's for components)
  - [ ] Can be extended with `@extends('layouts.name')`
  
- [ ] **Component Files** (`components/*.blade.php`):
  - [ ] Uses `{{ $slot }}` for main content
  - [ ] Does NOT use `@yield()` (that's for layouts)
  - [ ] Can be used with `<x-component>` syntax
  
- [ ] **Pages/Views that extend layouts**:
  - [ ] Uses `@extends('layouts.name')`
  - [ ] Uses `@section('section_name')` to fill content
  - [ ] Does NOT use `<x-layout>` syntax (unless layout is a component)

**Quick Verification:**
```bash
# Check for mixed syntax in layouts
grep -r "\$slot" resources/views/layouts/
# Should return empty (layouts use @yield)

# Check for @yield in components
grep -r "@yield" resources/views/components/
# Should return empty (components use $slot)
```

**Reference**: See `project-docs/v2/sprints/sprint_helper/blade_layout_component_guide.md`

---

### Eager Loading Check (Sprint 4) ⭐ **NEW**

**Before committing ANY service method that uses relationships:**

- [ ] **Identify all relationships used**:
  - [ ] Check for `$model->relationship->method()` patterns
  - [ ] Check for `$model->relationship` access
  - [ ] Check for nested relationships: `$model->relationship->nested`
  
- [ ] **Add eager loading**:
  - [ ] Add `->with('relationship')` to query
  - [ ] For nested: `->with('relationship.nested')`
  - [ ] For multiple: `->with(['relationship1', 'relationship2'])`
  
- [ ] **Verify no N+1 queries**:
  - [ ] Test in tinker: `$model->relationship` should not trigger extra query
  - [ ] Check query log for multiple queries

**Quick Verification:**
```bash
# Check for relationship access without eager loading
grep -r "->.*->" app/Domain/*/Services/*.php
# Verify all have ->with() in the query above
```

**Reference**: `project-docs/v2/sprints/sprint_4/reviews/sprint_4_review_devb_fixes_guide.md`

---

### Business Isolation Check (Sprint 4) ⭐ **NEW**

**Before committing ANY query that touches multi-tenant data:**

- [ ] **Identify multi-tenant data**:
  - [ ] Products, Categories, Content, Media, Layouts, Modules
  - [ ] Any data that belongs to a business
  
- [ ] **Get business_id from context**:
  - [ ] From `$module->business_id` (in module views)
  - [ ] From `$content->business_id` (in content views)
  - [ ] From `$request` or `auth()->user()->business_id`
  
- [ ] **Add business scoping**:
  - [ ] Add `->where('business_id', $businessId)` to query
  - [ ] Verify `$businessId` is not null before querying
  - [ ] Add fallback if business_id is missing

**Quick Verification:**
```bash
# Check for multi-tenant queries without business_id
grep -r "Product::\|Category::\|Content::" resources/views/themes/default/modules/*.blade.php
# Verify all have ->where('business_id', ...)
```

**Reference**: `project-docs/v2/sprints/sprint_4/reviews/sprint_4_review_devb_fixes_guide.md`

---

### Blade Helper Namespace Check (Sprint 4) ⭐ **NEW**

**Before committing ANY Blade view that uses helper classes:**

- [ ] **Identify helper usage**:
  - [ ] `Str::`, `Arr::`, `Collection::`, etc.
  - [ ] Check for bare helper calls without namespace
  
- [ ] **Add namespace**:
  - [ ] Use full namespace: `\Illuminate\Support\Str::method()`
  - [ ] OR add `@php use Illuminate\Support\Str; @endphp` at top
  
- [ ] **Verify existing patterns**:
  - [ ] Check similar views in codebase
  - [ ] Follow same pattern

**Quick Verification:**
```bash
# Check for helper usage without namespace
grep -r "Str::\|Arr::\|Collection::" resources/views/themes/default/modules/*.blade.php
# Verify all have full namespace or use statement
```

**Reference**: `project-docs/v2/sprints/sprint_4/reviews/sprint_4_review_devb_fixes_guide.md`

---

## 📝 Pre-Commit Checklist (Original)

Before committing code, check:

- [ ] **Run Laravel Pint**: `./vendor/bin/pint app/Domain/{Domain}` (fixes formatting automatically)
- [ ] Code follows PSR-12
- [ ] No linting errors
- [ ] All tests passing
- [ ] No debug code (`dd()`, `dump()`, etc.)
- [ ] No commented code (unless explaining)
- [ ] PHPDoc updated (if needed)
- [ ] **No hardcoded values** (see Hardcoded Values Checklist below) ⭐ **ENHANCED**
- [ ] Security checks (validation, sanitization)
- [ ] Performance checks (no N+1, eager loading)
- [ ] Error handling in place
- [ ] Sprint notes updated (if significant change)
- [ ] **Task Completion Verification** (see below) ⭐ **NEW**
- [ ] **Consistency Check** (see below)
- [ ] **Relationship Chain Verification** (if adding relationships) ⭐ **NEW**
- [ ] **Blade Views Syntax Check** (if modifying Blade views) ⭐ **NEW** (see below)

### ✅ Task Completion Verification (Critical for All Devs) ⭐ **NEW**

**Before marking task as complete:**

1. **Re-read task description** (open sprint file)
2. **List ALL deliverables** (every bullet point)
3. **List ALL acceptance criteria**
4. **For each deliverable:**
   - [ ] File exists? (verify with `ls` or file search)
   - [ ] Used in code? (verify with `grep` or code search)
   - [ ] Follows conventions?
5. **For each acceptance criteria:**
   - [ ] Can I verify it? (test manually or automated)
   - [ ] Is it actually met?

**For Relationships (Dev B Specific):**
6. **For each relationship mentioned in spec:**
   - [ ] Migration has foreign key column?
   - [ ] Model has relationship method?
   - [ ] Model has column in `$fillable`?
   - [ ] Service sets foreign key (if creating records)?
   - [ ] Resource/Controller uses relationship? (if yes, verify it exists)
   - [ ] **See**: `project-docs/v2/sprints/sprint_helper/relationship_implementation_guide.md` for detailed checklist

**Example for API Task:**
- [ ] Controller created
- [ ] Routes registered
- [ ] **API Resource created** (if mentioned in deliverables)
- [ ] Controller uses API Resource (if required)
- [ ] **Paginated responses use `$this->paginated()` helper** (not manual JSON)
- [ ] **Single resource uses `$this->success(new Resource())`**
- [ ] **Errors use `$this->error()` helper**
- [ ] Error handling implemented
- [ ] **Error codes documented** (if "documentation" mentioned)
- [ ] Rate limiting configured
- [ ] Tests written (if required)

**Example for Documentation Task:**
- [ ] **Separate file created** (not just comments)
- [ ] Location follows pattern (`project-docs/v2/`)
- [ ] Format follows existing docs (Markdown)
- [ ] Content includes examples, solutions, implementation details

**Key Rule**: If task says "documentation", it means **separate file**, not just comments!

### 🔍 Consistency Check (Critical for Dev B)

**Before committing migrations/models:**

- [ ] **Cross-reference with Sprint Plan**: Verify all fields from sprint plan are in migration
- [ ] **Pattern Consistency**: If similar entities exist (e.g., Content, MediaFolder), check they have same audit fields:
  - `created_by` (if one has it, similar ones should too)
  - `updated_by` (if applicable)
  - `deleted_at` (if soft deletes)
- [ ] **Model-Migration Match**: Verify model `$fillable` matches migration columns
- [ ] **Relationship Consistency**: If entity A has relationship to User, similar entity B should too
- [ ] **Index Consistency**: Similar entities should have similar indexes

**Example Checklist for MediaFolder:**
- [ ] Sprint plan specifies `created_by` → Check migration has it ✅
- [ ] Content model has `created_by` → MediaFolder should too ✅
- [ ] Content model has `creator()` relationship → MediaFolder should too ✅
- [ ] Migration has foreign key → Model has relationship method ✅

### 🔗 Relationship Implementation Checklist (NEW) ⭐ **CRITICAL**

**When adding a relationship (e.g., `creator()`, `folder()`, etc.):**

**Step 1: Check Sprint Spec**
- [ ] Read spec deliverables for relationships list
- [ ] Note ALL relationships mentioned (every bullet point)
- [ ] Cross-reference with Acceptance Criteria

**Step 2: Migration Verification**
- [ ] Foreign key column exists in migration (e.g., `created_by`)
- [ ] Foreign key constraint added: `->constrained('users')->nullOnDelete()`
- [ ] Index added if needed (for frequently queried relationships)

**Step 3: Model Verification**
- [ ] Column added to `$fillable` array
- [ ] Relationship method created: `public function creator(): BelongsTo`
- [ ] Correct foreign key specified: `'created_by'`
- [ ] Correct model class imported: `use App\Models\User;`

**Step 4: Service Verification** (if creating records)
- [ ] Service sets the foreign key when creating records
- [ ] Example: `'created_by' => auth()->id()`

**Step 5: Resource/Controller Verification** (if used in API/Views)
- [ ] Check if Resource uses the relationship (e.g., `$media->creator`)
- [ ] If used, verify relationship exists in model
- [ ] Test that relationship loads correctly

**Step 6: Cross-Reference Pattern**
```
Sprint Spec → Migration → Model → Service → Resource/Controller
     ↓            ↓         ↓        ↓            ↓
  creator()   created_by  creator() created_by  $media->creator
```

**Example Verification for Media Model `creator()`:**
```bash
# 1. Check migration
grep "created_by" database/migrations/v2_*_create_media_table.php
# Should show: $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

# 2. Check model fillable
grep "created_by" app/Domain/Media/Models/Media.php
# Should show in $fillable array

# 3. Check relationship method
grep "function creator" app/Domain/Media/Models/Media.php
# Should show: public function creator(): BelongsTo

# 4. Check service
grep "created_by" app/Domain/Media/Services/UploadMediaService.php
# Should show: 'created_by' => auth()->id(),

# 5. Check resource (if used)
grep "creator" app/Http/Resources/MediaResource.php
# Should show: 'creator' => $media->creator ? [...]
```

**Common Mistakes to Avoid:**
- ❌ **Mistake**: Adding relationship method but forgetting foreign key in migration
- ✅ **Fix**: Always check migration first, then add relationship
- ❌ **Mistake**: Adding foreign key in migration but forgetting `$fillable`
- ✅ **Fix**: Always add to `$fillable` when adding foreign key
- ❌ **Mistake**: Resource uses relationship but model doesn't have it
- ✅ **Fix**: Check all Resources/Controllers that use the model before committing
- ❌ **Mistake**: Service creates record but doesn't set foreign key
- ✅ **Fix**: Check all services that create records

---

## 🔄 Pre-Push Checklist

Before pushing to repository:

- [ ] All tests passing locally
- [ ] Code reviewed (self-review)
- [ ] No merge conflicts
- [ ] Branch up to date with main
- [ ] Commit messages clear
- [ ] Related documentation updated
- [ ] Sprint notes updated

---

## 🐛 Bug Prevention Tips

### 1. Always Test
- Test locally before pushing
- Test edge cases
- Test error scenarios

### 2. Read Code
- Read related code before changing
- Understand dependencies
- Check for side effects

### 3. Ask Questions
- Don't assume
- Clarify requirements
- Discuss architecture

### 4. Review Your Code
- Read your code as if reviewing
- Look for bugs
- Look for improvements

### 5. Use Tools
- IDE warnings
- Linters
- Static analysis
- Tests

### 6. Consistency Checks (Dev B Specific) ⭐
- **Cross-reference sprint plan**: Before creating migration, verify all fields from sprint plan are included
- **Pattern matching**: If similar entities exist (Content, MediaFolder), check they follow same patterns
- **Model-Migration sync**: After creating migration, verify model `$fillable` matches all columns
- **Relationship audit**: If one entity has `created_by`, similar entities should too
- **Self-review checklist**: Use the Consistency Check section above before every commit
- **Relationship Implementation Checklist**: Use the new Relationship Implementation Checklist for every relationship

### 7. Relationship Chain Verification (Dev B Specific) ⭐ **NEW**

**Before marking a task complete, verify the entire chain:**

**For each relationship in the spec:**
1. **Migration** → Has foreign key column?
2. **Model** → Has relationship method + `$fillable` entry?
3. **Service** → Sets foreign key when creating?
4. **Resource/Controller** → Uses relationship? (if yes, verify it exists)

### 8. Data Flow Verification (Dev B Specific) ⭐ **NEW** (Sprint 3)

**Before marking block views complete, verify data flow:**

**For each block type:**
1. **Admin Controller** → What props does it save? (prop names, format)
2. **Block View** → What props does it expect? (must match controller)
3. **Media Loading** → Does it handle controller format? (with fallbacks)
4. **End-to-End Test** → Create test content, verify rendering works

**Quick Verification**:
```bash
# 1. Check Admin Controller
grep -A 10 "if.*{block}" app/Http/Controllers/Admin/ContentController.php

# 2. Check block view props
grep "\$" resources/views/themes/default/blocks/{block}.blade.php

# 3. Test in tinker
php artisan tinker
>>> $content = Content::first();
>>> $block = collect($content->body_json)->firstWhere('type', '{block}');
>>> $block['props']; // Verify format matches view expectations
```

**Common Mistakes to Avoid**:
- ❌ **Mistake**: Controller saves `image_id` but view uses `$image`
- ✅ **Fix**: Use same prop name in both places
- ❌ **Mistake**: Controller saves array of objects but view expects array of IDs
- ✅ **Fix**: Handle both formats in view (with fallback)
- ❌ **Mistake**: View doesn't check if media exists
- ✅ **Fix**: Always check for null before accessing media properties

**See**: `project-docs/v2/sprints/sprint_helper/data_flow_verification_guide.md` for detailed guide

**Quick Verification Command:**
```bash
# For a relationship like creator()
# 1. Check migration
grep -r "created_by" database/migrations/v2_*_create_media_table.php

# 2. Check model
grep -A 3 "function creator" app/Domain/Media/Models/Media.php

# 3. Check service
grep "created_by" app/Domain/Media/Services/*.php

# 4. Check resource
grep "creator" app/Http/Resources/MediaResource.php
```

**If ANY step fails, the relationship is incomplete!**

### 🚫 Hardcoded Values Prevention Checklist ⭐ **NEW**

**Before committing code, check for hardcoded values:**

**Common Hardcoded Values to Avoid:**
- ❌ **User IDs**: `created_by => 1`, `user_id => 1`
  - ✅ **Fix**: Use `auth()->id()`, `User::where('is_admin', true)->first()`, or get from database
- ❌ **Business IDs**: `business_id => 1`
  - ✅ **Fix**: Use `Business::active()->firstOrFail()`, or get from request/context
- ❌ **Status Values**: `status => 'published'` (if should be dynamic)
  - ✅ **Fix**: Use constants, enums, or get from request
- ❌ **File Paths**: `'/storage/uploads'`
  - ✅ **Fix**: Use `Storage::disk()`, `config()`, or `env()`
- ❌ **URLs**: `'https://example.com'`
  - ✅ **Fix**: Use `url()`, `route()`, `config('app.url')`
- ❌ **Magic Numbers**: `->limit(10)` (if should be configurable)
  - ✅ **Fix**: Use constants, config values, or request parameters

**Verification Commands:**
```bash
# Check for hardcoded user IDs
grep -r "created_by.*=>.*[0-9]" app/ --exclude-dir=vendor
grep -r "user_id.*=>.*[0-9]" app/ --exclude-dir=vendor

# Check for hardcoded business IDs
grep -r "business_id.*=>.*[0-9]" app/ --exclude-dir=vendor

# Check for hardcoded URLs
grep -r "http://\|https://" app/ --exclude-dir=vendor | grep -v "config\|env\|url()"
```

**Pattern Recognition:**
- **In Commands**: Always get user/business from database, not hardcoded
- **In Services**: Use `auth()->id()` or get from context, not hardcoded
- **In Controllers**: Get from request/context, not hardcoded
- **In Migrations/Seeders**: Hardcoded values are OK (they're data, not logic)

**Example - Wrong:**
```php
// ❌ DON'T DO THIS
$this->createContentService->execute([
    'created_by' => 1, // Hardcoded!
]);
```

**Example - Correct:**
```php
// ✅ DO THIS
$adminUser = User::where('is_admin', true)->first() 
    ?? User::find(1); // Fallback only

$this->createContentService->execute([
    'created_by' => $adminUser->id, // Dynamic!
]);
```

**Or:**
```php
// ✅ DO THIS (if in authenticated context)
$this->createContentService->execute([
    'created_by' => auth()->id(), // From authenticated user
]);
```

---

## 📚 Resources

### Project Documentation
- [v2 Overview](./v2_overview.md) — Architecture, strategy & technical specs
- [v2 Migration Guide](./v2_migration_guide.md) — Migration steps
- [Project Conventions](../conventions.md)
- [Architecture Documentation](../architecture.md)
- [Relationship Implementation Guide](./sprints/sprint_helper/relationship_implementation_guide.md) ⭐ **NEW** — Step-by-step guide for relationships
- [Data Flow Verification Guide](./sprints/sprint_helper/data_flow_verification_guide.md) ⭐ **NEW** — Step-by-step guide for data flow verification

### External Resources
- [Laravel Best Practices](https://laravel.com/docs)
- [PSR-12 Coding Standard](https://www.php-fig.org/psr/psr-12/)

---

**Last Updated**: 2024-11-27

---

## 🎯 Enhanced Prevention Patterns (After Sprint 2 Review)

### Pattern 1: Relationship Chain Verification

**Problem**: Missing `creator()` relationship in Media model despite being in spec.

**Root Cause**: 
- Didn't verify complete chain: Migration → Model → Service → Resource
- Assumed migration was correct without checking model
- Didn't check if Resource uses the relationship

**Prevention Pattern**:
```
For EVERY relationship in spec:
1. Migration: foreign key exists? ✅
2. Model: relationship method exists? ✅
3. Model: column in $fillable? ✅
4. Service: sets foreign key? ✅
5. Resource/Controller: uses relationship? ✅ (if yes, verify it exists)
```

**Checklist Added**: See "Relationship Implementation Checklist" above.

---

### Pattern 4: Hardcoded Values Prevention (Sprint 3)

**Problem**: Hardcoded user ID (`created_by => 1`) in migration command.

**Root Cause**: 
- Didn't consider command runs in CLI (no auth context)
- Used hardcoded value instead of database query
- Didn't check for hardcoded values before commit

**Prevention Pattern**:
```
For EVERY ID/value in code:
1. Is it hardcoded? (check with grep)
2. Can it be dynamic? (get from database/context)
3. Is there a pattern? (check existing similar code)
4. Use dynamic value (auth()->id(), query, or context)
```

**Checklist Added**: See "Hardcoded Values Prevention Checklist" above.

**Verification Commands**:
```bash
# Before commit, run:
grep -r "created_by.*=>.*[0-9]" app/
grep -r "business_id.*=>.*[0-9]" app/
```

---

### Pattern 2: Cross-Reference Before Commit

**Problem**: Formatting issues (Laravel Pint violations).

**Root Cause**:
- Didn't run Pint before committing
- Assumed code was formatted correctly

**Prevention Pattern**:
```
Before EVERY commit:
1. Run: ./vendor/bin/pint app/Domain/{Domain}
2. Fix all formatting issues
3. Verify no linting errors
```

**Added to Pre-Commit Checklist**: Always run Pint before commit.

---

### Pattern 3: Resource Dependency Check

**Problem**: MediaResource uses `$media->creator` but relationship didn't exist.

**Root Cause**:
- Didn't check what Resources/Controllers use the model
- Assumed model was complete without checking dependencies

**Prevention Pattern**:
```
Before marking model complete:
1. Search for model usage: grep -r "Media" app/Http/Resources/
2. Check if any Resource uses relationships
3. Verify all used relationships exist in model
4. Test Resource in tinker: new MediaResource($media)->toArray()
```

**Added to Relationship Checklist**: Step 5 - Resource/Controller Verification.

---

### Pattern 5: Data Flow Verification (Sprint 3) ⭐ **NEW**

**Problem**: Hero & Gallery block views had data flow mismatches with Admin Controller.

**Root Cause**: 
- Didn't verify how Admin Controller saves block props
- Assumed view would receive props in expected format
- Didn't do end-to-end test before commit

**Prevention Pattern**:
```
For EVERY block view:
1. Check Admin Controller: How does it save props? (prop names, format)
2. Write Block View: Match controller format
3. Add Fallbacks: Support legacy formats
4. End-to-End Test: Create test content, verify rendering
```

**Checklist Added**: See "Data Flow Verification" section above.

**Verification Commands**:
```bash
# Before commit, run:
grep -A 10 "if.*hero\|if.*gallery" app/Http/Controllers/Admin/ContentController.php
grep "\$" resources/views/themes/default/blocks/{block}.blade.php

# Test in tinker:
php artisan tinker
>>> $content = Content::first();
>>> $block = collect($content->body_json)->firstWhere('type', '{block}');
>>> $block['props']; // Verify format
```

**See**: `project-docs/v2/sprints/sprint_helper/data_flow_verification_guide.md` for detailed guide

---

### Pattern 6: Blade Layout vs Component Syntax (Sprint 3)

**Problem**: `Undefined variable $slot` error when using layout syntax with component syntax.

**Root Cause**: 
- Mixed `@yield()` (layout syntax) with `{{ $slot }}` (component syntax)
- Didn't verify syntax matches file type (layout vs component)
- Didn't check existing patterns in codebase

**Prevention Pattern**:
```
For EVERY Blade view file:
1. Is it a layout? (layouts/*.blade.php) → Use @yield('content')
2. Is it a component? (components/*.blade.php) → Use {{ $slot }}
3. Does it extend a layout? → Use @extends/@section
4. Verify syntax matches file type (check existing similar files)
```

**Checklist Added**: See "Blade Views Syntax Check" in Pre-Commit Checklist above.

**Verification Commands**:
```bash
# Before commit, run:
grep -r "\$slot" resources/views/layouts/
# Should return empty (layouts use @yield)

grep -r "@yield" resources/views/components/
# Should return empty (components use $slot)
```

**Reference**: `project-docs/v2/sprints/sprint_helper/blade_layout_component_guide.md`

---

### Pattern 7: Eager Loading for Relationships (Sprint 4) ⭐ **NEW**

**Problem**: N+1 query issues when accessing relationships without eager loading.

**Root Cause**: 
- Accessed `$layout->business->getTheme()` without eager loading
- Accessed `$module->business` in views without eager loading
- Didn't check if relationships were loaded before use

**Prevention Pattern**:
```
For EVERY service method that uses relationships:
1. Identify all relationships used in the method
2. Add ->with('relationship') to the query
3. For nested relationships: ->with('relationship.nested')
4. Check if relationship is loaded before accessing (if dynamic)
```

**Checklist Added**: See "Eager Loading Checklist" below.

**Verification Commands**:
```bash
# Before commit, check for relationship access:
grep -r "->.*->" app/Domain/*/Services/*.php
# Verify all have ->with() in the query above

# Test in tinker:
php artisan tinker
>>> $layout = Layout::find(1);
>>> $layout->business; // Should not trigger extra query if eager loaded
```

**Reference**: `project-docs/v2/sprints/sprint_4/reviews/sprint_4_review_devb_fixes_guide.md`

---

### Pattern 8: Business Isolation in Multi-Tenant Queries (Sprint 4) ⭐ **NEW**

**Problem**: Catalog modules (menu, products-grid, categories-list) loaded data from all businesses.

**Root Cause**: 
- Didn't scope queries with business_id
- Assumed business context was implicit
- Didn't verify multi-tenant data isolation

**Prevention Pattern**:
```
For EVERY query that touches multi-tenant data:
1. Identify if data is business-scoped (products, categories, content, media, etc.)
2. Get business_id from context (module, content, request, etc.)
3. Add ->where('business_id', $businessId) to query
4. Verify business_id is available before querying
```

**Checklist Added**: See "Business Isolation Checklist" below.

**Verification Commands**:
```bash
# Before commit, check for multi-tenant queries:
grep -r "Product::\|Category::\|Content::" resources/views/themes/default/modules/*.blade.php
# Verify all have ->where('business_id', ...)

# Test in tinker:
php artisan tinker
>>> $products = Product::all(); // Should be scoped
>>> $products->pluck('business_id')->unique(); // Should return only current business
```

**Reference**: `project-docs/v2/sprints/sprint_4/reviews/sprint_4_review_devb_fixes_guide.md`

---

### Pattern 9: Blade Helper Namespace (Sprint 4) ⭐ **NEW**

**Problem**: Used `Str::limit()` in Blade views without full namespace.

**Root Cause**: 
- Assumed helper classes are auto-imported in Blade
- Didn't check if namespace is required
- Didn't verify existing patterns in codebase

**Prevention Pattern**:
```
For EVERY Blade view that uses helper classes:
1. Use full namespace: \Illuminate\Support\Str::method()
2. OR add @php use statement at top: @php use Illuminate\Support\Str; @endphp
3. Check existing patterns in similar views
```

**Checklist Added**: See "Blade Helper Namespace Check" below.

**Verification Commands**:
```bash
# Before commit, check for helper usage:
grep -r "Str::\|Arr::\|Collection::" resources/views/themes/default/modules/*.blade.php
# Verify all have full namespace or use statement
```

**Reference**: `project-docs/v2/sprints/sprint_4/reviews/sprint_4_review_devb_fixes_guide.md`

---

### Pattern 10: Update Service Business Isolation (Sprint 4) ⭐ **NEW**

**Problem**: `UpdateModuleInstanceService` allowed changing `business_id`, breaking multi-tenant isolation.

**Root Cause**: 
- Didn't validate immutable fields in update operations
- Assumed business_id wouldn't be in $data array
- Didn't check for security vulnerabilities

**Prevention Pattern**:
```
For EVERY update service method:
1. Identify immutable fields (business_id, id, created_at, etc.)
2. Check if $data contains immutable fields
3. Validate that values match existing model values
4. Throw ValidationException if trying to change immutable field
```

**Checklist Added**: See "Update Service Business Isolation Check" below.

**Verification Commands**:
```bash
# Before commit, check for update services:
grep -r "public function.*update\|public function execute" app/Domain/*/Services/*Service.php
# Verify all have business_id validation

# Test in tinker:
php artisan tinker
>>> $module = ModuleInstance::first();
>>> $service = app(UpdateModuleInstanceService::class);
>>> $service->execute($module, ['business_id' => 999]); // Should fail
```

**Reference**: `project-docs/v2/sprints/sprint_4/reviews/sprint_4_review_deva_fixes_guide.md`

---

### Pattern 11: Related Model Loading with Business Scoping (Sprint 4) ⭐ **NEW**

**Problem**: `AssignModuleToContentService` loaded Layout without business scoping, allowing cross-business access.

**Root Cause**: 
- Used `Layout::findOrFail()` without business scoping
- Assumed foreign key would be safe
- Didn't verify business isolation for related models

**Prevention Pattern**:
```
For EVERY related model loading from foreign key:
1. Identify if model is business-scoped (Layout, ModuleInstance, etc.)
2. Get business_id from parent model (content, assignment, etc.)
3. Use Model::forBusiness($businessId)->findOrFail($id)
4. Never use Model::findOrFail($id) for business-scoped models
```

**Checklist Added**: See "Related Model Loading Check" below.

**Verification Commands**:
```bash
# Before commit, check for related model loading:
grep -r "::findOrFail\|::find(" app/Domain/*/Services/*Service.php
# Verify business-scoped models use ->forBusiness()

# Test in tinker:
php artisan tinker
>>> $content = Content::first();
>>> $layout = Layout::findOrFail($content->layout_id); // Should use forBusiness()
```

**Reference**: `project-docs/v2/sprints/sprint_4/reviews/sprint_4_review_deva_fixes_guide.md`

---

## 📋 Quick Reference: Common Mistakes & Fixes

| Mistake | Fix | Checklist Item |
|---------|-----|----------------|
| Relationship in spec but not in model | Add relationship method + foreign key | Relationship Checklist Step 3 |
| Foreign key in migration but not in `$fillable` | Add to `$fillable` array | Relationship Checklist Step 3 |
| Resource uses relationship but model doesn't have it | Add relationship to model | Relationship Checklist Step 5 |
| Service creates record but doesn't set foreign key | Add foreign key to create array | Relationship Checklist Step 4 |
| Formatting issues (Pint violations) | Run `./vendor/bin/pint` | Pre-Commit Checklist |
| Missing relationship in similar entities | Check pattern consistency | Consistency Check |
| Data flow mismatch (block views) | Verify Admin Controller → Block View | Data Flow Verification |
| Prop name mismatch | Use same prop names as controller | Data Flow Verification |
| Format mismatch (array vs objects) | Handle both formats in view | Data Flow Verification |
| Blade syntax mixing (`$slot` in layouts) | Use `@yield('content')` in layouts, `$slot` in components | Blade Views Syntax Check |
| N+1 query (relationship access) | Add ->with('relationship') to query | Eager Loading Checklist |
| Multi-tenant query without business_id | Add ->where('business_id', $businessId) | Business Isolation Checklist |
| Helper class without namespace in Blade | Use \Illuminate\Support\Str::method() | Blade Helper Namespace Check |
| Update service allows business_id change | Add validation to prevent immutable field changes | Update Service Business Isolation Check |
| Related model loading without business scoping | Use Model::forBusiness($id)->findOrFail() | Related Model Loading Check |

