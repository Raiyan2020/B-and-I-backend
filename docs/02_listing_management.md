# Feature 02 — Project Listing Management (Advertiser)

## Business Context

Advertisers post business opportunities on the platform.
A listing is never immediately public — it must pass admin review first.
There are two listing types that drive different field requirements:
- **investment** — advertiser seeks funding in exchange for equity share
- **sale** — advertiser wants to sell the business entirely

The platform exposes two information layers per listing:
- **Public layer** — visible to all logged-in users (name, sector, price, short description)
- **Booklet layer** — visible only after purchase (financials, full details, contact context)

---

## Database

### Table: `projects`

| column               | type                                                                                              | nullable | notes                                        |
|----------------------|---------------------------------------------------------------------------------------------------|----------|----------------------------------------------|
| id                   | bigIncrements PK                                                                                  | NO       |                                              |
| owner_id             | foreignId → users.id                                                                              | NO       | must be advertiser role                      |
| listing_purpose      | enum(investment, sale)                                                                            | NO       |                                              |
| status               | enum(draft, pending, under_review, needs_revision, approved, published, rejected, suspended)     | NO       | default: pending                             |
| name_ar              | string                                                                                            | NO       |                                              |
| name_en              | string                                                                                            | NO       |                                              |
| category_ar          | string                                                                                            | NO       |                                              |
| category_en          | string                                                                                            | NO       |                                              |
| image_url            | string                                                                                            | YES      |                                              |
| location_ar          | string                                                                                            | NO       |                                              |
| location_en          | string                                                                                            | NO       |                                              |
| age_ar               | string                                                                                            | NO       | e.g. "3 سنوات"                              |
| age_en               | string                                                                                            | NO       | e.g. "3 Years"                              |
| legal_entity_ar      | string                                                                                            | YES      |                                              |
| legal_entity_en      | string                                                                                            | YES      |                                              |
| company_type_ar      | string                                                                                            | YES      |                                              |
| company_type_en      | string                                                                                            | YES      |                                              |
| company_stage_ar     | string                                                                                            | YES      |                                              |
| company_stage_en     | string                                                                                            | YES      |                                              |
| asking_price         | decimal(12,3)                                                                                     | NO       | KWD — investment amount or sale price        |
| capital              | decimal(12,3)                                                                                     | YES      |                                              |
| share_offered        | decimal(5,2)                                                                                      | YES      | % — required if purpose = investment         |
| description_short_ar | text                                                                                              | NO       | public layer                                 |
| description_short_en | text                                                                                              | NO       | public layer                                 |
| description_full_ar  | text                                                                                              | YES      | booklet layer (paid)                         |
| description_full_en  | text                                                                                              | YES      | booklet layer (paid)                         |
| investment_reason_ar | text                                                                                              | YES      | booklet layer                                |
| investment_reason_en | text                                                                                              | YES      | booklet layer                                |
| financial_health     | enum(Very Strong, Strong, Stable, Moderate, Needs Improvement, Weak, Critical, Not Disclosed)    | YES      | booklet layer                                |
| views_count          | unsignedInteger default 0                                                                         | NO       |                                              |
| booklet_purchases_count | unsignedInteger default 0                                                                      | NO       |                                              |
| interests_count      | unsignedInteger default 0                                                                         | NO       |                                              |
| admin_notes          | text                                                                                              | YES      | internal admin review notes                  |
| rejection_reason     | text                                                                                              | YES      | shown to advertiser on rejection             |
| published_at         | timestamp                                                                                         | YES      |                                              |
| created_at           | timestamp                                                                                         | NO       |                                              |
| updated_at           | timestamp                                                                                         | NO       |                                              |

---

## Status Flow

```
[advertiser submits]
       ↓
    pending
       ↓
  under_review   ← admin opens it
       ↓
  ┌────┴────┐
approved   needs_revision
  ↓              ↓
published   [advertiser edits & resubmits → pending again]
  ↓
rejected / suspended   ← admin can do this at any stage
```

**Rules:**
- Only admin can move status forward or reject
- Advertiser can only edit a listing in `needs_revision` or `draft` state
- Once `published`, edits require re-review (status goes back to `pending`)
- `suspended` means hidden from public, advertiser notified

---

## API Endpoints

All routes prefixed: `/api/v1`

| Method | Route                    | Auth Required  | Role         | Description                    |
|--------|--------------------------|----------------|--------------|--------------------------------|
| POST   | /listings                | Yes            | advertiser   | Create new listing             |
| GET    | /listings                | No             | public       | List published listings        |
| GET    | /listings/{id}           | No             | public       | Get listing public layer       |
| PUT    | /listings/{id}           | Yes            | advertiser   | Edit own listing               |
| DELETE | /listings/{id}           | Yes            | advertiser   | Delete own listing (if pending)|
| GET    | /dashboard/listings      | Yes            | advertiser   | My listings with stats         |

