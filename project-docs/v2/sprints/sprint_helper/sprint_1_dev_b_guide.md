# 🎯 Sprint 1 — Dev B Guide (From Dev A Experience)

**Γεια σου Dev B!** 👋

Αυτός ο οδηγός βασίζεται στην **πραγματική εμπειρία** του Dev A από το Sprint 1. Περιέχει:
- ✅ Τι να κάνεις
- ❌ Τι να αποφύγεις
- 🔍 Πώς να verify ότι έχεις ολοκληρώσει όλα
- 📋 Checklists για κάθε task

---

## 🚨 ΚΡΙΣΙΜΟ: Πριν ξεκινήσεις

### 1. **Διάβασε ΟΛΟ το Sprint 1 spec**

**ΜΗΝ** ξεκινήσεις coding πριν:
- [ ] Διαβάσεις **ολόκληρο** το `sprint_1.md`
- [ ] Κάνεις list **όλων** των deliverables (κάθε bullet point)
- [ ] Κάνεις list **όλων** των Acceptance Criteria
- [ ] Κάνεις cross-reference: Deliverables ↔ Acceptance Criteria

**Παράδειγμα λάθους που έκανα:**
- Έβλεπα "API Resources: ContentResource" αλλά δεν το θεώρησα explicit deliverable
- **Σωστό**: Κάθε bullet point στα Deliverables = **explicit deliverable**, όχι optional

---

## 📋 Task B1 — Content Migrations

### Deliverables (από sprint_1.md line 47-54):
- [ ] `create_content_types_table` migration
- [ ] `create_contents_table` migration
- [ ] `create_content_revisions_table` migration
- [ ] Foreign keys, indexes
- [ ] Seeders: default content types (page, article, block)

### Acceptance Criteria (line 56-59):
- [ ] `php artisan migrate` runs successfully
- [ ] Default content types seeded
- [ ] Database ready for content entries

### ⚠️ Common Mistakes to Avoid:

1. **Missing Fields**
   - ❌ Λάθος: Να ξεχάσεις fields από το spec
   - ✅ Σωστό: Cross-reference με sprint spec για κάθε field

2. **Inconsistent Patterns**
   - ❌ Λάθος: Να μην έχεις `created_by` αν το Content model το έχει
   - ✅ Σωστό: Check similar entities (Content, MediaFolder) για consistency

3. **Missing Indexes**
   - ❌ Λάθος: Να ξεχάσεις indexes για frequently queried columns
   - ✅ Σωστό: Add indexes για: business_id, slug, type, status, published_at

### ✅ Verification Checklist:

```bash
# 1. Check migration runs
php artisan migrate

# 2. Check tables created
php artisan tinker
>>> Schema::hasTable('contents')
>>> Schema::hasTable('content_types')
>>> Schema::hasTable('content_revisions')

# 3. Check seeders
php artisan db:seed --class=ContentTypeSeeder
>>> \App\Domain\Content\Models\ContentType::count() // Should be 3 (page, article, block)

# 4. Check foreign keys
# Verify in database or migration file
```

---

## 📋 Task B2 — Content Models

### Deliverables (από sprint_1.md line 68-79):
- [ ] `Content` model:
  - [ ] Relationships: `business()`, `contentType()`, `revisions()`, `creator()`
  - [ ] Scopes: `published()`, `draft()`, `archived()`, `ofType()`, `forBusiness()`
  - [ ] Casts: `body_json` → array, `meta` → array, `published_at` → datetime
  - [ ] Helper methods: `isPublished()`, `isDraft()`, `publish()`, `archive()`
- [ ] `ContentType` model:
  - [ ] Relationships: `contents()`
  - [ ] Helper: `getFieldDefinitions()`
- [ ] `ContentRevision` model:
  - [ ] Relationships: `content()`, `user()`
  - [ ] Helper: `restore()`

### Acceptance Criteria (line 81-84):
- [ ] All relationships working
- [ ] Scopes tested
- [ ] Models ready for services

### ⚠️ Critical: Scopes Required by Dev A

**Ο Dev A χρησιμοποιεί αυτά τα scopes:**
- `Content::forBusiness($businessId)` — **REQUIRED** (χρησιμοποιείται σε GetContentService)
- `Content::ofType($type)` — **REQUIRED** (χρησιμοποιείται σε GetContentService)
- `Content::published()` — Already exists, verify it works
- `Content::draft()` — Already exists, verify it works
- `Content::archived()` — **REQUIRED** (mentioned in spec)

