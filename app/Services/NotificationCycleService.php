<?php

namespace App\Services;

use App\Enums\NotificationCategory;
use App\Enums\NotificationType;
use App\Enums\OpportunityStatus;
use App\Enums\UserRole;
use App\Models\AccountDeletionRequest;
use App\Models\Admin;
use App\Models\CompanyInvestorInterestRequest;
use App\Models\InterestRequest;
use App\Models\InvestmentSeat;
use App\Models\Opportunity;
use App\Models\ProfileUpdateRequest;
use App\Models\User;
use App\Notifications\GeneralNotification;
use App\Services\Notifications\GeneralNotificationService;

class NotificationCycleService
{
    public function __construct(
        private readonly GeneralNotificationService $notifications,
    ) {}

    public function adminNewUserRegistered(User $user): void
    {
        $role = $this->roleLabel($user);

        $this->sendToAdmins(new GeneralNotification(
            title: [
                'ar' => "تسجيل {$role['ar']} جديد",
                'en' => "New {$role['en']} registration",
            ],
            body: [
                'ar' => "تم تسجيل {$role['ar']} جديد: {$this->userName($user)}.",
                'en' => "A new {$role['en']} has registered: {$this->userName($user)}.",
            ],
            notificationType: NotificationType::RegisterUserForAdmin,
            category: NotificationCategory::System,
            model: $user,
            payload: [
                'user_id' => $user->id,
                'role' => $user->role?->value,
            ],
        ));
    }

    public function adminProfileUpdateSubmitted(ProfileUpdateRequest $request): void
    {
        $request->loadMissing('user');

        $this->sendToAdmins(new GeneralNotification(
            title: [
                'ar' => 'طلب تعديل ملف شخصي جديد',
                'en' => 'New profile update request',
            ],
            body: [
                'ar' => "قام {$this->userName($request->user)} بإرسال طلب تعديل بياناته.",
                'en' => "{$this->userName($request->user)} submitted a profile update request.",
            ],
            notificationType: NotificationType::CreateProfileUpdateRequestForAdmin,
            category: NotificationCategory::System,
            model: $request,
            payload: [
                'profile_update_request_id' => $request->id,
                'user_id' => $request->user_id,
            ],
        ));
    }

    public function userProfileUpdateReviewed(ProfileUpdateRequest $request): void
    {
        $request->loadMissing('user');

        if (! $request->user) {
            return;
        }

        $approved = $request->status?->value === 'approved';
        $reason = $request->rejection_reason;

        $this->notifications->sendToUser($request->user, new GeneralNotification(
            title: [
                'ar' => $approved ? 'تم قبول طلب تعديل الملف الشخصي' : 'تم رفض طلب تعديل الملف الشخصي',
                'en' => $approved ? 'Profile update request approved' : 'Profile update request rejected',
            ],
            body: [
                'ar' => $approved
                    ? 'تمت الموافقة على طلب تعديل بياناتك.'
                    : 'تم رفض طلب تعديل بياناتك.'.($reason ? " السبب: {$reason}" : ''),
                'en' => $approved
                    ? 'Your profile update request has been approved.'
                    : 'Your profile update request has been rejected.'.($reason ? " Reason: {$reason}" : ''),
            ],
            notificationType: $approved ? NotificationType::ApproveProfileUpdateRequest : NotificationType::RejectProfileUpdateRequest,
            category: NotificationCategory::System,
            model: $request,
            payload: [
                'profile_update_request_id' => $request->id,
                'rejection_reason' => $reason,
            ],
        ));
    }

    public function adminAccountDeletionSubmitted(AccountDeletionRequest $request): void
    {
        $request->loadMissing('user');

        $this->sendToAdmins(new GeneralNotification(
            title: [
                'ar' => 'طلب حذف حساب جديد',
                'en' => 'New account deletion request',
            ],
            body: [
                'ar' => "قام {$this->userName($request->user)} بإرسال طلب حذف الحساب.",
                'en' => "{$this->userName($request->user)} submitted an account deletion request.",
            ],
            notificationType: NotificationType::CreateAccountDeletionRequestForAdmin,
            category: NotificationCategory::System,
            model: $request,
            payload: [
                'account_deletion_request_id' => $request->id,
                'user_id' => $request->user_id,
            ],
        ));
    }

