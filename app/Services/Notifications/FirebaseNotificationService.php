<?php

namespace App\Services\Notifications;

use App\Enums\DeviceType;
use App\Models\Admin;
use App\Models\GeneralSetting;
use App\Models\User;
use App\Services\Devices\DeviceService;
use App\Traits\SendsFirebaseNotifications;
use Illuminate\Database\Eloquent\Collection;

class FirebaseNotificationService
{
    use SendsFirebaseNotifications;

    public function __construct(private readonly DeviceService $deviceService) {}

    public function sendToUser(User $user, array $message, ?string $deviceType = null, array $data = []): array
    {
        return $this->sendToDevices($this->deviceService->getUserDevices($user, $deviceType), $message, $data);
    }

    public function sendToAdmin(Admin $admin, array $message, ?string $deviceType = null, array $data = []): array
    {
        return $this->sendToDevices($this->deviceService->getAdminDevices($admin, $deviceType), $message, $data);
    }

    public function sendToDevices(Collection $devices, array $message, array $data = []): array
    {
        $summary = [
            'sent' => 0,
            'failed' => 0,
            'responses' => [],
        ];

        $groupedByLocale = $devices->groupBy(fn ($device) => $device->locale ?: app()->getLocale());

        foreach ($groupedByLocale as $locale => $localizedDevices) {
            $notification = $this->resolveLocalizedMessage($message, (string) $locale);
            $groupedByDeviceType = $localizedDevices->groupBy(fn ($device) => $device->device_type?->value ?? $device->device_type);

            foreach ($groupedByDeviceType as $deviceType => $platformDevices) {
                $tokens = $platformDevices->pluck('token')->filter()->values()->all();
                $result = $this->dispatchToPlatform((string) $deviceType, $tokens, $notification, $data + ['locale' => (string) $locale]);

                $summary['sent'] += $result['sent'];
                $summary['failed'] += $result['failed'];
                $summary['responses'] = array_merge($summary['responses'], $result['responses']);
            }
        }

        return $summary;
    }

    private function dispatchToPlatform(string $deviceType, array $tokens, array $notification, array $data): array
    {
        return match (DeviceType::normalize($deviceType)) {
            DeviceType::Android => $this->sendAndroidNotifications($tokens, $notification, $data),
            DeviceType::Ios => $this->sendIosNotifications($tokens, $notification, $data),
            DeviceType::Web => $this->sendWebNotifications($tokens, $notification, $data),
        };
    }

    private function resolveLocalizedMessage(array $message, string $locale): array
    {
        $resolvedLocale = in_array($locale, ['ar', 'en'], true) ? $locale : app()->getLocale();
        $defaultIcon = $this->defaultNotificationIcon();

        return [
            'title' => $message['title'][$resolvedLocale] ?? $message['title']['en'] ?? $message['title']['ar'] ?? '',
            'body' => $message['body'][$resolvedLocale] ?? $message['body']['en'] ?? $message['body']['ar'] ?? '',
            'click_action' => $message['click_action'] ?? null,
            'icon' => $message['icon'] ?? $defaultIcon,
        ];
    }

    private function defaultNotificationIcon(): string
    {
        $favicon = GeneralSetting::getValueForKey('favicon2');

        if (filled($favicon)) {
            return asset('Site/assets/images/logo/'.$favicon);
        }

        return asset('dashboardAssets/app-assets/images/logo/N-FAVICON.png');
    }
}
