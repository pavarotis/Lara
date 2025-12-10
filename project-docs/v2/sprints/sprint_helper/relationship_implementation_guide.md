# 🔗 Relationship Implementation Guide

**Purpose**: Step-by-step guide για να αποφύγουμε missing relationships (όπως το `creator()` στο Sprint 2).

**Based on**: Sprint 2 Review — Missing `creator()` relationship

---

## 📋 Quick Checklist

**Before marking a task complete, verify:**

```
Sprint Spec → Migration → Model → Service → Resource/Controller
     ↓            ↓         ↓        ↓            ↓
  creator()   created_by  creator() created_by  $media->creator
```

**If ANY step is missing, the relationship is incomplete!**

---

## 🔍 Step-by-Step Verification

### Step 1: Check Sprint Spec

**Action**: Read the deliverables section for relationships

**Example**:
```markdown
- `Media` model:
  - Relationships: `business()`, `folder()`, `creator()` ← CHECK THIS
```

**Verification**:
- [ ] List ALL relationships mentioned
- [ ] Cross-reference with Acceptance Criteria
- [ ] Note if similar entities have the same relationship

---

### Step 2: Verify Migration

**Action**: Check if foreign key column exists

**Example**:
```php
// database/migrations/v2_*_create_media_table.php
$table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
```

**Verification**:
- [ ] Foreign key column exists (e.g., `created_by`)
- [ ] Foreign key constraint added: `->constrained('users')`
- [ ] Nullable if needed: `->nullable()`
- [ ] Delete behavior: `->nullOnDelete()` or `->cascadeOnDelete()`
- [ ] Index added if frequently queried

**Quick Check**:
```bash
grep "created_by" database/migrations/v2_*_create_media_table.php
```

---

### Step 3: Verify Model

**Action**: Check if model has relationship method and `$fillable` entry

**Example**:
```php
// app/Domain/Media/Models/Media.php
protected $fillable = [
    // ...
    'created_by', // ← CHECK THIS
];

public function creator(): BelongsTo
{
    return $this->belongsTo(\App\Models\User::class, 'created_by');
}
```

**Verification**:
- [ ] Column in `$fillable` array
- [ ] Relationship method exists: `public function creator(): BelongsTo`
- [ ] Correct foreign key specified: `'created_by'`
- [ ] Correct model class: `\App\Models\User::class`
- [ ] Import statement if using short class name

**Quick Check**:
```bash
# Check fillable
grep "created_by" app/Domain/Media/Models/Media.php

# Check relationship method
grep -A 3 "function creator" app/Domain/Media/Models/Media.php
```

---

### Step 4: Verify Service (if creating records)

**Action**: Check if service sets the foreign key when creating records

**Example**:
```php
// app/Domain/Media/Services/UploadMediaService.php
Media::create([
    // ...
    'created_by' => auth()->id(), // ← CHECK THIS
]);
```

**Verification**:
- [ ] Service sets foreign key when creating
- [ ] Uses correct value (e.g., `auth()->id()`)
- [ ] Handles nullable case if needed

**Quick Check**:
```bash
grep "created_by" app/Domain/Media/Services/*.php
```

---

### Step 5: Verify Resource/Controller (if used)

**Action**: Check if Resource or Controller uses the relationship

**Example**:
```php
// app/Http/Resources/MediaResource.php
'creator' => $media->creator ? [ // ← CHECK THIS
    'id' => $media->creator->id,
    'name' => $media->creator->name,
] : null,
```

**Verification**:
- [ ] Search for model usage in Resources/Controllers
- [ ] If relationship is used, verify it exists in model
- [ ] Test that relationship loads correctly

**Quick Check**:
```bash
# Check if Resource uses relationship
grep "creator" app/Http/Resources/MediaResource.php

# Check all Resources that use the model
grep -r "Media" app/Http/Resources/
```

**Test in Tinker**:
```bash
php artisan tinker
>>> $media = Media::first();
>>> $media->creator; // Should return User or null, not error
>>> new MediaResource($media)->toArray(); // Should work
```

