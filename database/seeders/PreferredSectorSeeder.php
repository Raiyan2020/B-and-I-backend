<?php

namespace Database\Seeders;

use App\Models\PreferredSector;
use Illuminate\Database\Seeder;

class PreferredSectorSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['name' => ['ar' => 'العقارات', 'en' => 'Real Estate']],
            ['name' => ['ar' => 'التقنية', 'en' => 'Technology']],
            ['name' => ['ar' => 'التجارة', 'en' => 'Commerce']],
            ['name' => ['ar' => 'الطاقة', 'en' => 'Energy']],
        ];

        foreach ($rows as $row) {
            PreferredSector::create(array_merge($row, ['status' => true]));
        }
    }
}
