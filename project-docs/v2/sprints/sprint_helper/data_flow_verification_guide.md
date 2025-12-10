# 🔄 Data Flow Verification Guide

**Purpose**: Step-by-step guide για να αποφύγουμε data flow mismatches μεταξύ Admin Controllers και Public Block Views (όπως στο Sprint 3).

**Based on**: Sprint 3 Review — Hero & Gallery Block Data Flow Issues

---

## 📋 Quick Checklist

**Before marking block views complete, verify:**

```
Admin Controller → Block Props → Block View → Media Loading
     ↓                ↓              ↓            ↓
  image_id      image_id      $image_id    Media::find()
  images[]      images[]      $images[]    foreach + find()
```

**If ANY step doesn't match, the data flow is broken!**

---

## 🔍 Step-by-Step Verification

### Step 1: Check Admin Controller Data Format

**Action**: Check how Admin Controller saves block props

**Example** (Hero Block):
```php
// app/Http/Controllers/Admin/ContentController.php
if ($block['type'] === 'hero') {
    if (isset($props['image_id'])) {
        $props['image_id'] = $props['image_id'];
        $props['image_url'] = $props['image_url'] ?? null;
        $props['image_thumbnail_url'] = $props['image_thumbnail_url'] ?? null;
    }
}
```

**Verification**:
- [ ] What prop names does controller save? (e.g., `image_id`, `images`)
- [ ] What format? (e.g., numeric ID, array of IDs, array of objects)
- [ ] Any additional props? (e.g., `image_url`, `image_thumbnail_url`)

**Quick Check**:
```bash
# Check Admin Controller
grep -A 10 "if.*hero" app/Http/Controllers/Admin/ContentController.php
grep -A 10 "if.*gallery" app/Http/Controllers/Admin/ContentController.php
```

---

### Step 2: Check Block View Props Usage

**Action**: Check what props the block view expects

**Example** (Hero Block):
```php
// resources/views/themes/default/blocks/hero.blade.php
$imageId = $image_id ?? $image ?? null;
```

**Verification**:
- [ ] What prop names does view use? (e.g., `$image_id`, `$images`)
- [ ] Does it match controller format?
- [ ] Any fallback props? (e.g., `$image` for legacy support)

**Quick Check**:
```bash
# Check block view props
grep "\$" resources/views/themes/default/blocks/hero.blade.php | head -10
grep "\$" resources/views/themes/default/blocks/gallery.blade.php | head -10
```

---

### Step 3: Verify Media Loading Logic

**Action**: Check how block view loads media from props

**Example** (Hero Block):
```php
if ($imageId) {
    if (is_numeric($imageId)) {
        $media = \App\Domain\Media\Models\Media::find($imageId);
        if ($media) {
            $imageUrl = $media->url;
        }
    }
}
```

**Verification**:
- [ ] Does it handle the format from controller?
- [ ] Does it handle legacy formats? (for backward compatibility)
- [ ] Does it handle edge cases? (null, empty, invalid IDs)

**Quick Check**:
```bash
# Check media loading
grep -A 5 "Media::find" resources/views/themes/default/blocks/hero.blade.php
grep -A 5 "Media::find" resources/views/themes/default/blocks/gallery.blade.php
```

---

### Step 4: Test Data Flow End-to-End

**Action**: Test with actual data from controller

**Test in Tinker**:
```bash
php artisan tinker
>>> $content = Content::where('type', 'page')->first();
>>> $blocks = $content->body_json;
>>> $heroBlock = collect($blocks)->firstWhere('type', 'hero');
>>> $heroBlock['props']; // Check what props are saved
>>> // Now check if block view can handle these props
```

**Verification**:
- [ ] Create test content via Admin
- [ ] Check saved props in database
- [ ] Render block view with those props
- [ ] Verify media loads correctly

---

## 🚨 Common Data Flow Mistakes

### Mistake 1: Prop Name Mismatch

**Symptom**: Controller saves `image_id` but view uses `$image`

**Example**:
```php
// Controller saves:
$props['image_id'] = $props['image_id'];

// View expects:
$imageId = $image ?? null; // ❌ Wrong prop name
```

**Fix**: Use same prop name in both places
```php
// View should use:
$imageId = $image_id ?? null; // ✅ Correct
```

