# Development Commands

## 🚀 Start Development Server

```bash
cd D:\laragon\www\larashop
npm run build
php artisan serve
```

Η εφαρμογή θα είναι διαθέσιμη στο: http://127.0.0.1:8000

---

## 📦 Συχνές Εντολές

### Build Assets (TailwindCSS)
```bash
npm run build
```
Τρέξε αυτό όταν αλλάζεις CSS/JS αρχεία.

### Development Mode (Hot Reload)
```bash
npm run dev
```
Για live reload κατά την ανάπτυξη (χρειάζεται ξεχωριστό terminal).

### Clear Cache
```bash
php artisan cache:clear
php artisan view:clear
php artisan config:clear
```

### Run Migrations
```bash
php artisan migrate
```

### Run Seeders
```bash
php artisan db:seed
```

### Storage Link (μία φορά)
```bash
php artisan storage:link
```
Δημιουργεί symbolic link για uploaded files.

---

## 🔧 Troubleshooting

### Σπασμένο CSS
```bash
npm run build
```
Μετά refresh browser.

### Database errors
```bash
php artisan migrate:fresh --seed
```
⚠️ Διαγράφει όλα τα δεδομένα!

### Permission errors σε uploads
Βεβαιώσου ότι υπάρχει το `storage/app/public` folder.

