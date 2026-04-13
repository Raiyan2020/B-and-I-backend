<?php

namespace Database\Seeders;

use App\Enums\DeviceType;
use App\Enums\NotificationCategory;
use App\Enums\OpportunityGoal;
use App\Enums\OpportunityStatus;
use App\Enums\UserRole;
use App\Enums\WalletTransactionTypeEnum;
use App\Models\Admin;
use App\Models\AuthUpdate;
use App\Models\Category;
use App\Models\Device;
use App\Models\FcmToken;
use App\Models\GeneralSetting;
use App\Models\InterestRequest;
use App\Models\InvestmentSeat;
use App\Models\Notification;
use App\Models\Opportunity;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Database\Seeder;

class MarketplaceScenarioSeeder extends Seeder
{
    public function run(): void
    {
        $admins = Admin::query()->orderBy('id')->get()->values();
        $advertisers = User::query()->where('role', UserRole::Advertiser)->orderBy('id')->get()->values();
        $investors = User::query()->where('role', UserRole::Investor)->orderBy('id')->get()->values();
        $categories = Category::query()->orderBy('order')->get()->values();
        $seatPrice = (float) (GeneralSetting::getValueForKey('seat_price') ?? 2500);

        $opportunities = [
            'artisan_roastery' => Opportunity::query()->create([
                'user_id' => $advertisers[0]->id,
                'category_id' => $categories[0]->id,
                'goal' => OpportunityGoal::RequestInvestment,
                'status' => OpportunityStatus::Pending,
                'contact_name' => 'Noura AlSaleh',
                'contact_phone' => '+96550123510',
                'contact_email' => 'deals@nourahospitality.test',
                'owner_name' => 'Noura AlSaleh',
                'admin_company_name' => 'Noura Hospitality Holding',
                'license_number' => 'HOSP-KW-24001',
                'company_name' => 'Artisan Roastery Expansion',
                'business_age_years' => 4,
                'investment_required' => 180000.000,
                'business_stage' => 'Operating and expanding to a second branch',
                'sale_percentage' => 18.00,
                'legal_entity' => 'Limited Liability Company',
                'financial_status' => 'Profitable for the last 18 months with stable gross margins',
                'investment_reason' => 'Capital is needed to fund a central kitchen and a new high-footfall branch.',
                'full_description' => 'A specialty coffee and bakery concept with proven unit economics, strong repeat customers, and signed supplier agreements.',
                'created_at' => now()->subDays(2),
                'updated_at' => now()->subDays(2),
            ]),
            'fleetops' => Opportunity::query()->create([
                'user_id' => $advertisers[1]->id,
                'category_id' => $categories[1]->id,
                'reviewed_by_admin_id' => $admins[0]->id,
                'goal' => OpportunityGoal::RequestInvestment,
                'status' => OpportunityStatus::NeedsRevision,
                'contact_name' => 'Faisal AlRashid',
                'contact_phone' => '+96550123511',
                'contact_email' => 'finance@faisalventures.test',
                'owner_name' => 'Faisal AlRashid',
                'admin_company_name' => 'Faisal Ventures Group',
                'license_number' => 'TECH-KW-24018',
                'company_name' => 'FleetOps Dispatch Platform',
                'business_age_years' => 2,
                'investment_required' => 250000.000,
                'business_stage' => 'Live product with 11 paid B2B clients',
                'sale_percentage' => 22.50,
                'legal_entity' => 'Closed Shareholding Company',
                'financial_status' => 'Growing annual recurring revenue but needs cleaner cohort reporting',
                'investment_reason' => 'Seeking capital to expand sales and improve product analytics.',
                'full_description' => 'FleetOps is a dispatch and route-optimization SaaS built for regional logistics operators and field-service fleets.',
                'review_note' => 'Please update the financial KPI section and clarify current client churn.',
                'reviewed_at' => now()->subDay(),
                'created_at' => now()->subDays(9),
                'updated_at' => now()->subDay(),
            ]),
            'wellness_clinic' => Opportunity::query()->create([
                'user_id' => $advertisers[2]->id,
                'category_id' => $categories[3]->id,
                'reviewed_by_admin_id' => $admins[0]->id,
                'goal' => OpportunityGoal::RequestInvestment,
                'status' => OpportunityStatus::Published,
                'contact_name' => 'Reem AlAjmi',
                'contact_phone' => '+96550123512',
                'contact_email' => 'growth@reemhealthcare.test',
                'owner_name' => 'Reem AlAjmi',
                'admin_company_name' => 'Reem Healthcare Partners',
                'license_number' => 'MED-KW-24027',
                'company_name' => 'Wellness Clinic Rollout',
                'business_age_years' => 5,
                'investment_required' => 420000.000,
                'business_stage' => 'Two profitable clinics with expansion blueprint ready',
                'sale_percentage' => 20.00,
                'legal_entity' => 'Limited Liability Company',
                'financial_status' => 'Healthy margins, repeat patient base, and positive monthly operating cash flow',
                'investment_reason' => 'Funding is required to launch a third clinic and centralize diagnostics services.',
                'full_description' => 'Integrated outpatient wellness clinics focused on diagnostics, preventive care, and subscription-based health programs.',
                'reviewed_at' => now()->subDays(4),
                'created_at' => now()->subDays(12),
                'updated_at' => now()->subDays(4),
            ]),
            'smart_logistics' => Opportunity::query()->create([
                'user_id' => $advertisers[1]->id,
                'category_id' => $categories[2]->id,
                'reviewed_by_admin_id' => $admins[0]->id,
                'goal' => OpportunityGoal::SellBusiness,
                'status' => OpportunityStatus::Reserved,
                'contact_name' => 'Faisal AlRashid',
                'contact_phone' => '+96550123511',
                'contact_email' => 'mna@faisalventures.test',
                'owner_name' => 'Faisal AlRashid',
                'admin_company_name' => 'Faisal Ventures Logistics',
                'license_number' => 'LOG-KW-24044',
                'company_name' => 'Smart Logistics Hub',
                'business_age_years' => 7,
                'investment_required' => 780000.000,
                'business_stage' => 'Operating distribution hub with contracted last-mile clients',
                'sale_percentage' => null,
                'legal_entity' => 'Limited Liability Company',
                'financial_status' => 'Positive EBITDA with stable enterprise contracts and fleet utilization above 80%',
                'investment_reason' => 'Owner is considering a strategic exit or majority acquisition by an operating investor.',
                'full_description' => 'A logistics operator managing warehousing, dispatch, and last-mile coordination for regional retail clients.',
                'reviewed_at' => now()->subDays(6),
                'created_at' => now()->subDays(20),
                'updated_at' => now()->subDays(6),
            ]),
            'learning_centers' => Opportunity::query()->create([
                'user_id' => $advertisers[0]->id,
                'category_id' => $categories[4]->id,
                'reviewed_by_admin_id' => $admins[1]->id,
                'goal' => OpportunityGoal::RequestInvestment,
                'status' => OpportunityStatus::Completed,
                'contact_name' => 'Noura AlSaleh',
                'contact_phone' => '+96550123510',
                'contact_email' => 'projects@nourahospitality.test',
                'owner_name' => 'Noura AlSaleh',
                'admin_company_name' => 'Noura Education Ventures',
                'license_number' => 'EDU-KW-24063',
                'company_name' => 'NextStep Learning Centers',
                'business_age_years' => 6,
                'investment_required' => 360000.000,
                'business_stage' => 'Multi-branch tutoring and training operation',
                'sale_percentage' => 25.00,
                'legal_entity' => 'Limited Liability Company',
                'financial_status' => 'Deal completed after final payment and legal transfer process',
                'investment_reason' => 'Historical opportunity retained to demonstrate completed lifecycle records.',
                'full_description' => 'A network of after-school learning centers focused on math, languages, and exam preparation.',
                'reviewed_at' => now()->subDays(30),
                'created_at' => now()->subDays(45),
                'updated_at' => now()->subDays(10),
            ]),
            'precision_parts' => Opportunity::query()->create([
                'user_id' => $advertisers[2]->id,
                'category_id' => $categories[5]->id,
                'reviewed_by_admin_id' => $admins[1]->id,
                'goal' => OpportunityGoal::RequestInvestment,
                'status' => OpportunityStatus::Published,
                'contact_name' => 'Reem AlAjmi',
                'contact_phone' => '+96550123512',
                'contact_email' => 'industrial@reemhealthcare.test',
                'owner_name' => 'Reem AlAjmi',
                'admin_company_name' => 'Reem Industrial Partners',
                'license_number' => 'IND-KW-24072',
                'company_name' => 'Precision Parts Manufacturing',
                'business_age_years' => 8,
                'investment_required' => 510000.000,
                'business_stage' => 'Established operation supplying local industrial buyers',
                'sale_percentage' => 17.50,
                'legal_entity' => 'Limited Liability Company',
                'financial_status' => 'Stable contracted revenue and equipment base with room for margin expansion',
                'investment_reason' => 'Capital will fund CNC upgrades and expansion into higher-margin custom jobs.',
                'full_description' => 'A precision machining workshop serving industrial maintenance, oilfield contractors, and specialist fabrication requests.',
                'reviewed_at' => now()->subDays(5),
                'created_at' => now()->subDays(16),
                'updated_at' => now()->subDays(5),
            ]),
        ];

        $seats = [
            InvestmentSeat::query()->create([
                'user_id' => $investors[0]->id,
                'opportunity_id' => $opportunities['wellness_clinic']->id,
                'price_paid' => $seatPrice,
                'purchased_at' => now()->subDays(3),
                'created_at' => now()->subDays(3),
                'updated_at' => now()->subDays(3),
            ]),
            InvestmentSeat::query()->create([
                'user_id' => $investors[1]->id,
                'opportunity_id' => $opportunities['wellness_clinic']->id,
                'price_paid' => $seatPrice,
                'purchased_at' => now()->subDays(2),
                'created_at' => now()->subDays(2),
                'updated_at' => now()->subDays(2),
            ]),
            InvestmentSeat::query()->create([
                'user_id' => $investors[2]->id,
                'opportunity_id' => $opportunities['smart_logistics']->id,
                'price_paid' => $seatPrice,
                'purchased_at' => now()->subDays(5),
                'created_at' => now()->subDays(5),
                'updated_at' => now()->subDays(5),
            ]),
            InvestmentSeat::query()->create([
                'user_id' => $investors[3]->id,
                'opportunity_id' => $opportunities['precision_parts']->id,
                'price_paid' => $seatPrice,
                'purchased_at' => now()->subDay(),
                'created_at' => now()->subDay(),
                'updated_at' => now()->subDay(),
            ]),
        ];

        InterestRequest::query()->create([
            'user_id' => $investors[1]->id,
            'opportunity_id' => $opportunities['wellness_clinic']->id,
            'investment_seat_id' => $seats[1]->id,
            'created_at' => now()->subDay(),
            'updated_at' => now()->subDay(),
        ]);

        InterestRequest::query()->create([
            'user_id' => $investors[2]->id,
            'opportunity_id' => $opportunities['smart_logistics']->id,
            'investment_seat_id' => $seats[2]->id,
            'created_at' => now()->subDays(4),
            'updated_at' => now()->subDays(4),
        ]);

        $this->seedWalletsAndTransactions($investors, $advertisers, $seatPrice);
        $this->seedNotifications($investors, $advertisers, $opportunities);
        $this->seedDevices($admins, $investors, $advertisers);
        $this->seedAuthUpdates($investors, $advertisers);
        $this->seedFcmTokens($investors, $advertisers);

        $this->command?->info('Marketplace demo scenario seeded with opportunities across all statuses, seats, and interest requests.');
    }

