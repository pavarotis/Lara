# Sprint 5 — API & Headless Support — REVISED

**Status**: ⏳ Pending  
**Start Date**: _TBD_  
**End Date**: _TBD_  
**Διάρκεια**: 1 εβδομάδα

---

## 📋 Sprint Goal

REST API for headless consumption. Complete API endpoints για businesses, content, catalog, orders, και media με authentication, rate limiting, και documentation.

---

## 🎯 High-Level Objectives

- API Authentication (Sanctum tokens)
- API Controllers (Businesses, Content, Catalog, Orders, Media)
- API Resources (consistent JSON format)
- API Documentation
- Rate limiting
- Error handling enhancement

⚠️ **Σημείωση**: Το API foundation έχει ήδη δημιουργηθεί στο Sprint 0. Στο Sprint 5 κάνουμε:
- Complete API endpoints
- Authentication & tokens
- Full documentation
- Production-ready features

---

## 👥 Tasks by Developer

### Dev A — API Implementation

#### Task A1 — API Authentication (Sanctum)

**Περιγραφή**: Token-based authentication για API.

**Deliverables:**
- Sanctum setup (already in Sprint 0, enhance here):
  - Token generation
  - Token revocation
  - Token expiration (optional)
- API token management UI (admin panel):
  - List user tokens
  - Create new token
  - Revoke token
  - Token permissions (scopes, optional)
- Middleware:
  - `auth:sanctum` for API routes
  - Token validation

**Acceptance Criteria:**
- Tokens working
- Token management UI functional
- API routes protected

---

#### Task A2 — API Controllers

**Περιγραφή**: Complete API endpoints για όλα τα modules.

**Deliverables:**
- `Api/BusinessController`:
  - `index()` — GET list businesses
  - `show($id)` — GET single business
  - `settings($id)` — GET business settings
- `Api/ContentController`:
  - `index($businessId)` — GET list content (with filters)
  - `show($businessId, $slug)` — GET single content by slug
  - `byType($businessId, $type)` — GET content by type
- `Api/CatalogController`:
  - `products($businessId)` — GET products (with filters)
  - `product($businessId, $id)` — GET single product
  - `categories($businessId)` — GET categories
- `Api/OrderController`:
  - `store($businessId)` — POST create order
  - `show($orderNumber)` — GET order by number
- `Api/MediaController` (already in Sprint 2, enhance):
  - Complete endpoints
  - Filter by folder, type

**Acceptance Criteria:**
- All endpoints working
- Consistent response format
- Filters working

---

#### Task A3 — API Resources

**Περιγραφή**: Consistent JSON response format.

**Deliverables:**
- `BusinessResource`:
  - id, name, type, settings, theme
- `ContentResource`:
  - id, type, slug, title, body (blocks), meta, published_at
- `ProductResource`:
  - id, name, slug, description, price, image, category, is_available
- `OrderResource`:
  - id, order_number, status, items, totals, customer, created_at
- `MediaResource`:
  - id, name, url, thumbnail, type, size

**Acceptance Criteria:**
- All resources return consistent format
- Nested relationships included
- No sensitive data exposed

---

#### Task A4 — Rate Limiting

**Deliverables:**
- Rate limiting per endpoint:
  - Public endpoints: 60 requests/minute
  - Authenticated endpoints: 120 requests/minute
  - Admin endpoints: 200 requests/minute
- Rate limit headers in responses:
  - `X-RateLimit-Limit`
  - `X-RateLimit-Remaining`
  - `X-RateLimit-Reset`
- Rate limit exceeded response (429)

**Acceptance Criteria:**
- Rate limiting working
- Headers included
- 429 response correct

---

### Dev B — API Services & Validation

#### Task B1 — API Services Enhancement

**Περιγραφή**: Refactor existing services να support API responses.

**Deliverables:**
- Update services να return API-friendly data
- API-specific validation
- Error handling for API
- No breaking changes για web routes

**Acceptance Criteria:**
- Services work for both web & API
- API responses consistent

---

#### Task B2 — API Validation

**Deliverables:**
- API-specific form requests:
  - `Api/StoreOrderRequest`
  - `Api/UploadMediaRequest`
- Validation rules:
  - Different messages for API (JSON)
  - Consistent error format

**Acceptance Criteria:**
- Validation working
- Error format consistent

---

### Dev C — API Documentation

#### Task C1 — API Documentation

**Περιγραφή**: Complete API documentation.

**Deliverables:**
- Update `project-docs/v2/v2_api_spec.md`:
  - All endpoints documented
  - Request/response examples
  - Authentication guide
  - Error codes
  - Rate limiting info
- Optional: OpenAPI/Swagger spec (if time)

**Acceptance Criteria:**
- Documentation complete
- Examples working
- Easy to follow

---

#### Task C2 — API Testing

**Deliverables:**
- Postman collection (optional):
  - All endpoints
  - Authentication flow
  - Example requests
- API tests (PHPUnit):
  - Test all endpoints
  - Test authentication
  - Test rate limiting

**Acceptance Criteria:**
- Tests passing
- Postman collection working (if created)

---

## ✅ Deliverables (End of Sprint 5)

- [ ] API Authentication (Sanctum) working
- [ ] All API endpoints implemented
- [ ] API Resources (consistent format)
- [ ] Rate limiting working
- [ ] API Documentation complete
- [ ] API tests passing
- [ ] Token management UI

---

## ❌ Δεν θα υπάρχουν ακόμα

- GraphQL API (future)
- Webhooks (future)
- API versioning (v2, v3) — v1 only for now
- OAuth2 (Sanctum tokens only)

**Αυτά μπορούν να έρθουν σε future sprints.**

---

## 📝 Sprint Notes

_Εδώ μπορείς να γράφεις ελεύθερο κείμενο για το sprint:_
- Progress updates
- Issues encountered
- Decisions made
- Questions for team
- Any other notes

---

## 🐛 Issues & Blockers

_Καταγράψε εδώ οποιαδήποτε issues ή blockers_

---

## 📚 References

- [v2 Overview](../v2_overview.md) — Architecture & strategy
- [API Specification](../v2_api_spec.md)
- [**Developer Responsibilities**](../dev-responsibilities.md) ⭐ **Read this for quality checks & best practices**

---

**Last Updated**: _TBD_

