<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Category;
use App\Models\GeneralSetting;
use App\Models\PreferredSector;
use App\Models\SubscriptionPackage;
use App\Models\User;
use Illuminate\Database\Seeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\AdminSeeder;
use App\Models\Role;
use Database\Seeders\CategorySeeder;
use Illuminate\Support\Facades\Schema;
use Database\Seeders\PermissionsSeeder;
use Spatie\Permission\Models\Permission;
use Database\Seeders\GeneralSettingSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        GeneralSetting::truncate();
        Admin::truncate();
        Role::truncate();
        Permission::truncate();
        Category::truncate();
        User::truncate();
        PreferredSector::truncate();
        SubscriptionPackage::truncate();
        Schema::enableForeignKeyConstraints();

        $this->call([
            PermissionsSeeder::class, // يجب أن يكون أولاً لإنشاء الرولات والـ permissions
            AdminSeeder::class, // بعد PermissionsSeeder لاستخدام الرولات
            GeneralSettingSeeder::class,
            SubscriptionPackageSeeder::class,
            CategorySeeder::class,
            PreferredSectorSeeder::class,
            UserSeeder::class,
        ]);
    }
}
