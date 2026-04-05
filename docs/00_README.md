# B&I Platform — Backend Implementation Docs

## Overview

Business & Investments (B&I) is a brokerage platform connecting investors
with business owners. The admin is the operational intermediary for all deals.

**Stack:** Laravel + Sanctum + MySQL
**Pattern:** Request → DTO → Service → Controller → Resource → ResponseTrait
**Response format:** All responses via `App\Traits\ResponseTrait`

---

## Features Index

| File | Feature | Priority |
|------|---------|----------|
| [01_auth_registration.md](./01_auth_registration.md) | Authentication & Registration (2 user types) | 🔴 Critical |
| [02_listing_management.md](./02_listing_management.md) | Project Listing Management (Advertiser) | 🔴 Critical |
| [03_booklet_purchase.md](./03_booklet_purchase.md) | Booklet Purchase System | 🔴 Critical |
| [04_interest_registration.md](./04_interest_registration.md) | Interest Registration System | 🔴 Critical |
| [05_admin_panel.md](./05_admin_panel.md) | Admin Panel (Review, Coordination, Deals) | 🔴 Critical |
| [06_notifications.md](./06_notifications.md) | Notifications System | 🟡 High |
| [07_favorites.md](./07_favorites.md) | Favorites System | 🟡 High |
| [08_investor_discovery.md](./08_investor_discovery.md) | Investor Discovery Page | 🟡 High |
| [09_subscription_plans.md](./09_subscription_plans.md) | Subscription Plans | 🟢 Medium |
| [10_statistics.md](./10_statistics.md) | Platform Statistics | 🟢 Medium |

---

## Revenue Model

| Source | Mechanism | Feature |
|--------|-----------|---------|
| Booklet purchase | Investor pays per project | Feature 03 |
| Subscriptions | Advertiser monthly plan | Feature 09 |
| 2.5% commission | Admin records on deal close | Feature 05 |

---

## Core Business Rules (apply across all features)

1. **No direct contact** between investor and advertiser — admin is always the intermediary
2. **Booklet must be purchased** before interest can be registered
3. **Identity protection** — investor identity never exposed to advertiser and vice versa
4. **Admin controls listing visibility** — no listing goes public without admin approval
5. **Commission protection** — all deals are tracked through the platform

---

## Global Architecture

```
routes/api.php
    → EnsureUserIsInvestor / EnsureUserIsAdvertiser / EnsureUserIsAdmin middleware
    → FormRequest (validation)
    → Controller (thin — build DTO, call service, return response)
    → DTO (typed data container)
    → Service (all business logic)
    → Eloquent Model
    → API Resource (role-aware response shaping)
    → ResponseTrait (unified envelope: success/errors)
```

---

## Shared Exceptions

```
app/Exceptions/
├── BookletNotPurchasedException.php   → 403
├── AlreadyPurchasedException.php      → 200 (idempotent)
├── ProjectNotAvailableException.php   → 404
├── PlanLimitExceededException.php     → 422
└── UnauthorizedRoleException.php      → 403
```

Register all in `bootstrap/app.php` (Laravel 11) or `Handler.php` (Laravel 10)
and map to appropriate HTTP status codes using `ResponseTrait`.
