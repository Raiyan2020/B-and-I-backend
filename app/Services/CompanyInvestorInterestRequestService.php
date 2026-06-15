<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Models\CompanyInvestorInterestRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class CompanyInvestorInterestRequestService
{
    public function __construct(
        private readonly NotificationCycleService $notificationCycleService,
    ) {}

    public function create(User $company, int $investorId): CompanyInvestorInterestRequest
    {
        if ($company->role !== UserRole::Advertiser) {
            throw new AccessDeniedHttpException(__('apis.have_no_permission'));
        }

        if (CompanyInvestorInterestRequest::query()
            ->where('company_id', $company->id)
            ->where('investor_id', $investorId)
            ->exists()) {
            throw ValidationException::withMessages([
                'investor_id' => [__('apis.company_investor_interest_already_submitted')],
            ]);
        }

        $interestRequest = DB::transaction(function () use ($company, $investorId) {
            return CompanyInvestorInterestRequest::query()->create([
                'company_id' => $company->id,
                'investor_id' => $investorId,
            ]);
        });

        DB::afterCommit(fn () => $this->notificationCycleService->adminCompanyInvestorInterestCreated(
            $interestRequest->fresh(['company', 'investor'])
        ));

        return $interestRequest;
    }
}
