# Laravel Application Setup Script for Windows (Laragon)
# Run this script after cloning the repository

Write-Host "🚀 Starting Laravel Application Setup..." -ForegroundColor Cyan

# Step 1: Setup PATH
Write-Host "`n📦 Step 1: Setting up PATH..." -ForegroundColor Yellow
$phpPath = "C:\laragon\bin\php\php-8.3.28-Win32-vs16-x64"
$composerPath = "C:\laragon\bin\composer"
$gitPath = "C:\laragon\bin\git\cmd"
$nodePath = "C:\laragon\bin\nodejs\node-v22"

$env:PATH = "$phpPath;$composerPath;$gitPath;$nodePath;$env:PATH"

# Verify tools
Write-Host "Checking tools..." -ForegroundColor Gray
try {
    $phpVersion = & php -v 2>&1 | Select-Object -First 1
    Write-Host "  ✓ PHP: $phpVersion" -ForegroundColor Green
} catch {
    Write-Host "  ✗ PHP not found" -ForegroundColor Red
    exit 1
}

try {
    $composerVersion = & composer --version 2>&1 | Select-Object -First 1
    Write-Host "  ✓ Composer: $composerVersion" -ForegroundColor Green
} catch {
    Write-Host "  ✗ Composer not found" -ForegroundColor Red
    exit 1
}

try {
    $nodeVersion = & node -v 2>&1
    Write-Host "  ✓ Node.js: $nodeVersion" -ForegroundColor Green
} catch {
    Write-Host "  ✗ Node.js not found" -ForegroundColor Red
    exit 1
}

# Step 2: Composer Install
Write-Host "`n📦 Step 2: Installing PHP dependencies..." -ForegroundColor Yellow
$env:COMPOSER_CACHE_DIR = "$PWD\.composer-cache"
New-Item -ItemType Directory -Force -Path ".composer-cache" | Out-Null

& C:\laragon\bin\composer\composer.bat install --no-interaction --prefer-dist
if ($LASTEXITCODE -ne 0) {
    Write-Host "  ✗ Composer install failed" -ForegroundColor Red
    exit 1
}
Write-Host "  ✓ Composer dependencies installed" -ForegroundColor Green

# Step 3: Environment Setup
Write-Host "`n⚙️  Step 3: Setting up environment..." -ForegroundColor Yellow
if (-not (Test-Path ".env")) {
    Copy-Item .env.example .env
    Write-Host "  ✓ Created .env file" -ForegroundColor Green
} else {
    Write-Host "  ℹ .env file already exists" -ForegroundColor Gray
}

# Generate APP_KEY if not set
$envContent = Get-Content .env -Raw
if ($envContent -notmatch "APP_KEY=base64:") {
    & php artisan key:generate --force
    Write-Host "  ✓ Generated APP_KEY" -ForegroundColor Green
} else {
    Write-Host "  ℹ APP_KEY already set" -ForegroundColor Gray
}

# Step 4: Database Setup
Write-Host "`n🗄️  Step 4: Setting up database..." -ForegroundColor Yellow
Write-Host "  ⚠ Make sure MySQL is running and database 'lara' exists" -ForegroundColor Yellow
$continue = Read-Host "  Continue? (y/n)"
if ($continue -ne "y") {
    Write-Host "  Skipping database setup" -ForegroundColor Gray
} else {
    & php artisan migrate --force
    if ($LASTEXITCODE -ne 0) {
        Write-Host "  ✗ Migrations failed" -ForegroundColor Red
        Write-Host "  You may need to fix migration foreign key issues manually" -ForegroundColor Yellow
    } else {
        Write-Host "  ✓ Migrations completed" -ForegroundColor Green
    }

    & php artisan db:seed --force
    if ($LASTEXITCODE -ne 0) {
        Write-Host "  ✗ Seeders failed" -ForegroundColor Red
    } else {
        Write-Host "  ✓ Database seeded" -ForegroundColor Green
    }
}

# Step 5: Storage Link
Write-Host "`n📁 Step 5: Creating storage link..." -ForegroundColor Yellow
& php artisan storage:link
Write-Host "  ✓ Storage link created" -ForegroundColor Green

# Step 6: Node.js Dependencies
Write-Host "`n📦 Step 6: Installing Node.js dependencies..." -ForegroundColor Yellow
& npm install
if ($LASTEXITCODE -ne 0) {
    Write-Host "  ✗ npm install failed" -ForegroundColor Red
    Write-Host "  You may need to run this manually" -ForegroundColor Yellow
} else {
    Write-Host "  ✓ Node.js dependencies installed" -ForegroundColor Green
}

# Step 7: Build Assets
Write-Host "`n🎨 Step 7: Building assets..." -ForegroundColor Yellow
& npm run build
if ($LASTEXITCODE -ne 0) {
    Write-Host "  ✗ Build failed" -ForegroundColor Red
    Write-Host "  You may need to run 'npm run build' manually" -ForegroundColor Yellow
} else {
    Write-Host "  ✓ Assets built successfully" -ForegroundColor Green
}

# Step 8: Clear Cache
Write-Host "`n🧹 Step 8: Clearing cache..." -ForegroundColor Yellow
& php artisan view:clear
& php artisan config:clear
& php artisan cache:clear
Write-Host "  ✓ Cache cleared" -ForegroundColor Green

# Final Check
Write-Host "`n✅ Setup Complete!" -ForegroundColor Green
Write-Host "`nNext steps:" -ForegroundColor Cyan
Write-Host "  1. Configure .env file (database credentials, APP_URL)" -ForegroundColor White
Write-Host "  2. Setup virtual host in Laragon (Menu → Tools → Quick add)" -ForegroundColor White
Write-Host "  3. Visit http://lara.test (or your configured domain)" -ForegroundColor White
Write-Host "  4. Login to admin: http://lara.test/admin" -ForegroundColor White
