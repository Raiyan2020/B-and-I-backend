# Feature 04 — Interest Registration System

## Business Context

This is the most critical business logic feature of the platform.

When an investor is interested in a project after reading its booklet:
- The investor presses "I'm Interested" (أنا مهتم)
- **No direct contact with the advertiser happens**
- The system records the interest and notifies the admin
- The admin then manually coordinates between both parties
- All communication between investor and advertiser goes through the admin

This design protects two things:
1. The platform's commission (2.5% on completed deals)
2. Identity privacy of both parties until admin approves the connection

**Rule:** An investor can only register interest AFTER purchasing the booklet.
Expressing interest without purchasing the booklet is blocked at the API level.

---

## Database

### Table: `interests`

| column         | type                    | nullable | notes                                        |
|----------------|-------------------------|----------|----------------------------------------------|
| id             | bigIncrements PK        | NO       |                                              |
| investor_id    | foreignId → users.id    | NO       |                                              |
| project_id     | foreignId → projects.id | NO       |                                              |
| status         | enum(sent, seen, in_progress, accepted, rejected, completed) | NO | default: sent |
| investor_note  | text                    | YES      | optional message from investor to admin      |
| admin_notes    | text                    | YES      | internal admin notes — never shown to either party |
| notified_at    | timestamp               | YES      | when admin was notified                      |
| responded_at   | timestamp               | YES      | when admin first took action                 |
| created_at     | timestamp               | NO       |                                              |
| updated_at     | timestamp               | NO       |                                              |

**Unique constraint:** `(investor_id, project_id)` — one interest per investor per project.

---

## Interest Status Flow

```
[investor presses "I'm Interested"]
            ↓
           sent       ← default on creation, admin notified
            ↓
           seen        ← admin opened the lead
            ↓
       in_progress     ← admin started coordinating
            ↓
     ┌──────┴──────┐
  accepted       rejected
     ↓
  completed      ← deal reached, commission recorded
```

---

## API Endpoints

All routes prefixed: `/api/v1`

| Method | Route                           | Auth Required | Role     | Description                                    |
|--------|---------------------------------|---------------|----------|------------------------------------------------|
| POST   | /interests/{project_id}         | Yes           | investor | Register interest in a project                 |
| GET    | /dashboard/interests/sent       | Yes           | investor | My sent interests and their status             |
| GET    | /dashboard/interests/incoming   | Yes           | advertiser | Interests on my listings (admin-filtered view) |

---

## Business Logic per Endpoint

### POST /interests/{project_id}

**Request (JSON):**
```
investor_note    string    optional | max:500
```

**Logic:**
1. Verify authenticated user has `role = investor`
2. Find project — must have `status = published`
3. **Verify booklet is purchased:**
   - Query `booklet_purchases` for `(investor_id, project_id)`
   - If not purchased → 403 with message: "You must purchase the booklet before registering interest"
4. Check for duplicate interest:
   - If already exists → return 200 idempotently (do not error, frontend may retry)
5. Insert row into `interests` with `status = sent`
6. Increment `projects.interests_count` atomically
7. Notify admin via `NewInterestRegistered` notification (database + email)
8. Return 201

**Response 201:**
```json
{
  "success": true,
  "message": "Interest registered. Our team will contact you shortly.",
  "data": {
    "interest_id": 42,
    "status": "sent",
    "project_id": 7
  }
}
```

---

### GET /dashboard/interests/sent (Investor)

**Logic:**
- Return all interests where `investor_id = auth()->id()`
- Eager load project (public fields only — no booklet fields needed here)
- Include current `status` with human-readable label
- Sort by `created_at DESC`

**Note:** The investor sees status updates but NEVER sees the advertiser's identity or contact info.
That information is only shared by admin off-platform.

---

### GET /dashboard/interests/incoming (Advertiser)

**Logic:**
- Return all interests on projects where `owner_id = auth()->id()`
- Include project name and interest count
- **Do NOT expose investor identity** — show only:
  - Interest count per project
  - Status of each interest
  - Date registered
- Advertiser learns about specific investors only when admin initiates the introduction

---

## Architecture Pattern

```
Request → InterestRequest → DTO → InterestService → InterestResource → ResponseTrait
```

### DTO

```php
// app/DTO/Interest/RegisterInterestDTO.php
readonly class RegisterInterestDTO
{
    public function __construct(
        public int     $investorId,
        public int     $projectId,
        public ?string $investorNote,
    ) {}
}
```

### Service Interface

```php
interface InterestServiceInterface
{
    public function register(RegisterInterestDTO $dto): Interest;
    // throws BookletNotPurchasedException if booklet not bought
    // throws ProjectNotAvailableException if project not published
    // returns existing record silently if already registered (idempotent)

    public function getInvestorInterests(User $investor): Collection;

    public function getAdvertiserIncomingInterests(User $advertiser): Collection;
}
```

---

## Admin Notification

When `register()` is called successfully, dispatch:

```php
// app/Notifications/NewInterestRegisteredNotification.php
// Channel: database + mail
// Payload: project_id, project_name, investor_id, investor_type, created_at
// Admin never sees investor contact details here — just type and date
```

---

## File Tree

```
app/
├── DTO/Interest/
│   └── RegisterInterestDTO.php
├── Services/Interest/
│   ├── InterestServiceInterface.php
│   └── InterestService.php
├── Http/
│   ├── Controllers/Api/V1/
│   │   └── InterestController.php
│   ├── Requests/
│   │   └── RegisterInterestRequest.php
│   └── Resources/
│       ├── InterestSentResource.php
│       └── InterestIncomingResource.php
├── Models/
│   └── Interest.php
├── Notifications/
│   └── NewInterestRegisteredNotification.php
└── Exceptions/
    └── BookletNotPurchasedException.php
```
