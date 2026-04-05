<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        Category::truncate();
        Schema::enableForeignKeyConstraints();

        $categories = [
            [
                'name' => [
                    'ar' => 'ملابس',
                    'en' => 'Clothing'
                ],
                'status' => 1,
            ],
            [
                'name' => [
                    'ar' => 'إلكترونيات',
                    'en' => 'Electronics'
                ],
                'status' => 1,
            ],
            [
                'name' => [
                    'ar' => 'أجهزة منزلية',
                    'en' => 'Home Appliances'
                ],
                'status' => 1,
            ],
            [
                'name' => [
                    'ar' => 'أثاث',
                    'en' => 'Furniture'
                ],
                'status' => 1,
            ],
            [
                'name' => [
                    'ar' => 'ألعاب',
                    'en' => 'Toys'
                ],
                'status' => 1,
            ],
            [
                'name' => [
                    'ar' => 'كتب',
                    'en' => 'Books'
                ],
                'status' => 1,
            ],
            [
                'name' => [
                    'ar' => 'مستحضرات تجميل',
                    'en' => 'Cosmetics'
                ],
                'status' => 1,
            ],
            [
                'name' => [
                    'ar' => 'أطعمة ومشروبات',
                    'en' => 'Food & Beverages'
                ],
                'status' => 1,
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
