# Feature 07 — Favorites System

## Business Context

Investors can save projects to a favorites list before deciding to purchase
the booklet. This is a decision-support tool — it allows the investor to
shortlist opportunities and return to them later.

Favorites are:
- Only available to investors (not advertisers)
- Not visible to the advertiser or admin
- Not a signal of intent — they do NOT trigger any notification
- Purely a personal bookmarking feature

---

## Database

### Table: `favorites`

| column      | type                    | nullable | notes                        |
|-------------|-------------------------|----------|------------------------------|
| id          | bigIncrements PK        | NO       |                              |
| investor_id | foreignId → users.id    | NO       | must be investor role        |
| project_id  | foreignId → projects.id | NO       |                              |
| created_at  | timestamp               | NO       |                              |

**Unique constraint:** `(investor_id, project_id)`

No `updated_at` — favorites are binary (exists or not).

---

## API Endpoints

All routes prefixed: `/api/v1/favorites`

| Method | Route          | Auth Required | Role     | Description                         |
|--------|----------------|---------------|----------|-------------------------------------|
| GET    | /              | Yes           | investor | List all favorited projects          |
| POST   | /{project_id}  | Yes           | investor | Toggle favorite (add or remove)      |
| GET    | /{project_id}  | Yes           | investor | Check if project is favorited        |

---

## Business Logic per Endpoint

### POST /{project_id} — Toggle

**Logic:**
1. Verify `role = investor`
2. Find project — must be `status = published`
3. Check if favorite exists for `(investor_id, project_id)`:
   - If exists → delete it, return `{ is_favorite: false }`
   - If not exists → insert it, return `{ is_favorite: true }`
4. No notification dispatched — favorites are silent

**Response 200:**
```json
{
  "success": true,
  "data": {
    "is_favorite": true,
    "project_id": 7
  }
}
```

---

### GET /

**Logic:**
1. Query `favorites` where `investor_id = auth()->id()`
2. Eager load project with public fields only
3. Sort by `favorites.created_at DESC`
4. Return as `ListingPublicResource` collection (same shape as projects list)
5. Each item includes `is_favorite: true` and `is_unlocked` flag

---

### GET /{project_id}

**Logic:**
1. Check existence of `(investor_id, project_id)` row
2. Return:
```json
{
  "success": true,
  "data": { "is_favorite": true }
}
```

---

## Architecture Pattern

No DTO needed — favorites are simple toggle operations.

```
Request → FavoriteService → ResponseTrait
```

### Service Interface

```php
interface FavoriteServiceInterface
{
    public function toggle(User $investor, Project $project): bool;
    // returns true if now favorited, false if unfavorited

    public function isFavorite(User $investor, Project $project): bool;

    public function getInvestorFavorites(User $investor): Collection;
}
```

---

## File Tree

```
app/
├── Services/Favorite/
│   ├── FavoriteServiceInterface.php
│   └── FavoriteService.php
├── Http/
│   ├── Controllers/Api/V1/
│   │   └── FavoriteController.php
│   └── Resources/
│       └── (reuses ListingPublicResource from Feature 02)
├── Models/
│   └── Favorite.php
```
