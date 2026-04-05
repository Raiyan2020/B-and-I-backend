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

        $users = [
            [
                'name' => 'أحمد محمد',
                'country_code' => '+966',
                'phone' => '501234567',
                'email' => 'ahmed@example.com',
                'password' => '123456789',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'فاطمة علي',
                'country_code' => '+966',
                'phone' => '502345678',
                'email' => 'fatima@example.com',
                'password' => '123456789',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'محمد خالد',
                'country_code' => '+966',
                'phone' => '503456789',
                'email' => 'mohammed@example.com',
                'password' => '123456789',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'سارة أحمد',
                'country_code' => '+966',
                'phone' => '504567890',
                'email' => 'sara@example.com',
                'password' => '123456789',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'علي حسن',
                'country_code' => '+966',
                'phone' => '505678901',
                'email' => 'ali@example.com',
                'password' => '123456789',
                'email_verified_at' => now(),
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}
