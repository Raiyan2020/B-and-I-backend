<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateNotificationSettingsRequest;
use App\Http\Resources\NotificationSettingsResource;
use App\Services\Notifications\NotificationPreferenceService;
use App\Traits\ResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationSettingsController extends Controller
{
    use ResponseTrait;

    public function __construct(private readonly NotificationPreferenceService $service) {}

    public function show(Request $request): JsonResponse
    {
        return $this->jsonResponse(
            data: NotificationSettingsResource::make($request->user()),
        );
    }

    public function update(UpdateNotificationSettingsRequest $request): JsonResponse
    {
        $user = $this->service->update($request->user(), $request->validated());

        return $this->jsonResponse(
            data: NotificationSettingsResource::make($user),
            msg: __('apis.notification_settings_updated'),
        );
    }
}