**Αν δεν υπάρχουν, ο Dev A θα χρησιμοποιήσει `where()` clauses, αλλά είναι καλύτερο να υπάρχουν scopes.**

### ✅ Verification Checklist:

```php
// Test relationships
$content = Content::first();
$content->business; // Should return Business
$content->creator; // Should return User
$content->revisions; // Should return Collection
$content->contentType; // Should return ContentType (if relationship exists)

// Test scopes
Content::forBusiness(1)->get(); // Should work
Content::ofType('page')->get(); // Should work
Content::published()->get(); // Should work
Content::draft()->get(); // Should work
Content::archived()->get(); // Should work

// Test casts
$content->body_json; // Should be array, not JSON string
$content->meta; // Should be array, not JSON string
$content->published_at; // Should be Carbon instance

// Test helper methods
$content->isPublished(); // Should return bool
$content->isDraft(); // Should return bool
$content->publish(); // Should update status and published_at
```

---

## 📋 Task B3 — Content Services

### Deliverables (από sprint_1.md line 93-112):
- [ ] `GetContentService`:
  - [ ] `bySlug($businessId, $slug)` — get published content by slug
  - [ ] `byType($businessId, $type)` — get all content of type
  - [ ] `withRevisions($contentId)` — get content with revision history
- [ ] `CreateContentService`:
  - [ ] Create content entry
  - [ ] Create initial revision
  - [ ] Validate business rules
- [ ] `UpdateContentService`:
  - [ ] Update content
  - [ ] Auto-create new revision before update
  - [ ] Validate business rules
- [ ] `DeleteContentService`:
  - [ ] Soft delete option
  - [ ] Cascade to revisions (optional)
- [ ] `CreateRevisionService`:
  - [ ] Manual revision creation
  - [ ] Store current state
- [ ] `RenderContentService` (skeleton, full implementation in Sprint 3):
  - [ ] Block → view renderer (placeholder)

### ⚠️ Important Note:

**Ο Dev A έχει ήδη δημιουργήσει:**
- ✅ `GetContentService` (bySlug, byType, withRevisions)
- ✅ `CreateContentService` (with initial revision)
- ✅ `UpdateContentService` (auto-create revision)
- ✅ `DeleteContentService`
- ✅ `PublishContentService` (additional service)

**Εσύ (Dev B) πρέπει να:**
- [ ] Verify ότι τα services του Dev A πληρούν τα requirements
- [ ] Add `CreateRevisionService` (manual revision creation)
- [ ] Add `RenderContentService` (skeleton/placeholder)
- [ ] Verify business rules validation
- [ ] Test revision system

### ✅ Verification Checklist:

```php
// Test GetContentService
$service = app(\App\Domain\Content\Services\GetContentService::class);
$content = $service->bySlug(1, 'homepage'); // Should return Content or null
$contents = $service->byType(1, 'page'); // Should return Collection
$content = $service->withRevisions(1); // Should return Content with revisions loaded

// Test CreateContentService
$service = app(\App\Domain\Content\Services\CreateContentService::class);
$content = $service->execute([...]);
// Verify: Content created, initial revision created

// Test UpdateContentService
$service = app(\App\Domain\Content\Services\UpdateContentService::class);
$content = $service->execute($content, [...]);
// Verify: Content updated, new revision created before update

// Test revision system
$content->revisions->count(); // Should be > 0
$content->revisions->first()->user; // Should return User
```

---

## 🔍 Pre-Commit Checklist για Dev B

### Before Committing Migrations/Models:

- [ ] **Cross-reference with Sprint Plan**: Verify all fields from sprint plan are in migration
- [ ] **Pattern Consistency**: Check similar entities (Content, MediaFolder) have same audit fields
- [ ] **Model-Migration Match**: Verify model `$fillable` matches migration columns
- [ ] **Relationship Consistency**: If Content has `created_by`, similar entities should too
- [ ] **Index Consistency**: Similar entities should have similar indexes
- [ ] **Scopes Required**: Verify all scopes mentioned in spec exist and work
- [ ] **Casts Correct**: Verify casts match actual storage (JSON → array, datetime → Carbon)

