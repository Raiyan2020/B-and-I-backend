# Feature 05 — Admin Panel

## Business Context

The admin is the operational heart of the platform.
The admin panel is NOT a public-facing feature — it is an internal tool
accessed only by platform staff.

Admin responsibilities:
1. **Account verification** — review advertiser license documents
2. **Listing review** — approve/reject/request revision on new listings
3. **Interest coordination** — manually connect investors with advertisers
4. **Commission tracking** — record and close deals at 2.5% commission
5. **Content moderation** — suspend listings or accounts

The admin never exposes investor or advertiser data to the opposing party.
All deal coordination happens off-platform (phone/email/in-person) but is
logged and tracked through the admin panel.

---

## Database Additions

### Table: `admin_actions` (audit log)

| column        | type                                                          | nullable | notes                              |
|---------------|---------------------------------------------------------------|----------|------------------------------------|
| id            | bigIncrements PK                                             | NO       |                                    |
| admin_id      | foreignId → users.id                                         | NO       | must be admin role                 |
| action_type   | enum(approve_listing, reject_listing, request_revision, verify_account, suspend_account, update_interest_status, record_deal) | NO |  |
| target_type   | string (morphs)                                              | NO       | Project, User, Interest            |
| target_id     | unsignedBigInteger                                           | NO       |                                    |
| notes         | text                                                         | YES      | internal admin notes               |
| created_at    | timestamp                                                     | NO       |                                    |

### Table: `deals` (commission tracking)

| column           | type                    | nullable | notes                               |
|------------------|-------------------------|----------|-------------------------------------|
| id               | bigIncrements PK        | NO       |                                     |
| interest_id      | foreignId → interests.id | NO      |                                     |
| investor_id      | foreignId → users.id    | NO       |                                     |
| advertiser_id    | foreignId → users.id    | NO       |                                     |
| project_id       | foreignId → projects.id | NO       |                                     |
| deal_value       | decimal(14,3)           | NO       | KWD — total deal amount             |
| commission_rate  | decimal(4,2) default 2.5 | NO      |                                     |
| commission_amount | decimal(12,3)          | NO       | computed: deal_value * commission_rate / 100 |
| status           | enum(pending, invoiced, paid) | NO  | default: pending                    |
| closed_at        | timestamp               | YES      |                                     |
| created_at       | timestamp               | NO       |                                     |
| updated_at       | timestamp               | NO       |                                     |

---

## API Endpoints

All routes prefixed: `/api/v1/admin`
All routes require: `auth:sanctum` + `EnsureUserIsAdmin` middleware

| Method | Route                              | Description                                  |
|--------|------------------------------------|----------------------------------------------|
| GET    | /listings/pending                  | All listings awaiting review                 |
| POST   | /listings/{id}/approve             | Approve listing → status: approved/published |
| POST   | /listings/{id}/reject              | Reject listing with reason                   |
| POST   | /listings/{id}/request-revision    | Send back to advertiser with notes           |
| POST   | /listings/{id}/suspend             | Suspend published listing                    |
| GET    | /users/pending-verification        | Advertisers awaiting account verification    |
| POST   | /users/{id}/verify                 | Mark advertiser account as verified          |
| POST   | /users/{id}/suspend                | Suspend any user account                     |
| GET    | /interests                         | All interests (with filters)                 |
| PUT    | /interests/{id}/status             | Update interest status                       |
| POST   | /deals                             | Record a completed deal                      |
| GET    | /deals                             | List all deals + commission totals           |
| GET    | /stats                             | Platform-wide statistics                     |

---

## Business Logic per Endpoint

### POST /listings/{id}/approve

**Logic:**
1. Find listing — must be in `pending` or `under_review` status
2. Update status to `published`
3. Set `published_at = now()`
4. Log action to `admin_actions`
5. Notify advertiser: "Your listing has been approved and is now live"

---

### POST /listings/{id}/reject

**Request:**
```
rejection_reason    string    required | min:10
```

**Logic:**
1. Update status to `rejected`
2. Store `rejection_reason`
3. Log action
4. Notify advertiser with the reason

---

### POST /listings/{id}/request-revision

**Request:**
```
revision_notes    string    required | min:10
```

