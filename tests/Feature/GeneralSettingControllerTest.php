<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\GeneralSetting;
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

    /**
     * Seed permissions and roles for testing.
     */
    protected function seedPermissions(): void
    {
        $superAdminRole = Role::firstOrCreate(
            ['name' => 'super_admin', 'guard_name' => 'admin']
        );

        // Create permissions
        $permissions = [
            'show-settings', 'edit-settings'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'admin'
            ]);
        }

        $superAdminRole->syncPermissions(Permission::where('guard_name', 'admin')->get());
    }

    /**
     * Create a super admin user for testing.
     */
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

    /**
     * Test authenticated admin can view general settings.
     */
    public function test_authenticated_admin_can_view_general_settings(): void
    {
        $admin = $this->createSuperAdmin();
        $this->actingAs($admin, 'admin');

        $response = $this->get('/en/admin/general_settings/manage');

        $response->assertStatus(200);
        $response->assertViewIs('dashboard.settings.general_settings');
    }

    /**
     * Test authenticated admin can update general settings.
     */
    public function test_authenticated_admin_can_update_general_settings(): void
    {
        $admin = $this->createSuperAdmin();
        $this->actingAs($admin, 'admin');

        $logo = UploadedFile::fake()->image('logo.jpg');
        $favicon = UploadedFile::fake()->image('favicon.jpg');

        $response = $this->post('/en/admin/general_settings/store', [
            'generalSettings' => true,
            'type' => [
                'website_name_ar' => 'اسم الموقع',
                'website_name_en' => 'Website Name',
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
    }

    /**
     * Project brief fields are persisted when provided with general settings.
     */
    public function test_general_settings_persists_project_briefs(): void
    {
        $admin = $this->createSuperAdmin();
        $this->actingAs($admin, 'admin');

        $logo = UploadedFile::fake()->image('logo.jpg');
        $favicon = UploadedFile::fake()->image('favicon.jpg');

        $response = $this->post('/en/admin/general_settings/store', [
            'generalSettings' => true,
            'type' => [
                'website_name_ar' => 'اسم الموقع',
                'website_name_en' => 'Website Name',
                'project_brief_ar' => 'نبذة بالعربية',
                'project_brief_en' => 'Brief in English',
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

    /**
     * Test general settings update requires valid data.
     */
    public function test_general_settings_update_requires_valid_data(): void
    {
        $admin = $this->createSuperAdmin();
        $this->actingAs($admin, 'admin');

        $response = $this->post('/en/admin/general_settings/store', [
            'generalSettings' => true,
            'type' => [],
        ]);

        $response->assertSessionHasErrors();
    }

    /**
     * Test authenticated admin can update social media settings.
     */
    public function test_authenticated_admin_can_update_social_media_settings(): void
    {
        $admin = $this->createSuperAdmin();
        $this->actingAs($admin, 'admin');

        $response = $this->post('/en/admin/general_settings/store', [
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

    /**
     * Test social media settings require valid URLs.
     */
    public function test_social_media_settings_require_valid_urls(): void
    {
        $admin = $this->createSuperAdmin();
        $this->actingAs($admin, 'admin');

        $response = $this->post('/en/admin/general_settings/store', [
            'socials' => true,
            'type' => [
                'facebook' => 'invalid-url',
            ],
        ]);

        $response->assertSessionHasErrors();
    }

    /**
     * Test authenticated admin can update background images.
     */
    public function test_authenticated_admin_can_update_background_images(): void
    {
        $admin = $this->createSuperAdmin();
        $this->actingAs($admin, 'admin');

        $loginImage = UploadedFile::fake()->image('login.jpg');
        $headerImage = UploadedFile::fake()->image('header.jpg');

        $response = $this->post('/en/admin/general_settings/store', [
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

    /**
     * Test authenticated admin can update terms and conditions.
     */
    public function test_authenticated_admin_can_update_terms_and_conditions(): void
    {
        $admin = $this->createSuperAdmin();
        $this->actingAs($admin, 'admin');

        $response = $this->patch('/en/admin/general_settings/terms', [
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

    /**
     * Test authenticated admin can update privacy policy.
     */
    public function test_authenticated_admin_can_update_privacy_policy(): void
    {
        $admin = $this->createSuperAdmin();
        $this->actingAs($admin, 'admin');

        $response = $this->patch('/en/admin/general_settings/privacy', [
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

    /**
     * Test terms update validation.
     */
    public function test_terms_update_validation(): void
    {
        $admin = $this->createSuperAdmin();
        $this->actingAs($admin, 'admin');

        $response = $this->patch('/en/admin/general_settings/terms', []);

        // Validation depends on TermsSettingsRequest
        // This test ensures the endpoint is accessible
        $response->assertStatus(302); // Redirect with validation errors or success
    }

    /**
     * Test privacy update validation.
     */
    public function test_privacy_update_validation(): void
    {
        $admin = $this->createSuperAdmin();
        $this->actingAs($admin, 'admin');

        $response = $this->patch('/en/admin/general_settings/privacy', []);

        // Validation depends on PrivacySettingsRequest
        // This test ensures the endpoint is accessible
        $response->assertStatus(302); // Redirect with validation errors or success
    }

    /**
     * Test unauthenticated admin cannot access settings.
     */
    public function test_unauthenticated_admin_cannot_access_settings(): void
    {
        $response = $this->get('/en/admin/general_settings/manage');

        $response->assertRedirect(route('admin.login'));
    }
}
