<?php

namespace App\Enums;

enum NotificationType: string
{
    case Generic = 'generic';
    case LegacyNotification = 'legacy_notification';
    case UserNotification = 'user_notification';
    case AdminNotification = 'admin_notification';
    case UserBlocked = 'user_blocked';
    case UserDeactivated = 'user_deactivated';
    case AdminBlocked = 'admin_blocked';
    case RegisterUserForAdmin = 'register_user_for_admin';
    case CreateProfileUpdateRequestForAdmin = 'create_profile_update_request_for_admin';
    case ApproveProfileUpdateRequest = 'approve_profile_update_request';
    case RejectProfileUpdateRequest = 'reject_profile_update_request';
    case CreateAccountDeletionRequestForAdmin = 'create_account_deletion_request_for_admin';
    case ApproveAccountDeletionRequest = 'approve_account_deletion_request';
    case RejectAccountDeletionRequest = 'reject_account_deletion_request';
    case CreateOpportunityForAdmin = 'create_opportunity_for_admin';
    case UpdateOpportunityForAdmin = 'update_opportunity_for_admin';
    case ChangeOpportunityStatus = 'change_opportunity_status';
    case PublishOpportunity = 'publish_opportunity';
    case CreateCompanyInvestorInterestForAdmin = 'create_company_investor_interest_for_admin';
    case PurchaseInvestmentSeatForAdmin = 'purchase_investment_seat_for_admin';
    case PurchaseOpportunityBooklet = 'purchase_opportunity_booklet';
    case CreateInterestRequestForAdmin = 'create_interest_request_for_admin';
    case CreateInterestRequestForOpportunityOwner = 'create_interest_request_for_opportunity_owner';

    public static function normalize(self|string|null $value): self
    {
        if ($value instanceof self) {
            return $value;
        }

        $value = (string) $value;

        return self::tryFrom($value)
            ?? self::tryFrom(self::legacyMap()[$value] ?? '')
            ?? self::Generic;
    }

    /**
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * @return array<string, string>
     */
    private static function legacyMap(): array
    {
        return [
            'legacy.notification' => self::LegacyNotification->value,
            'admin.user.registered' => self::RegisterUserForAdmin->value,
            'admin.profile_update_request.created' => self::CreateProfileUpdateRequestForAdmin->value,
            'profile_update_request.approved' => self::ApproveProfileUpdateRequest->value,
            'profile_update_request.rejected' => self::RejectProfileUpdateRequest->value,
            'admin.account_deletion_request.created' => self::CreateAccountDeletionRequestForAdmin->value,
            'account_deletion_request.approved' => self::ApproveAccountDeletionRequest->value,
            'account_deletion_request.rejected' => self::RejectAccountDeletionRequest->value,
            'admin.opportunity.created' => self::CreateOpportunityForAdmin->value,
            'admin.opportunity.updated' => self::UpdateOpportunityForAdmin->value,
            'opportunity.status_changed' => self::ChangeOpportunityStatus->value,
            'opportunity.published' => self::PublishOpportunity->value,
            'admin.company_investor_interest.created' => self::CreateCompanyInvestorInterestForAdmin->value,
            'admin.investment_seat.purchased' => self::PurchaseInvestmentSeatForAdmin->value,
            'opportunity.booklet_purchased' => self::PurchaseOpportunityBooklet->value,
            'admin.interest_request.created' => self::CreateInterestRequestForAdmin->value,
            'opportunity.interest_request.created' => self::CreateInterestRequestForOpportunityOwner->value,
        ];
    }
}
