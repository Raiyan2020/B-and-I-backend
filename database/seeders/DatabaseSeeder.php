<?php

namespace Database\Seeders;

use App\Models\AboutUsItem;
use App\Models\Admin;
use App\Models\AuthUpdate;
use App\Models\Category;
use App\Models\Device;
use App\Models\FcmToken;
use App\Models\Feature;
use App\Models\GeneralSetting;
use App\Models\InterestRequest;
use App\Models\InvestmentSeat;
use App\Models\Notification;
use App\Models\Opportunity;
use App\Models\PreferredSector;
use App\Models\Role;
use App\Models\SubscriptionPackage;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('personal_access_tokens')->truncate();
        DB::table('model_has_roles')->truncate();
        DB::table('model_has_permissions')->truncate();
        DB::table('role_has_permissions')->truncate();
        FcmToken::query()->truncate();
        AuthUpdate::query()->truncate();
        Device::query()->truncate();
        Notification::query()->truncate();
        InterestRequest::query()->truncate();
        InvestmentSeat::query()->truncate();
        Opportunity::query()->truncate();
        WalletTransaction::query()->truncate();
        Wallet::query()->truncate();
        User::query()->truncate();
        SubscriptionPackage::query()->truncate();
        PreferredSector::query()->truncate();
        Category::query()->truncate();
        Feature::query()->truncate();
        AboutUsItem::query()->truncate();
        GeneralSetting::query()->truncate();
        Admin::query()->truncate();
        Role::query()->truncate();
        Permission::query()->truncate();
        Schema::enableForeignKeyConstraints();

        $this->call([
            PermissionsSeeder::class,
            AdminSeeder::class,
            GeneralSettingSeeder::class,
            SubscriptionPackageSeeder::class,
            CategorySeeder::class,
            PreferredSectorSeeder::class,
            UserSeeder::class,
            FeatureSeeder::class,
            AboutUsItemSeeder::class,
            MarketplaceScenarioSeeder::class,
        ]);
    }
}
