# Dynamic Settings Page - Filament v4

## 📋 Περιγραφή

Ένα πλήρως δυναμικό Filament v4 page που διαβάζει μεταβλητές από τη βάση δεδομένων και δημιουργεί αυτόματα form fields, οργανωμένα σε tabs ανά category.

## 🚀 Εγκατάσταση

### 1. Τρέξε το Migration

```bash
php artisan migrate
```

Αυτό θα προσθέσει τη στήλη `category` στον πίνακα `variables`.

### 2. Τρέξε το Seeder (Προαιρετικό)

```bash
php artisan db:seed --class=DynamicVariablesSeeder
```

Αυτό θα δημιουργήσει παραδείγματα μεταβλητών με διαφορετικές κατηγορίες.

### 3. Πρόσβαση στη Σελίδα

Μετά την εγκατάσταση, η σελίδα θα είναι διαθέσιμη στο:
```
/admin/dynamic-settings
```

## 📁 Αρχεία που Δημιουργήθηκαν

1. **Migration**: `database/migrations/2026_01_23_121903_add_category_to_variables_table.php`
   - Προσθέτει τη στήλη `category` στον πίνακα `variables`

2. **Page Class**: `app/Filament/Pages/CMS/DynamicSettings.php`
   - Πλήρως δυναμικό Filament page με auto-generated form fields

3. **Blade View**: `resources/views/filament/pages/cms/dynamic-settings.blade.php`
   - View template για το page

4. **Seeder**: `database/seeders/DynamicVariablesSeeder.php`
   - Παραδείγματα μεταβλητών με categories

## 🎯 Χρήση

### Προσθήκη Νέας Μεταβλητής

Απλά πρόσθεσε μια νέα εγγραφή στον πίνακα `variables`:

```php
Variable::create([
    'business_id' => $business->id,
    'key' => 'my_new_setting',
    'value' => 'default value',
    'type' => 'string', // 'string', 'number', 'boolean', 'json'
    'category' => 'general', // για ομαδοποίηση σε tabs
    'description' => 'My New Setting', // εμφανίζεται ως label
]);
```

Η μεταβλητή θα εμφανιστεί **αυτόματα** στο admin panel!

### Υποστηριζόμενοι Τύποι

- **string**: TextInput field
- **number**: Numeric TextInput field
- **boolean**: Toggle switch
- **json**: Textarea με JSON formatting

### Categories (Tabs)

Οι μεταβλητές ομαδοποιούνται αυτόματα σε tabs ανά `category`:
- `general` - Γενικές ρυθμίσεις
- `appearance` - Εμφάνιση/Θέμα
- `seo` - SEO ρυθμίσεις
- `email` - Email ρυθμίσεις
- `social` - Social media links
- `payment` - Payment settings
- `shipping` - Shipping settings

Μπορείς να χρησιμοποιήσεις οποιαδήποτε category θέλεις!

## 🔧 Προσαρμογές

### Προσθήκη Νέου Field Type

Επεξεργάσου τη μέθοδο `createFieldForVariable()` στο `DynamicSettings.php`:

```php
protected function createFieldForVariable(Variable $variable)
{
    return match ($variable->type) {
        'string' => TextInput::make($variable->key)...
        'color' => ColorPicker::make($variable->key)... // Νέος τύπος!
        // ...
    };
}
```

### Προσθήκη Νέου Category Icon

Επεξεργάσου τη μέθοδο `getCategoryIcon()`:

```php
protected function getCategoryIcon(string $category): string
{
    return match ($category) {
        'my_category' => 'heroicon-o-star', // Νέο icon!
        // ...
    };
}
```

## 📊 Παράδειγμα Δεδομένων

Το `DynamicVariablesSeeder` δημιουργεί παραδείγματα όπως:

- **General**: site_name, items_per_page, enable_maintenance
- **Appearance**: primary_color, theme_colors (JSON), logo_width
- **SEO**: meta_description, google_analytics_id, seo_keywords (JSON)
- **Email**: contact_email, email_from_name, enable_email_notifications
- **Social**: facebook_url, twitter_url, social_links (JSON)
- **Payment**: currency, payment_methods (JSON), enable_paypal
- **Shipping**: free_shipping_threshold, default_shipping_cost, shipping_zones (JSON)

## ✨ Χαρακτηριστικά

- ✅ **100% Dynamic**: Δεν χρειάζεται hardcoding
- ✅ **Scalable**: Αυξάνεται αυτόματα με νέες μεταβλητές
- ✅ **Type-Safe**: Υποστηρίζει string, number, boolean, json
- ✅ **Organized**: Tabs ανά category
- ✅ **User-Friendly**: Auto-generated labels από descriptions
- ✅ **Cache-Aware**: Καθαρίζει cache μετά από save

## 🔄 Workflow

1. Προσθήκη μεταβλητής στη βάση → Εμφανίζεται αυτόματα
2. Αλλαγή τιμής στο form → Αποθηκεύεται στη βάση
3. Διαγραφή μεταβλητής → Εξαφανίζεται από το form

## 📝 Σημειώσεις

- Όλες οι μεταβλητές είναι scoped per business (multi-tenant)
- Το JSON type εμφανίζεται ως Textarea με monospace font
- Boolean values αποθηκεύονται ως '1' ή '0' strings
- Number values αποθηκεύονται ως strings στη βάση

## 🐛 Troubleshooting

**Πρόβλημα**: Δεν εμφανίζονται μεταβλητές
- **Λύση**: Βεβαιώσου ότι έχεις τρέξει το migration και ότι υπάρχουν εγγραφές στη βάση

**Πρόβλημα**: JSON field δεν αποθηκεύεται σωστά
- **Λύση**: Βεβαιώσου ότι το JSON είναι valid format

**Πρόβλημα**: Tabs δεν εμφανίζονται
- **Λύση**: Βεβαιώσου ότι οι μεταβλητές έχουν `category` value

---

**Created**: 2026-01-23  
**Version**: 1.0.0  
**Filament Version**: 4.x
