# 🛠️ Development Commands

## Server Setup

### Option 1: Laragon (Recommended for Windows)

Αν χρησιμοποιείς **Laragon**, το server τρέχει ήδη:

1. **Άνοιξε Laragon**
2. **Κάνε "Start All"** (ή μόνο Apache + MySQL)
3. **Άνοιξε browser**: `http://larashop.test/admin`

**Laragon URLs**:
- Public site: `http://larashop.test`
- Admin panel: `http://larashop.test/admin`

---

### Option 2: Laravel Built-in Server

Αν **ΔΕΝ** χρησιμοποιείς Laragon, μπορείς να χρησιμοποιήσεις το built-in Laravel server:

```bash
php artisan serve
```

Αυτό θα ξεκινήσει server στο `http://127.0.0.1:8000`

**URLs**:
- Public site: `http://127.0.0.1:8000`
- Admin panel: `http://127.0.0.1:8000/admin`

**Σημείωση**: Πρέπει να κρατήσεις το terminal ανοιχτό. Για να σταματήσεις: `Ctrl+C`

---

## 🔍 Pre-Flight Checks

Πριν ανοίξεις το admin panel, βεβαιώσου ότι:

### 1. Migrations Run ✅
```bash
php artisan migrate:status
```

Αν υπάρχουν pending migrations:
```bash
php artisan migrate
```

### 2. Seeders Run (Optional)
```bash
php artisan db:seed
```

### 3. Storage Link (για images)
```bash
php artisan storage:link
```

### 4. Assets Compiled
```bash
npm install
npm run build
```

---

## 🚀 Quick Start

### Με Laragon:
1. Άνοιξε Laragon → "Start All"
2. Browser: `http://larashop.test/admin`
3. Login με admin user

### Με Laravel Server:
1. Terminal: `php artisan serve`
2. Browser: `http://127.0.0.1:8000/admin`
3. Login με admin user

---

## 👤 Admin User

Αν δεν έχεις admin user, δημιούργησε ένα:

```bash
php artisan tinker
```

Στο tinker:
```php
$user = \App\Models\User::create([
    'name' => 'Admin',
    'email' => 'admin@example.com',
    'password' => bcrypt('password'),
    'is_admin' => true,
]);
$user->roles()->attach(\App\Domain\Auth\Models\Role::where('slug', 'admin')->first());
```

Ή χρησιμοποίησε το Filament UserResource στο `/admin/users` (αν έχεις ήδη έναν admin user).

---

## 📝 Common Commands

### Clear Cache
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Run Tests
```bash
php artisan test
```

### Code Formatting
```bash
./vendor/bin/pint
```

### Check Routes
```bash
php artisan route:list --path=admin
```

---

## ⚠️ Troubleshooting

### "404 Not Found" στο `/admin`
- Ελέγξε ότι το Filament είναι installed: `composer show filament/filament`
- Clear cache: `php artisan optimize:clear`

### "Access Denied" στο `/admin`
- Ελέγξε ότι ο user έχει admin role
- Ελέγξε το `AdminMiddleware`

### "Connection Refused"
- Ελέγξε ότι το MySQL τρέχει (Laragon → Start All)
- Ελέγξε το `.env` file (DB credentials)

---

**Last Updated**: 2024-11-27
