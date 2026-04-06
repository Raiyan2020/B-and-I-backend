<?php

namespace Database\Seeders;

use App\Enums\InvestorExperience;
use App\Enums\InvestorType;
use App\Enums\UserRole;
use App\Models\Category;
use App\Models\PreferredSector;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        User::truncate();
        Schema::enableForeignKeyConstraints();

        $sectorIds = PreferredSector::query()->orderBy('id')->pluck('id')->values()->all();
        $categoryIds = Category::query()->orderBy('id')->pluck('id')->values()->all();

        // مستثمرين من شركات مختلفة مع بيانات كاملة
        $users = [
            // مستثمرين من شركة الرياض للتطوير العقاري
            [
                'role' => UserRole::Investor,
                'first_name' => 'أحمد',
                'last_name' => 'محمد الدعيع',
                'country_code' => '+966',
                'phone' => '50123456',
                'email' => 'ahmad.aldoaae@riyadhdev.com',
                'password' => 'Password@123',
                'email_verified_at' => now(),
                'is_active' => true,
                'is_blocked' => false,
                'investor_type' => InvestorType::Company,
                'preferred_sector_id' => $sectorIds[0],
                'category_id' => $categoryIds[0],
                'available_capital' => 500000.000,
                'capital' => 500000.000,
                'previous_investments_count' => 10,
                'investor_experience' => InvestorExperience::Expert,
            ],
            [
                'role' => UserRole::Investor,
                'first_name' => 'فاطمة',
                'last_name' => 'علي العتيبي',
                'country_code' => '+966',
                'phone' => '50234567',
                'email' => 'fatima.alotaibi@riyadhdev.com',
                'password' => 'Password@123',
                'email_verified_at' => now(),
                'is_active' => true,
                'is_blocked' => false,
                'investor_type' => InvestorType::Angel,
                'preferred_sector_id' => $sectorIds[0],
                'category_id' => $categoryIds[1],
                'available_capital' => 100000.000,
                'capital' => 100000.000,
                'previous_investments_count' => 5,
                'investor_experience' => InvestorExperience::Intermediate,
            ],
            [
                'role' => UserRole::Investor,
                'first_name' => 'محمد',
                'last_name' => 'خالد القحطاني',
                'country_code' => '+966',
                'phone' => '50345678',
                'email' => 'mohammad.alqahtani@riyadhdev.com',
                'password' => 'Password@123',
                'email_verified_at' => now(),
                'is_active' => true,
                'is_blocked' => false,
                'investor_type' => InvestorType::Crowdfunding,
                'preferred_sector_id' => $sectorIds[1],
                'category_id' => $categoryIds[2],
                'available_capital' => 10000.000,
                'capital' => 10000.000,
                'previous_investments_count' => 20,
                'investor_experience' => InvestorExperience::Beginner,
            ],

            // مستثمرين من شركة الجزيرة للاستثمار
            [
                'role' => UserRole::Investor,
                'first_name' => 'سارة',
                'last_name' => 'أحمد السليمان',
                'country_code' => '+966',
                'phone' => '50456789',
                'email' => 'sarah.alsulaiman@jazira.com',
                'password' => 'Password@123',
                'email_verified_at' => now(),
                'is_active' => true,
                'is_blocked' => false,
                'investor_type' => InvestorType::Angel,
                'preferred_sector_id' => $sectorIds[2],
                'category_id' => $categoryIds[3],
                'available_capital' => 300000.000,
                'capital' => 300000.000,
                'previous_investments_count' => 8,
                'investor_experience' => InvestorExperience::Intermediate,
            ],
            [
                'role' => UserRole::Investor,
                'first_name' => 'علي',
                'last_name' => 'حسن المطيري',
                'country_code' => '+966',
                'phone' => '50567890',
                'email' => 'ali.almutairi@jazira.com',
                'password' => 'Password@123',
                'email_verified_at' => now(),
                'is_active' => true,
                'is_blocked' => false,
                'investor_type' => InvestorType::Company,
                'preferred_sector_id' => $sectorIds[3],
                'category_id' => $categoryIds[4],
                'available_capital' => 1500000.000,
                'capital' => 1500000.000,
                'previous_investments_count' => 12,
                'investor_experience' => InvestorExperience::Expert,
            ],

            // معلنين من الخليج للأوراق المالية
            [
                'role' => UserRole::Advertiser,
                'first_name' => 'نور',
                'last_name' => 'محمود الحربي',
                'country_code' => '+966',
                'phone' => '50678901',
                'email' => 'noor.alharbi@gulffinance.com',
                'password' => 'Password@123',
                'email_verified_at' => now(),
                'is_active' => true,
                'is_blocked' => false,
                'company_license' => 'licenses/noor_license.pdf',
            ],
            [
                'role' => UserRole::Advertiser,
                'first_name' => 'حسام',
                'last_name' => 'علاء الدين',
                'country_code' => '+966',
                'phone' => '50789012',
                'email' => 'hossam.aldin@gulffinance.com',
                'password' => 'Password@123',
                'email_verified_at' => now(),
                'is_active' => true,
                'is_blocked' => false,
                'company_license' => 'licenses/hossam_license.pdf',
            ],

            // معلنين من شركة النقل الموحدة
            [
                'role' => UserRole::Advertiser,
                'first_name' => 'ليلى',
                'last_name' => 'محمد الشهري',
                'country_code' => '+966',
                'phone' => '50890123',
                'email' => 'layla.alshehri@transport.com',
                'password' => 'Password@123',
                'email_verified_at' => now(),
                'is_active' => true,
                'is_blocked' => false,
                'company_license' => 'licenses/layla_license.pdf',
            ],
            [
                'role' => UserRole::Advertiser,
                'first_name' => 'خالد',
                'last_name' => 'إبراهيم العنزي',
                'country_code' => '+966',
                'phone' => '50901234',
                'email' => 'khaled.alenezi@transport.com',
                'password' => 'Password@123',
                'email_verified_at' => now(),
                'is_active' => true,
                'is_blocked' => false,
                'company_license' => 'licenses/khaled_license.pdf',
            ],

            // حساب تجريبي (دور admin غير موجود في users؛ الأدمن الفعلي في جدول admins عبر AdminSeeder)
            [
                'role' => UserRole::Investor,
                'first_name' => 'سوبر',
                'last_name' => 'أدمن',
                'country_code' => '+966',
                'phone' => '51012345',
                'email' => 'admin@admin.com',
                'password' => 'Password@123',
                'email_verified_at' => now(),
                'is_active' => true,
                'is_blocked' => false,
                'investor_type' => InvestorType::Angel,
                'preferred_sector_id' => $sectorIds[1],
                'category_id' => $categoryIds[5],
                'available_capital' => 50000.000,
                'capital' => 50000.000,
                'previous_investments_count' => 1,
                'investor_experience' => InvestorExperience::Beginner,
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }

        $this->command->info('✅ تم إنشاء 6 مستثمرين و4 معلنين تجريبيين بنجاح!');
    }
}
