<?php

/**
 * Pre-flight check script
 * Run this to verify your installation is correct
 */

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$errors = [];
$warnings = [];

echo "🔍 Checking installation...\n\n";

// Check PHP version
$phpVersion = PHP_VERSION;
if (version_compare($phpVersion, '8.3.0', '<')) {
    $errors[] = "PHP version must be 8.3+ (current: $phpVersion)";
} else {
    echo "✓ PHP version: $phpVersion\n";
}

// Check required extensions
$requiredExtensions = ['pdo', 'pdo_mysql', 'mbstring', 'openssl', 'json', 'zip'];
foreach ($requiredExtensions as $ext) {
    if (! extension_loaded($ext)) {
        $errors[] = "Missing PHP extension: $ext";
    } else {
        echo "✓ PHP extension: $ext\n";
    }
}

// Check .env file
if (! file_exists(__DIR__.'/../.env')) {
    $errors[] = '.env file not found. Run: cp .env.example .env';
} else {
    echo "✓ .env file exists\n";
}

// Check APP_KEY
$envContent = file_get_contents(__DIR__.'/../.env');
if (! preg_match('/APP_KEY=base64:/', $envContent)) {
    $errors[] = 'APP_KEY not set. Run: php artisan key:generate';
} else {
    echo "✓ APP_KEY is set\n";
}

// Check database connection
try {
    DB::connection()->getPdo();
    echo "✓ Database connection successful\n";
} catch (\Exception $e) {
    $errors[] = 'Database connection failed: '.$e->getMessage();
}

// Check migrations
$pendingMigrations = DB::table('migrations')->count();
$migrationFiles = count(glob(__DIR__.'/../database/migrations/*.php'));
if ($pendingMigrations < $migrationFiles) {
    $warnings[] = 'Some migrations may not have run. Run: php artisan migrate:status';
} else {
    echo "✓ Migrations appear to be up to date\n";
}

// Check storage link
if (! file_exists(__DIR__.'/../public/storage')) {
    $warnings[] = 'Storage link not found. Run: php artisan storage:link';
} else {
    echo "✓ Storage link exists\n";
}

// Check Vite manifest
if (! file_exists(__DIR__.'/../public/build/manifest.json')) {
    $warnings[] = 'Vite manifest not found. Run: npm run build';
} else {
    echo "✓ Vite manifest exists\n";
}

// Check businesses
$businessCount = \App\Domain\Businesses\Models\Business::active()->count();
if ($businessCount === 0) {
    $warnings[] = 'No active businesses found. Run: php artisan db:seed';
} else {
    echo "✓ Active businesses: $businessCount\n";
}

// Check home pages
$businessesWithoutHome = \App\Domain\Businesses\Models\Business::active()
    ->whereDoesntHave('contents', function ($query) {
        $query->where('slug', '/');
    })
    ->count();

if ($businessesWithoutHome > 0) {
    $warnings[] = "$businessesWithoutHome business(es) without home page content";
} else {
    echo "✓ All businesses have home pages\n";
}

// Summary
echo "\n";
if (count($errors) > 0) {
    echo "❌ ERRORS:\n";
    foreach ($errors as $error) {
        echo "  - $error\n";
    }
    echo "\n";
}

if (count($warnings) > 0) {
    echo "⚠️  WARNINGS:\n";
    foreach ($warnings as $warning) {
        echo "  - $warning\n";
    }
    echo "\n";
}

if (count($errors) === 0 && count($warnings) === 0) {
    echo "✅ All checks passed! Installation looks good.\n";
    exit(0);
} elseif (count($errors) === 0) {
    echo "⚠️  Installation has warnings but should work.\n";
    exit(0);
} else {
    echo "❌ Installation has errors. Please fix them before continuing.\n";
    exit(1);
}
