# Media Domain

## Overview

Media library with folder structure and file management. Supports image variants, file organization, and metadata.

## Structure

```
Media/
├── Models/
│   ├── Media.php           # Media file model
│   └── MediaFolder.php     # Folder structure
├── Services/               # Business logic (Sprint 2)
└── Policies/               # Authorization (Sprint 2)
```

## Database Schema

- `media` — Media files
- `media_folders` — Folder hierarchy

## Status

**Sprint 0**: Skeleton only (models, migrations)  
**Sprint 2**: Full implementation (upload, variants, folder management)

## Related Documentation

- [v2 Overview](../v2/v2_overview.md)
- [Sprint 2 Details](../v2/sprints/sprint_2.md)

