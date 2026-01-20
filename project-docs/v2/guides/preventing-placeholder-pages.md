# Preventing Placeholder Pages - Best Practices

## Problem
Placeholder pages (pages that show "functionality will be implemented here") can confuse users and create a poor UX. They should be replaced with actual functionality or removed.

## Best Practices

### 1. **Never Create Placeholder Pages**
- ❌ **Don't**: Create a Filament Page with placeholder text
- ✅ **Do**: Create the actual Resource/Page immediately, even if basic

### 2. **If You Must Create a Placeholder**
- Add a `TODO` comment in the code
- Add it to the sprint backlog immediately
- Set a deadline for completion
- Mark it clearly in navigation (e.g., "Coming Soon" badge)

### 3. **Check Before Creating Navigation Items**
Before adding a navigation item, verify:
- Does the functionality already exist?
- Is there a Resource/Page that handles this?
- Can we reuse existing functionality?

### 4. **Content Management Pattern**
For content types (Blog Posts, Pages, Articles):
- Use a single `ContentResource` with type filtering
- Don't create separate placeholder pages for each type
- Use `getEloquentQuery()` to filter by type

### 5. **Regular Audits**
- Review all Filament Pages quarterly
- Check for placeholder text
- Remove or implement incomplete pages

### 6. **Documentation**
- Document what each navigation item does
- Keep a list of planned features separate from implemented ones

## Example: Blog Posts

**❌ Bad Approach:**
```php
// app/Filament/Pages/CMS/Blog/Posts.php
class Posts extends Page {
    // Shows "Blog Posts functionality will be implemented here"
}
```

**✅ Good Approach:**
```php
// app/Filament/Resources/ContentResource.php
class ContentResource extends Resource {
    protected static ?string $navigationLabel = 'Blog Posts';
    
    public static function getEloquentQuery(): Builder {
        return parent::getEloquentQuery()
            ->where('type', 'article'); // Filter for blog posts
    }
}
```

## Checklist for New Features

- [ ] Feature is fully implemented (not placeholder)
- [ ] Navigation item points to working functionality
- [ ] No "will be implemented" messages visible to users
- [ ] Feature is tested and working
- [ ] Documentation updated

## Related Files
- `app/Filament/Pages/` - Check for placeholder pages
- `app/Filament/Resources/` - Verify Resources exist for navigation items
- Navigation configuration - Ensure all items have working targets
