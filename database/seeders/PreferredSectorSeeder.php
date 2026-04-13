<?php

namespace Database\Seeders;

use App\Models\PreferredSector;
use Illuminate\Database\Seeder;

class PreferredSectorSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['name' => ['ar' => 'التقنية', 'en' => 'Technology'], 'status' => true],
            ['name' => ['ar' => 'الأغذية والمشروبات', 'en' => 'Food & Beverage'], 'status' => true],
            ['name' => ['ar' => 'الخدمات اللوجستية', 'en' => 'Logistics'], 'status' => true],
            ['name' => ['ar' => 'الرعاية الصحية', 'en' => 'Healthcare'], 'status' => true],
            ['name' => ['ar' => 'التعليم', 'en' => 'Education'], 'status' => true],
            ['name' => ['ar' => 'الصناعة', 'en' => 'Industrial'], 'status' => true],
            ['name' => ['ar' => 'العقار', 'en' => 'Real Estate'], 'status' => true],
        ];

        foreach ($rows as $row) {
            PreferredSector::query()->create($row);
        }
    }
}
