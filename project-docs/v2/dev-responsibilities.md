# 👥 Developer Responsibilities & Best Practices

## 📋 Overview

Αυτό το έγγραφο περιγράφει τι πρέπει να κάνει **κάθε dev σε κάθε sprint** πέρα από τα tasks που αναγράφονται. Focus σε **quality, communication, και error prevention**.

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

Before committing code, check:

- [ ] **Run Laravel Pint**: `./vendor/bin/pint app/Domain/{Domain}` (fixes formatting automatically)
- [ ] Code follows PSR-12
- [ ] No linting errors
- [ ] All tests passing
- [ ] No debug code (`dd()`, `dump()`, etc.)
- [ ] No commented code (unless explaining)
- [ ] PHPDoc updated (if needed)
- [ ] No hardcoded values
- [ ] Security checks (validation, sanitization)
- [ ] Performance checks (no N+1, eager loading)
- [ ] Error handling in place
- [ ] Sprint notes updated (if significant change)
- [ ] **Task Completion Verification** (see below) ⭐ **NEW**
- [ ] **Consistency Check** (see below)
- [ ] **Relationship Chain Verification** (if adding relationships) ⭐ **NEW**

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

---

## 📚 Resources

### Project Documentation
- [v2 Overview](./v2_overview.md) — Architecture, strategy & technical specs
- [v2 Migration Guide](./v2_migration_guide.md) — Migration steps
- [Project Conventions](../conventions.md)
- [Architecture Documentation](../architecture.md)
- [Relationship Implementation Guide](./sprints/sprint_helper/relationship_implementation_guide.md) ⭐ **NEW** — Step-by-step guide for relationships

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

## 📋 Quick Reference: Common Mistakes & Fixes

| Mistake | Fix | Checklist Item |
|---------|-----|----------------|
| Relationship in spec but not in model | Add relationship method + foreign key | Relationship Checklist Step 3 |
| Foreign key in migration but not in `$fillable` | Add to `$fillable` array | Relationship Checklist Step 3 |
| Resource uses relationship but model doesn't have it | Add relationship to model | Relationship Checklist Step 5 |
| Service creates record but doesn't set foreign key | Add foreign key to create array | Relationship Checklist Step 4 |
| Formatting issues (Pint violations) | Run `./vendor/bin/pint` | Pre-Commit Checklist |
| Missing relationship in similar entities | Check pattern consistency | Consistency Check |

