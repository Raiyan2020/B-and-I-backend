<?php

namespace Tests\Feature;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class GeneralSettingControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedPermissions();
        Storage::fake('public');
    }

    protected function seedPermissions(): void
    {
        $superAdminRole = Role::firstOrCreate(
            ['name' => 'super_admin', 'guard_name' => 'admin']
        );

        foreach (['show-settings', 'edit-settings'] as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'admin',
            ]);
        }

        $superAdminRole->syncPermissions(Permission::where('guard_name', 'admin')->get());
    }

    protected function createSuperAdmin(array $attributes = []): Admin
    {
        $defaults = [
            'name' => 'Super Admin',
            'email' => 'admin@test.com',
            'password' => 'password123',
            'phone' => '1234567890',
            'is_blocked' => false,
        ];

        $admin = Admin::create(array_merge($defaults, $attributes));
        $superAdminRole = Role::where('name', 'super_admin')->where('guard_name', 'admin')->first();
        if ($superAdminRole) {
            $admin->assignRole($superAdminRole);
        }

        return $admin;
    }

    public function test_authenticated_admin_can_view_general_settings(): void
    {
        $admin = $this->createSuperAdmin();
        $this->actingAs($admin, 'admin');

        $response = $this->get(route('admin.generalSetting.index'));

        $response->assertStatus(200);
        $response->assertViewIs('dashboard.settings.general_settings');
    }

    public function test_authenticated_admin_can_update_general_settings(): void
    {
        $admin = $this->createSuperAdmin();
        $this->actingAs($admin, 'admin');

        $logo = UploadedFile::fake()->image('logo.jpg');
        $favicon = UploadedFile::fake()->image('favicon.jpg');

        $response = $this->post(route('admin.generalSetting.store'), [
            'generalSettings' => true,
            'type' => [
                'website_name_ar' => 'اسم الموقع',
                'website_name_en' => 'Website Name',
                'completed_deals_commission' => '7.5',
                'commercial_register' => '123456789',
                'tax_number' => '987654321',
                'contact_number' => '1234567890',
                'copy_right' => 'Copyright 2024',
            ],
            'logo1' => $logo,
            'favicon2' => $favicon,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('general_settings', [
            'key' => 'website_name_ar',
            'value' => 'اسم الموقع',
        ]);

        $this->assertDatabaseHas('general_settings', [
            'key' => 'website_name_en',
            'value' => 'Website Name',
        ]);

        $this->assertDatabaseHas('general_settings', [
            'key' => 'completed_deals_commission',
            'value' => '7.5',
        ]);
    }

    public function test_general_settings_persists_project_briefs(): void
    {
        $admin = $this->createSuperAdmin();
        $this->actingAs($admin, 'admin');

        $logo = UploadedFile::fake()->image('logo.jpg');
        $favicon = UploadedFile::fake()->image('favicon.jpg');

        $response = $this->post(route('admin.generalSetting.store'), [
            'generalSettings' => true,
            'type' => [
                'website_name_ar' => 'اسم الموقع',
                'website_name_en' => 'Website Name',
                'project_brief_ar' => 'نبذة بالعربية',
                'project_brief_en' => 'Brief in English',
                'completed_deals_commission' => '5',
                'commercial_register' => '123456789',
                'tax_number' => '987654321',
                'contact_number' => '1234567890',
                'copy_right' => 'Copyright 2024',
            ],
            'logo1' => $logo,
            'favicon2' => $favicon,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('general_settings', [
            'key' => 'project_brief_ar',
            'value' => 'نبذة بالعربية',
        ]);
        $this->assertDatabaseHas('general_settings', [
            'key' => 'project_brief_en',
            'value' => 'Brief in English',
        ]);
    }

    public function test_general_settings_update_requires_valid_data(): void
    {
        $admin = $this->createSuperAdmin();
        $this->actingAs($admin, 'admin');

        $response = $this->post(route('admin.generalSetting.store'), [
            'generalSettings' => true,
            'type' => [],
        ]);

        $response->assertSessionHasErrors();
    }

    public function test_authenticated_admin_can_update_social_media_settings(): void
    {
        $admin = $this->createSuperAdmin();
        $this->actingAs($admin, 'admin');

        $response = $this->post(route('admin.generalSetting.store'), [
            'socials' => true,
            'type' => [
                'facebook' => 'https://facebook.com/test',
                'twitter' => 'https://twitter.com/test',
                'instagram' => 'https://instagram.com/test',
            ],
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('general_settings', [
            'key' => 'facebook',
            'value' => 'https://facebook.com/test',
        ]);
    }

    public function test_social_media_settings_require_valid_urls(): void
    {
        $admin = $this->createSuperAdmin();
        $this->actingAs($admin, 'admin');

        $response = $this->post(route('admin.generalSetting.store'), [
            'socials' => true,
            'type' => [
                'facebook' => 'invalid-url',
            ],
        ]);

        $response->assertSessionHasErrors();
    }

    public function test_authenticated_admin_can_update_background_images(): void
    {
        $admin = $this->createSuperAdmin();
        $this->actingAs($admin, 'admin');

        $loginImage = UploadedFile::fake()->image('login.jpg');
        $headerImage = UploadedFile::fake()->image('header.jpg');

        $response = $this->post(route('admin.generalSetting.store'), [
            'background' => true,
            'login_page_image3' => $loginImage,
            'pages_header_image5' => $headerImage,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => __('dashboard.item updated successfully')]);

        $this->assertDatabaseHas('general_settings', [
            'key' => 'login_page_image3',
        ]);

        $this->assertDatabaseHas('general_settings', [
            'key' => 'pages_header_image5',
        ]);
    }

    public function test_authenticated_admin_can_update_terms_and_conditions(): void
    {
        $admin = $this->createSuperAdmin();
        $this->actingAs($admin, 'admin');

        $response = $this->patch(route('admin.generalSetting.terms.update'), [
            'terms_ar' => 'الشروط والأحكام بالعربية',
            'terms_en' => 'Terms and Conditions in English',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('general_settings', [
            'key' => 'terms_ar',
            'value' => 'الشروط والأحكام بالعربية',
        ]);

        $this->assertDatabaseHas('general_settings', [
            'key' => 'terms_en',
            'value' => 'Terms and Conditions in English',
        ]);
    }

    public function test_authenticated_admin_can_update_privacy_policy(): void
    {
        $admin = $this->createSuperAdmin();
        $this->actingAs($admin, 'admin');

        $response = $this->patch(route('admin.generalSetting.privacy.update'), [
            'privacy_policy_ar' => 'سياسة الخصوصية بالعربية',
            'privacy_policy_en' => 'Privacy Policy in English',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('general_settings', [
            'key' => 'privacy_policy_ar',
            'value' => 'سياسة الخصوصية بالعربية',
        ]);

        $this->assertDatabaseHas('general_settings', [
            'key' => 'privacy_policy_en',
            'value' => 'Privacy Policy in English',
        ]);
    }

    public function test_terms_update_validation(): void
    {
        $admin = $this->createSuperAdmin();
        $this->actingAs($admin, 'admin');

        $response = $this->patch(route('admin.generalSetting.terms.update'), []);

        $response->assertStatus(302);
    }

    public function test_privacy_update_validation(): void
    {
        $admin = $this->createSuperAdmin();
        $this->actingAs($admin, 'admin');

        $response = $this->patch(route('admin.generalSetting.privacy.update'), []);

        $response->assertStatus(302);
    }

    public function test_unauthenticated_admin_cannot_access_settings(): void
    {
        $response = $this->get(route('admin.generalSetting.index'));

        $response->assertRedirect(route('admin.login'));
    }
}
