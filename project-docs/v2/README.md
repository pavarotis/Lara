# 📚 v2 Documentation

Documentation για το v2 migration και τα νέα features.

---

## 📄 Αρχεία

### [dev-responsibilities.md](./dev-responsibilities.md) ⭐ **READ THIS FIRST**
Comprehensive guide για τι πρέπει να κάνει **κάθε dev σε κάθε sprint** πέρα από τα tasks:
- Code quality checklist
- Testing requirements
- Documentation updates
- Communication guidelines
- Error prevention tips
- Pre-commit & pre-push checklists
- Dev-specific responsibilities
- Common mistakes to avoid

### [v2_migration_guide.md](./v2_migration_guide.md)
Step-by-step guide για τη migration από v1 → v2. Περιλαμβάνει:
- Pre-migration checklist
- Phase-by-phase migration steps
- Cleanup tasks
- Activation checklist
- Rollback plan

### [v2_api_spec.md](./v2_api_spec.md)
REST API specification για headless consumption:
- Authentication (Sanctum)
- Business endpoints
- Content endpoints
- Media endpoints
- Catalog endpoints
- Order endpoints
- Error handling
- Rate limiting

### [v2_plugin_guide.md](./v2_plugin_guide.md)
Plugin development guide:
- Plugin structure
- Service provider setup
- Custom blocks
- Plugin hooks
- Installation
- Testing
- Best practices

### [v2_content_model.md](./v2_content_model.md)
Content model specification:
- Database schema
- Block structure
- Built-in block types
- Content lifecycle
- Querying
- Rendering
- Permissions

---

## 🎯 Αρχές v2

1. **Clean Migration** — Replace v1, don't duplicate
2. **No Legacy Code** — Delete deprecated files after migration
3. **Feature Flags** — Smooth transition with rollback capability
4. **Modular** — Clean module boundaries
5. **Extensible** — Plugin system for customizations

---

## 📋 Quick Links

- [v2 Overview](./v2_overview.md) — Architecture, strategy & technical specs
- [Architecture Documentation](../architecture.md)
- [Database Schema](../database-schema.md)

---

## 🏃 Sprints

Individual sprint files για detailed notes και progress tracking:

- [Sprint 0 — Infrastructure & Foundation](./sprints/sprint_0.md)
- [Sprint 1 — Content Module](./sprints/sprint_1.md)
- [Sprint 2 — Media Library](./sprints/sprint_2.md)
- [Sprint 3 — Content Rendering & Theming](./sprints/sprint_3.md)
- [Sprint 4 — RBAC & Permissions](./sprints/sprint_4.md)
- [Sprint 5 — API & Headless Support](./sprints/sprint_5.md)
- [Sprint 6 — Plugins & Polish](./sprints/sprint_6.md)

**💡 Tip**: Κάθε sprint file έχει section "📝 Sprint Notes" όπου μπορείς να γράφεις ελεύθερο κείμενο για progress, issues, decisions, κλπ.

---

**Last Updated**: 2024-11-27

