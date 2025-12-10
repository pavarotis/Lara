# 📚 Sprint 3 — Lessons Learned (Dev B)

## 🔍 Γιατί έγιναν τα data flow errors?

### 1. **Hero Block Data Flow Mismatch**

**Πρόβλημα:**
- Το hero block view χρησιμοποιούσε `$image` prop
- Το Admin ContentController σώζει `image_id` prop
- Data flow mismatch → images δεν φορτώνονταν

**Root Cause:**
- ❌ Δεν έκανα cross-reference: Admin Controller → Block View
- ❌ Δεν έλεγξα πώς το ContentController σώζει τα props
- ❌ Υπέθεσα ότι το view θα λάβει τα props όπως τα έγραψα
- ❌ Δεν έκανα end-to-end test πριν commit

**Fix Applied:**
- Updated hero block to support both `image_id` (from media picker) and legacy `image` (URL)
- Added fallback support: `$image_id ?? $image ?? null`
- Added support for `image_url` and `image_thumbnail_url` (from media picker)

**Lesson Learned**: Πάντα verify data flow: Admin Controller → Block Props → Block View

---

### 2. **Gallery Block Data Flow Mismatch**

**Πρόβλημα:**
- Το gallery block view περίμενε array με image IDs: `[1, 2, 3]`
- Το Admin ContentController σώζει array με objects: `[{id: 1, url: '...'}, ...]`
- Data flow mismatch → gallery δεν φορτώνονταν

**Root Cause:**
- ❌ Δεν έκανα cross-reference: Admin Controller → Block View
- ❌ Δεν έλεγξα πώς το ContentController σώζει τα props
- ❌ Υπέθεσα ότι το view θα λάβει array of IDs
- ❌ Δεν έκανα end-to-end test πριν commit

**Fix Applied:**
- Updated gallery block to handle both formats:
  - Array of objects: `[{id: 1, url: '...'}, ...]` (from media picker)
  - Array of IDs: `[1, 2, 3]` (legacy format)
- Added format detection: `is_array($item) && isset($item['id'])`

**Lesson Learned**: Πάντα verify data format: Check controller format before writing view

---

## ✅ Πώς να αποφύγουμε τέτοια σφάλματα στο μέλλον

### 1. **Data Flow Verification Pattern**

**ΠΡΙΝ mark block view ως complete:**

```
For EVERY block type:
1. Admin Controller: What props does it save? (prop names, format)
2. Block View: What props does it expect? (must match controller)
3. Media Loading: Does it handle controller format? (with fallbacks)
4. End-to-End Test: Create test content, verify rendering works
```

**Quick Verification Commands:**
```bash
# 1. Check Admin Controller
grep -A 10 "if.*hero\|if.*gallery" app/Http/Controllers/Admin/ContentController.php

# 2. Check block view props
grep "\$" resources/views/themes/default/blocks/{block}.blade.php

# 3. Test in tinker
php artisan tinker
>>> $content = Content::first();
>>> $block = collect($content->body_json)->firstWhere('type', '{block}');
>>> $block['props']; // Verify format matches view expectations
```

---

### 2. **Pre-Commit Checklist Enhancement**

**ΠΡΙΝ commit block views:**

- [ ] **Check Admin Controller**: How does it save props?
- [ ] **Check Block View Props**: Do they match controller format?
- [ ] **Check Media Loading**: Does it handle controller format?
- [ ] **End-to-End Test**: Create test content, verify rendering

---

### 3. **Cross-Reference Pattern**

**ΠΡΙΝ write block view:**

1. **Read Admin Controller** (how it saves data)
2. **Note prop names** (e.g., `image_id`, `images`)
3. **Note data format** (e.g., numeric ID, array of objects)
4. **Write view** to match controller format
5. **Add fallbacks** for legacy formats (backward compatibility)
6. **Test** with actual data from controller

**Example:**
```
Before writing hero.blade.php:
1. Check ContentController: saves image_id ✅
2. Note: image_id is numeric ID ✅
3. Write view: $image_id ✅
4. Add fallback: $image_id ?? $image ✅
5. Test: Create hero block, verify image loads ✅
```

---

### 4. **Format Detection Pattern**

**For arrays/objects:**

**❌ Wrong:**
```php
// Assumes array of IDs
foreach ($images as $imageId) {
    $media = Media::find($imageId);
}
```

**✅ Correct:**
```php
// Handles both formats
foreach ($images as $item) {
    $imageId = is_array($item) ? $item['id'] : $item;
    $media = Media::find($imageId);
}
```

**Pattern:**
- Check format first (is_array, is_object, is_numeric)
- Extract ID based on format
- Handle both new and legacy formats

---

### 5. **Prop Name Consistency Pattern**

**For prop names:**

**❌ Wrong:**
```php
// Controller saves: image_id
// View uses: $image
$imageId = $image ?? null; // Mismatch!
```

**✅ Correct:**
```php
// Controller saves: image_id
// View uses: $image_id (match!)
$imageId = $image_id ?? $image ?? null; // With fallback
```

**Pattern:**
- Use same prop name as controller
- Add fallback for legacy prop names
- Document prop names in comments

---

## 📋 Enhanced Checklist for Block Views

**When creating block views:**

### Step 1: Check Admin Controller
- [ ] What prop names does controller save?
- [ ] What format? (ID, array of IDs, array of objects)
- [ ] Any additional props? (e.g., `image_url`, `image_thumbnail_url`)

### Step 2: Write Block View Props
- [ ] Use same prop names as controller
- [ ] Add fallback for legacy prop names
- [ ] Document prop names in comments

### Step 3: Media Loading
- [ ] Handle controller format
- [ ] Handle legacy formats (backward compatibility)
- [ ] Check for null/empty before accessing
- [ ] Filter by media type if needed (e.g., only images)

### Step 4: End-to-End Test
- [ ] Create test content via Admin
- [ ] Check saved props in database
- [ ] Render block view
- [ ] Verify media loads correctly

---

## 🎯 Key Takeaways

1. **Always verify data flow**: Admin Controller → Block Props → Block View
2. **Check controller format**: Before writing view, check how controller saves data
3. **Add fallbacks**: Support both new and legacy formats
4. **End-to-end test**: Create test content, verify rendering works
5. **Use same prop names**: Match controller prop names in view

---

## 📚 Related Documentation

- **Data Flow Verification Guide**: `project-docs/v2/sprints/sprint_helper/data_flow_verification_guide.md`
- **Dev Responsibilities**: `project-docs/v2/dev-responsibilities.md` (Enhanced with Data Flow Checklist)
- **Sprint 3 Review**: `project-docs/v2/sprints/sprint_3/reviews/sprint_3_review_devb.md`

---

**Last Updated**: 2024-11-27  
**Created by**: Dev B (Sprint 3 Review)

