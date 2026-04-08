<?php

namespace Tests\Feature\Api;

use App\Models\GeneralSetting;
use App\Models\SubscriptionPackage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PackagesApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_sees_packages_with_can_register_false(): void
    {
        GeneralSetting::insert([
            ['key' => 'packages_page_title_en', 'value' => 'Title', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'packages_page_description_en', 'value' => '<p>Intro</p>', 'created_at' => now(), 'updated_at' => now()],
        ]);

        SubscriptionPackage::query()->create([
            'name' => ['ar' => 'أساسي', 'en' => 'Basic'],
            'price_monthly' => 19,
            'description' => ['ar' => '<ul><li>أ</li></ul>', 'en' => '<ul><li>a</li></ul>'],
            'status' => true,
        ]);

        $response = $this->getJson('/api/v1/general/packages');

        $response->assertOk()
            ->assertJsonPath('data.page.title', 'Title')
            ->assertJsonPath('data.packages.0.can_register', false)
            ->assertJsonPath('data.packages.0.is_subscribed', false);
    }

    public function test_authenticated_user_can_register_flags(): void
    {
        GeneralSetting::insert([
            ['key' => 'packages_page_title_en', 'value' => 'T', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'packages_page_description_en', 'value' => 'D', 'created_at' => now(), 'updated_at' => now()],
        ]);

        $pkg = SubscriptionPackage::query()->create([
            'name' => ['ar' => 'ب', 'en' => 'B'],
            'price_monthly' => 10,
            'description' => ['ar' => '<p>x</p>', 'en' => '<p>x</p>'],
            'status' => true,
        ]);

        $user = User::factory()->create([
            'password' => 'password',
            'subscription_package_id' => $pkg->id,
        ]);

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/v1/general/packages');

        $response->assertOk();
        $response->assertJsonPath('data.packages.0.can_register', true);
        $response->assertJsonPath('data.packages.0.is_subscribed', true);
    }
}
