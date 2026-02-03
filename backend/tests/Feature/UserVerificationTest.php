<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserVerificationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test admin can approve user
     */
    public function test_admin_can_approve_user(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $unverifiedUser = User::factory()->create(['is_verified' => false]);

        $response = $this->actingAs($admin)
                         ->postJson("/api/admin/users/{$unverifiedUser->id}/approve");

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'User approved successfully.',
                 ]);

        $this->assertDatabaseHas('users', [
            'id' => $unverifiedUser->id,
            'is_verified' => true,
        ]);
    }

    /**
     * Test admin can reject user
     */
    public function test_admin_can_reject_user(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $unverifiedUser = User::factory()->create(['is_verified' => false]);

        $response = $this->actingAs($admin)
                         ->postJson("/api/admin/users/{$unverifiedUser->id}/reject", [
                             'reason' => 'Invalid information'
                         ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                 ]);

        $this->assertDatabaseMissing('users', [
            'id' => $unverifiedUser->id,
        ]);
    }

    /**
     * Test non-admin cannot approve user
     */
    public function test_non_admin_cannot_approve_user(): void
    {
        $alumniUser = User::factory()->create(['role' => 'alumni']);
        $unverifiedUser = User::factory()->create(['is_verified' => false]);

        $response = $this->actingAs($alumniUser)
                         ->postJson("/api/admin/users/{$unverifiedUser->id}/approve");

        $response->assertStatus(403);
    }

    /**
     * Test cannot modify admin account
     */
    public function test_cannot_modify_admin_account(): void
    {
        $admin1 = User::factory()->create(['role' => 'admin']);
        $admin2 = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin1)
                         ->postJson("/api/admin/users/{$admin2->id}/approve");

        $response->assertStatus(403)
                 ->assertJson([
                     'success' => false,
                     'message' => 'Cannot modify admin accounts.',
                 ]);
    }
}
