<?php

namespace Database\Seeders;

use App\Models\AboutUsItem;
use Illuminate\Database\Seeder;

class AboutUsItemSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            [
                'title' => ['ar' => 'إخفاء البيانات الحساسة', 'en' => 'Sensitive data stays protected'],
                'description' => [
                    'ar' => 'تبقى بيانات المعلنين الخاصة مخفية عن جميع المستخدمين حتى المراحل المصرح بها فقط.',
                    'en' => 'Advertiser-sensitive information remains hidden from all users except in explicitly permitted stages.',
                ],
                'status' => true,
            ],
            [
                'title' => ['ar' => 'دورة صفقة واضحة', 'en' => 'Clear deal lifecycle'],
                'description' => [
                    'ar' => 'من حالة pending حتى reserved ثم completed، كل مرحلة لها قواعد عرض وتعامل واضحة.',
                    'en' => 'From pending to reserved and completed, every stage has a clear visibility and action model.',
                ],
                'status' => true,
            ],
            [
                'title' => ['ar' => 'ملاءمة بين المستثمر والفرصة', 'en' => 'Better investor-opportunity fit'],
                'description' => [
                    'ar' => 'تدعم المنصة التصفية حسب التفضيلات والقطاعات ونوع المستثمر لتسريع الوصول للفرص المناسبة.',
                    'en' => 'The platform supports preference- and sector-based matching to surface the most relevant opportunities faster.',
                ],
                'status' => true,
            ],
        ];

        foreach ($items as $item) {
            AboutUsItem::query()->create($item);
        }
    }
}
