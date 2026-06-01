<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserNotificationResource;
use App\Models\Notification;
use App\Traits\ResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    use ResponseTrait;

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $perPage = $this->resolvePerPage($request);
        $paginator = $user->notifications()->paginate($perPage)->withQueryString();

        return $this->jsonResponse(data: [
            'notifications' => UserNotificationResource::collection($paginator->items())->resolve($request),
            'pagination' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
        ]);
    }

    public function unreadCount(Request $request): JsonResponse
    {
        return $this->jsonResponse(data: [
            'unread_notifications_count' => $request->user()->notifications()->whereNull('read_at')->count(),
        ]);
    }

    public function markAllAsRead(Request $request): JsonResponse
    {
        $request->user()->notifications()->whereNull('read_at')->update(['read_at' => now()]);

        return $this->jsonResponse(msg: __('apis.success'), data: [
            'unread_notifications_count' => 0,
        ]);
    }

    public function destroy(Request $request, Notification $notification): JsonResponse
    {
        abort_unless(
            $notification->notifiable_type === $request->user()::class
            && (int) $notification->notifiable_id === (int) $request->user()->id,
            404
        );

        $notification->delete();

        return $this->jsonResponse(msg: __('apis.success'), data: [
            'deleted_id' => $notification->id,
            'unread_notifications_count' => $request->user()->notifications()->whereNull('read_at')->count(),
        ]);
    }

    public function destroyAll(Request $request): JsonResponse
    {
        $request->user()->notifications()->delete();

        return $this->jsonResponse(msg: __('apis.success'), data: [
            'unread_notifications_count' => 0,
        ]);
    }

    private function resolvePerPage(Request $request): int
    {
        return max(1, min((int) $request->query('per_page', 15), 100));
    }

    public function markAsRead(Request $request, Notification $notification): JsonResponse
    {
        abort_unless(
            $notification->notifiable_type === $request->user()::class
            && (int) $notification->notifiable_id === (int) $request->user()->id,
            404
        );

        $notification->update(['read_at' => now()]);

        return $this->jsonResponse(msg: __('apis.success'), data: [
            'unread_notifications_count' => $request->user()->notifications()->whereNull('read_at')->count(),
        ]);
    }
}
