# Installation Guide - Laravel Application

Οδηγός πλήρους εγκατάστασης του Laravel application από git repository.

> **💡 Quick Start:** Για αυτόματη εγκατάσταση, τρέξε `.\setup.ps1` (Windows PowerShell)

## Προαπαιτούμενα

- **Laragon** (ή XAMPP/WAMP) με:
  - PHP 8.3+
  - MySQL/MariaDB
  - Apache/Nginx
  - Composer
  - Node.js & npm

## Βήμα 1: Clone Repository

```bash
cd C:\laragon\www
git clone <repository-url> lara
cd lara
```

## Βήμα 2: PHP Dependencies (Composer)

### 2.1 Προσθήκη PHP/Composer στο PATH

```powershell
# Προσθήκη στο PATH για την τρέχουσα session
$env:PATH="C:\laragon\bin\php\php-8.3.28-Win32-vs16-x64;C:\laragon\bin\composer;C:\laragon\bin\git\cmd;$env:PATH"
```

### 2.2 Ενεργοποίηση PHP Extensions

Στο Laragon:
1. Menu → PHP → Extensions
2. Ενεργοποίησε το **zip** extension
3. Restart Apache

### 2.3 Composer Install

```powershell
cd C:\laragon\www\lara

# Αν υπάρχουν permission issues με cache, χρησιμοποίησε local cache
$env:COMPOSER_CACHE_DIR="C:\laragon\www\lara\.composer-cache"

# Install dependencies
C:\laragon\bin\composer\composer.bat install --no-interaction --prefer-dist
```

**Σημείωση:** Αν υπάρχουν permission errors, δοκίμασε:
- Κλείσε προγράμματα που μπορεί να κρατούν locks (IDE, file explorers)
- Run PowerShell ως Administrator

## Βήμα 3: Environment Configuration

### 3.1 Δημιουργία .env File

```powershell
Copy-Item .env.example .env
```

### 3.2 Ρύθμιση .env

Άνοιξε το `.env` και ενημέρωσε:

```env
APP_NAME="LaraShop"
APP_URL=http://lara.test

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=lara
DB_USERNAME=root
DB_PASSWORD=root
```

### 3.3 Generate Application Key

```powershell
php artisan key:generate
```

## Βήμα 4: Database Setup

### 4.1 Δημιουργία Database

```sql
-- Στο phpMyAdmin ή MySQL CLI
CREATE DATABASE lara CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 4.2 Migrations

```powershell
php artisan migrate
```

**Σημείωση:** Αν υπάρχουν foreign key errors:
- Κάποια migrations μπορεί να χρειάζονται τροποποίηση (δείτε παρακάτω)

### 4.3 Seeders

```powershell
php artisan db:seed
```

Αυτό θα δημιουργήσει:
- Roles & Permissions
- Sample Businesses
- Products & Categories
- Settings

## Βήμα 5: Storage Link

```powershell
php artisan storage:link
```

## Βήμα 6: Node.js Dependencies & Build

### 6.1 Προσθήκη Node.js στο PATH

```powershell
$env:PATH="C:\laragon\bin\nodejs\node-v22;$env:PATH"
```

### 6.2 Install Dependencies

```powershell
npm install
```

### 6.3 Build Assets

```powershell
npm run build
```

Αυτό θα δημιουργήσει τα compiled CSS/JS files στο `public/build/`.

## Βήμα 7: Virtual Host Configuration (Laragon)

### 7.1 Δημιουργία Virtual Host

Στο Laragon:
1. Menu → Tools → Quick add → Domain
2. Domain: `lara.test`
3. Path: `C:\laragon\www\lara\public`
4. OK

### 7.2 Hosts File

Το Laragon συνήθως το κάνει αυτόματα. Αν όχι, πρόσθεσε στο `C:\Windows\System32\drivers\etc\hosts`:

```
127.0.0.1    lara.test
```

### 7.3 Restart Apache

Στο Laragon: Menu → Apache → Restart

## Βήμα 8: Home Page Content

**✅ Αυτόματη Δημιουργία:** Τα seeders (`BusinessSeeder`, `GasStationSeeder`, `BakerySeeder`) δημιουργούν αυτόματα home page content για κάθε business.

Αν χρειάζεται να δημιουργήσεις home page χειροκίνητα:

```powershell
php artisan tinker
```

Στο tinker:
```php
$business = \App\Domain\Businesses\Models\Business::active()->first();
$pageType = \App\Domain\Content\Models\ContentType::where('slug', 'page')->first();