    private function seedWalletsAndTransactions($investors, $advertisers, float $seatPrice): void
    {
        $walletBlueprints = [
            [
                'user' => $investors[0],
                'transactions' => [
                    ['type' => WalletTransactionTypeEnum::CHARGE, 'amount' => 25000.00, 'description' => 'Initial wallet top-up'],
                    ['type' => WalletTransactionTypeEnum::PAYMENT, 'amount' => $seatPrice, 'description' => 'Seat purchase for Wellness Clinic Rollout'],
                ],
            ],
            [
                'user' => $investors[1],
                'transactions' => [
                    ['type' => WalletTransactionTypeEnum::CHARGE, 'amount' => 40000.00, 'description' => 'Initial wallet top-up'],
                    ['type' => WalletTransactionTypeEnum::PAYMENT, 'amount' => $seatPrice, 'description' => 'Seat purchase for Wellness Clinic Rollout'],
                ],
            ],
            [
                'user' => $investors[2],
                'transactions' => [
                    ['type' => WalletTransactionTypeEnum::CHARGE, 'amount' => 60000.00, 'description' => 'Initial wallet top-up'],
                    ['type' => WalletTransactionTypeEnum::PAYMENT, 'amount' => $seatPrice, 'description' => 'Seat purchase for Smart Logistics Hub'],
                ],
            ],
            [
                'user' => $investors[3],
                'transactions' => [
                    ['type' => WalletTransactionTypeEnum::CHARGE, 'amount' => 120000.00, 'description' => 'Initial wallet top-up'],
                    ['type' => WalletTransactionTypeEnum::PAYMENT, 'amount' => $seatPrice, 'description' => 'Seat purchase for Precision Parts Manufacturing'],
                ],
            ],
            [
                'user' => $investors[4],
                'transactions' => [
                    ['type' => WalletTransactionTypeEnum::CHARGE, 'amount' => 10000.00, 'description' => 'Initial wallet top-up'],
                ],
            ],
        ];

        foreach ($advertisers as $advertiser) {
            $walletBlueprints[] = [
                'user' => $advertiser,
                'transactions' => [
                    ['type' => WalletTransactionTypeEnum::CHARGE, 'amount' => 5000.00, 'description' => 'Advertiser operational balance'],
                ],
            ];
        }

        foreach ($walletBlueprints as $blueprint) {
            $wallet = Wallet::query()->create([
                'walletable_type' => User::class,
                'walletable_id' => $blueprint['user']->id,
                'available_balance' => 0,
                'reserved_balance' => 0,
                'created_at' => now()->subDays(20),
                'updated_at' => now()->subDay(),
            ]);

            $runningBalance = 0;

            foreach ($blueprint['transactions'] as $index => $transaction) {
                $before = $runningBalance;
                $runningBalance = $transaction['type'] === WalletTransactionTypeEnum::PAYMENT
                    ? $runningBalance - $transaction['amount']
                    : $runningBalance + $transaction['amount'];

                WalletTransaction::query()->create([
                    'wallet_id' => $wallet->id,
                    'type' => $transaction['type'],
                    'amount' => $transaction['amount'],
                    'balance_before' => $before,
                    'balance_after' => $runningBalance,
                    'description' => $transaction['description'],
                    'metadata' => ['seeded' => true, 'sequence' => $index + 1],
                    'created_at' => now()->subDays(max(1, 10 - $index)),
                    'updated_at' => now()->subDays(max(1, 10 - $index)),
                ]);
            }

            $wallet->update([
                'available_balance' => $runningBalance,
                'reserved_balance' => 0,
            ]);
        }
    }

