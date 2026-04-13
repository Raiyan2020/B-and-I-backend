<?php

namespace Database\Seeders;

use App\Models\Feature;
use Illuminate\Database\Seeder;

class FeatureSeeder extends Seeder
{
    public function run(): void
    {
        $features = [
            [
                'title' => ['ar' => 'فرص مدققة', 'en' => 'Vetted opportunities'],
                'description' => [
                    'ar' => 'تمر كل فرصة بمراجعة إدارية قبل النشر لضمان اكتمال البيانات الأساسية.',
                    'en' => 'Every opportunity passes through an admin review before it becomes publicly visible.',
                ],
                'status' => true,
            ],
            [
                'title' => ['ar' => 'مقاعد اهتمام مدفوعة', 'en' => 'Paid investor seats'],
                'description' => [
                    'ar' => 'يمنح المقعد المستثمر حق الوصول الأعمق والتعبير الرسمي عن الاهتمام دون كشف بيانات حساسة للجميع.',
                    'en' => 'Investor seats unlock deeper access and formal interest submission without exposing sensitive data to everyone.',
                ],
                'status' => true,
            ],
            [
                'title' => ['ar' => 'تفاوض بإشراف إداري', 'en' => 'Admin-assisted negotiation'],
                'description' => [
                    'ar' => 'تقوم الإدارة بتجميع طلبات الاهتمام والتنسيق مع الأطراف للوصول إلى قرار نهائي.',
                    'en' => 'The admin team groups interest requests and coordinates between parties toward a final award decision.',
                ],
                'status' => true,
            ],
        ];

        foreach ($features as $feature) {
            Feature::query()->create($feature);
        }
    }
}
