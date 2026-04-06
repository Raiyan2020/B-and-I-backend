# Feature 03 — Booklet Purchase System

## Business Context

The booklet is the core monetization unit for investors.
A project listing has two information layers:
- **Public layer** — free, visible to all
- **Booklet layer** — paid, unlocked per investor per project

When an investor purchases a booklet:
- The project status for that investor changes from `locked` → `unlocked`
- The investor gains permanent access to the full details of that project
- The purchased booklet appears in the investor's dashboard under "My Booklets"
- The `booklet_purchases_count` counter on the project increments

This system serves two business goals:
1. Direct revenue for the platform
2. Filtering serious investors from casual browsers (gated access = signal of intent)

---

## Database

### Table: `booklet_purchases`

| column        | type                    | nullable | notes                          |
|---------------|-------------------------|----------|--------------------------------|
| id            | bigIncrements PK        | NO       |                                |
| investor_id   | foreignId → users.id   | NO       | must be investor role          |
| project_id    | foreignId → projects.id | NO      |                                |
| amount_paid   | decimal(8,3)            | NO       | KWD — price at time of purchase|
| payment_ref   | string                  | YES      | external payment gateway ref   |
| purchased_at  | timestamp               | NO       |                                |

**Unique constraint:** `(investor_id, project_id)` — one purchase per investor per project.

---

## API Endpoints

All routes prefixed: `/api/v1`

| Method | Route                              | Auth Required | Role     | Description                            |
|--------|------------------------------------|---------------|----------|----------------------------------------|
| POST   | /booklets/{project_id}/purchase    | Yes           | investor | Purchase booklet for a project         |
| GET    | /dashboard/booklets                | Yes           | investor | List all purchased booklets            |
| GET    | /booklets/{project_id}/check       | Yes           | investor | Check if investor has purchased        |

---

## Business Logic per Endpoint

### POST /booklets/{project_id}/purchase

**Logic:**
1. Verify authenticated user has `role = investor`
2. Find project — must have `status = published`
3. Check if already purchased: query `booklet_purchases` for this `(investor_id, project_id)` pair
   - If already purchased → return 200 with message "Already purchased" (idempotent, not an error)
4. Retrieve current booklet price from config or pricing table
5. Process payment:
   - For now: simulate with `payment_ref = 'SIMULATED-{uuid}'`
   - Design the service method to accept a payment result object so a real gateway can be swapped in later
6. Insert row into `booklet_purchases`
7. Increment `projects.booklet_purchases_count` atomically
8. Return 201 with the full listing data (ListingFullResource) — investor now has access

**Business rule:** Purchase is permanent. No refunds. No expiry.

---

### GET /dashboard/booklets

**Logic:**
1. Query `booklet_purchases` where `investor_id = auth()->id()`
2. Eager load the associated project
3. Return collection with for each booklet:
   - Project public info
   - Full booklet content (investor already paid)
   - `purchased_at` timestamp

---

### GET /booklets/{project_id}/check

**Logic:**
1. Query `booklet_purchases` where `investor_id = auth()->id()` AND `project_id = {id}`
2. Return:
```json
{
  "success": true,
  "data": {
    "is_purchased": true,
    "purchased_at": "2025-01-01T10:00:00Z"
  }
}
```

This endpoint is used by the frontend to determine lock/unlock UI state.

---

## Helper: isUnlocked(User $user, Project $project)

Add this method to `BookletService` (and optionally as a scope on the model):

```php
public function isUnlocked(User $investor, Project $project): bool
{
    return BookletPurchase::where('investor_id', $investor->id)
        ->where('project_id', $project->id)
        ->exists();
}
```

This is called inside `GET /listings/{id}` (Feature 02) to decide which resource to return.

---

## Pricing

Booklet price is a platform-level config, not per-project.
Store in `config/platform.php`:

```php
'booklet_price' => env('BOOKLET_PRICE_KWD', 10.000),
```

---

## Architecture Pattern

```
Request → BookletPurchaseRequest → DTO → BookletService → BookletPurchaseResource → ResponseTrait
```

### DTO

```php
// app/DTO/Booklet/PurchaseBookletDTO.php
readonly class PurchaseBookletDTO
{
    public function __construct(
        public int    $investorId,
        public int    $projectId,
        public float  $amountPaid,
        public string $paymentRef,
    ) {}
}
```

### Service Interface

```php
interface BookletServiceInterface
{
    public function purchase(PurchaseBookletDTO $dto): BookletPurchase;
    // throws AlreadyPurchasedException if duplicate
    // throws ProjectNotAvailableException if project not published

    public function isUnlocked(User $investor, Project $project): bool;

    public function getInvestorBooklets(User $investor): Collection;
}
```

---

## File Tree

```
app/
├── DTO/Booklet/
│   └── PurchaseBookletDTO.php
├── Services/Booklet/
│   ├── BookletServiceInterface.php
│   └── BookletService.php
├── Http/
│   ├── Controllers/Api/V1/
│   │   └── BookletController.php
│   ├── Requests/
│   │   └── PurchaseBookletRequest.php
│   └── Resources/
│       └── BookletPurchaseResource.php
├── Models/
│   └── BookletPurchase.php
└── Exceptions/
    ├── AlreadyPurchasedException.php
    └── ProjectNotAvailableException.php
```
