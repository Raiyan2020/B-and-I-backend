<?php

namespace App\Services\Devices;

use App\Enums\DeviceType;
use App\Models\Admin;
use App\Models\Device;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class DeviceService
{
    public function syncUserDevice(User $user, string $token, ?string $deviceType = null, ?string $locale = null): Device
    {
        return $this->syncDevice(
            token: $token,
            deviceType: $deviceType,
            locale: $locale,
            attributes: [
                'user_id' => $user->id,
                'admin_id' => null,
            ],
        );
    }

    public function syncAdminDevice(Admin $admin, string $token, ?string $deviceType = null, ?string $locale = null): Device
    {
        return $this->syncDevice(
            token: $token,
            deviceType: $deviceType ?? DeviceType::Web->value,
            locale: $locale,
            attributes: [
                'user_id' => null,
                'admin_id' => $admin->id,
            ],
        );
    }

    public function updateUserDeviceLocale(User $user, string $token, string $locale): int
    {
        return Device::query()
            ->where('user_id', $user->id)
            ->where('token', $this->normalizeToken($token))
            ->update(['locale' => $this->normalizeLocale($locale)]);
    }

    public function updateAdminDeviceLocale(Admin $admin, string $token, string $locale): int
    {
        return Device::query()
            ->where('admin_id', $admin->id)
            ->where('token', $this->normalizeToken($token))
            ->update(['locale' => $this->normalizeLocale($locale)]);
    }

    public function forgetUserDevice(User $user, ?string $token): void
    {
        if (blank($token)) {
            return;
        }

        Device::query()
            ->where('user_id', $user->id)
            ->where('token', $this->normalizeToken($token))
            ->delete();
    }

    public function forgetAllUserDevices(User $user): void
    {
        Device::query()
            ->where('user_id', $user->id)
            ->delete();
    }

    public function forgetAdminDevice(Admin $admin, ?string $token): void
    {
        if (blank($token)) {
            return;
        }

        Device::query()
            ->where('admin_id', $admin->id)
            ->where('token', $this->normalizeToken($token))
            ->delete();
    }

    public function getUserDevices(User $user, ?string $deviceType = null): Collection
    {
        return $this->getDevicesForOwner('user_id', $user->id, $deviceType);
    }

    public function getAdminDevices(Admin $admin, ?string $deviceType = null): Collection
    {
        return $this->getDevicesForOwner('admin_id', $admin->id, $deviceType);
    }

    private function syncDevice(string $token, ?string $deviceType, ?string $locale, array $attributes): Device
    {
        return Device::query()->updateOrCreate(
            ['token' => $this->normalizeToken($token)],
            $attributes + [
                'device_type' => DeviceType::normalize($deviceType)->value,
                'locale' => $this->normalizeLocale($locale),
            ],
        );
    }

    private function getDevicesForOwner(string $column, int $ownerId, ?string $deviceType = null): Collection
    {
        return Device::query()
            ->where($column, $ownerId)
            ->when(
                filled($deviceType),
                fn ($query) => $query->where('device_type', DeviceType::normalize($deviceType)->value)
            )
            ->get();
    }

    private function normalizeToken(string $token): string
    {
        return trim($token);
    }

    private function normalizeLocale(?string $locale): string
    {
        $normalized = strtolower(substr((string) $locale, 0, 2));

        return in_array($normalized, ['ar', 'en'], true) ? $normalized : app()->getLocale();
    }
}
