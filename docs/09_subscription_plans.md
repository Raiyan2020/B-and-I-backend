# Feature 09 — Subscription Plans

## Business Context

The platform has three subscription tiers:
- **Basic**
- **Premium**
- **VIP**

Subscriptions apply primarily to advertisers (to list their projects).
For investors, subscription may affect the number of booklets they can purchase
or unlock additional filtering features — exact tier benefits are defined below.

This is a revenue stream separate from booklet purchases and commission.

**Current state:** The frontend shows the pricing page but subscription is not
enforced functionally. This feature makes it real — plans affect access.

---

## Database

### Table: `subscription_plans`

| column      | type                           | nullable | notes                     |
|-------------|--------------------------------|----------|---------------------------|
| id          | bigIncrements PK               | NO       |                           |
| key         | enum(basic, premium, vip)      | NO       | unique                    |
| name_ar     | string                         | NO       |                           |
| name_en     | string                         | NO       |                           |
| price_kwd   | decimal(8,3)                   | NO       | monthly price             |
| features_ar | json                           | NO       | array of feature strings  |
| features_en | json                           | NO       |                           |
| is_active   | boolean default true           | NO       |                           |
| created_at  | timestamp                      | NO       |                           |
| updated_at  | timestamp                      | NO       |                           |

Seed with 3 rows on deploy. Prices and features managed via admin later.

### Table: `user_subscriptions`

| column           | type                           | nullable | notes                         |
|------------------|--------------------------------|----------|-------------------------------|
| id               | bigIncrements PK               | NO       |                               |
| user_id          | foreignId → users.id           | NO       |                               |
| plan_key         | enum(basic, premium, vip)      | NO       |                               |
| started_at       | timestamp                      | NO       |                               |
| expires_at       | timestamp                      | YES      | null = active (no expiry yet) |
| payment_ref      | string                         | YES      |                               |
| status           | enum(active, expired, cancelled) | NO     | default: active               |
| created_at       | timestamp                      | NO       |                               |
| updated_at       | timestamp                      | NO       |                               |

Also add `subscription_plan` column to `users` table (reflects current active plan key).
This is a denormalized fast-read field — updated whenever subscription changes.

---

## Plan Access Rules

Defined in `config/platform.php`:

```php
'plan_rules' => [
    'basic' => [
        'max_active_listings'  => 1,
        'booklet_price_discount' => 0,      // no discount
        'can_feature_listing'  => false,
    ],
    'premium' => [
        'max_active_listings'  => 3,
        'booklet_price_discount' => 10,     // 10% off booklets
        'can_feature_listing'  => true,
    ],
    'vip' => [
        'max_active_listings'  => 10,
        'booklet_price_discount' => 20,     // 20% off booklets
        'can_feature_listing'  => true,
    ],
],
```

---

## API Endpoints

All routes prefixed: `/api/v1`

| Method | Route                       | Auth Required | Description                            |
|--------|-----------------------------|---------------|----------------------------------------|
| GET    | /plans                      | No            | List all plans (pricing page)          |
| POST   | /subscriptions/subscribe    | Yes           | Subscribe to a plan                    |
| GET    | /subscriptions/current      | Yes           | Get current subscription status        |
| POST   | /subscriptions/cancel       | Yes           | Cancel current subscription            |

---

## Business Logic per Endpoint

### GET /plans

**Logic:**
1. Return all plans where `is_active = true`
2. Sort by price ascending
3. No auth required — public page

---

### POST /subscriptions/subscribe

**Request:**
```
plan_key      enum      required | in:basic,premium,vip
payment_ref   string    optional
```

**Logic:**
1. Find plan by `plan_key`
2. Check if user has an active subscription:
   - If yes → return 400 "Already subscribed. Cancel current plan first."
3. Simulate payment (accept `payment_ref` or generate UUID)
4. Insert into `user_subscriptions` with `status = active`
5. Update `users.subscription_plan = plan_key`
6. Return 201 with subscription details

---

### POST /subscriptions/cancel

**Logic:**
1. Find active subscription for authenticated user
2. Set `status = cancelled`, `expires_at = now()`
3. Update `users.subscription_plan = null`
4. Return 200

---

## Enforcement

The `max_active_listings` rule is enforced in `ListingService::create()`:

```php
// In ListingService::create()
$plan = config("platform.plan_rules.{$owner->subscription_plan}");
$activeCount = Project::where('owner_id', $owner->id)
    ->whereIn('status', ['pending', 'under_review', 'approved', 'published'])
    ->count();

if ($activeCount >= ($plan['max_active_listings'] ?? 1)) {
    throw new PlanLimitExceededException("Upgrade your plan to post more listings.");
}
```

---

## Architecture Pattern

```
Request → SubscriptionService → SubscriptionResource → ResponseTrait
```

### Service Interface

```php
interface SubscriptionServiceInterface
{
    public function getPlans(): Collection;
    public function subscribe(User $user, string $planKey, ?string $paymentRef): UserSubscription;
    public function cancel(User $user): void;
    public function getCurrentSubscription(User $user): ?UserSubscription;
    public function hasActivePlan(User $user): bool;
}
```

---

## File Tree

```
app/
├── Services/Subscription/
│   ├── SubscriptionServiceInterface.php
│   └── SubscriptionService.php
├── Http/
│   ├── Controllers/Api/V1/
│   │   ├── PlanController.php
│   │   └── SubscriptionController.php
│   ├── Requests/
│   │   └── SubscribeRequest.php
│   └── Resources/
│       ├── PlanResource.php
│       └── SubscriptionResource.php
├── Models/
│   ├── SubscriptionPlan.php
│   └── UserSubscription.php
├── Exceptions/
│   └── PlanLimitExceededException.php
└── Database/Seeders/
    └── SubscriptionPlanSeeder.php
```
