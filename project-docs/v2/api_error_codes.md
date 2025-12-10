# API Error Codes Documentation

## Overview

All API endpoints follow a standardized error response format:

```json
{
  "success": false,
  "message": "Error description",
  "errors": {},
  "data": null
}
```

## HTTP Status Codes

### 400 - Bad Request
**When**: Invalid request parameters or malformed request

**Example**:
```json
{
  "success": false,
  "message": "Invalid request",
  "errors": {},
  "data": null
}
```

---

### 401 - Unauthenticated
**When**: Authentication required but not provided or invalid

**Example**:
```json
{
  "success": false,
  "message": "Unauthenticated",
  "errors": [],
  "data": null
}
```

**Solution**: Include valid authentication token in request headers

---

### 403 - Forbidden
**When**: User is authenticated but lacks required permissions

**Example**:
```json
{
  "success": false,
  "message": "Forbidden",
  "errors": [],
  "data": null
}
```

**Solution**: User needs appropriate role/permission

---

### 404 - Not Found
**When**: Requested resource does not exist

**Example**:
```json
{
  "success": false,
  "message": "Resource not found",
  "errors": [],
  "data": null
}
```

**Solution**: Verify resource ID/slug exists

---

### 405 - Method Not Allowed
**When**: HTTP method not allowed for this endpoint

**Example**:
```json
{
  "success": false,
  "message": "Method not allowed",
  "errors": [],
  "data": null
}
```

**Solution**: Use correct HTTP method (GET, POST, PUT, DELETE, etc.)

---

### 422 - Validation Error
**When**: Request validation failed

**Example**:
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "title": ["The title field is required."],
    "slug": ["The slug has already been taken."]
  },
  "data": null
}
```

**Solution**: Fix validation errors in request data

---

### 429 - Too Many Requests
**When**: Rate limit exceeded

**Example**:
```json
{
  "success": false,
  "message": "Too many requests. Please try again later.",
  "errors": [],
  "data": null
}
```

**Solution**: Wait before making additional requests (check `Retry-After` header if provided)

---

### 500 - Internal Server Error
**When**: Server-side error occurred

**Example** (Production):
```json
{
  "success": false,
  "message": "An error occurred",
  "errors": [],
  "data": null
}
```

**Example** (Debug Mode):
```json
{
  "success": false,
  "message": "SQLSTATE[42S22]: Column not found: 1054 Unknown column...",
  "errors": {
    "exception": "Illuminate\\Database\\QueryException"
  },
  "data": null
}
```

**Solution**: Contact support if issue persists

---

## Success Response Format

All successful API responses follow this format:

```json
{
  "success": true,
  "data": {},
  "message": "Operation successful"
}
```

For paginated responses:

```json
{
  "success": true,
  "data": [],
  "message": "Data retrieved successfully",
  "meta": {
    "current_page": 1,
    "per_page": 15,
    "total": 100,
    "last_page": 7
  },
  "links": {
    "first": "http://example.com/api/v1/endpoint?page=1",
    "last": "http://example.com/api/v1/endpoint?page=7",
    "prev": null,
    "next": "http://example.com/api/v1/endpoint?page=2"
  }
}
```

---

## Rate Limiting

API endpoints are rate-limited to prevent abuse. Default limits:
- **Public endpoints**: 60 requests per minute per IP
- **Authenticated endpoints**: 120 requests per minute per user

Rate limit headers are included in responses:
- `X-RateLimit-Limit`: Maximum number of requests allowed
- `X-RateLimit-Remaining`: Number of requests remaining
- `Retry-After`: Seconds to wait before retrying (when limit exceeded)

---

## Implementation

Error handling is implemented in `bootstrap/app.php` using Laravel's exception handling system. All API errors automatically follow the standardized format.

**Last Updated**: 2024-11-27

