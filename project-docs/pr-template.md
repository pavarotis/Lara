# Pull Request Template

## 📋 Description

Brief description of what this PR does.

## 🎯 Type of Change

- [ ] 🐛 Bug fix (non-breaking change which fixes an issue)
- [ ] ✨ New feature (non-breaking change which adds functionality)
- [ ] 💥 Breaking change (fix or feature that would cause existing functionality to not work as expected)
- [ ] 📚 Documentation update
- [ ] 🔧 Refactoring (no functional changes)
- [ ] ⚡ Performance improvement
- [ ] ✅ Test update

## 🔗 Related Issues

Closes #(issue number)

## ✅ Checklist

### Code Quality
- [ ] Code follows project conventions (`project-docs/conventions.md`)
- [ ] Code is self-documenting with clear variable/function names
- [ ] No hardcoded values (use config/env)
- [ ] No console.log/debug statements left
- [ ] All linter errors fixed (Pint, PHPStan)

### Testing
- [ ] Tests added/updated for new functionality
- [ ] All existing tests pass
- [ ] Manual testing completed

### Documentation
- [ ] README.md updated (if needed)
- [ ] CHANGELOG.md updated
- [ ] Code comments added for complex logic
- [ ] API documentation updated (if applicable)

### Security
- [ ] Input validation implemented
- [ ] Authorization checks in place
- [ ] No sensitive data exposed in logs/errors
- [ ] SQL injection prevention (using Eloquent/Query Builder)

### Database
- [ ] Migrations tested (up & down)
- [ ] Seeders tested (if applicable)
- [ ] No breaking changes to existing schema

### Frontend (if applicable)
- [ ] Responsive design tested
- [ ] Browser compatibility checked
- [ ] Accessibility considered
- [ ] No console errors

## 📸 Screenshots (if applicable)

<!-- Add screenshots for UI changes -->

## 🧪 Testing Instructions

1. Step 1
2. Step 2
3. Step 3

## 📝 Additional Notes

<!-- Any additional information for reviewers -->