    private function seedNotifications($investors, $advertisers, array $opportunities): void
    {
        $notifications = [
            [
                'user_id' => $advertisers[1]->id,
                'title_ar' => 'الإعلان يحتاج تعديل',
                'title_en' => 'Opportunity needs revision',
                'body_ar' => 'يرجى تحديث مؤشرات الأداء المالية في فرصة FleetOps Dispatch Platform ثم إعادة الإرسال.',
                'body_en' => 'Please update the financial KPI section for FleetOps Dispatch Platform and submit it again.',
                'notification_category' => NotificationCategory::System->value,
                'notification_type' => 'opportunity.reviewed',
                'model_type' => Opportunity::class,
                'model_id' => $opportunities['fleetops']->id,
                'payload' => ['status' => OpportunityStatus::NeedsRevision->value],
                'seen' => false,
            ],
            [
                'user_id' => $advertisers[1]->id,
                'title_ar' => 'تم حجز الصفقة',
                'title_en' => 'Deal moved to reserved',
                'body_ar' => 'تمت ترقية فرصة Smart Logistics Hub إلى حالة reserved بعد دفعة أولية من المستثمر.',
                'body_en' => 'Smart Logistics Hub has moved to reserved after an investor paid the initial deposit.',
                'notification_category' => NotificationCategory::Interest->value,
                'notification_type' => 'opportunity.reserved',
                'model_type' => Opportunity::class,
                'model_id' => $opportunities['smart_logistics']->id,
                'payload' => ['status' => OpportunityStatus::Reserved->value],
                'seen' => false,
            ],
            [
                'user_id' => $investors[1]->id,
                'title_ar' => 'تم إرسال طلب الاهتمام',
                'title_en' => 'Interest request submitted',
                'body_ar' => 'تم تسجيل طلب اهتمامك في فرصة Wellness Clinic Rollout بنجاح.',
                'body_en' => 'Your interest request for Wellness Clinic Rollout has been recorded successfully.',
                'notification_category' => NotificationCategory::Interest->value,
                'notification_type' => 'interest_request.created',
                'model_type' => Opportunity::class,
                'model_id' => $opportunities['wellness_clinic']->id,
                'payload' => ['status' => OpportunityStatus::Published->value],
                'seen' => true,
            ],
            [
                'user_id' => $investors[3]->id,
                'title_ar' => 'مقعدك مفعل',
                'title_en' => 'Your seat is active',
                'body_ar' => 'يمكنك الآن متابعة تفاصيل Precision Parts Manufacturing وتقديم اهتمامك عند الجاهزية.',
                'body_en' => 'You can now review Precision Parts Manufacturing in more detail and submit your interest when ready.',
                'notification_category' => NotificationCategory::Orders->value,
                'notification_type' => 'seat.created',
                'model_type' => Opportunity::class,
                'model_id' => $opportunities['precision_parts']->id,
                'payload' => ['status' => OpportunityStatus::Published->value],
                'seen' => false,
            ],
        ];

        foreach ($notifications as $notification) {
            Notification::query()->create(array_merge($notification, [
                'created_at' => now()->subDays(2),
                'updated_at' => now()->subDays(2),
            ]));
        }
    }