---

## 🚨 Common Mistakes

### Mistake 1: Relationship in spec but not in migration

**Symptom**: Spec says `creator()` but migration doesn't have `created_by`

**Fix**: Add foreign key to migration first

**Prevention**: Always check migration before adding relationship method

---

### Mistake 2: Foreign key in migration but not in `$fillable`

**Symptom**: Migration has `created_by` but model can't set it

**Fix**: Add to `$fillable` array

**Prevention**: Always add to `$fillable` when adding foreign key

---

### Mistake 3: Relationship method but wrong foreign key

**Symptom**: `creator()` method uses wrong column name

**Fix**: Verify foreign key name matches migration

**Prevention**: Copy foreign key name from migration

---

### Mistake 4: Resource uses relationship but model doesn't have it

**Symptom**: `MediaResource` uses `$media->creator` but model doesn't have `creator()`

**Fix**: Add relationship method to model

**Prevention**: Always check Resources/Controllers before marking model complete

---

### Mistake 5: Service creates record but doesn't set foreign key

**Symptom**: Service creates Media but `created_by` is null

**Fix**: Add `'created_by' => auth()->id()` to create array

**Prevention**: Check all services that create records

---

## ✅ Complete Verification Example

**For Media Model `creator()` relationship:**

```bash
# 1. Check spec
grep "creator" project-docs/v2/sprints/sprint_2/sprint_2.md
# Should show: Relationships: `business()`, `folder()`, `creator()`

# 2. Check migration
grep "created_by" database/migrations/v2_*_create_media_table.php
# Should show: $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

# 3. Check model fillable
grep "created_by" app/Domain/Media/Models/Media.php
# Should show in $fillable array

# 4. Check relationship method
grep -A 3 "function creator" app/Domain/Media/Models/Media.php
# Should show: public function creator(): BelongsTo { ... }

# 5. Check service
grep "created_by" app/Domain/Media/Services/UploadMediaService.php
# Should show: 'created_by' => auth()->id(),

# 6. Check resource
grep "creator" app/Http/Resources/MediaResource.php
# Should show: 'creator' => $media->creator ? [...]
```

**If ALL 6 steps pass, the relationship is complete! ✅**

---

## 📝 Template: Relationship Verification Checklist

**Copy this for each relationship:**

```markdown
## Relationship: {name}() (e.g., creator())

- [ ] **Spec**: Relationship mentioned in sprint spec
- [ ] **Migration**: Foreign key column exists
- [ ] **Migration**: Foreign key constraint added
- [ ] **Model**: Column in `$fillable` array
- [ ] **Model**: Relationship method exists
- [ ] **Model**: Correct foreign key specified
- [ ] **Service**: Sets foreign key (if creating records)
- [ ] **Resource/Controller**: Uses relationship? (if yes, verify it exists)
- [ ] **Test**: Relationship works in tinker
```

---

## 🎯 Quick Reference Commands

```bash
# Check all relationships for a model
grep "function.*():" app/Domain/Media/Models/Media.php

# Check all foreign keys in migration
grep "foreignId\|foreign" database/migrations/v2_*_create_media_table.php

# Check if Resource uses relationships
grep -E "->(creator|folder|business)" app/Http/Resources/MediaResource.php

# Check if Service sets foreign keys
grep "created_by\|updated_by" app/Domain/Media/Services/*.php

# Test relationship in tinker
php artisan tinker
>>> $media = Media::first();
>>> $media->creator; // Should work
>>> $media->folder; // Should work
```

---

## 📚 Related Documentation

- **Dev Responsibilities**: `project-docs/v2/dev-responsibilities.md` — Full checklist
- **Sprint 2 Review**: `project-docs/v2/sprints/sprint_2/reviews/sprint_2_review_devb.md`
- **Lessons Learned**: `project-docs/v2/sprints/sprint_helper/sprint_2_lessons_learned.md`

---

**Last Updated**: 2024-11-27  
**Created from**: Sprint 2 Review — Missing `creator()` relationship