    public function userAccountDeletionApproved(AccountDeletionRequest $request): void
    {
        $request->loadMissing('user');

        if (! $request->user) {
            return;
        }

        $this->notifications->sendToUser($request->user, new GeneralNotification(
            title: [
                'ar' => 'تم قبول طلب حذف الحساب',
                'en' => 'Account deletion request approved',
            ],
            body: [
                'ar' => 'تمت الموافقة على طلب حذف حسابك، وسيتم تسجيل خروجك من جميع الأجهزة.',
                'en' => 'Your account deletion request has been approved, and you will be logged out from all devices.',
            ],
            notificationType: NotificationType::ApproveAccountDeletionRequest,
            category: NotificationCategory::System,
            model: $request,
            payload: [
                'account_deletion_request_id' => $request->id,
            ],
        ));
    }

    public function userAccountDeletionRejected(AccountDeletionRequest $request, string $reason): void
    {
        $request->loadMissing('user');

        if (! $request->user) {
            return;
        }

        $this->notifications->sendToUser($request->user, new GeneralNotification(
            title: [
                'ar' => 'تم رفض طلب حذف الحساب',
                'en' => 'Account deletion request rejected',
            ],
            body: [
                'ar' => "تم رفض طلب حذف الحساب. السبب: {$reason}",
                'en' => "Your account deletion request has been rejected. Reason: {$reason}",
            ],
            notificationType: NotificationType::RejectAccountDeletionRequest,
            category: NotificationCategory::System,
            model: $request,
            payload: [
                'account_deletion_request_id' => $request->id,
                'rejection_reason' => $reason,
            ],
        ));
    }

    public function adminOpportunityCreated(Opportunity $opportunity): void
    {
        $opportunity->loadMissing('user');

        $this->sendToAdmins(new GeneralNotification(
            title: [
                'ar' => 'إعلان جديد بانتظار المراجعة',
                'en' => 'New opportunity pending review',
            ],
            body: [
                'ar' => "قام {$this->userName($opportunity->user)} بإضافة إعلان جديد: {$this->opportunityName($opportunity)}.",
                'en' => "{$this->userName($opportunity->user)} created a new opportunity: {$this->opportunityName($opportunity)}.",
            ],
            notificationType: NotificationType::CreateOpportunityForAdmin,
            category: NotificationCategory::System,
            model: $opportunity,
            payload: [
                'opportunity_id' => $opportunity->id,
                'owner_id' => $opportunity->user_id,
            ],
        ));
    }

    public function adminOpportunityUpdated(Opportunity $opportunity): void
    {
        $opportunity->loadMissing('user');

        $this->sendToAdmins(new GeneralNotification(
            title: [
                'ar' => 'تعديل إعلان بانتظار المراجعة',
                'en' => 'Opportunity update pending review',
            ],
            body: [
                'ar' => "قام {$this->userName($opportunity->user)} بتعديل الإعلان: {$this->opportunityName($opportunity)}.",
                'en' => "{$this->userName($opportunity->user)} updated the opportunity: {$this->opportunityName($opportunity)}.",
            ],
            notificationType: NotificationType::UpdateOpportunityForAdmin,
            category: NotificationCategory::System,
            model: $opportunity,
            payload: [
                'opportunity_id' => $opportunity->id,
                'owner_id' => $opportunity->user_id,
            ],
        ));
    }

    public function userOpportunityStatusChanged(Opportunity $opportunity): void
    {
        $opportunity->loadMissing('user');

        if (! $opportunity->user) {
            return;
        }

        $status = $this->statusLabel($opportunity->status);

        $this->notifications->sendToUser($opportunity->user, new GeneralNotification(
            title: [
                'ar' => 'تم تحديث حالة إعلانك',
                'en' => 'Your opportunity status changed',
            ],
            body: [
                'ar' => "حالة الإعلان {$this->opportunityName($opportunity)} أصبحت: {$status['ar']}.",
                'en' => "The status of {$this->opportunityName($opportunity)} is now: {$status['en']}.",
            ],
            notificationType: NotificationType::ChangeOpportunityStatus,
            category: NotificationCategory::System,
            model: $opportunity,
            payload: [
                'opportunity_id' => $opportunity->id,
                'status' => $opportunity->status?->value,
                'review_note' => $opportunity->review_note,
            ],
        ));
    }

