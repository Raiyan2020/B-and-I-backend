<?php

namespace Tests\Feature\Api;

use App\Enums\InvestorExperience;
use App\Enums\InvestorType;
use App\Enums\UserRole;
use App\Jobs\SendEmailVerificationJob;
use App\Models\Category;
use App\Models\PreferredSector;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Queue;
use Laravel\Sanctum\PersonalAccessToken;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuthProfileManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_investor_can_update_profile_details(): void
    {
        $investor = User::factory()->create([
            'role' => UserRole::Investor,
            'password' => 'password123',
        ]);
        $preferredSector = PreferredSector::query()->create([
            'name' => ['ar' => 'التقنية', 'en' => 'Technology'],
            'status' => true,
        ]);
        $category = Category::factory()->create(['status' => true]);

        Sanctum::actingAs($investor);

        $response = $this->patchJson('/api/v1/auth/profile', [
            'display_name' => 'Verified Investor',
            'bio' => 'نبذة للمستثمر',
            'short_description' => 'وصف مختصر',
            'available_capital' => 250000,
            'preferred_sector_id' => $preferredSector->id,
            'category_id' => $category->id,
            'investor_experience' => InvestorExperience::Beginner->value,
            'investor_type' => InvestorType::Angel->value,
        ]);

        $response->assertOk()
            ->assertJsonPath('data.display_name', 'Verified Investor')
            ->assertJsonPath('data.bio', 'نبذة للمستثمر')
            ->assertJsonPath('data.short_description', 'وصف مختصر')
            ->assertJsonPath('data.available_capital', '250000.000')
            ->assertJsonPath('data.preferred_sector.id', $preferredSector->id)
            ->assertJsonPath('data.category.id', $category->id)
            ->assertJsonPath('data.investor_experience', InvestorExperience::Beginner->value)
            ->assertJsonPath('data.investor_type', InvestorType::Angel->value);

        $this->assertDatabaseHas('users', [
            'id' => $investor->id,
            'display_name' => 'Verified Investor',
            'bio' => 'نبذة للمستثمر',
            'short_description' => 'وصف مختصر',
            'available_capital' => 250000,
            'preferred_sector_id' => $preferredSector->id,
            'category_id' => $category->id,
        ]);
    }

    public function test_advertiser_can_update_profile_and_email_becomes_unverified(): void
    {
        Queue::fake();

        $advertiser = User::factory()->create([
            'role' => UserRole::Advertiser,
            'email' => 'old@example.com',
            'email_verified_at' => now(),
            'password' => 'password123',
        ]);

        Sanctum::actingAs($advertiser);

        $response = $this->patchJson('/api/v1/auth/profile', [
            'display_name' => 'Company Display Name',
            'bio' => 'Company bio',
            'short_description' => 'Company short description',
            'email' => 'new@gmail.com',
        ]);

        $response->assertOk()
            ->assertJsonPath('data.display_name', 'Company Display Name')
            ->assertJsonPath('data.email', 'new@gmail.com')
            ->assertJsonPath('data.email_verified', false);

        $this->assertDatabaseHas('users', [
            'id' => $advertiser->id,
            'display_name' => 'Company Display Name',
            'email' => 'new@gmail.com',
        ]);

        $this->assertNull($advertiser->fresh()->email_verified_at);
        Queue::assertPushed(SendEmailVerificationJob::class);
    }

    public function test_user_can_change_password(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::Investor,
            'password' => 'oldpassword',
        ]);

        Sanctum::actingAs($user);

        $response = $this->patchJson('/api/v1/auth/password', [
            'current_password' => 'oldpassword',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertOk();
        $this->assertTrue(Hash::check('newpassword123', $user->fresh()->password));
    }

    public function test_change_password_fails_with_wrong_current_password(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::Advertiser,
            'password' => 'oldpassword',
        ]);

        Sanctum::actingAs($user);

        $response = $this->patchJson('/api/v1/auth/password', [
            'current_password' => 'wrongpassword',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('key', 'fail');
    }

    public function test_investor_can_logout(): void
    {
        $investor = User::factory()->create([
            'role' => UserRole::Investor,
            'password' => 'password123',
        ]);
        $token = $investor->createToken('investor-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/v1/auth/logout');

        $response->assertOk()
            ->assertJsonPath('key', 'success');

        $this->assertSame(0, PersonalAccessToken::query()->count());
    }

    public function test_advertiser_can_logout(): void
    {
        $advertiser = User::factory()->create([
            'role' => UserRole::Advertiser,
            'password' => 'password123',
        ]);
        $token = $advertiser->createToken('advertiser-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/v1/auth/logout');

        $response->assertOk()
            ->assertJsonPath('key', 'success');

        $this->assertSame(0, PersonalAccessToken::query()->count());
    }
}