    private function seedDevices($admins, $investors, $advertisers): void
    {
        $devices = [
            ['user_id' => $investors[0]->id, 'admin_id' => null, 'token' => 'device-investor-1-web', 'device_type' => DeviceType::Web, 'locale' => 'ar'],
            ['user_id' => $investors[1]->id, 'admin_id' => null, 'token' => 'device-investor-2-ios', 'device_type' => DeviceType::Ios, 'locale' => 'en'],
            ['user_id' => $advertisers[0]->id, 'admin_id' => null, 'token' => 'device-advertiser-1-web', 'device_type' => DeviceType::Web, 'locale' => 'ar'],
            ['user_id' => $advertisers[1]->id, 'admin_id' => null, 'token' => 'device-advertiser-2-android', 'device_type' => DeviceType::Android, 'locale' => 'en'],
            ['user_id' => null, 'admin_id' => $admins[0]->id, 'token' => 'device-admin-1-web', 'device_type' => DeviceType::Web, 'locale' => 'ar'],
        ];

        foreach ($devices as $device) {
            Device::query()->create(array_merge($device, [
                'created_at' => now()->subDays(7),
                'updated_at' => now()->subDay(),
            ]));
        }
    }

    private function seedAuthUpdates($investors, $advertisers): void
    {
        AuthUpdate::query()->create([
            'auth_updateable_type' => User::class,
            'auth_updateable_id' => $investors[0]->id,
            'type' => 'email_change',
            'sub_type' => 'verify_new',
            'attribute' => 'maya.updated@bi.test',
            'country_code' => null,
            'code' => '452191',
            'code_expires_at' => now()->addHours(2),
            'verified_at' => null,
            'created_at' => now()->subMinutes(15),
            'updated_at' => now()->subMinutes(15),
        ]);

        AuthUpdate::query()->create([
            'auth_updateable_type' => User::class,
            'auth_updateable_id' => $advertisers[0]->id,
            'type' => 'email_change',
            'sub_type' => 'verify_current',
            'attribute' => 'noura.operations@bi.test',
            'country_code' => null,
            'code' => '883204',
            'code_expires_at' => now()->addHour(),
            'verified_at' => now()->subMinutes(5),
            'created_at' => now()->subMinutes(35),
            'updated_at' => now()->subMinutes(5),
        ]);
    }

    private function seedFcmTokens($investors, $advertisers): void
    {
        FcmToken::query()->create([
            'user_id' => $investors[0]->id,
            'tokens' => json_encode(['fcm-maya-primary', 'fcm-maya-web'], JSON_UNESCAPED_UNICODE),
            'created_at' => now()->subDays(3),
            'updated_at' => now()->subDay(),
        ]);

        FcmToken::query()->create([
            'user_id' => $advertisers[0]->id,
            'tokens' => json_encode(['fcm-noura-ops'], JSON_UNESCAPED_UNICODE),
            'created_at' => now()->subDays(2),
            'updated_at' => now()->subDay(),
        ]);
    }
}
