<?php

namespace Database\Seeders;

use App\Models\SubscriptionPackage;
use Illuminate\Database\Seeder;

class SubscriptionPackageSeeder extends Seeder
{
    public function run(): void
    {
        $packages = [
            [
                'name' => [
                    'ar' => 'أساسي',
                    'en' => 'Basic',
                ],
                'price_monthly' => 19,
                'description' => [
                    'ar' => '<ul><li>تصفح المشاريع</li><li>دعم أساسي</li><li>5 مفضلة</li></ul>',
                    'en' => '<ul><li>Browse projects</li><li>Basic support</li><li>5 favorites</li></ul>',
                ],
                'status' => true,
            ],
            [
                'name' => [
                    'ar' => 'بريميوم',
                    'en' => 'Premium',
                ],
                'price_monthly' => 49,
                'description' => [
                    'ar' => '<ul><li>أولوية الوصول</li><li>تحليلات متقدمة</li><li>مفضلة غير محدودة</li><li>تنبيهات البريد</li></ul>',
                    'en' => '<ul><li>Priority access</li><li>Advanced analytics</li><li>Unlimited favorites</li><li>Email alerts</li></ul>',
                ],
                'status' => true,
            ],
            [
                'name' => [
                    'ar' => 'كبار الشخصيات',
                    'en' => 'VIP',
                ],
                'price_monthly' => 299,
                'description' => [
                    'ar' => '<ul><li>كونسيرج إداري مباشر</li><li>فرص مخفية</li><li>0% رسوم المنصة في الصفقة الأولى</li><li>باقة التحليلات الكاملة</li></ul>',
                    'en' => '<ul><li>Direct administrative concierge</li><li>Hidden opportunities</li><li>0% platform fees on the first deal</li><li>Full analytics package</li></ul>',
                ],
                'status' => true,
            ],
        ];

        foreach ($packages as $row) {
            SubscriptionPackage::query()->create($row);
        }
    }
}
