# 📚 Sprint 3 — Lessons Learned (Dev A)

## 🔍 Γιατί έγινε το λάθος;

### **Hardcoded User ID in Migration Command (Task A2)**

**Πρόβλημα:**
- `MigrateStaticPagesToCms` command χρησιμοποιούσε `created_by => 1` (hardcoded)
- Δεν ήταν dynamic, δεν έπαιρνε admin user από database
- Δεν είχε fallback handling

**Root Cause:**
- ❌ Δεν σκέφτηκα ότι το command τρέχει από CLI (no auth context)
- ❌ Χρησιμοποίησα hardcoded value αντί για database query
- ❌ Δεν έκανα pattern matching: "Πώς παίρνουμε admin user σε commands;"
- ❌ Δεν έλεγξα για hardcoded values πριν commit

**Fix Applied:**
- Changed hardcoded `1` → dynamic admin user lookup
- Gets admin user from database: `User::where('is_admin', true)->first()`
- Fallback to user ID 1 if no admin found
- Error handling if no user found

**Files Fixed:**
- `app/Console/Commands/MigrateStaticPagesToCms.php` (handle method)

**Lesson Learned**: Never use hardcoded IDs in business logic. Always get from database or context.

---

## ✅ Πώς να αποφύγουμε τέτοια σφάλματα στο μέλλον

### 1. **Hardcoded Values Pattern Recognition**

**ΠΡΙΝ commit, check για:**

- [ ] **User IDs**: `created_by => 1`, `user_id => 1`
  - ✅ **Fix**: `auth()->id()` (if authenticated) or `User::where(...)->first()->id`
- [ ] **Business IDs**: `business_id => 1`
  - ✅ **Fix**: `Business::active()->firstOrFail()->id` or from context
- [ ] **URLs**: `'https://example.com'`
  - ✅ **Fix**: `url()`, `route()`, `config('app.url')`
- [ ] **File Paths**: `'/storage/uploads'`
  - ✅ **Fix**: `Storage::disk()`, `config()`
- [ ] **Magic Numbers**: `->limit(10)` (if should be configurable)
  - ✅ **Fix**: Constants, config values, request parameters

**Verification Commands:**
```bash
# Check for hardcoded user IDs
grep -r "created_by.*=>.*[0-9]" app/ --exclude-dir=vendor
grep -r "user_id.*=>.*[0-9]" app/ --exclude-dir=vendor

# Check for hardcoded business IDs
grep -r "business_id.*=>.*[0-9]" app/ --exclude-dir=vendor
```

---

### 2. **Command-Specific Patterns**

**Για Artisan Commands:**

**❌ Wrong:**
```php
public function handle(): int
{
    $this->createContentService->execute([
        'created_by' => 1, // Hardcoded!
    ]);
}
```

**✅ Correct:**
```php
public function handle(): int
{
    // Get admin user from database
    $adminUser = User::where('is_admin', true)->first() 
        ?? User::find(1); // Fallback only
    
    if (! $adminUser) {
        $this->error('No admin user found.');
        return Command::FAILURE;
    }
    
    $this->createContentService->execute([
        'created_by' => $adminUser->id, // Dynamic!
    ]);
}
```

**Pattern:**
1. Query database for user (with criteria)
2. Fallback to safe default (if needed)
3. Error handling if not found
4. Use dynamic value

---

### 3. **Service-Specific Patterns**

**Για Services (authenticated context):**

**❌ Wrong:**
```php
public function execute(array $data): Content
{
    $data['created_by'] = 1; // Hardcoded!
    return Content::create($data);
}
```

**✅ Correct:**
```php
public function execute(array $data): Content
{
    $data['created_by'] = $data['created_by'] ?? auth()->id(); // Dynamic!
    return Content::create($data);
}
```

**Pattern:**
- Use `auth()->id()` if in authenticated context
- Allow override via parameter
- Fallback to auth if not provided

---

### 4. **Pre-Commit Checklist Enhancement**

**Before committing:**

- [ ] **Run grep checks** for hardcoded IDs:
  ```bash
  grep -r "created_by.*=>.*[0-9]" app/
  grep -r "business_id.*=>.*[0-9]" app/
  ```
- [ ] **Check commands**: Do they get users from database?
- [ ] **Check services**: Do they use `auth()->id()` or get from context?
- [ ] **Check controllers**: Do they get IDs from request/context?
- [ ] **Verify**: No magic numbers (use constants/config)

---

### 5. **Pattern Matching**

**Before writing code:**

- [ ] **Check existing similar code**: How do other commands get users?
- [ ] **Check existing services**: How do they handle `created_by`?
- [ ] **Check existing controllers**: How do they get business/user IDs?

**Example:**
```
Before writing MigrateStaticPagesToCms:
1. Check other commands (if any) ✅
2. See how services handle created_by ✅
3. Use same pattern ✅
```

---

## 📋 Enhanced Checklist for Commands

**When creating Artisan commands:**

- [ ] **User IDs**: Get from database, not hardcoded
- [ ] **Business IDs**: Get from database or context
- [ ] **Error Handling**: Check if user/business exists
- [ ] **Fallback**: Safe fallback if primary query fails
- [ ] **Output**: Informative messages for user

**Example Checklist:**
```markdown
## Artisan Command Verification

### User/Business Handling:
- [ ] Gets user from database (not hardcoded)
- [ ] Has fallback if primary query fails
- [ ] Error handling if not found
- [ ] Informative error messages

### Data Creation:
- [ ] Uses services (not direct model creation)
- [ ] Passes dynamic IDs (not hardcoded)
- [ ] Proper error handling
```

---

## 🎯 Key Takeaways

1. **Never Hardcode IDs** — Always get from database or context
2. **Pattern Matching** — Check existing similar code before writing
3. **Command Context** — Commands run in CLI (no auth), need database queries
4. **Service Context** — Services can use `auth()->id()` if authenticated
5. **Verification** — Use grep to check for hardcoded values before commit

---

## 📚 Related Documentation

- **Dev Responsibilities**: `project-docs/v2/dev-responsibilities.md` (Enhanced with Hardcoded Values Checklist)
- **Conventions**: `project-docs/conventions.md` (Section 15.5 - Hardcoded Values Prevention)
- **Sprint 3 Review**: `project-docs/v2/sprints/sprint_3/reviews/sprint_3_review_deva.md`

---

**Last Updated**: 2024-11-27  
**Created by**: Dev A (Sprint 3 Review)

