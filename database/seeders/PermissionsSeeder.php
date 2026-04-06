<?php

namespace Database\Seeders;

use Illuminate\Support\Str;
use App\Facades\BaseService;
use App\Support\QueryOptions;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Role;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;

class PermissionsSeeder extends Seeder
{

    public function run()
    {
        Schema::disableForeignKeyConstraints();
        DB::table('permissions')->truncate();
        DB::table('model_has_permissions')->truncate();
        DB::table('roles')->truncate();
        DB::table('role_has_permissions')->truncate();
        Schema::enableForeignKeyConstraints();
        $permissions = [

            ['guard_name' => 'admin', 'name' => 'users'],
            ['guard_name' => 'admin', 'name' => 'add-user'],
            ['guard_name' => 'admin', 'name' => 'edit-user'],
            ['guard_name' => 'admin', 'name' => 'show-user'],
            ['guard_name' => 'admin', 'name' => 'delete-user'],
            ['guard_name' => 'admin', 'name' => 'block-user'],

            ['guard_name' => 'admin', 'name' => 'roles'],
            ['guard_name' => 'admin', 'name' => 'add-role'],
            ['guard_name' => 'admin', 'name' => 'edit-role'],
            ['guard_name' => 'admin', 'name' => 'show-role'],
            ['guard_name' => 'admin', 'name' => 'delete-role'],

            ['guard_name' => 'admin', 'name' => 'admins'],
            ['guard_name' => 'admin', 'name' => 'add-admin'],
            ['guard_name' => 'admin', 'name' => 'edit-admin'],
            ['guard_name' => 'admin', 'name' => 'show-admin'],
            ['guard_name' => 'admin', 'name' => 'delete-admin'],
            ['guard_name' => 'admin', 'name' => 'block-admin'],

            ['guard_name' => 'admin', 'name' => 'show-settings'],
            ['guard_name' => 'admin', 'name' => 'edit-settings'],

            ['guard_name' => 'admin', 'name' => 'categories'],
            ['guard_name' => 'admin', 'name' => 'add-category'],
            ['guard_name' => 'admin', 'name' => 'edit-category'],
            ['guard_name' => 'admin', 'name' => 'show-category'],
            ['guard_name' => 'admin', 'name' => 'delete-category'],

            ['guard_name' => 'admin', 'name' => 'preferred-sectors'],
            ['guard_name' => 'admin', 'name' => 'add-preferred-sector'],
            ['guard_name' => 'admin', 'name' => 'edit-preferred-sector'],
            ['guard_name' => 'admin', 'name' => 'show-preferred-sector'],
            ['guard_name' => 'admin', 'name' => 'delete-preferred-sector'],

            ['guard_name' => 'admin', 'name' => 'about-us-items'],
            ['guard_name' => 'admin', 'name' => 'add-about-us-item'],
            ['guard_name' => 'admin', 'name' => 'edit-about-us-item'],
            ['guard_name' => 'admin', 'name' => 'delete-about-us-item'],

        ];

        // إنشاء الرول super_admin
        $superAdminRole = Role::create([
            'name' => 'super_admin',
            'guard_name' => 'admin',
        ]);
        $superAdminRole->setTranslations('title', [
            'ar' => 'مدير النظام',
            'en' => 'Super Admin',
        ]);
        $superAdminRole->save();

        // إنشاء الرول admin
        $adminRole = Role::create([
            'name' => 'admin',
            'guard_name' => 'admin',
        ]);
        $adminRole->setTranslations('title', [
            'ar' => 'مدير',
            'en' => 'Admin',
        ]);
        $adminRole->save();

        // إدراج الـ permissions
        Permission::insert($permissions);
        $permissions = BaseService::setModel(Permission::class)->all(new QueryOptions());

        // ربط جميع الـ permissions بالرول super_admin
        $superAdminRole->syncPermissions($permissions);

        $adminRole->syncPermissions(
            $permissions->filter(function ($permission) {
                return ! Str::contains($permission->name, ['role', 'admin']);
            })
        );
    }
}
