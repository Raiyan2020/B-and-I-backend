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
                'name' => ['ar' => 'مستكشف', 'en' => 'Explorer'],
                'price_monthly' => 0,
                'description' => [
                    'ar' => '<ul><li>استعراض الفرص العامة</li><li>تنبيهات أساسية</li><li>إدارة ملف المستثمر</li></ul>',
                    'en' => '<ul><li>Browse public opportunities</li><li>Basic alerts</li><li>Investor profile management</li></ul>',
                ],
                'status' => true,
            ],
            [
                'name' => ['ar' => 'احترافي', 'en' => 'Professional'],
                'price_monthly' => 39,
                'description' => [
                    'ar' => '<ul><li>تنبيهات فورية</li><li>متابعة فرص أكثر</li><li>أولوية في الدعم</li></ul>',
                    'en' => '<ul><li>Instant alerts</li><li>Track more opportunities</li><li>Priority support</li></ul>',
                ],
                'status' => true,
            ],
            [
                'name' => ['ar' => 'مؤسسي', 'en' => 'Institutional'],
                'price_monthly' => 149,
                'description' => [
                    'ar' => '<ul><li>دعم مخصص</li><li>إدارة فرق متعددة</li><li>رؤية أوسع للفرص</li></ul>',
                    'en' => '<ul><li>Dedicated support</li><li>Multi-team management</li><li>Broader deal visibility</li></ul>',
                ],
                'status' => true,
            ],
        ];

        foreach ($packages as $package) {
            SubscriptionPackage::query()->create($package);
        }
    }
}
