<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\PlatformNotifications\StoreRequest;
use App\Jobs\SendPlatformNotificationJob;
use App\Services\Queue\QueueWorkerLauncher;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PlatformNotificationController extends Controller
{
    public function __construct(private readonly QueueWorkerLauncher $queueWorkerLauncher)
    {
        $this->middleware('permission:platform-notifications', ['only' => ['index']]);
        $this->middleware('permission:send-platform-notification', ['only' => ['store']]);
    }

    public function index(): View
    {
        return view('dashboard.platform_notifications.index');
    }

    public function store(StoreRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        SendPlatformNotificationJob::dispatch(
            title: [
                'ar' => $validated['title_ar'],
                'en' => $validated['title_en'],
            ],
            body: [
                'ar' => $validated['body_ar'],
                'en' => $validated['body_en'],
            ],
            sendTo: $validated['send_to'],
            actorAdminId: (int) auth('admin')->id(),
        );

        $this->queueWorkerLauncher->launchPlatformNotificationsWorker();

        return back()->with('success', __('dashboard.platform_notification_queued_successfully'));
    }
}
