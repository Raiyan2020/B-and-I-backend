# Feature 10 — Platform Statistics

## Business Context

The statistics page serves two audiences:

1. **Public/Visitors** — high-level trust-building numbers (total projects, investors, deals)
   Shown on the homepage or a dedicated stats page to establish credibility.

2. **Admin** — full operational dashboard with revenue, conversion rates, and activity metrics.
   Already covered in Feature 05 (`GET /admin/stats`). Do not duplicate here.

This feature covers the **public-facing statistics endpoint** only.

---

## Database

No new tables. All stats are computed from existing tables:
- `users` — count by role
- `projects` — count by status
- `booklet_purchases` — purchase volume
- `interests` — engagement volume
- `deals` — completed deals and commission

For performance, consider caching stats with a 15-minute TTL using Laravel Cache.

---

## API Endpoints

| Method | Route          | Auth Required | Description                         |
|--------|----------------|---------------|-------------------------------------|
| GET    | /api/v1/stats  | No            | Public platform statistics          |

---

## Business Logic

### GET /api/v1/stats

**Logic:**
1. Compute (or retrieve from cache) the following metrics
2. Cache key: `platform_stats`, TTL: 15 minutes
3. Return via `StatsResource`

**Response:**
```json
{
  "success": true,
  "data": {
    "total_investors": 120,
    "total_advertisers": 45,
    "total_published_listings": 38,
    "total_booklet_purchases": 214,
    "total_interests": 89,
    "total_completed_deals": 12,
    "sectors": [
      { "name_ar": "التكنولوجيا", "name_en": "Technology", "count": 12 },
      { "name_ar": "المطاعم",     "name_en": "Food & Dining", "count": 9 }
    ]
  }
}
```

**Sectors** are derived by grouping published projects by `category_en`.

---

## Caching Strategy

```php
// In StatsService::getPublicStats()

return Cache::remember('platform_stats', now()->addMinutes(15), function () {
    return [
        'total_investors'          => User::where('role', 'investor')->count(),
        'total_advertisers'        => User::where('role', 'advertiser')->count(),
        'total_published_listings' => Project::where('status', 'published')->count(),
        'total_booklet_purchases'  => BookletPurchase::count(),
        'total_interests'          => Interest::count(),
        'total_completed_deals'    => Deal::where('status', 'paid')->count(),
        'sectors'                  => Project::where('status', 'published')
                                        ->selectRaw('category_en, category_ar, count(*) as count')
                                        ->groupBy('category_en', 'category_ar')
                                        ->orderByDesc('count')
                                        ->limit(8)
                                        ->get(),
    ];
});
```

---

## Architecture Pattern

```
Request → StatsService (cached) → StatsResource → ResponseTrait
```

No DTO needed — stats are read-only computed values.

---

## File Tree

```
app/
├── Services/Stats/
│   └── StatsService.php
├── Http/
│   ├── Controllers/Api/V1/
│   │   └── StatsController.php
│   └── Resources/
│       └── StatsResource.php
```
