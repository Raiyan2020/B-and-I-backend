<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use App\Models\Role;

class AboutUsPermissionsSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            ['guard_name' => 'admin', 'name' => 'about-us-items'],
            ['guard_name' => 'admin', 'name' => 'add-about-us-item'],
            ['guard_name' => 'admin', 'name' => 'edit-about-us-item'],
            ['guard_name' => 'admin', 'name' => 'delete-about-us-item'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate($permission);
        }

        // Assign all about-us permissions to super_admin role
        $superAdminRole = Role::where('name', 'super_admin')->where('guard_name', 'admin')->first();
        if ($superAdminRole) {
            $aboutUsPermissions = Permission::where('name', 'like', '%about-us%')->get();
            $superAdminRole->givePermissionTo($aboutUsPermissions);
        }
    }
}
