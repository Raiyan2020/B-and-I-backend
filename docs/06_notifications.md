# Feature 06 — Notifications System

## Business Context

Notifications keep users connected to platform activity without requiring
direct communication between parties.

The system serves:
- **Investor** — new matching projects, booklet updates, interest status changes
- **Advertiser** — new interest received (via admin), listing status changes, verification updates
- **Admin** — new listings pending review, new interests requiring coordination

Notifications are internal to the platform.
They do NOT carry sensitive identity information across roles.

---

## Database

### Table: `notifications`

Use Laravel's built-in `notifications` table (via `php artisan notifications:table`).

Default schema (already provided by Laravel):

| column       | type         | nullable | notes                              |
|--------------|--------------|----------|------------------------------------|
| id           | uuid PK      | NO       |                                    |
| type         | string       | NO       | PHP class name of notification     |
| notifiable_type | string    | NO       | polymorphic — always "App\Models\User" |
| notifiable_id | bigInteger  | NO       |                                    |
| data         | json         | NO       | notification payload               |
| read_at      | timestamp    | YES      | null = unread                      |
| created_at   | timestamp    | NO       |                                    |
| updated_at   | timestamp    | NO       |                                    |

**Do not create a custom notifications table.** Use Laravel's built-in system.

---

## Notification Types

| type key         | trigger                                          | recipient    |
|------------------|--------------------------------------------------|--------------|
| `new_interest`   | Investor registers interest on a project         | Admin        |
| `deal_update`    | Admin updates interest status                    | Investor     |
| `listing_approved` | Admin approves a listing                       | Advertiser   |
| `listing_rejected` | Admin rejects a listing                        | Advertiser   |
| `listing_needs_revision` | Admin requests revision                  | Advertiser   |
| `account_verified` | Admin verifies advertiser account              | Advertiser   |
| `new_listing`    | Advertiser submits new listing                   | Admin        |
| `booklet_purchased` | Investor purchases a booklet                  | Advertiser (anonymized — no investor identity) |
| `system`         | Platform-level announcements                     | Any user     |

---

## API Endpoints

All routes prefixed: `/api/v1/notifications`

| Method | Route          | Auth Required | Description                            |
|--------|----------------|---------------|----------------------------------------|
| GET    | /              | Yes           | Get all notifications (paginated)      |
| GET    | /unread-count  | Yes           | Get count of unread notifications      |
| POST   | /{id}/read     | Yes           | Mark single notification as read       |
| POST   | /read-all      | Yes           | Mark all notifications as read         |
| DELETE | /{id}          | Yes           | Delete a notification                  |
| DELETE | /clear-all     | Yes           | Delete all notifications               |

---

## Business Logic per Endpoint

### GET /

**Query parameters:**
```
type      string    optional — filter by type key (e.g. deal_update)
read      boolean   optional — filter by read/unread
page      integer   optional
per_page  integer   optional | default:20 | max:50
```

**Logic:**
1. Scope to `notifiable_id = auth()->id()`
2. Apply filters
3. Sort by `created_at DESC`
4. Return paginated collection via `NotificationResource`

### POST /{id}/read

**Logic:**
1. Find notification belonging to authenticated user only (scope check)
2. Set `read_at = now()` if not already set
3. Return 200

### DELETE /clear-all

**Logic:**
1. Delete all notifications for `notifiable_id = auth()->id()`
2. Return 200

---

## Notification Payload Structure

All notifications store this JSON shape in the `data` column:

```json
{
  "type": "deal_update",
  "title": {
    "ar": "تحديث الصفقة",
    "en": "Deal Update"
  },
  "message": {
    "ar": "تم قبول اهتمامك بالمشروع PROJ-1002",
    "en": "Your interest in project PROJ-1002 has been accepted"
  },
  "link": "/projects/7",
  "created_at": "2025-01-01T10:00:00Z"
}
```

**Rule:** Never include the opposing party's identity in the notification payload.
- `booklet_purchased` tells the advertiser a booklet was purchased but NOT by whom
- `new_interest` tells admin there is a new interest but does not expose investor to advertiser

---

## NotificationResource

```php
// app/Http/Resources/NotificationResource.php

return [
    'id'         => $this->id,
    'type'       => $this->data['type'] ?? null,
    'title'      => $this->data['title'] ?? null,      // {ar, en}
    'message'    => $this->data['message'] ?? null,    // {ar, en}
    'link'       => $this->data['link'] ?? null,
    'is_read'    => !is_null($this->read_at),
    'created_at' => $this->created_at,
];
```

---

## Dispatching Notifications

Notifications are dispatched from Services, never from Controllers.

```php
// In any service method that needs to notify:
$user->notify(new DealUpdateNotification($interest));

// For admin notifications:
$admin = User::where('role', 'admin')->first();
$admin->notify(new NewInterestRegisteredNotification($interest));
```

Use `database` channel for all notifications (in-app).
Add `mail` channel optionally per notification class as a separate configuration step.

---

## Architecture Pattern

```
Service dispatches Notification → stored via Laravel database channel
    → GET /notifications fetches and returns via NotificationResource
```

No DTO needed — Laravel notification classes handle their own data building.

---

## File Tree

```
app/
├── Http/
│   ├── Controllers/Api/V1/
│   │   └── NotificationController.php
│   └── Resources/
│       └── NotificationResource.php
├── Notifications/
│   ├── NewInterestRegisteredNotification.php
│   ├── DealUpdateNotification.php
│   ├── ListingApprovedNotification.php
│   ├── ListingRejectedNotification.php
│   ├── ListingNeedsRevisionNotification.php
│   ├── AccountVerifiedNotification.php
│   ├── NewListingSubmittedNotification.php
│   ├── BookletPurchasedNotification.php
│   └── SystemNotification.php
```
