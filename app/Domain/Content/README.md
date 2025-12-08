# Content Domain

## Overview

Block-based content management system. Content entries consist of JSON blocks stored in `body_json` field.

## Structure

```
Content/
├── Models/
│   ├── Content.php          # Main content model
│   ├── ContentType.php      # Content type definitions
│   └── ContentRevision.php  # Version history
├── Services/                # Business logic (Sprint 1)
└── Policies/                # Authorization (Sprint 1)
```

## Database Schema

- `contents` — Main content entries
- `content_types` — Content type definitions
- `content_revisions` — Version history

## Status

**Sprint 0**: Skeleton only (models, migrations)  
**Sprint 1**: Full implementation (services, controllers, editor)

## Related Documentation

- [v2 Content Model](../v2/v2_content_model.md)
- [v2 Overview](../v2/v2_overview.md)

