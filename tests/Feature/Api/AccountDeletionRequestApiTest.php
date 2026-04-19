<?php

namespace Tests\Feature\Api;

use App\Enums\UserRole;
use App\Models\AccountDeletionRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AccountDeletionRequestApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_submit_account_deletion_request_and_view_latest_status(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::Advertiser,
        ]);

        Sanctum::actingAs($user);

        $submitResponse = $this->postJson('/api/v1/auth/account-deletion-requests');

        $submitResponse->assertCreated()
            ->assertJsonPath('key', 'success')
            ->assertJsonPath('msg', __('apis.account_deletion_request_submitted'))
            ->assertJsonPath('data.status.key', 'pending');

        $request = AccountDeletionRequest::query()->first();

        $this->assertNotNull($request);
        $this->assertDatabaseHas('account_deletion_requests', [
            'id' => $request->id,
            'user_id' => $user->id,
            'status' => 'pending',
        ]);

        $latestResponse = $this->getJson('/api/v1/auth/account-deletion-requests/latest');

        $latestResponse->assertOk()
            ->assertJsonPath('data.id', $request->id)
            ->assertJsonPath('data.status.key', 'pending');
    }

    public function test_user_cannot_submit_duplicate_pending_account_deletion_request(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::Investor,
        ]);

        AccountDeletionRequest::query()->create([
            'user_id' => $user->id,
            'status' => 'pending',
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/auth/account-deletion-requests');

        $response->assertStatus(422)
            ->assertJsonPath('key', 'fail')
            ->assertJsonPath('msg', __('apis.account_deletion_request_already_pending'))
            ->assertJsonPath('data.status.key', 'pending');

        $this->assertSame(1, AccountDeletionRequest::query()->count());
    }
}