    public function usersOpportunityPublished(Opportunity $opportunity): void
    {
        $users = User::query()
            ->whereKeyNot($opportunity->user_id)
            ->where('is_active', true)
            ->where('is_blocked', false)
            ->get();

        $this->notifications->sendToUsers($users, new GeneralNotification(
            title: [
                'ar' => 'إعلان استثماري جديد',
                'en' => 'New investment opportunity',
            ],
            body: [
                'ar' => "تم نشر إعلان جديد: {$this->opportunityName($opportunity)}.",
                'en' => "A new opportunity has been published: {$this->opportunityName($opportunity)}.",
            ],
            notificationType: NotificationType::PublishOpportunity,
            category: NotificationCategory::System,
            model: $opportunity,
            payload: [
                'opportunity_id' => $opportunity->id,
            ],
        ));
    }

    public function adminCompanyInvestorInterestCreated(CompanyInvestorInterestRequest $request): void
    {
        $request->loadMissing(['company', 'investor']);

        $this->sendToAdmins(new GeneralNotification(
            title: [
                'ar' => 'طلب اهتمام شركة بمستثمر',
                'en' => 'Company interest in investor',
            ],
            body: [
                'ar' => "قامت {$this->userName($request->company)} بإرسال طلب اهتمام بالمستثمر {$this->userName($request->investor)}.",
                'en' => "{$this->userName($request->company)} submitted interest in investor {$this->userName($request->investor)}.",
            ],
            notificationType: NotificationType::CreateCompanyInvestorInterestForAdmin,
            category: NotificationCategory::Interest,
            model: $request,
            payload: [
                'company_investor_interest_request_id' => $request->id,
                'company_id' => $request->company_id,
                'investor_id' => $request->investor_id,
            ],
        ));
    }

    public function adminInvestmentSeatPurchased(InvestmentSeat $seat): void
    {
        $seat->loadMissing(['user', 'opportunity.user']);

        $this->sendToAdmins(new GeneralNotification(
            title: [
                'ar' => 'شراء كراسة استثمار',
                'en' => 'Investment booklet purchased',
            ],
            body: [
                'ar' => "قام {$this->userName($seat->user)} بشراء كراسة للإعلان {$this->opportunityName($seat->opportunity)}.",
                'en' => "{$this->userName($seat->user)} purchased a booklet for {$this->opportunityName($seat->opportunity)}.",
            ],
            notificationType: NotificationType::PurchaseInvestmentSeatForAdmin,
            category: NotificationCategory::Interest,
            model: $seat,
            payload: [
                'investment_seat_id' => $seat->id,
                'opportunity_id' => $seat->opportunity_id,
                'buyer_id' => $seat->user_id,
            ],
        ));
    }

    public function opportunityOwnerSeatPurchased(InvestmentSeat $seat): void
    {
        $seat->loadMissing(['user', 'opportunity.user']);
        $opportunity = $seat->opportunity;

        if (! $opportunity?->user || $opportunity->user_id === $seat->user_id) {
            return;
        }

        $this->notifications->sendToUser($opportunity->user, new GeneralNotification(
            title: [
                'ar' => 'تم شراء كراسة لإعلانك',
                'en' => 'Your opportunity booklet was purchased',
            ],
            body: [
                'ar' => "قام {$this->userName($seat->user)} بشراء كراسة الإعلان {$this->opportunityName($opportunity)}.",
                'en' => "{$this->userName($seat->user)} purchased the booklet for {$this->opportunityName($opportunity)}.",
            ],
            notificationType: NotificationType::PurchaseOpportunityBooklet,
            category: NotificationCategory::Interest,
            model: $opportunity,
            payload: [
                'opportunity_id' => $opportunity->id,
                'investment_seat_id' => $seat->id,
                'buyer_id' => $seat->user_id,
            ],
        ));
    }

