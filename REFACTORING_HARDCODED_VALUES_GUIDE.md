# 🔧 Οδηγός Αντικατάστασης Hardcoded Επαναλαμβανόμενων Values

## 📋 Στόχος

Να αντικαταστήσουμε **όλα** τα hardcoded επαναλαμβανόμενα values στο `Variables.php` με **δυναμικές functions** που δέχονται parameters.

---

## 🎯 Αρχή

**Αν βλέπεις τον ίδιο κώδικα 2+ φορές → Δημιούργησε function που δέχεται parameter**

---

## 🔍 Βήμα 1: Εντοπισμός Hardcoded Patterns

### Πώς να βρεις hardcoded values:

1. **Ψάξε για επαναλαμβανόμενα arrays:**
   ```php
   // ❌ BAD - Hardcoded
   ['key' => 'general.spacing.xs', 'value' => $data['general']['spacing_xs'] ?? null, 'type' => 'string'],
   ['key' => 'general.spacing.sm', 'value' => $data['general']['spacing_sm'] ?? null, 'type' => 'string'],
   ['key' => 'general.spacing.md', 'value' => $data['general']['spacing_md'] ?? null, 'type' => 'string'],
   ```

2. **Ψάξε για επαναλαμβανόμενες δομές:**
   ```php
   // ❌ BAD - Hardcoded
   [
       'key' => 'legacy.spacing.small',
       'value' => isset($data['legacy']['spacing_small_value']) 
           ? ($data['legacy']['spacing_small_value'].($data['legacy']['spacing_small_unit'] ?? 'rem')) 
           : null,
       'type' => 'string'
   ],
   [
       'key' => 'legacy.spacing.medium',
       'value' => isset($data['legacy']['spacing_medium_value']) 
           ? ($data['legacy']['spacing_medium_value'].($data['legacy']['spacing_medium_unit'] ?? 'rem')) 
           : null,
       'type' => 'string'
   ],
   ```

3. **Ψάξε για hardcoded strings/numbers:**
   ```php
   // ❌ BAD - Hardcoded
   $set('general.color_primary', $style['color_primary'] ?? '#3b82f6');
   $set('general.color_secondary', $style['color_secondary'] ?? '#ffffff');
   ```

---

## 🛠️ Βήμα 2: Δημιουργία Dynamic Function

### Παράδειγμα 1: Legacy Spacing Variables

**ΠΡΙΝ (Hardcoded):**
```php
'legacy' => array_merge(
    $this->generateSaveVariablesFromConfig('legacy', $data, 'legacy'),
    array_filter([
        [
            'key' => 'legacy.spacing.small',
            'value' => isset($data['legacy']['spacing_small_value']) 
                ? ($data['legacy']['spacing_small_value'].($data['legacy']['spacing_small_unit'] ?? 'rem')) 
                : null,
            'type' => 'string'
        ],
        [
            'key' => 'legacy.spacing.medium',
            'value' => isset($data['legacy']['spacing_medium_value']) 
                ? ($data['legacy']['spacing_medium_value'].($data['legacy']['spacing_medium_unit'] ?? 'rem')) 
                : null,
            'type' => 'string'
        ],
        [
            'key' => 'legacy.spacing.large',
            'value' => isset($data['legacy']['spacing_large_value']) 
                ? ($data['legacy']['spacing_large_value'].($data['legacy']['spacing_large_unit'] ?? 'rem')) 
                : null,
            'type' => 'string'
        ],
    ], fn($item) => $item['value'] !== null)
),
```

**ΜΕΤΑ (Dynamic):**
```php
// 1. Δημιούργησε function που δέχεται το size key
protected function generateLegacySpacingVariable(string $size, array $data): ?array
{
    $valueKey = "legacy.spacing_{$size}_value";
    $unitKey = "legacy.spacing_{$size}_unit";
    
    $value = data_get($data, $valueKey);
    
    if ($value === null) {
        return null;
    }
    
    $unit = data_get($data, $unitKey, 'rem');
    
    return [
        'key' => "legacy.spacing.{$size}",
        'value' => $value.$unit,
        'type' => 'string',
    ];
}

// 2. Χρησιμοποίησε array_map για όλα τα sizes
'legacy' => array_merge(
    $this->generateSaveVariablesFromConfig('legacy', $data, 'legacy'),
    array_filter(
        array_map(
            fn($size) => $this->generateLegacySpacingVariable($size, $data),
            ['small', 'medium', 'large']
        ),
        fn($item) => $item !== null && $item['value'] !== null
    )
),
```

---

### Παράδειγμα 2: Color Style Application