---

## Business Logic per Endpoint

### POST /listings

**Request (multipart/form-data):**
```
listing_purpose        enum     required | in:investment,sale
name_ar                string   required
name_en                string   required
category               string   required
location_ar            string   required
location_en            string   required
age_ar                 string   required
age_en                 string   required
asking_price           numeric  required | min:1
share_offered          numeric  required_if:listing_purpose,investment | between:1,100
legal_entity_ar        string   sometimes
company_type_ar        string   sometimes
company_stage_ar       string   sometimes
description_short_ar   string   required | max:300
description_short_en   string   required | max:300
description_full_ar    string   sometimes
investment_reason_ar   string   required
financial_health       enum     sometimes
image                  file     sometimes | mimes:jpg,jpeg,png | max:5120
```

**Logic:**
1. Verify authenticated user has `role = advertiser`
2. Validate fields — `share_offered` required only when `listing_purpose = investment`
3. If image uploaded: store at `storage/app/public/listings/{uuid}.jpg`
4. Insert project row with `status = pending`, `owner_id = auth()->id()`
5. Notify admin of new pending listing (dispatch `NewListingSubmitted` notification/event)
6. Return 201 with listing public data

---

### GET /listings

**Query parameters:**
```
category    string    optional — filter by category_en
purpose     string    optional — in:investment,sale
sort        string    optional — in:newest,price_asc,price_desc
page        integer   optional — pagination
per_page    integer   optional — default 15, max 50
```

**Logic:**
1. Scope: `status = published` only
2. Apply filters and sort
3. Return paginated collection via `ListingPublicResource` (public layer only — no booklet fields)
4. Increment `views_count` is handled separately via a dedicated lightweight endpoint
   (not here, to avoid inflating on list scroll)

---

### GET /listings/{id}

**Logic:**
1. Find project where `status = published`
2. Increment `views_count` atomically: `project->increment('views_count')`
3. If requester is authenticated AND has purchased booklet:
   - Return `ListingFullResource` (includes booklet layer)
4. Otherwise:
   - Return `ListingPublicResource` (excludes booklet fields, shows `is_locked: true`)

---

### PUT /listings/{id}

**Logic:**
1. Verify ownership: `project->owner_id === auth()->id()`
2. Verify editable status: only `needs_revision` or `draft` allowed
3. Apply changes
4. Reset status back to `pending` (triggers re-review)
5. Notify admin of resubmission

---

### DELETE /listings/{id}

**Logic:**
1. Verify ownership
2. Only deletable if status is `pending` or `draft`
3. Soft delete or hard delete based on project decision
   - Recommended: soft delete (`deleted_at`) to preserve purchase history

---

### GET /dashboard/listings

**Logic:**
- Return all listings owned by authenticated advertiser
- Include performance stats per listing: `views_count`, `booklet_purchases_count`, `interests_count`
- Include current `status` with human-readable label
- No pagination limit on dashboard (user owns all their own listings)

---

## Architecture Pattern

```
Request → FormRequest → DTO → ListingService → ListingResource → ResponseTrait
```

### DTOs

```
app/DTO/Listing/CreateListingDTO.php
app/DTO/Listing/UpdateListingDTO.php
```

### Service Interface

```php
interface ListingServiceInterface
{
    public function create(CreateListingDTO $dto, User $owner): Project;
    public function update(UpdateListingDTO $dto, Project $project): Project;
    public function delete(Project $project): void;
    public function getPublicListings(array $filters): LengthAwarePaginator;
    public function getForDashboard(User $advertiser): Collection;
}
```

### Resources

| Resource                  | Used for                                        |
|---------------------------|-------------------------------------------------|
| `ListingPublicResource`   | Public listing (no booklet fields, is_locked: true) |
| `ListingFullResource`     | After booklet purchase (all fields)             |
| `ListingDashboardResource`| Advertiser dashboard (includes stats + status)  |

---

## File Tree

```
app/
├── DTO/Listing/
│   ├── CreateListingDTO.php
│   └── UpdateListingDTO.php
├── Services/Listing/
│   ├── ListingServiceInterface.php
│   └── ListingService.php
├── Http/
│   ├── Controllers/Api/V1/
│   │   └── ListingController.php
│   ├── Requests/
│   │   ├── CreateListingRequest.php
│   │   └── UpdateListingRequest.php
│   └── Resources/
│       ├── ListingPublicResource.php
│       ├── ListingFullResource.php
│       └── ListingDashboardResource.php
├── Models/
│   └── Project.php
└── Notifications/
    └── NewListingSubmittedNotification.php
```