    public function adminInterestRequestSubmitted(InterestRequest $interestRequest): void
    {
        $interestRequest->loadMissing(['user', 'opportunity']);

        $this->sendToAdmins(new GeneralNotification(
            title: [
                'ar' => 'طلب اهتمام جديد',
                'en' => 'New interest request',
            ],
            body: [
                'ar' => "قام {$this->userName($interestRequest->user)} بإرسال طلب اهتمام للإعلان {$this->opportunityName($interestRequest->opportunity)}.",
                'en' => "{$this->userName($interestRequest->user)} submitted interest in {$this->opportunityName($interestRequest->opportunity)}.",
            ],
            notificationType: NotificationType::CreateInterestRequestForAdmin,
            category: NotificationCategory::Interest,
            model: $interestRequest,
            payload: [
                'interest_request_id' => $interestRequest->id,
                'opportunity_id' => $interestRequest->opportunity_id,
                'requester_id' => $interestRequest->user_id,
            ],
        ));
    }

    public function opportunityOwnerInterestSubmitted(InterestRequest $interestRequest): void
    {
        $interestRequest->loadMissing(['user', 'opportunity.user']);
        $opportunity = $interestRequest->opportunity;

        if (! $opportunity?->user || $opportunity->user_id === $interestRequest->user_id) {
            return;
        }

        $this->notifications->sendToUser($opportunity->user, new GeneralNotification(
            title: [
                'ar' => 'طلب اهتمام جديد على إعلانك',
                'en' => 'New interest in your opportunity',
            ],
            body: [
                'ar' => "قام {$this->userName($interestRequest->user)} بإرسال طلب اهتمام على الإعلان {$this->opportunityName($opportunity)}.",
                'en' => "{$this->userName($interestRequest->user)} submitted interest in {$this->opportunityName($opportunity)}.",
            ],
            notificationType: NotificationType::CreateInterestRequestForOpportunityOwner,
            category: NotificationCategory::Interest,
            model: $opportunity,
            payload: [
                'opportunity_id' => $opportunity->id,
                'interest_request_id' => $interestRequest->id,
                'requester_id' => $interestRequest->user_id,
            ],
        ));
    }

    private function sendToAdmins(GeneralNotification $notification): void
    {
        $admins = Admin::query()
            ->where('is_blocked', false)
            ->get();

        $this->notifications->sendToAdmins($admins, $notification);
    }

    /**
     * @return array{ar: string, en: string}
     */
    private function roleLabel(?User $user): array
    {
        return match ($user?->role) {
            UserRole::Investor => ['ar' => 'مستثمر', 'en' => 'investor'],
            UserRole::Advertiser => ['ar' => 'معلن', 'en' => 'advertiser'],
            default => ['ar' => 'مستخدم', 'en' => 'user'],
        };
    }

    /**
     * @return array{ar: string, en: string}
     */
    private function statusLabel(OpportunityStatus|string|null $status): array
    {
        $value = $status instanceof OpportunityStatus ? $status->value : $status;

        return match ($value) {
            OpportunityStatus::Pending->value => ['ar' => 'قيد المراجعة', 'en' => 'Pending review'],
            OpportunityStatus::NeedsRevision->value => ['ar' => 'تحتاج تعديل', 'en' => 'Needs revision'],
            OpportunityStatus::Published->value => ['ar' => 'منشور', 'en' => 'Published'],
            OpportunityStatus::Reserved->value => ['ar' => 'محجوز', 'en' => 'Reserved'],
            OpportunityStatus::Completed->value => ['ar' => 'مكتمل', 'en' => 'Completed'],
            default => ['ar' => 'غير محدد', 'en' => 'Unknown'],
        };
    }

    private function userName(?User $user): string
    {
        return $user?->name ?: $user?->email ?: __('dashboard.not_available');
    }

    private function opportunityName(?Opportunity $opportunity): string
    {
        return $opportunity?->company_name ?: __('dashboard.not_available');
    }
}