**Logic:**
1. Update status to `needs_revision`
2. Store notes in `admin_notes` on projects table
3. Log action
4. Notify advertiser: "Your listing needs revision" + notes

---

### POST /users/{id}/verify

**Logic:**
1. Find user — must have `role = advertiser`
2. Confirm license document exists (`company_license_url` not null)
3. Log action
4. Notify advertiser: "Your account has been verified"

---

### PUT /interests/{id}/status

**Request:**
```
status       enum    required | in:seen,in_progress,accepted,rejected,completed
admin_notes  string  optional
```

**Logic:**
1. Update interest status
2. Set `responded_at = now()` on first action (when status moves past `sent`)
3. Log action
4. Notify investor of status change (except for `seen` — internal only)
5. If status = `completed` → prompt admin to record a deal

---

### POST /deals

**Request:**
```
interest_id    integer    required | exists:interests,id
deal_value     numeric    required | min:1
```

**Logic:**
1. Find interest — must have `status = completed`
2. Compute: `commission_amount = deal_value * 2.5 / 100`
3. Insert into `deals` table
4. Update interest status to `completed` if not already
5. Update project status to `suspended` (deal closed, no new investors)
6. Log action
7. Return deal summary including commission amount

---

### GET /stats

**Returns:**
```json
{
  "total_users": { "investors": 120, "advertisers": 45 },
  "total_listings": { "published": 38, "pending": 7, "rejected": 3 },
  "total_booklet_purchases": 214,
  "total_interests": 89,
  "total_deals": { "count": 12, "total_value": 950000.000, "total_commission": 23750.000 },
  "monthly_revenue": [
    { "month": "2025-01", "booklets": 1500.000, "commissions": 5000.000 }
  ]
}
```

---

## Middleware

```php
// app/Http/Middleware/EnsureUserIsAdmin.php

public function handle($request, Closure $next)
{
    if (!$request->user() || $request->user()->role !== 'admin') {
        return response()->json([
            'success' => false,
            'message' => 'Unauthorized.',
            'errors'  => null,
        ], 403);
    }
    return $next($request);
}
```

---

## Architecture Pattern

```
Request → AdminFormRequest → DTO → AdminService → Resource → ResponseTrait
```

All admin operations go through `AdminService`.
The service logs every action to `admin_actions` before returning.

### Service Interface (partial)

```php
interface AdminServiceInterface
{
    public function approveListing(Project $project, User $admin): Project;
    public function rejectListing(Project $project, User $admin, string $reason): Project;
    public function requestRevision(Project $project, User $admin, string $notes): Project;
    public function verifyUser(User $target, User $admin): User;
    public function suspendUser(User $target, User $admin): User;
    public function updateInterestStatus(Interest $interest, User $admin, string $status, ?string $notes): Interest;
    public function recordDeal(Interest $interest, User $admin, float $dealValue): Deal;
    public function getPlatformStats(): array;
}
```

---

## File Tree

```
app/
├── DTO/Admin/
│   ├── ApproveListingDTO.php
│   ├── RejectListingDTO.php
│   ├── UpdateInterestStatusDTO.php
│   └── RecordDealDTO.php
├── Services/Admin/
│   ├── AdminServiceInterface.php
│   └── AdminService.php
├── Http/
│   ├── Controllers/Api/V1/Admin/
│   │   ├── ListingReviewController.php
│   │   ├── UserVerificationController.php
│   │   ├── InterestManagementController.php
│   │   ├── DealController.php
│   │   └── StatsController.php
│   ├── Requests/Admin/
│   │   ├── RejectListingRequest.php
│   │   ├── RequestRevisionRequest.php
│   │   ├── UpdateInterestStatusRequest.php
│   │   └── RecordDealRequest.php
│   ├── Resources/
│   │   ├── AdminListingResource.php
│   │   ├── AdminInterestResource.php
│   │   └── DealResource.php
│   └── Middleware/
│       └── EnsureUserIsAdmin.php
├── Models/
│   ├── AdminAction.php
│   └── Deal.php
└── Notifications/
    ├── ListingApprovedNotification.php
    ├── ListingRejectedNotification.php
    ├── ListingNeedsRevisionNotification.php
    ├── AccountVerifiedNotification.php
    └── InterestStatusUpdatedNotification.php
```
