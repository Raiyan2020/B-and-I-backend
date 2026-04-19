<?php

namespace App\Http\Controllers\Dashboard;

use App\Enums\ProfileUpdateRequestStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\ProfileUpdateRequests\ReviewRequest;
use App\Models\ProfileUpdateRequest;
use App\Models\User;
use App\Services\ProfileUpdateRequestService;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class ProfileUpdateRequestController extends Controller
{
    public function __construct(private readonly ProfileUpdateRequestService $profileUpdateRequestService)
    {
        $this->middleware('permission:profile-update-requests', ['only' => ['show']]);
        $this->middleware('permission:review-profile-update-request', ['only' => ['review']]);
    }

    public function show(ProfileUpdateRequest $profileUpdateRequest): View
    {
        $profileUpdateRequest->load(['user', 'reviewer']);
        $user = $profileUpdateRequest->user;
        $fields = $this->profileUpdateRequestService->editableFieldsFor($user);

        return view('dashboard.profile_update_requests.show', [
            'row' => $profileUpdateRequest,
            'user' => $user,
            'comparisonRows' => $this->buildComparisonRows($profileUpdateRequest, $fields),
            'history' => $user->profileUpdateRequests()
                ->whereKeyNot($profileUpdateRequest->id)
                ->latest()
                ->get(),
            'listTitle' => $user->isInvestor()
                ? __('dashboard.investors_list')
                : __('dashboard.advertisers_companies_list'),
            'indexRouteName' => $user->isInvestor()
                ? 'admin.investors.index'
                : 'admin.advertisers.index',
        ]);
    }

    public function review(ReviewRequest $request, ProfileUpdateRequest $profileUpdateRequest): JsonResponse
    {
        $status = ProfileUpdateRequestStatus::from($request->validated('status'));

        if ($status === ProfileUpdateRequestStatus::Approved) {
            $this->profileUpdateRequestService->approve(auth('admin')->user(), $profileUpdateRequest);
        } else {
            $this->profileUpdateRequestService->reject(
                auth('admin')->user(),
                $profileUpdateRequest,
                (string) $request->validated('rejection_reason')
            );
        }

        return response()->json([
            'key' => 'success',
            'msg' => __('dashboard.profile_update_request_review_saved'),
            'url' => route('admin.profile-update-requests.show', $profileUpdateRequest),
        ]);
    }

    /**
     * @param  array<int, string>  $fields
     * @return array<int, array<string, mixed>>
     */
    private function buildComparisonRows(ProfileUpdateRequest $profileUpdateRequest, array $fields): array
    {
        $rows = [];

        foreach ($fields as $field) {
            $oldValue = data_get($profileUpdateRequest->old_data, $field);
            $newValue = data_get($profileUpdateRequest->new_data, $field);

            $rows[] = [
                'key' => $field,
                'label' => $this->fieldLabel($field),
                'type' => in_array($field, ['image', 'company_license'], true) ? 'file' : 'text',
                'changed' => $this->profileUpdateRequestService->valuesDiffer($oldValue, $newValue),
                'old_value' => $oldValue,
                'new_value' => $newValue,
                'old_display' => $this->profileUpdateRequestService->displayValue($field, $oldValue),
                'new_display' => $this->profileUpdateRequestService->displayValue($field, $newValue),
                'old_url' => $this->fileUrl($field, $oldValue),
                'new_url' => $this->fileUrl($field, $newValue),
            ];
        }

        return $rows;
    }

    private function fieldLabel(string $field): string
    {
        return match ($field) {
            'image' => __('dashboard.table image'),
            'first_name' => __('dashboard.table first name'),
            'last_name' => __('dashboard.table last name'),
            'country_code' => __('dashboard.country code'),
            'phone' => __('dashboard.table phone'),
            'company_license' => __('dashboard.table company license'),
            'investor_type' => __('dashboard.investor_type'),
            'capital' => __('dashboard.capital'),
            'available_capital' => __('dashboard.available_capital'),
            'preferred_sector_id' => __('dashboard.preferred_sectors'),
            'category_id' => __('dashboard.category'),
            'experience_level' => __('dashboard.experience_level'),
            'previous_investments_count' => __('dashboard.previous_investments_count'),
            'investor_experience' => __('dashboard.investor_experience'),
            default => str($field)->replace('_', ' ')->title()->toString(),
        };
    }

    private function fileUrl(string $field, mixed $value): ?string
    {
        if (blank($value)) {
            return null;
        }

        return match ($field) {
            'image', 'company_license' => User::getImage((string) $value, User::FOLDER),
            default => null,
        };
    }
}
