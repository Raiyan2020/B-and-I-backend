<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Category;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class HomeControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedPermissions();
    }

    /**
     * Seed permissions and roles for testing.
     */
    protected function seedPermissions(): void
    {
        $superAdminRole = Role::firstOrCreate(
            ['name' => 'super_admin', 'guard_name' => 'admin']
        );
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
     * Test authenticated admin can view dashboard.
     */
    public function test_authenticated_admin_can_view_dashboard(): void
    {
        $admin = $this->createSuperAdmin();
        $this->actingAs($admin, 'admin');

        $response = $this->get('/en/admin/home');

        $response->assertStatus(200);
        $response->assertViewIs('dashboard.home.index');
    }

    /**
     * Test unauthenticated admin cannot view dashboard.
     */
    public function test_unauthenticated_admin_cannot_view_dashboard(): void
    {
        $response = $this->get('/en/admin/home');

        $response->assertRedirect(route('admin.login'));
    }

    /**
     * Test dashboard displays correct statistics.
     */
    public function test_dashboard_displays_correct_statistics(): void
    {
        $admin = $this->createSuperAdmin();
        $this->actingAs($admin, 'admin');

        // Create test data
        User::factory()->count(5)->create();
        Admin::factory()->count(3)->create();
        Category::factory()->count(4)->create();

        $response = $this->get('/en/admin/home');

        $response->assertStatus(200);
        $response->assertViewHas('clientsCount', 5);
        $response->assertViewHas('adminsCount', 4); // 3 created + 1 authenticated admin
        $response->assertViewHas('categoriesCount', 4);
    }

    /**
     * Test dashboard displays today's statistics.
     */
    public function test_dashboard_displays_today_statistics(): void
    {
        $admin = $this->createSuperAdmin();
        $this->actingAs($admin, 'admin');

        // Create users today
        User::factory()->count(2)->create([
            'created_at' => Carbon::today(),
        ]);

        // Create users yesterday
        User::factory()->count(3)->create([
            'created_at' => Carbon::yesterday(),
        ]);

        $response = $this->get('/en/admin/home');

        $response->assertStatus(200);
        $response->assertViewHas('todayUsersCount', 2);
    }

    /**
     * Test dashboard displays last 7 days data.
     */
    public function test_dashboard_displays_last_7_days_data(): void
    {
        $admin = $this->createSuperAdmin();
        $this->actingAs($admin, 'admin');

        // Create users over last 7 days
        for ($i = 0; $i < 7; $i++) {
            User::factory()->create([
                'created_at' => Carbon::now()->subDays($i),
            ]);
        }

        $response = $this->get('/en/admin/home');

        $response->assertStatus(200);
        $response->assertViewHas('last7Days');
        $response->assertViewHas('clientsData');
        $this->assertCount(7, $response->viewData('last7Days'));
        $this->assertCount(7, $response->viewData('clientsData'));
    }

    /**
     * Test dashboard displays recent activity.
     */
    public function test_dashboard_displays_recent_activity(): void
    {
        $admin = $this->createSuperAdmin();
        $this->actingAs($admin, 'admin');

        // Create recent users
        User::factory()->count(5)->create();

        $response = $this->get('/en/admin/home');

        $response->assertStatus(200);
        $response->assertViewHas('recentUsers');
        $this->assertCount(5, $response->viewData('recentUsers'));
    }

    /**
     * Test dashboard calculates growth percentages.
     */
    public function test_dashboard_calculates_growth_percentages(): void
    {
        $admin = $this->createSuperAdmin();
        $this->actingAs($admin, 'admin');

        // Create users in last week (7-14 days ago)
        User::factory()->count(10)->create([
            'created_at' => Carbon::now()->subDays(10),
        ]);

        // Create users this week (0-7 days ago)
        User::factory()->count(20)->create([
            'created_at' => Carbon::now()->subDays(3),
        ]);

        $response = $this->get('/en/admin/home');

        $response->assertStatus(200);
        $response->assertViewHas('usersGrowth');
        $this->assertGreaterThan(0, $response->viewData('usersGrowth'));
    }

    /**
     * Test dashboard handles empty data gracefully.
     */
    public function test_dashboard_handles_empty_data_gracefully(): void
    {
        $admin = $this->createSuperAdmin();
        $this->actingAs($admin, 'admin');

        $response = $this->get('/en/admin/home');

        $response->assertStatus(200);
        $response->assertViewHas('clientsCount', 0);
        $response->assertViewHas('adminsCount', 1); // Only authenticated admin
        $response->assertViewHas('categoriesCount', 0);
        $response->assertViewHas('todayUsersCount', 0);
    }
}
