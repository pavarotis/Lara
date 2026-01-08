# Deployment Checklist

**Purpose**: Prevent common deployment errors (missing migrations, seeders, etc.)

---

## 🚀 Pre-Deployment Checklist

### Database

- [ ] **Run migrations**: `php artisan migrate:status` - verify no pending migrations
- [ ] **Run seeders**: Check if new seeders need to run
- [ ] **Verify tables exist**: Check database for new tables
- [ ] **Check foreign keys**: Verify relationships are correct

### Code

- [ ] **Linter**: `php artisan pint` - no errors
- [ ] **Type hints**: All Filament 4 API correct (see `filament_4_api_reference.md`)
- [ ] **Namespaces**: All imports correct
- [ ] **Routes**: New routes registered in `routes/web.php` or `routes/api.php`

### Configuration

- [ ] **Config files**: New config files exist (e.g., `config/header_variants.php`)
- [ ] **Middleware**: New middleware registered in `bootstrap/app.php`
- [ ] **Service providers**: New providers registered if needed

### Views

- [ ] **Blade views**: All new views exist
- [ ] **View paths**: All `@include` and `view()` calls use correct paths
- [ ] **Components**: All Blade components exist

---

## 🔍 Post-Deployment Verification

### Quick Tests

1. **Check migrations:**
   ```bash
   php artisan migrate:status
   ```
   Should show all migrations as "Ran"

2. **Check database:**
   ```bash
   php artisan tinker
   >>> Schema::hasTable('theme_tokens')
   => true
   ```

3. **Check routes:**
   ```bash
   php artisan route:list | grep sitemap
   ```

4. **Check services:**
   ```bash
   php artisan tinker
   >>> app(\App\Domain\Themes\Services\GetThemeTokensService::class)
   => App\Domain\Themes\Services\GetThemeTokensService {#1234}
   ```

---

## 🐛 Common Errors & Solutions

### Error: "Table doesn't exist"

**Solution:**
```bash
php artisan migrate
```

**Prevention:** Always run `migrate:status` before testing

---

### Error: "Class not found"

**Solution:**
- Check namespace
- Run `composer dump-autoload`
- Check if file exists

**Prevention:** Use IDE autocomplete, run linter

---

### Error: "Type mismatch" (Filament)

**Solution:** Check `filament_4_api_reference.md` for correct API

**Prevention:** Always reference existing working Filament code

---

### Error: "Route not found"

**Solution:**
- Check `routes/web.php` or `routes/api.php`
- Run `php artisan route:clear`
- Run `php artisan route:cache`

**Prevention:** Add routes immediately when creating controllers

---

## 📝 Sprint Completion Checklist

When completing a sprint:

1. ✅ All migrations run successfully
2. ✅ All seeders run successfully (if applicable)
3. ✅ All routes registered
4. ✅ All middleware registered
5. ✅ All config files created
6. ✅ All services/controllers created
7. ✅ All views created
8. ✅ Linter passes (`php artisan pint`)
9. ✅ No runtime errors in browser
10. ✅ Review document created

---

## 🔗 Related Documents

- `filament_4_api_reference.md` - Filament 4 API guide
- `mvc_checklist.md` - MVC compliance checklist
- `hybrid_admin_decision_tree.md` - When to use Filament vs Blade

