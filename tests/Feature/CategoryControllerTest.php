<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CategoryControllerTest extends TestCase
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
            'categories', 'add-category', 'edit-category', 'show-category', 'delete-category'
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
     * Test authenticated admin can view categories index.
     */
    public function test_authenticated_admin_can_view_categories_index(): void
    {
        $admin = $this->createSuperAdmin();
        $this->actingAs($admin, 'admin');

        $response = $this->get('/en/admin/categories');

        $response->assertStatus(200);
        $response->assertViewIs('dashboard.categories.index');
    }

    /**
     * Test authenticated admin can view create category form.
     */
    public function test_authenticated_admin_can_view_create_category_form(): void
    {
        $admin = $this->createSuperAdmin();
        $this->actingAs($admin, 'admin');

        $response = $this->get('/en/admin/categories/create');

        $response->assertStatus(200);
        $response->assertViewIs('dashboard.categories.create');
    }

    /**
     * Test authenticated admin can create category.
     */
    public function test_authenticated_admin_can_create_category(): void
    {
        $admin = $this->createSuperAdmin();
        $this->actingAs($admin, 'admin');

        $image = UploadedFile::fake()->image('category.jpg');

        $response = $this->postJson('/en/admin/categories', [
            'name' => [
                'ar' => 'فئة جديدة',
                'en' => 'New Category',
            ],
            'image' => $image,
            'status' => true,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('categories', [
            'status' => true,
        ]);
    }

    /**
     * Test category creation requires valid data.
     */
    public function test_category_creation_requires_valid_data(): void
    {
        $admin = $this->createSuperAdmin();
        $this->actingAs($admin, 'admin');

        $response = $this->postJson('/en/admin/categories', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name', 'image', 'status']);
    }

    /**
     * Test authenticated admin can view category details.
     */
    public function test_authenticated_admin_can_view_category_details(): void
    {
        $admin = $this->createSuperAdmin();
        $this->actingAs($admin, 'admin');

        $category = Category::factory()->create();

        $response = $this->get("/en/admin/categories/{$category->id}");

        $response->assertStatus(200);
        $response->assertViewIs('dashboard.categories.show');
        $response->assertViewHas('row');
    }

    /**
     * Test authenticated admin can view edit category form.
     */
    public function test_authenticated_admin_can_view_edit_category_form(): void
    {
        $admin = $this->createSuperAdmin();
        $this->actingAs($admin, 'admin');

        $category = Category::factory()->create();

        $response = $this->get("/en/admin/categories/{$category->id}/edit");

        $response->assertStatus(200);
        $response->assertViewIs('dashboard.categories.edit');
        $response->assertViewHas('row');
        $response->assertViewHas('max_order');
    }

    /**
     * Test authenticated admin can update category.
     */
    public function test_authenticated_admin_can_update_category(): void
    {
        $admin = $this->createSuperAdmin();
        $this->actingAs($admin, 'admin');

        $category = Category::factory()->create();

        $response = $this->putJson("/en/admin/categories/{$category->id}", [
            'name' => [
                'ar' => 'فئة محدثة',
                'en' => 'Updated Category',
            ],
            'status' => false,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'status' => false,
        ]);
    }

    /**
     * Test authenticated admin can delete category.
     */
    public function test_authenticated_admin_can_delete_category(): void
    {
        $admin = $this->createSuperAdmin();
        $this->actingAs($admin, 'admin');

        $category = Category::factory()->create();

        $response = $this->deleteJson("/en/admin/categories/{$category->id}");

        $response->assertStatus(200);
        $this->assertSoftDeleted('categories', ['id' => $category->id]);
    }

    /**
     * Test authenticated admin can delete multiple categories.
     */
    public function test_authenticated_admin_can_delete_multiple_categories(): void
    {
        $admin = $this->createSuperAdmin();
        $this->actingAs($admin, 'admin');

        $category1 = Category::factory()->create();
        $category2 = Category::factory()->create();

        $response = $this->postJson('/en/admin/categories/destroy-multiple', [
            'ids' => [$category1->id, $category2->id]
        ]);

        $response->assertStatus(200);
        $this->assertSoftDeleted('categories', ['id' => $category1->id]);
        $this->assertSoftDeleted('categories', ['id' => $category2->id]);
    }

    /**
     * Test authenticated admin can toggle category status.
     */
    public function test_authenticated_admin_can_toggle_category_status(): void
    {
        $admin = $this->createSuperAdmin();
        $this->actingAs($admin, 'admin');

        $category = Category::factory()->create(['status' => false]);

        $response = $this->getJson("/en/admin/categories/{$category->id}/toggle-status");

        $response->assertStatus(200);
        $response->assertJson(['key' => 'success']);
        $this->assertTrue($category->fresh()->status);
    }

    /**
     * Test authenticated admin can create category with parent.
     */
    public function test_authenticated_admin_can_create_category_with_parent(): void
    {
        $admin = $this->createSuperAdmin();
        $this->actingAs($admin, 'admin');

        $parentCategory = Category::factory()->create();
        $image = UploadedFile::fake()->image('category.jpg');

        $response = $this->postJson('/en/admin/categories', [
            'name' => [
                'ar' => 'فئة فرعية',
                'en' => 'Sub Category',
            ],
            'image' => $image,
            'status' => true,
            'parent_id' => $parentCategory->id,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('categories', [
            'parent_id' => $parentCategory->id,
        ]);
    }

    /**
     * Test categories index returns JSON for AJAX requests.
     */
    public function test_categories_index_returns_json_for_ajax_requests(): void
    {
        $admin = $this->createSuperAdmin();
        $this->actingAs($admin, 'admin');

        Category::factory()->count(3)->create();

        $response = $this->getJson('/en/admin/categories');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => ['id', 'name', 'status']
            ]
        ]);
    }
}
