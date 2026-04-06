<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use App\Models\Role;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        Admin::truncate();
        Schema::enableForeignKeyConstraints();

        // الحصول على الرول super_admin الذي تم إنشاؤه في PermissionsSeeder
        $superAdminRole = Role::where('name', 'super_admin')->where('guard_name', 'admin')->first();
        $adminRole = Role::where('name', 'admin')->where('guard_name', 'admin')->first();
        if (!$superAdminRole) {
            $this->command->error('Role super_admin not found. Please run PermissionsSeeder first.');
            return;
        }

        // إنشاء super admin
        $superAdmin = Admin::create([
            'name' => 'Super Admin',
            'email' => 'admin@info.com',
            'password' => '123456789',
            'phone' => '1234567890',
            'is_blocked' => false,
        ]);
        $superAdmin->assignRole($superAdminRole);

        // إنشاء admin عادي
        $admin = Admin::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => '123456789',
            'phone' => '0987654321',
            'is_blocked' => false,
        ]);
        $admin->assignRole($adminRole);
    }
}