**ΠΡΙΝ (Hardcoded):**
```php
$set('general.color_primary', $style['color_primary'] ?? '#3b82f6');
$set('general.color_secondary', $style['color_secondary'] ?? '#ffffff');
$set('general.color_accent', $style['color_accent'] ?? '#10b981');
$set('general.color_success', $style['color_success'] ?? '#22c55e');
$set('general.color_warning', $style['color_warning'] ?? '#f59e0b');
$set('general.color_danger', $style['color_danger'] ?? '#ef4444');
$set('general.color_info', $style['color_info'] ?? '#06b6d4');
$set('general.color_background', $style['color_background'] ?? '#ffffff');
$set('general.color_text', $style['color_text'] ?? '#1f2937');
```

**ΜΕΤΑ (Dynamic):**
```php
// 1. Δημιούργησε function
protected function applyColorFromStyle(string $colorKey, array $style, callable $set): void
{
    $fieldKey = "general.color_{$colorKey}";
    $styleKey = "color_{$colorKey}";
    $defaultValue = $this->getVariableValue("general.color.{$colorKey}", '');
    
    $set($fieldKey, $style[$styleKey] ?? $defaultValue);
}

// 2. Χρησιμοποίησε loop
$colorKeys = ['primary', 'secondary', 'accent', 'success', 'warning', 'danger', 'info', 'background', 'text'];
foreach ($colorKeys as $colorKey) {
    $this->applyColorFromStyle($colorKey, $style, $set);
}
```

---

## 📝 Βήμα 3: Checklist για Refactoring

### ✅ Βήματα:

1. **Εντοπίζω το pattern:**
   - [ ] Βρίσκω 2+ γραμμές με ίδια δομή
   - [ ] Εντοπίζω τι αλλάζει (key, field name, default value, etc.)

2. **Δημιουργώ function:**
   - [ ] Function name: `generate*` ή `apply*` ή `create*`
   - [ ] Parameters: Όλα τα μεταβλητά μέρη
   - [ ] Return type: Ό,τι επιστρέφει (array, void, etc.)

3. **Αντικαθιστώ hardcoded:**
   - [ ] Χρησιμοποιώ `array_map()` για arrays
   - [ ] Χρησιμοποιώ `foreach` για loops
   - [ ] Χρησιμοποιώ configuration arrays όπου είναι δυνατό

4. **Ελέγχω:**
   - [ ] Όλα τα hardcoded values αντικαταστάθηκαν
   - [ ] Η function δουλεύει για όλα τα cases
   - [ ] Δεν έχω χάσει λογική

---

## 🎯 Παραδείγματα από Variables.php

### Pattern 1: Save Variables Arrays
**Εντοπίζω:** Arrays με `['key' => ..., 'value' => ..., 'type' => ...]`

**Λύση:** `generateSaveVariablesFromConfig()` - ήδη υπάρχει!

---

### Pattern 2: Legacy Spacing (3x επανάληψη)
**Εντοπίζω:** 3 arrays με ίδια δομή, μόνο το `size` αλλάζει

**Λύση:** `generateLegacySpacingVariable($size, $data)`

---

### Pattern 3: Color Style Application (9x επανάληψη)
**Εντοπίζω:** 9 `$set()` calls με ίδια δομή

**Λύση:** `applyColorFromStyle($colorKey, $style, $set)` + loop

---

### Pattern 4: Typography Fonts (EN/GR - 4x επανάληψη)
**Εντοπίζω:** 4 Select fields με ίδια δομή για EN, 4 για GR

**Λύση:** `createFieldsFromConfig('typography_en_fonts')` - ήδη υπάρχει!

---

## 🔍 Πού να ψάξεις στο Variables.php

1. **Στο `save()` method (γραμμές ~700-760):**
   - `$variablesToSave` match expression
   - Arrays με `['key' => ..., 'value' => ..., 'type' => ...]`

2. **Στο `form()` method (γραμμές ~278-907):**
   - `afterStateUpdated` callbacks
   - `$set()` calls με hardcoded values

3. **Σε helper methods:**
   - `getFavoriteFonts()` - ήδη fixed
   - `parseSpacingValue()` - έχει fallback, OK

---

## ✅ Κανόνες

1. **Αν βλέπεις 2+ ίδιες γραμμές → Function**
2. **Αν βλέπεις hardcoded array → Configuration array + Loop**
3. **Αν βλέπεις hardcoded default values → Φόρτωση από βάση**
4. **Fallback values μόνο όταν είναι απαραίτητα** (π.χ. parseSpacingValue)

---

## 📌 Priority Order

1. **Υψηλή προτεραιότητα:** Save arrays (γραμμές 700-760)
2. **Μέση προτεραιότητα:** Form callbacks με hardcoded values
3. **Χαμηλή προτεραιότητα:** Fallback values σε utility functions

---

**Version:** 1.0  
**Date:** 2026-01-23  
**Status:** Ready for Refactoring