\App\Domain\Content\Models\Content::create([
    'business_id' => $business->id,
    'type' => 'page',
    'slug' => '/',
    'title' => 'Home',
    'body_json' => [['type' => 'text', 'content' => '<h1>Welcome</h1>']],
    'status' => 'published',
    'published_at' => now(),
]);
```

## Βήμα 9: Verify Installation

### 9.1 Έλεγχος Routes

- `http://lara.test/` → Redirect στο πρώτο business
- `http://lara.test/{business-slug}` → Business home page
- `http://lara.test/login` → Login page
- `http://lara.test/admin` → Admin panel (Filament)

### 9.2 Έλεγχος Assets

Άνοιξε Developer Tools (F12) και ελέγξω:
- CSS files φορτώνονται από `/build/widgets/`
- JS files φορτώνονται
- Δεν υπάρχουν 404 errors

## Troubleshooting

### Composer Permission Errors

```powershell
# Χρησιμοποίησε local cache
$env:COMPOSER_CACHE_DIR="C:\laragon\www\lara\.composer-cache"
composer install --no-interaction --prefer-dist
```

### Migration Foreign Key Errors

**✅ ΔΙΟΡΘΩΜΕΝΟ:** Τα migrations `blog_comments` και `gift_vouchers` έχουν ήδη διορθωθεί να δημιουργούν foreign keys μετά τη δημιουργία του table.

Αν συναντήσεις παρόμοιο πρόβλημα σε άλλο migration:

1. Άνοιξε το migration file
2. Αλλάξε `foreignId()` σε `unsignedBigInteger()`
3. Προσθήκη foreign keys μετά τη δημιουργία του table:

```php
// Μετά το Schema::create()
Schema::table('table_name', function (Blueprint $table) {
    $table->foreign('column_id')
        ->references('id')
        ->on('referenced_table')
        ->onDelete('cascade');
});
```

### Vite Manifest Not Found

```powershell
# Εκτέλεσε build
npm run build

# Clear Laravel cache
php artisan view:clear
php artisan config:clear
```

### 404 on Home Page

**✅ ΔΙΟΡΘΩΜΕΝΟ:** Τα seeders δημιουργούν αυτόματα home page content για κάθε business.

Αν συναντήσεις 404, τρέξε:
```powershell
php artisan db:seed --class=BusinessSeeder
```

### npm Not Found

```powershell
# Προσθήκη Node.js στο PATH
$env:PATH="C:\laragon\bin\nodejs\node-v22;$env:PATH"

# Verify
npm --version
node --version
```

## Quick Setup Script

### Option 1: Automated Setup Script (Recommended)

```powershell
# Run the setup script
.\setup.ps1
```

Αυτό το script κάνει όλα τα βήματα αυτόματα με validation checks.

### Option 2: Manual Commands

```powershell
# Setup PATH
$env:PATH="C:\laragon\bin\php\php-8.3.28-Win32-vs16-x64;C:\laragon\bin\composer;C:\laragon\bin\git\cmd;C:\laragon\bin\nodejs\node-v22;$env:PATH"

# Composer
$env:COMPOSER_CACHE_DIR="C:\laragon\www\lara\.composer-cache"
C:\laragon\bin\composer\composer.bat install --no-interaction --prefer-dist

# Environment
Copy-Item .env.example .env
php artisan key:generate

# Database
php artisan migrate
php artisan db:seed

# Storage
php artisan storage:link

# Assets
npm install
npm run build

# Clear cache
php artisan view:clear
php artisan config:clear
```

### Verify Installation

```powershell
# Run pre-flight checks
php scripts/check-setup.php
```

Αυτό θα ελέγξει:
- PHP version & extensions
- .env configuration
- Database connection
- Migrations status
- Storage link
- Vite manifest
- Businesses & home pages

## Default Credentials

Μετά το `db:seed`, δημιουργείται αυτόματα admin user:

**Admin Panel:** `http://lara.test/admin`

**Default Credentials:**
- **Email:** `admin@larashop.test`
- **Password:** `password`

**Σημείωση:** Αλλάξε το password αμέσως μετά το πρώτο login!

### Αν δεν έχεις admin user:

```powershell
php artisan db:seed --class=UserSeeder
```

Ή δημιούργησε έναν χειροκίνητα:

```powershell
php artisan tinker
```

Στο tinker:
```php
$user = \App\Models\User::create([
    'name' => 'Admin',
    'email' => 'admin@example.com',
    'password' => bcrypt('password'),
    'email_verified_at' => now(),
]);

$adminRole = \App\Domain\Auth\Models\Role::where('slug', 'admin')->first();
$user->roles()->attach($adminRole->id);
```

## Next Steps

1. Login στο admin panel: `http://lara.test/admin`
2. Δημιούργησε περισσότερο content
3. Customize theme & settings
4. Προσθήκη products & categories

## Support

Αν αντιμετωπίσεις προβλήματα:
1. Ελέγξω τα logs: `storage/logs/laravel.log`
2. Clear όλα τα caches
3. Verify database connection
4. Check PHP extensions

---

**Last Updated:** 2025-01-27