**Prevention**: Always check controller prop names before writing view

---

### Mistake 2: Format Mismatch

**Symptom**: Controller saves array of objects but view expects array of IDs

**Example**:
```php
// Controller saves:
$props['images'] = [{id: 1, url: '...'}, {id: 2, url: '...'}];

// View expects:
foreach ($images as $imageId) { // ❌ Expects IDs, gets objects
    $media = Media::find($imageId);
}
```

**Fix**: Handle both formats
```php
// View should handle both:
foreach ($images as $item) {
    $imageId = is_array($item) ? $item['id'] : $item;
    $media = Media::find($imageId);
}
```

**Prevention**: Always check controller data format before writing view

---

### Mistake 3: Missing Fallback Support

**Symptom**: View breaks with legacy data format

**Example**:
```php
// View only handles new format:
$imageId = $image_id; // ❌ Breaks if old data has $image (URL)
```

**Fix**: Support both formats
```php
// View should support both:
$imageId = $image_id ?? $image ?? null; // ✅ Backward compatible
```

**Prevention**: Always add fallback for legacy formats

---

### Mistake 4: Not Checking Media Existence

**Symptom**: View breaks if media ID doesn't exist

**Example**:
```php
$media = Media::find($imageId);
$imageUrl = $media->url; // ❌ Breaks if $media is null
```

**Fix**: Check if media exists
```php
$media = Media::find($imageId);
if ($media) {
    $imageUrl = $media->url;
}
```

**Prevention**: Always check for null before accessing properties

---

## ✅ Complete Verification Example

**For Hero Block Data Flow:**

```bash
# 1. Check Admin Controller
grep -A 10 "hero" app/Http/Controllers/Admin/ContentController.php
# Should show: $props['image_id'] = ...

# 2. Check block view props
grep "\$image" resources/views/themes/default/blocks/hero.blade.php
# Should show: $image_id ?? $image ?? null

# 3. Check media loading
grep -A 5 "Media::find" resources/views/themes/default/blocks/hero.blade.php
# Should show: Media::find($imageId) with null check

# 4. Test in tinker
php artisan tinker
>>> $content = Content::first();
>>> $hero = collect($content->body_json)->firstWhere('type', 'hero');
>>> $hero['props']['image_id']; // Should exist
>>> $media = Media::find($hero['props']['image_id']);
>>> $media->url; // Should work
```

**If ALL 4 steps pass, the data flow is correct! ✅**

---

## 📝 Template: Data Flow Verification Checklist

**Copy this for each block type:**

```markdown
## Block Type: {name} (e.g., hero)

### Step 1: Admin Controller
- [ ] What prop names does controller save?
- [ ] What format? (ID, array of IDs, array of objects)
- [ ] Any additional props?

### Step 2: Block View Props
- [ ] What prop names does view use?
- [ ] Does it match controller format?
- [ ] Any fallback props for legacy support?

### Step 3: Media Loading
- [ ] Does it handle controller format?
- [ ] Does it handle legacy formats?
- [ ] Does it check for null/empty?

### Step 4: End-to-End Test
- [ ] Create test content via Admin
- [ ] Check saved props in database
- [ ] Render block view
- [ ] Verify media loads correctly
```

---

## 🎯 Quick Reference Commands

```bash
# Check Admin Controller data format
grep -A 10 "if.*hero\|if.*gallery" app/Http/Controllers/Admin/ContentController.php

# Check block view props
grep "\$" resources/views/themes/default/blocks/{block}.blade.php

# Check media loading
grep -A 5 "Media::find" resources/views/themes/default/blocks/{block}.blade.php

# Test data flow
php artisan tinker
>>> $content = Content::first();
>>> $block = collect($content->body_json)->firstWhere('type', '{block}');
>>> $block['props']; // Check format
```

---

## 📚 Related Documentation

- **Dev Responsibilities**: `project-docs/v2/dev-responsibilities.md` — Full checklist
- **Sprint 3 Review**: `project-docs/v2/sprints/sprint_3/reviews/sprint_3_review_devb.md`
- **Lessons Learned**: `project-docs/v2/sprints/sprint_helper/sprint_3_lessons_learned.md`

---

**Last Updated**: 2024-11-27  
**Created from**: Sprint 3 Review — Hero & Gallery Block Data Flow Issues

