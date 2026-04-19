<?php

namespace App\Http\Controllers\Dashboard;

use App\Enums\DeviceType;
use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Services\Devices\DeviceService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class NotificationsController extends Controller
{
    public function __construct(private readonly DeviceService $deviceService) {}

    public function readAll(){
        Notification::where('admin_id',auth('admin')->user()->id)->update(['seen'=>1]);
        return back();
    }

    public function read(Notification $notification){
        abort_unless((int) $notification->admin_id === (int) auth('admin')->id(), 403);

        $notification->update(['seen'=>1]);

        return $notification->targetUrl()
            ? redirect($notification->targetUrl())
            : back();
    }

    public function updateToken(Request $request){
        try{
            $validated = $request->validate([
                'token' => ['required', 'string', 'max:2048'],
                'device_type' => ['nullable', Rule::in(DeviceType::values())],
                'locale' => ['nullable', 'string', 'in:ar,en'],
            ]);

            $this->deviceService->syncAdminDevice(
                auth('admin')->user(),
                $validated['token'],
                $validated['device_type'] ?? DeviceType::Web->value,
                $validated['locale'] ?? app()->getLocale(),
            );

            return response()->json([
                'success'=>true
            ]);
        }catch(\Exception $e){
            report($e);
            return response()->json([
                'success'=>false
            ],500);
        }
    }
}