### Before Committing Services:

- [ ] **All methods implemented**: Check spec for all required methods
- [ ] **Business rules validated**: Verify validation logic
- [ ] **Transactions used**: Multi-step operations in transactions
- [ ] **Error handling**: Proper exception handling
- [ ] **Type hints**: All methods have type hints and return types

---

## 🎯 Key Lessons from Dev A Mistakes

### 1. **Explicit Deliverables = Required**

**Λάθος σκέψη:**
- "Αυτό είναι optional enhancement"
- "Αυτό μπορεί να έρθει αργότερα"

**Σωστή σκέψη:**
- Κάθε bullet point στα Deliverables = **explicit deliverable**
- Αν αναφέρεται στο spec, είναι **required**, όχι optional

### 2. **Deliverables ↔ Acceptance Criteria**

**Πάντα** κάνε cross-reference:
- Deliverable: "API Resources: ContentResource"
- Acceptance: "API returns consistent JSON"
- **Συμπέρασμα**: ContentResource is **required** για να πληρωθεί το acceptance criteria

### 3. **Documentation = Separate File**

**Λάθος:**
- "Τα comments στο code είναι αρκετά"

**Σωστό:**
- Documentation = **separate file** στο `project-docs/v2/`
- Follow existing patterns (api_spec.md, v2_content_model.md)

### 4. **Verify Before Commit**

**Πάντα** πριν commit:
1. Re-read task description
2. Check **όλα** τα deliverables (file exists, used correctly)
3. Test **όλα** τα acceptance criteria
4. Self-review: "Αν ήμουν reviewer, τι θα έλεγα;"

---

## 📚 Resources

### Must Read:
- [ ] `project-docs/v2/sprints/sprint_1.md` — **READ COMPLETELY**
- [ ] `project-docs/v2/sprints/sprint_1_lessons_learned.md` — Lessons from Dev A
- [ ] `project-docs/v2/dev-responsibilities.md` — Quality checks
- [ ] `project-docs/conventions.md` — Coding conventions
- [ ] `project-docs/architecture.md` — Architecture patterns

### Reference Files:
- `app/Domain/Catalog/Models/Product.php` — Example model with scopes
- `app/Domain/Catalog/Services/CreateProductService.php` — Example service
- `database/migrations/v2_2024_11_27_000002_create_contents_table.php` — Existing migration

---

## ✅ Final Verification Before Marking Complete

### For Each Task:

1. **Open sprint_1.md**
2. **Read task section completely**
3. **For each deliverable:**
   - [ ] File exists? (verify with `ls` or file search)
   - [ ] Used in code? (verify with `grep` or code search)
   - [ ] Follows conventions?
4. **For each acceptance criteria:**
   - [ ] Can I verify it? (test manually or automated)
   - [ ] Is it actually met?

### Example Verification:

```bash
# Task B2 - Content Models
✅ Content model exists: app/Domain/Content/Models/Content.php
✅ Relationships work: Test in tinker
✅ Scopes work: Content::forBusiness(1)->get()
✅ Casts work: $content->body_json is array
✅ Helper methods work: $content->isPublished()
```

---

## 🆘 If You Get Stuck

1. **Check Documentation**:
   - Re-read relevant section in conventions.md
   - Check architecture.md
   - Check dev-responsibilities.md

2. **Check Existing Code**:
   - Find similar implementation (Catalog, Orders)
   - Study the pattern
   - Adapt to your needs

3. **Ask for Help**:
   - Update `sprint_1.md` notes section with question
   - Be specific: "I'm trying to X, but Y happens"
   - Show code & error

---

## 🎯 Success Indicators

You're on the right track when:
- ✅ All deliverables from spec are implemented
- ✅ All acceptance criteria are met
- ✅ Code follows conventions (checked with existing code)
- ✅ Models have all required scopes and relationships
- ✅ Services use transactions for multi-step operations
- ✅ Type hints everywhere
- ✅ Manual testing works
- ✅ No linting errors

---

**Good luck! 🎉**

**Remember**: Κάθε bullet point = explicit deliverable. Verify όλα πριν mark ως complete.

**Last Updated**: 2024-11-27  
**Created by**: Dev A (based on Sprint 1 experience)

