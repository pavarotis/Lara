# Git Workflow — LaraShop

## 🌿 Branching Model

We use a **simplified GitFlow** approach:

### Main Branches

- **`main`**: Production-ready code. Always stable.
- **`develop`**: Integration branch for features. Latest development state.

### Supporting Branches

- **`feature/*`**: New features
  - Naming: `feature/sprint-0-admin-panel`, `feature/user-authentication`
  - Branch from: `develop`
  - Merge to: `develop`
  
- **`bugfix/*`**: Bug fixes
  - Naming: `bugfix/fix-order-calculation`, `bugfix/mobile-menu-overlay`
  - Branch from: `develop` (or `main` for hotfixes)
  - Merge to: `develop` (or `main` for hotfixes)

- **`hotfix/*`**: Critical production fixes
  - Naming: `hotfix/security-patch`, `hotfix/critical-bug`
  - Branch from: `main`
  - Merge to: `main` AND `develop`

- **`release/*`**: Release preparation
  - Naming: `release/v2.0.0`, `release/sprint-5`
  - Branch from: `develop`
  - Merge to: `main` and `develop`

## 📝 Commit Message Convention

We follow **Conventional Commits**:

```
<type>(<scope>): <subject>

<body>

<footer>
```

### Types

- `feat`: New feature
- `fix`: Bug fix
- `docs`: Documentation changes
- `style`: Code style changes (formatting, missing semicolons, etc.)
- `refactor`: Code refactoring
- `test`: Adding/updating tests
- `chore`: Maintenance tasks (dependencies, config, etc.)

### Examples

```
feat(admin): add user management UI with Filament

- Created UserResource with role assignment
- Added filters and search functionality
- Integrated with RBAC system

Closes #123
```

```
fix(orders): correct order total calculation

Fixed issue where order total was not including tax.
Updated CalculateOrderTotalService to include tax calculation.

Fixes #456
```

## 🔄 Workflow Steps

### Starting a New Feature

1. **Update develop branch:**
   ```bash
   git checkout develop
   git pull origin develop
   ```

2. **Create feature branch:**
   ```bash
   git checkout -b feature/sprint-0-admin-panel
   ```

3. **Work on feature:**
   - Make commits following commit convention
   - Push regularly: `git push origin feature/sprint-0-admin-panel`

### Completing a Feature

1. **Ensure code quality:**
   ```bash
   composer pint
   # Run tests
   php artisan test
   ```

2. **Update documentation:**
   - Update `CHANGELOG.md`
   - Update `README.md` if needed
   - Update sprint notes if applicable

3. **Create Pull Request:**
   - Use PR template (`project-docs/pr-template.md`)
   - Request review from team
   - Address review comments

4. **Merge to develop:**
   - After approval, merge PR
   - Delete feature branch

### Hotfix Workflow

1. **Create hotfix branch from main:**
   ```bash
   git checkout main
   git pull origin main
   git checkout -b hotfix/critical-bug
   ```

2. **Fix and test:**
   - Make fix
   - Test thoroughly
   - Update CHANGELOG.md

3. **Merge to main AND develop:**
   ```bash
   git checkout main
   git merge hotfix/critical-bug
   git push origin main
   
   git checkout develop
   git merge hotfix/critical-bug
   git push origin develop
   ```

## 🚫 Rules

1. **Never commit directly to `main` or `develop`**
2. **Always pull before starting work**
3. **Keep commits atomic** (one logical change per commit)
4. **Write descriptive commit messages**
5. **Run linters before committing** (use pre-commit hooks)
6. **Update CHANGELOG.md for user-facing changes**

## 🔧 Pre-commit Hooks

We use pre-commit hooks to ensure code quality:

- **Laravel Pint**: Automatic code formatting
- **PHPStan**: Static analysis (if configured)

See `project-docs/dev-workflow.md` for setup instructions.

## 📚 Resources

- [Conventional Commits](https://www.conventionalcommits.org/)
- [GitFlow Workflow](https://www.atlassian.com/git/tutorials/comparing-workflows/gitflow-workflow)

