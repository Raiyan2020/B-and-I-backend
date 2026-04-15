<?php

namespace App\Services;

use App\Enums\ProfileUpdateRequestStatus;
use App\Enums\UserRole;
use App\Models\Admin;
use App\Models\Category;
use App\Models\PreferredSector;
use App\Models\ProfileUpdateRequest;
use App\Models\User;
use App\Traits\UploadTrait;
use BackedEnum;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ProfileUpdateRequestService
{
    use UploadTrait;

    public function latestForUser(User $user): ?ProfileUpdateRequest
    {
        return $user->profileUpdateRequests()
            ->with('user')
            ->latest('id')
            ->first();
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array{status: string, request?: ProfileUpdateRequest}
     */
    public function submit(User $user, array $data): array
    {
        if ($this->hasPendingRequest($user)) {
            return ['status' => 'pending_request_exists'];
        }

        $normalizedData = $this->normalizeSubmittedData($user, $data);
        $editableFields = $this->editableFieldsFor($user);
        $oldData = $this->snapshotFor($user, $editableFields);
        $newData = $this->mergeSnapshot($oldData, $normalizedData, $editableFields);

        $request = DB::transaction(function () use ($user, $oldData, $newData) {
            return $user->profileUpdateRequests()->create([
                'status' => ProfileUpdateRequestStatus::Pending,
                'old_data' => $oldData,
                'new_data' => $newData,
            ]);
        });

        return [
            'status' => 'submitted',
            'request' => $request->fresh(['user']),
        ];
    }

    public function approve(Admin $admin, ProfileUpdateRequest $profileUpdateRequest): ProfileUpdateRequest
    {
        return DB::transaction(function () use ($admin, $profileUpdateRequest) {
            $profileUpdateRequest->refresh();

            if ($profileUpdateRequest->status !== ProfileUpdateRequestStatus::Pending) {
                throw ValidationException::withMessages([
                    'status' => [__('dashboard.profile_update_request_already_reviewed')],
                ]);
            }

            $user = $profileUpdateRequest->user()->lockForUpdate()->firstOrFail();
            $newData = $profileUpdateRequest->new_data ?? [];

            if (array_key_exists('first_name', $newData) || array_key_exists('last_name', $newData)) {
                $newData['display_name'] = trim(implode(' ', array_filter([
                    $newData['first_name'] ?? $user->first_name,
                    $newData['last_name'] ?? $user->last_name,
                ])));
            }

            $user->update($newData);

            $profileUpdateRequest->update([
                'status' => ProfileUpdateRequestStatus::Approved,
                'rejection_reason' => null,
                'reviewed_by_admin_id' => $admin->id,
                'reviewed_at' => now(),
            ]);

            return $profileUpdateRequest->fresh(['user', 'reviewer']);
        });
    }

    public function reject(Admin $admin, ProfileUpdateRequest $profileUpdateRequest, string $reason): ProfileUpdateRequest
    {
        return DB::transaction(function () use ($admin, $profileUpdateRequest, $reason) {
            $profileUpdateRequest->refresh();

            if ($profileUpdateRequest->status !== ProfileUpdateRequestStatus::Pending) {
                throw ValidationException::withMessages([
                    'status' => [__('dashboard.profile_update_request_already_reviewed')],
                ]);
            }

            $profileUpdateRequest->update([
                'status' => ProfileUpdateRequestStatus::Rejected,
                'rejection_reason' => $reason,
                'reviewed_by_admin_id' => $admin->id,
                'reviewed_at' => now(),
            ]);

            return $profileUpdateRequest->fresh(['user', 'reviewer']);
        });
    }

    public function hasPendingRequest(User $user): bool
    {
        return $user->profileUpdateRequests()
            ->where('status', ProfileUpdateRequestStatus::Pending)
            ->exists();
    }

    /**
     * @return array<int, string>
     */
    public function editableFieldsFor(User $user): array
    {
        $common = [
            'image',
            'first_name',
            'last_name',
            'country_code',
            'phone',
        ];

        if ($user->role === UserRole::Investor) {
            return array_merge($common, [
                'investor_type',
                'capital',
                'available_capital',
                'preferred_sector_id',
                'category_id',
                'experience_level',
                'previous_investments_count',
                'investor_experience',
            ]);
        }

        return array_merge($common, [
            'company_license',
        ]);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function normalizeSubmittedData(User $user, array $data): array
    {
        if ($user->role !== UserRole::Investor) {
            unset(
                $data['available_capital'],
                $data['preferred_sector_id'],
                $data['category_id'],
                $data['investor_experience'],
                $data['investor_type'],
                $data['capital'],
                $data['experience_level'],
                $data['previous_investments_count'],
            );
        } else {
            unset($data['company_license']);

            if (array_key_exists('available_capital', $data) && ! array_key_exists('capital', $data)) {
                $data['capital'] = $data['available_capital'];
            }
        }

        if (($data['image'] ?? null) instanceof UploadedFile) {
            $data['image'] = $this->uploadAllTypes($data['image'], User::FOLDER);
        } else {
            unset($data['image']);
        }

        if (($data['company_license'] ?? null) instanceof UploadedFile) {
            $data['company_license'] = $this->uploadAllTypes($data['company_license'], User::FOLDER);
        } else {
            unset($data['company_license']);
        }

        return $data;
    }

    /**
     * @param  array<int, string>  $editableFields
     * @return array<string, mixed>
     */
    public function snapshotFor(User $user, array $editableFields): array
    {
        $snapshot = [];

        foreach ($editableFields as $field) {
            $snapshot[$field] = $this->currentUserValue($user, $field);
        }

        return $snapshot;
    }

    /**
     * @param  array<string, mixed>  $oldData
     * @param  array<string, mixed>  $normalizedData
     * @param  array<int, string>  $editableFields
     * @return array<string, mixed>
     */
    public function mergeSnapshot(array $oldData, array $normalizedData, array $editableFields): array
    {
        $newData = $oldData;

        foreach ($editableFields as $field) {
            if (array_key_exists($field, $normalizedData)) {
                $newData[$field] = $normalizedData[$field];
            }
        }

        return $newData;
    }

    public function displayValue(string $field, mixed $value): string
    {
        if ($value === null || $value === '') {
            return __('dashboard.not_available');
        }

        return match ($field) {
            'investor_type' => __('enums.investor_type.'.(string) $value),
            'investor_experience' => __('enums.investor_experience.'.(string) $value),
            'preferred_sector_id' => PreferredSector::query()->find($value)?->name ?? (string) $value,
            'category_id' => Category::query()->find($value)?->name ?? (string) $value,
            default => (string) $value,
        };
    }

    /**
     * @return array<string, mixed>
     */
    public function changedPayload(ProfileUpdateRequest $profileUpdateRequest): array
    {
        $user = $profileUpdateRequest->relationLoaded('user')
            ? $profileUpdateRequest->user
            : $profileUpdateRequest->user()->firstOrFail();

        $payload = [];

        foreach ($this->editableFieldsFor($user) as $field) {
            $oldValue = data_get($profileUpdateRequest->old_data, $field);
            $newValue = data_get($profileUpdateRequest->new_data, $field);

            if (! $this->valuesDiffer($oldValue, $newValue)) {
                continue;
            }

            $payload = array_merge($payload, $this->mapFieldToApiPayload($field, $newValue));
        }

        if (array_key_exists('country_code', $payload) || array_key_exists('phone', $payload)) {
            $payload['full_phone'] = trim(implode('', array_filter([
                $payload['country_code'] ?? data_get($profileUpdateRequest->new_data, 'country_code'),
                $payload['phone'] ?? data_get($profileUpdateRequest->new_data, 'phone'),
            ])));
        }

        return $payload;
    }

    public function valuesDiffer(mixed $oldValue, mixed $newValue): bool
    {
        return (string) ($oldValue ?? '') !== (string) ($newValue ?? '');
    }

    /**
     * @return array<string, mixed>
     */
    private function mapFieldToApiPayload(string $field, mixed $value): array
    {
        return match ($field) {
            'image' => [
                'image' => blank($value)
                    ? null
                    : User::getImage((string) $value, User::FOLDER),
            ],
            'company_license' => [
                'company_license_url' => blank($value)
                    ? null
                    : User::getImage((string) $value, User::FOLDER),
            ],
            'investor_type' => [
                'investor_type' => [
                    'key' => (string) $value,
                    'label' => blank($value) ? null : __('enums.investor_type.'.(string) $value),
                ],
            ],
            'investor_experience' => [
                'investor_experience' => [
                    'key' => (string) $value,
                    'label' => blank($value) ? null : __('enums.investor_experience.'.(string) $value),
                ],
            ],
            'preferred_sector_id' => [
                'preferred_sector' => $value ? [
                    'id' => (int) $value,
                    'name' => PreferredSector::query()->find($value)?->name,
                ] : null,
                'preferred_sector_id' => $value ? (int) $value : null,
            ],
            'category_id' => [
                'category' => $value ? [
                    'id' => (int) $value,
                    'name' => Category::query()->find($value)?->name,
                ] : null,
                'category_id' => $value ? (int) $value : null,
            ],
            'capital',
            'available_capital',
            'experience_level' => [
                $field => $value !== null ? (float) $value : null,
            ],
            'previous_investments_count' => [
                $field => $value !== null ? (int) $value : null,
            ],
            default => [
                $field => $value,
            ],
        };
    }

    private function currentUserValue(User $user, string $field): mixed
    {
        if (in_array($field, ['image', 'company_license'], true)) {
            return $user->getRawOriginal($field);
        }

        $value = $user->{$field};

        if ($value instanceof BackedEnum) {
            return $value->value;
        }

        return $value;
    }
}
