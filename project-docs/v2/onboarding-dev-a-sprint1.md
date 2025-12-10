# 👋 Onboarding Guide — Dev A (Sprint 1)

**Welcome to LaraShop v2!** 🎉

Αυτός ο οδηγός θα σε βοηθήσει να ξεκινήσεις με το Sprint 1. Διάβασε το προσεκτικά πριν ξεκινήσεις.

---

## 📋 Table of Contents

1. [Ποιος είσαι](#ποιος-είσαι)
2. [Τι πρέπει να ξέρεις](#τι-πρέπει-να-ξέρεις)
3. [Πώς να είναι σαν προγραμματιστής](#πώς-να-είσαι-σαν-προγραμματιστής)
4. [Setup & Preparation](#setup--preparation)
5. [Sprint 1 Tasks](#sprint-1-tasks)
6. [Workflow & Best Practices](#workflow--best-practices)
7. [Common Pitfalls](#common-pitfalls)
8. [Resources & Documentation](#resources--documentation)

---

## 🎯 Ποιος είσαι

**Dev A** = Backend/Infrastructure Developer

**Ευθύνη σου**:
- Backend development (Services, Controllers, Models)
- Database design & migrations
- API development
- Business logic implementation
- Code quality & testing

**Δεν είσαι**:
- Frontend developer (αυτό είναι Dev C)
- DevOps engineer (αυτό είναι optional)
- Project manager

---

## 📚 Τι πρέπει να ξέρεις

### Required Knowledge

1. **Laravel 12.x** (Advanced)
   - Eloquent ORM
   - Service Layer Pattern
   - Form Requests & Validation
   - Policies & Authorization
   - Migrations & Seeders
   - Artisan Commands

2. **PHP 8.3+** (Advanced)
   - Type hints & return types
   - Strict types (`declare(strict_types=1)`)
   - Modern PHP features (enums, match, etc.)

3. **Database Design**
   - MySQL/MariaDB
   - Foreign keys & indexes
   - Relationships (one-to-many, many-to-many)
   - Soft deletes

4. **Architecture Patterns**
   - Domain-Driven Design (DDD)
   - Service Layer Pattern
   - Repository Pattern (NOT used — we use Services)
   - Dependency Injection

5. **Testing**
   - PHPUnit
   - Feature tests
   - Test data factories

### Nice to Have

- API development (REST)
- Caching strategies
- Queue & Jobs
- Events & Listeners

---

## 💼 Πώς να είσαι σαν προγραμματιστής

### 1. **Code Quality First**

- ✅ **Clean Code**: Κώδικας που διαβάζεται εύκολα
- ✅ **Self-Documenting**: Ονόματα μεταβλητών/μεθόδων που εξηγούνται μόνα τους
- ✅ **DRY**: Don't Repeat Yourself — reuse code
- ✅ **SOLID Principles**: Especially Single Responsibility
- ✅ **Type Safety**: Always use type hints & return types

### 2. **Follow Conventions**

- ✅ **Read `project-docs/conventions.md`** — **MUST READ**
- ✅ **Follow Laravel conventions**
- ✅ **Follow project-specific conventions** (Service Layer, Domain structure, etc.)
- ✅ **Consistent naming**: camelCase for methods, PascalCase for classes

### 3. **Think Before Coding**

- ✅ **Understand the requirement** before coding
- ✅ **Ask questions** if something is unclear
- ✅ **Plan the solution** (mentally or on paper)
- ✅ **Consider edge cases** (null values, empty arrays, etc.)
- ✅ **Think about performance** (N+1 queries, caching, etc.)

### 4. **Test Your Code**

- ✅ **Test manually** before committing
- ✅ **Write tests** for new features (when required)
- ✅ **Test edge cases** (empty data, invalid input, etc.)
- ✅ **Test error handling** (what happens when something fails?)

### 5. **Documentation**

- ✅ **PHPDoc comments** for complex methods
- ✅ **Update CHANGELOG.md** after completing tasks
- ✅ **Update sprint notes** with progress/issues
- ✅ **Comment complex logic** (explain WHY, not WHAT)

### 6. **Error Prevention**

- ✅ **Check for null** before using values
- ✅ **Validate input** (use Form Requests)
- ✅ **Handle exceptions** properly
- ✅ **Use transactions** for database operations
- ✅ **Check relationships** exist before using

---

## 🚀 Setup & Preparation

### Step 1: Read Documentation (MUST DO)

**ΠΡΙΝ** ξεκινήσεις, διάβασε:

1. ✅ **`project-docs/conventions.md`** — Coding conventions (MUST READ)
2. ✅ **`project-docs/architecture.md`** — Project architecture
3. ✅ **`project-docs/v2/v2_overview.md`** — v2 overview & strategy
4. ✅ **`project-docs/v2/dev-responsibilities.md`** — Developer responsibilities
5. ✅ **`project-docs/v2/sprints/sprint_1.md`** — Sprint 1 tasks
6. ✅ **`project-docs/v2/v2_content_model.md`** — Content model & blocks
7. ✅ **`CHANGELOG.md`** — Project history & current status

**⏱️ Time**: ~2-3 hours (worth it!)

### Step 2: Environment Setup

```bash
# 1. Clone repository (if not already done)
git clone <repo-url>
cd larashop

# 2. Install dependencies
composer install
npm install

# 3. Copy environment file
cp .env.example .env

# 4. Generate app key
php artisan key:generate

# 5. Run migrations
php artisan migrate

# 6. Run seeders
php artisan db:seed

# 7. Link storage
php artisan storage:link

# 8. Build assets
npm run build
```

### Step 3: Verify Setup

```bash
# Check Laravel version
php artisan --version

# Check routes
php artisan route:list

# Check migrations
php artisan migrate:status

# Run tests (if any)
php artisan test
```

### Step 4: Understand Current State

**Sprint 0 Completed**:
- ✅ RBAC system (roles, permissions)
- ✅ Settings module
- ✅ API foundation
- ✅ Content & Media database structure (skeleton)
- ✅ Admin panel (Filament + Blade hybrid)

**Sprint 1 Goal**:
- Implement Content Module (CRUD, Services, Controllers)
- Block-based content editor
- Content types management

---

## 📝 Sprint 1 Tasks

### Your Tasks (Dev A — Backend)

**Read**: `project-docs/v2/sprints/sprint_1.md` for detailed tasks

**High-Level Overview**:

1. **Content Services** (Business Logic)
   - `CreateContentService`
   - `UpdateContentService`
   - `DeleteContentService`
   - `GetContentService`
   - `PublishContentService`

2. **Content Controllers** (API & Admin)
   - `Admin/ContentController` (Blade admin)
   - `Api/V1/ContentController` (REST API)

3. **Form Requests** (Validation)
   - `StoreContentRequest`
   - `UpdateContentRequest`

4. **Policies** (Authorization)
   - `ContentPolicy`

5. **Migrations** (if needed)
   - Any additional fields or indexes

6. **Tests** (Feature Tests)
   - Content CRUD tests
   - Authorization tests

---

## 🔄 Workflow & Best Practices

### Daily Workflow

#### Morning Routine

1. **Pull latest changes**
   ```bash
   git pull origin develop
   ```

2. **Check for updates**
   - Read sprint notes
   - Check for new issues/comments

3. **Plan your day**
   - Which tasks will you work on?
   - Any blockers?

#### During Development

1. **Create feature branch**
   ```bash
   git checkout develop
   git pull origin develop
   git checkout -b feature/sprint-1-content-crud
   ```

2. **Work on task**
   - Follow conventions
   - Write clean code
   - Test manually

3. **Commit frequently**
   ```bash
   git add .
   git commit -m "feat(content): add CreateContentService"
   ```
   - Use [Conventional Commits](https://www.conventionalcommits.org/)
   - One logical change per commit

4. **Run Pint before commit**
   ```bash
   php vendor/bin/pint
   ```

5. **Push regularly**
   ```bash
   git push origin feature/sprint-1-content-crud
   ```

#### End of Day

1. **Update sprint notes**
   - What did you complete?
   - Any issues?
   - What's next?

2. **Push your work**
   ```bash
   git push origin feature/sprint-1-content-crud
   ```

### Code Review Process

1. **Before requesting review**:
   - ✅ Code follows conventions
   - ✅ Pint has been run
   - ✅ Manual testing done
   - ✅ CHANGELOG.md updated
   - ✅ Sprint notes updated

2. **Create Pull Request**:
   - Use PR template (`project-docs/pr-template.md`)
   - Describe what you did
   - Link to related issues/tasks

3. **Address review comments**:
   - Be open to feedback
   - Ask questions if unclear
   - Make requested changes

### Testing Workflow

1. **Manual Testing**:
   - Test happy path
   - Test error cases
   - Test edge cases
   - Test authorization

2. **Automated Testing** (when required):
   - Write feature tests
   - Run `php artisan test`
   - Ensure all tests pass

---

## ⚠️ Common Pitfalls (Avoid These!)

### 1. **Not Reading Documentation**

❌ **Wrong**: Start coding immediately  
✅ **Right**: Read conventions.md and architecture.md first

### 2. **Ignoring Conventions**

❌ **Wrong**: Create `app/Services/ContentService.php`  
✅ **Right**: Create `app/Domain/Content/Services/CreateContentService.php`

### 3. **Not Using Service Layer**

❌ **Wrong**: Put business logic in Controller  
✅ **Right**: Put business logic in Service, Controller calls Service

### 4. **Missing Type Hints**

❌ **Wrong**: `public function execute($data)`  
✅ **Right**: `public function execute(array $data): Content`

### 5. **Not Handling Errors**

❌ **Wrong**: `$content = Content::find($id); $content->delete();`  
✅ **Right**: Check if exists, handle exceptions, use transactions

### 6. **N+1 Query Problems**

❌ **Wrong**: `foreach ($contents as $content) { echo $content->business->name; }`  
✅ **Right**: `Content::with('business')->get()`

### 7. **Not Validating Input**

❌ **Wrong**: Accept any input in Controller  
✅ **Right**: Use Form Requests for validation

### 8. **Hardcoding Values**

❌ **Wrong**: `$tax = 0.24;`  
✅ **Right**: `config('app.tax_rate')` or database setting

### 9. **Not Updating Documentation**

❌ **Wrong**: Complete task, don't update CHANGELOG  
✅ **Right**: Update CHANGELOG.md and sprint notes

### 10. **Committing Without Testing**

❌ **Wrong**: Write code, commit immediately  
✅ **Right**: Test manually, run Pint, then commit

---

## 📖 Resources & Documentation

### Must-Read Documents

1. **`project-docs/conventions.md`** — Coding conventions ⭐
2. **`project-docs/architecture.md`** — Architecture overview
3. **`project-docs/v2/v2_overview.md`** — v2 strategy
4. **`project-docs/v2/dev-responsibilities.md`** — Best practices
5. **`project-docs/v2/sprints/sprint_1.md`** — Your tasks
6. **`project-docs/v2/v2_content_model.md`** — Content model
7. **`project-docs/git-workflow.md`** — Git workflow
8. **`project-docs/dev-commands.md`** — Common commands

### Reference Files

- **`CHANGELOG.md`** — Project history
- **`README.md`** — Project overview
- **`routes/web.php`** — Web routes
- **`routes/api.php`** — API routes

### External Resources

- [Laravel Documentation](https://laravel.com/docs)
- [Filament Documentation](https://filamentphp.com/docs)
- [PHP The Right Way](https://phptherightway.com/)

---

## 🎯 Sprint 1 — Your Focus Areas

### Content Module Implementation

**What you'll build**:
- Content CRUD operations
- Block-based content storage
- Content types management
- Publishing workflow
- Versioning (revisions)

**Key Concepts**:
- **Content** = JSON blocks stored in `body_json`
- **Content Types** = Templates for different content (page, article, block)
- **Blocks** = Reusable content components (text, hero, gallery)
- **Revisions** = Version history

**Architecture**:
- **Service Layer**: All business logic in Services
- **Controllers**: Thin controllers, call Services
- **Form Requests**: Validation
- **Policies**: Authorization

---

## ✅ Pre-Flight Checklist

Before you start coding, make sure:

- [ ] Read `conventions.md` completely
- [ ] Read `architecture.md`
- [ ] Read `sprint_1.md` (your tasks)
- [ ] Environment setup complete
- [ ] Can access `/admin` panel
- [ ] Understand Service Layer Pattern
- [ ] Understand Domain structure
- [ ] Know how to create Services
- [ ] Know how to create Controllers
- [ ] Know how to create Form Requests
- [ ] Know how to create Policies
- [ ] Know how to run migrations
- [ ] Know how to run tests
- [ ] Know Git workflow

---

## 🆘 Getting Help

### When to Ask for Help

- ✅ **Unclear requirements** — Ask Master DEV
- ✅ **Architecture questions** — Check docs first, then ask
- ✅ **Blocked by bug** — Try to debug, then ask
- ✅ **Uncertain about approach** — Propose solution, ask for feedback

### How to Ask

1. **Be specific**: "I'm trying to X, but Y happens"
2. **Show code**: Include relevant code snippets
3. **Show error**: Include full error message
4. **What you tried**: Mention what you already tried

### Communication Channels

- **Sprint Notes**: Update `sprint_1.md` with questions
- **PR Comments**: Ask in Pull Request
- **Direct Message**: For urgent issues

---

## 🎓 Learning Path

### Week 1: Foundation

- Day 1-2: Read all documentation
- Day 3: Setup environment, explore codebase
- Day 4-5: Start with simple tasks (Services)

### Week 2: Implementation

- Day 1-3: Content Services implementation
- Day 4-5: Controllers & API

### Week 3: Polish

- Day 1-2: Testing
- Day 3-4: Bug fixes
- Day 5: Documentation & cleanup

---

## 📝 Code Examples

### Service Example

```php
<?php

declare(strict_types=1);

namespace App\Domain\Content\Services;

use App\Domain\Content\Models\Content;
use Illuminate\Support\Facades\DB;

class CreateContentService
{
    public function execute(array $data): Content
    {
        return DB::transaction(function () use ($data) {
            $content = Content::create([
                'business_id' => $data['business_id'],
                'type' => $data['type'],
                'slug' => $data['slug'],
                'title' => $data['title'],
                'body_json' => $data['body_json'] ?? [],
                'meta' => $data['meta'] ?? [],
                'status' => $data['status'] ?? 'draft',
                'created_by' => auth()->id(),
            ]);

            // Create initial revision
            $content->revisions()->create([
                'body_json' => $content->body_json,
                'meta' => $content->meta,
                'user_id' => auth()->id(),
            ]);

            return $content;
        });
    }
}
```

### Controller Example

```php
<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Domain\Content\Services\CreateContentService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Content\StoreContentRequest;
use Illuminate\Http\RedirectResponse;

class ContentController extends Controller
{
    public function __construct(
        private CreateContentService $createContentService
    ) {}

    public function store(StoreContentRequest $request): RedirectResponse
    {
        $content = $this->createContentService->execute($request->validated());

        return redirect()
            ->route('admin.content.show', $content)
            ->with('success', 'Content created successfully!');
    }
}
```

---

## 🎯 Success Criteria

You'll know you're doing well when:

- ✅ Code follows all conventions
- ✅ Services are well-structured
- ✅ Controllers are thin
- ✅ Tests pass
- ✅ No linter errors
- ✅ Documentation updated
- ✅ Code reviewed and approved

---

## 🚀 Ready to Start?

1. **Read all documentation** (2-3 hours)
2. **Setup environment** (30 minutes)
3. **Explore codebase** (1 hour)
4. **Start with first task** (read `sprint_1.md`)

**Good luck! 🎉**

---

**Questions?** Update `sprint_1.md` notes section or ask Master DEV.

**Last Updated**: 2024-11-27

