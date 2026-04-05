# Feature 08 — Investor Discovery (Public Investors Page)

## Business Context

The investors page serves a trust-building purpose.
It shows that the platform has real, active investors — not just listings.

Who uses it:
- **Visitors / Advertisers** — browse to see what investors are available
- **Advertisers** — can express interest in a specific investor (signals admin)

What is shown:
- Investor type, preferred sector, capital range, experience level
- NO personal identity — no name, email, phone, or contact info

An advertiser can press "I'm Interested" on an investor profile.
This sends a signal to admin only — no direct contact opens.

---

## Database

No new table needed. Data comes from the `users` table filtered by `role = investor`.

Public investor profile fields (subset of `users`):
- `id`
- `investor_type`
- `investor_sector`
- `investor_capital`
- `investment_count`
- `investor_experience`
- `created_at` (to show "member since")

Fields **never** exposed publicly:
- `first_name`, `last_name`, `email`, `phone`, `password`
- Any subscription or internal fields

### Table: `investor_interests` (advertiser → investor signal)

| column        | type                    | nullable | notes                                |
|---------------|-------------------------|----------|--------------------------------------|
| id            | bigIncrements PK        | NO       |                                      |
| advertiser_id | foreignId → users.id    | NO       |                                      |
| investor_id   | foreignId → users.id    | NO       |                                      |
| status        | enum(sent, seen, rejected) | NO    | default: sent                        |
| created_at    | timestamp               | NO       |                                      |

**Unique constraint:** `(advertiser_id, investor_id)`

---

## API Endpoints

All routes prefixed: `/api/v1`

| Method | Route                          | Auth Required | Role       | Description                              |
|--------|--------------------------------|---------------|------------|------------------------------------------|
| GET    | /investors                     | No            | public     | List public investor profiles            |
| GET    | /investors/{id}                | No            | public     | Single public investor profile           |
| POST   | /investors/{id}/interest       | Yes           | advertiser | Express interest in an investor          |

---

## Business Logic per Endpoint

### GET /investors

**Query parameters:**
```
investor_type    enum      optional | in:angel,company,crowdfunding
sector           string    optional
experience       enum      optional | in:beginner,intermediate,expert
min_capital      numeric   optional
max_capital      numeric   optional
page             integer   optional
per_page         integer   optional | default:12 | max:50
```

**Logic:**
1. Query `users` where `role = investor`
2. Apply filters
3. Return via `PublicInvestorResource` — only non-sensitive fields
4. Sort by `created_at DESC` by default

---

### POST /investors/{id}/interest

**Logic:**
1. Verify authenticated user has `role = advertiser`
2. Find investor — must exist with `role = investor`
3. Check for duplicate in `investor_interests` for `(advertiser_id, investor_id)`:
   - If exists → return 200 idempotently
4. Insert row with `status = sent`
5. Notify admin: "An advertiser is interested in investor #{id}"
6. Return 201

**Response 201:**
```json
{
  "success": true,
  "message": "Your interest has been sent to our team. We will coordinate shortly.",
  "data": { "status": "sent" }
}
```

---

## PublicInvestorResource

```php
return [
    'id'                => $this->id,
    'investor_type'     => $this->investor_type,
    'investor_sector'   => $this->investor_sector,
    'investor_capital'  => $this->investor_capital,
    'investment_count'  => $this->investment_count,
    'investor_experience' => $this->investor_experience,
    'member_since'      => $this->created_at->format('Y'),
    // NEVER include: name, email, phone
];
```

---

## Architecture Pattern

```
Request → InvestorService → PublicInvestorResource → ResponseTrait
```

### Service Interface

```php
interface InvestorServiceInterface
{
    public function getPublicInvestors(array $filters): LengthAwarePaginator;
    public function getPublicProfile(int $investorId): User;
    public function advertiserExpressInterest(User $advertiser, User $investor): InvestorInterest;
}
```

---

## File Tree

```
app/
├── Services/Investor/
│   ├── InvestorServiceInterface.php
│   └── InvestorService.php
├── Http/
│   ├── Controllers/Api/V1/
│   │   └── InvestorController.php
│   └── Resources/
│       └── PublicInvestorResource.php
├── Models/
│   └── InvestorInterest.php
└── Notifications/
    └── AdvertiserInterestedInInvestorNotification.php
```
