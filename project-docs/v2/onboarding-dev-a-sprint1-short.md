# 👋 Welcome Dev A — Sprint 1 Onboarding

**Καλώς ήρθες στο LaraShop v2!** 🎉

Αυτός είναι ο **quick start guide** για να ξεκινήσεις. Για πλήρη details, διάβασε το `onboarding-dev-a-sprint1.md`.

---

## 🎯 Ποιος είσαι

**Dev A** = Backend/Infrastructure Developer

**Ευθύνη σου**: Backend development (Services, Controllers, Models, API, Database)

---

## ⚡ Quick Start (30 minutes)

### 1. Read Documentation (MUST DO — 2-3 hours)

**ΠΡΙΝ** ξεκινήσεις coding, διάβασε:

1. ✅ **`project-docs/conventions.md`** — Coding conventions ⭐ **MUST READ**
2. ✅ **`project-docs/architecture.md`** — Project architecture
3. ✅ **`project-docs/v2/v2_overview.md`** — v2 overview
4. ✅ **`project-docs/v2/dev-responsibilities.md`** — Best practices
5. ✅ **`project-docs/v2/sprints/sprint_1.md`** — Your tasks
6. ✅ **`project-docs/v2/v2_content_model.md`** — Content model

**⏱️ Time**: ~2-3 hours (worth it!)

### 2. Setup Environment

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan storage:link
npm run build
```

### 3. Verify Setup

```bash
php artisan --version
php artisan route:list
php artisan migrate:status
```

---

## 💼 Πώς να είσαι σαν προγραμματιστής

### Core Principles

1. **Code Quality First**
   - Clean, readable code
   - Self-documenting names
   - Type hints & return types everywhere
   - Follow SOLID principles

2. **Follow Conventions**
   - Read `conventions.md` — **MUST**
   - Service Layer Pattern (business logic in Services)
   - Domain-driven structure
   - Consistent naming

3. **Think Before Coding**
   - Understand requirements first
   - Ask questions if unclear
   - Plan the solution
   - Consider edge cases

4. **Test Your Code**
   - Test manually before committing
   - Write tests for new features
   - Test edge cases & error handling

5. **Documentation**
   - PHPDoc for complex methods
   - Update CHANGELOG.md after tasks
   - Update sprint notes with progress

---

## 📝 Sprint 1 — Your Tasks

**Read**: `project-docs/v2/sprints/sprint_1.md` for detailed tasks

**Your Focus** (Dev A — Backend):

1. **Content Services** (Business Logic)
   - `CreateContentService`, `UpdateContentService`, `DeleteContentService`
   - `GetContentService`, `PublishContentService`

2. **Content Controllers** (API & Admin)
   - `Admin/ContentController` (Blade admin)
   - `Api/V1/ContentController` (REST API)

3. **Form Requests** (Validation)
   - `StoreContentRequest`, `UpdateContentRequest`

4. **Policies** (Authorization)
   - `ContentPolicy`

5. **Tests** (Feature Tests)
   - Content CRUD tests

---

## 🔄 Daily Workflow

### Morning
1. `git pull origin develop`
2. Check sprint notes for updates
3. Plan your day

### During Development
1. Create feature branch: `git checkout -b feature/sprint-1-content-crud`
2. Work on task (follow conventions)
3. Test manually
4. Run Pint: `php vendor/bin/pint`
5. Commit: `git commit -m "feat(content): add CreateContentService"`
6. Push regularly

### End of Day
1. Update sprint notes
2. Push your work

---

## ⚠️ Common Mistakes (Avoid!)

1. ❌ **Not reading conventions.md** → ✅ Read it first!
2. ❌ **Business logic in Controller** → ✅ Use Services
3. ❌ **Missing type hints** → ✅ Always use type hints
4. ❌ **Not handling errors** → ✅ Check nulls, use transactions
5. ❌ **N+1 queries** → ✅ Use `with()` for relationships
6. ❌ **Not validating input** → ✅ Use Form Requests
7. ❌ **Hardcoding values** → ✅ Use config/database
8. ❌ **Not updating CHANGELOG** → ✅ Always update it

---

## 📚 Key Files to Read

1. **`project-docs/conventions.md`** — Coding conventions ⭐
2. **`project-docs/architecture.md`** — Architecture
3. **`project-docs/v2/sprints/sprint_1.md`** — Your tasks
4. **`project-docs/v2/dev-responsibilities.md`** — Best practices
5. **`CHANGELOG.md`** — Project history

---

## ✅ Pre-Flight Checklist

Before coding:

- [ ] Read `conventions.md` completely
- [ ] Read `architecture.md`
- [ ] Read `sprint_1.md` (your tasks)
- [ ] Environment setup complete
- [ ] Can access `/admin` panel
- [ ] Understand Service Layer Pattern
- [ ] Understand Domain structure
- [ ] Know Git workflow

---

## 🆘 Getting Help

**When to ask**:
- Unclear requirements
- Architecture questions
- Blocked by bug (after trying to debug)
- Uncertain about approach

**How to ask**:
- Be specific: "I'm trying to X, but Y happens"
- Show code & error messages
- Mention what you already tried

---

## 🎯 Success Criteria

You're doing well when:
- ✅ Code follows all conventions
- ✅ Services are well-structured
- ✅ Controllers are thin
- ✅ Tests pass
- ✅ No linter errors
- ✅ Documentation updated

---

## 🚀 Ready?

1. **Read documentation** (2-3 hours)
2. **Setup environment** (30 minutes)
3. **Explore codebase** (1 hour)
4. **Start with first task** (read `sprint_1.md`)

**Good luck! 🎉**

---

**Full Guide**: `project-docs/v2/onboarding-dev-a-sprint1.md`  
**Questions?** Update `sprint_1.md` notes or ask Master DEV.

