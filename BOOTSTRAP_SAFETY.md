# Bootstrap Safety Guidelines

## 🛡️ Preventing "Target class [config] does not exist" Errors

### What Happened

This error occurred after running `php artisan optimize` with corrupted cache files. The Laravel container tried to resolve the `config` service before it was properly bound, causing a fatal error.

### ✅ Protection Already Added

We've added safety checks in:

1. **AppServiceProvider** - Try-catch + config binding check**
2. **PluginRegistryService** - Checks before every `config()` call
3. **Log calls** - Protected with binding checks

### 📋 Service Provider Rules

**❌ NEVER DO THIS:**
```php
public function boot(): void
{
    // ❌ config() without check
    $value = config('key');
    
    // ❌ Log:: without check
    Log::info('Message');
}
```

**✅ ALWAYS DO THIS:**
```php
public function boot(): void
{
    try {
        if (app()->bound('config')) {
            $value = config('key');
            // ... rest of code
        }
    } catch (\Exception $e) {
        // Silently fail during bootstrap
    }
}
```

### 🔒 Cache Management

**Local Development:**
- Use `optimize:clear` when something is stuck
- DON'T run `optimize` in local (not necessary)
- After `filament:assets`, test the site first before running `optimize`

**Production:**
- Run `optimize` only after deploy
- Always run `optimize:clear` before `optimize` in production

### 🚨 If It Happens Again (Cache Corrupted)

**Συμπτώματα:**
- `php artisan` commands δεν δουλεύουν
- Error: "Target class [config] does not exist"
- Το site δεν φορτώνει

**Λύση (Step-by-step):**

1. **Κλείσε το Laragon** (Stop All) - ΚΡΙΣΙΜΟ!
2. **Διέγραψε cache files χειροκίνητα:**
   - Άνοιξε `C:\laragon\www\lara\bootstrap\cache\`
   - Διέγραψε: `config.php`, `packages.php`, `services.php` (αν υπάρχουν)
   - Ή τρέξε: `.\clear-cache.ps1` (αν το Laragon είναι κλειστό)
3. **Ξαναάνοιξε το Laragon**
4. **Δοκίμασε το site** - θα πρέπει να δουλεύει
5. **Μετά τρέξε:** `php artisan optimize:clear` (για να καθαρίσεις τα υπόλοιπα caches)

**⚠️ ΣΗΜΑΝΤΙΚΟ:**
- Αν τα cache files είναι **locked**, πρέπει να κλείσεις το Laragon ΠΡΙΝ τα διαγράψεις
- Το `optimize:clear` **ΔΕΝ** θα δουλέψει αν τα cache files είναι corrupted
- Πρέπει να τα διαγράψεις **χειροκίνητα** πρώτα

### 📝 New Service Provider Checklist

When writing a new Service Provider:

- [ ] Don't call `config()` in `register()` method
- [ ] If you need `config()` in `boot()`, wrap it in `if (app()->bound('config'))`
- [ ] If you need `Log::`, check `app()->bound('log')` first
- [ ] Use try-catch for critical operations in `boot()`
- [ ] Don't do heavy operations in `register()` (only bindings)
