<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => ['ar' => 'المطاعم والمقاهي', 'en' => 'Food & Beverage'], 'order' => 1, 'status' => true],
            ['name' => ['ar' => 'التقنية والبرمجيات', 'en' => 'Technology & SaaS'], 'order' => 2, 'status' => true],
            ['name' => ['ar' => 'الخدمات اللوجستية', 'en' => 'Logistics'], 'order' => 3, 'status' => true],
            ['name' => ['ar' => 'الرعاية الصحية', 'en' => 'Healthcare'], 'order' => 4, 'status' => true],
            ['name' => ['ar' => 'التعليم والتدريب', 'en' => 'Education'], 'order' => 5, 'status' => true],
            ['name' => ['ar' => 'التصنيع الخفيف', 'en' => 'Light Manufacturing'], 'order' => 6, 'status' => true],
            ['name' => ['ar' => 'التجزئة المتخصصة', 'en' => 'Specialty Retail'], 'order' => 7, 'status' => true],
            ['name' => ['ar' => 'العقار والخدمات المساندة', 'en' => 'Real Estate Services'], 'order' => 8, 'status' => true],
        ];

        foreach ($categories as $category) {
            Category::query()->create($category);
        }
    }
}
