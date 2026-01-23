# Clear Laravel cache files
# Run this script when cache files are corrupted or locked
# IMPORTANT: Close Laragon (Stop All) BEFORE running this script!

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "Laravel Cache Cleaner" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

$cachePath = "bootstrap\cache"

# Check if Laragon might be running
Write-Host "⚠️  IMPORTANT: Make sure Laragon is CLOSED (Stop All) before running this!" -ForegroundColor Yellow
Write-Host ""

# List of cache files to delete
$cacheFiles = @(
    "config.php",
    "packages.php",
    "services.php",
    "routes.php",
    "events.php"
)

$deletedCount = 0
$failedCount = 0

Write-Host "Clearing Laravel cache files..." -ForegroundColor Yellow
Write-Host ""

foreach ($file in $cacheFiles) {
    $filePath = Join-Path $cachePath $file
    if (Test-Path $filePath) {
        try {
            Remove-Item $filePath -Force -ErrorAction Stop
            Write-Host "  ✓ Deleted: $file" -ForegroundColor Green
            $deletedCount++
        } catch {
            Write-Host "  ✗ Could not delete $file" -ForegroundColor Red
            Write-Host "    Error: $($_.Exception.Message)" -ForegroundColor Red
            Write-Host "    → Close Laragon and delete manually from bootstrap\cache\" -ForegroundColor Yellow
            $failedCount++
        }
    } else {
        Write-Host "  - Not found: $file" -ForegroundColor Gray
    }
}

Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
if ($failedCount -eq 0) {
    Write-Host "✓ Cache clearing complete! ($deletedCount files deleted)" -ForegroundColor Green
    Write-Host ""
    Write-Host "Next steps:" -ForegroundColor Yellow
    Write-Host "1. Start Laragon" -ForegroundColor White
    Write-Host "2. Test your site" -ForegroundColor White
    Write-Host "3. Run: php artisan optimize:clear" -ForegroundColor White
} else {
    Write-Host "⚠️  Some files could not be deleted!" -ForegroundColor Red
    Write-Host ""
    Write-Host "Please:" -ForegroundColor Yellow
    Write-Host "1. Close Laragon (Stop All)" -ForegroundColor White
    Write-Host "2. Delete files manually from bootstrap\cache\" -ForegroundColor White
    Write-Host "3. Restart Laragon" -ForegroundColor White
}
Write-Host "========================================" -ForegroundColor Cyan
