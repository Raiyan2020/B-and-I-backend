<?php

namespace Database\Seeders;

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

        // مستثمرين من شركات مختلفة مع بيانات كاملة
        $users = [
            // مستثمرين من شركة الرياض للتطوير العقاري
            [
                'role' => 'investor',
                'first_name' => 'أحمد',
                'last_name' => 'محمد الدعيع',
                'country_code' => '+966',
                'phone' => '50123456',
                'email' => 'ahmad.aldoaae@riyadhdev.com',
                'password' => 'Password@123',
                'email_verified_at' => now(),
                'is_active' => true,
                'is_blocked' => false,
                'investor_type' => 'company',
                'investor_sector' => 'العقارات',
                'investor_capital' => 500000.000,
                'investment_count' => 10,
                'investor_experience' => 'expert',
            ],
            [
                'role' => 'investor',
                'first_name' => 'فاطمة',
                'last_name' => 'علي العتيبي',
                'country_code' => '+966',
                'phone' => '50234567',
                'email' => 'fatima.alotaibi@riyadhdev.com',
                'password' => 'Password@123',
                'email_verified_at' => now(),
                'is_active' => true,
                'is_blocked' => false,
                'investor_type' => 'angel',
                'investor_sector' => 'العقارات',
                'investor_capital' => 100000.000,
                'investment_count' => 5,
                'investor_experience' => 'intermediate',
            ],
            [
                'role' => 'investor',
                'first_name' => 'محمد',
                'last_name' => 'خالد القحطاني',
                'country_code' => '+966',
                'phone' => '50345678',
                'email' => 'mohammad.alqahtani@riyadhdev.com',
                'password' => 'Password@123',
                'email_verified_at' => now(),
                'is_active' => true,
                'is_blocked' => false,
                'investor_type' => 'crowdfunding',
                'investor_sector' => 'التقنية',
                'investor_capital' => 10000.000,
                'investment_count' => 20,
                'investor_experience' => 'beginner',
            ],

            // مستثمرين من شركة الجزيرة للاستثمار
            [
                'role' => 'investor',
                'first_name' => 'سارة',
                'last_name' => 'أحمد السليمان',
                'country_code' => '+966',
                'phone' => '50456789',
                'email' => 'sarah.alsulaiman@jazira.com',
                'password' => 'Password@123',
                'email_verified_at' => now(),
                'is_active' => true,
                'is_blocked' => false,
                'investor_type' => 'angel',
                'investor_sector' => 'التجارة',
                'investor_capital' => 300000.000,
                'investment_count' => 8,
                'investor_experience' => 'intermediate',
            ],
            [
                'role' => 'investor',
                'first_name' => 'علي',
                'last_name' => 'حسن المطيري',
                'country_code' => '+966',
                'phone' => '50567890',
                'email' => 'ali.almutairi@jazira.com',
                'password' => 'Password@123',
                'email_verified_at' => now(),
                'is_active' => true,
                'is_blocked' => false,
                'investor_type' => 'company',
                'investor_sector' => 'الطاقة',
                'investor_capital' => 1500000.000,
                'investment_count' => 12,
                'investor_experience' => 'expert',
            ],

            // معلنين من الخليج للأوراق المالية
            [
                'role' => 'advertiser',
                'first_name' => 'نور',
                'last_name' => 'محمود الحربي',
                'country_code' => '+966',
                'phone' => '50678901',
                'email' => 'noor.alharbi@gulffinance.com',
                'password' => 'Password@123',
                'email_verified_at' => now(),
                'is_active' => true,
                'is_blocked' => false,
                'company_name' => 'الخليج للأوراق',
                'company_license_url' => 'licenses/noor_license.pdf',
                'license_number' => 'LC-G901',
            ],
            [
                'role' => 'advertiser',
                'first_name' => 'حسام',
                'last_name' => 'علاء الدين',
                'country_code' => '+966',
                'phone' => '50789012',
                'email' => 'hossam.aldin@gulffinance.com',
                'password' => 'Password@123',
                'email_verified_at' => now(),
                'is_active' => true,
                'is_blocked' => false,
                'company_name' => 'الخليج للاستثمار',
                'company_license_url' => 'licenses/hossam_license.pdf',
                'license_number' => 'LC-G902',
            ],

            // معلنين من شركة النقل الموحدة
            [
                'role' => 'advertiser',
                'first_name' => 'ليلى',
                'last_name' => 'محمد الشهري',
                'country_code' => '+966',
                'phone' => '50890123',
                'email' => 'layla.alshehri@transport.com',
                'password' => 'Password@123',
                'email_verified_at' => now(),
                'is_active' => true,
                'is_blocked' => false,
                'company_name' => 'النقل الموحد',
                'company_license_url' => 'licenses/layla_license.pdf',
                'license_number' => 'LC-T123',
            ],
            [
                'role' => 'advertiser',
                'first_name' => 'خالد',
                'last_name' => 'إبراهيم العنزي',
                'country_code' => '+966',
                'phone' => '50901234',
                'email' => 'khaled.alenezi@transport.com',
                'password' => 'Password@123',
                'email_verified_at' => now(),
                'is_active' => true,
                'is_blocked' => false,
                'company_name' => 'النقل الموحد فرع',
                'company_license_url' => 'licenses/khaled_license.pdf',
                'license_number' => 'LC-T124',
            ],

            // أدمن
            [
                'role' => 'admin',
                'first_name' => 'سوبر',
                'last_name' => 'أدمن',
                'country_code' => '+966',
                'phone' => '51012345',
                'email' => 'admin@admin.com',
                'password' => 'Password@123',
                'email_verified_at' => now(),
                'is_active' => true,
                'is_blocked' => false,
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }

        $this->command->info('✅ تم إنشاء 10 مستثمرين من 5 شركات مختلفة بنجاح!');
    }
}
