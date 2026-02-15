<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserVerificationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test admin can approve user
     */
    public function test_admin_can_approve_user(): void
    {
        $admin = User::factory()->createOne(['role' => 'admin']);
        $admin = User::query()->whereKey($admin->id)->firstOrFail();
        $unverifiedUser = User::factory()->createOne(['is_verified' => false]);
        $unverifiedUser = User::query()->whereKey($unverifiedUser->id)->firstOrFail();

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
        $admin = User::factory()->createOne(['role' => 'admin']);
        $admin = User::query()->whereKey($admin->id)->firstOrFail();
        $unverifiedUser = User::factory()->createOne(['is_verified' => false]);
        $unverifiedUser = User::query()->whereKey($unverifiedUser->id)->firstOrFail();

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
        $alumniUser = User::factory()->createOne(['role' => 'alumni']);
        $alumniUser = User::query()->whereKey($alumniUser->id)->firstOrFail();
        $unverifiedUser = User::factory()->createOne(['is_verified' => false]);
        $unverifiedUser = User::query()->whereKey($unverifiedUser->id)->firstOrFail();

        $response = $this->actingAs($alumniUser)
                         ->postJson("/api/admin/users/{$unverifiedUser->id}/approve");

        $response->assertStatus(403);
    }

    /**
     * Test cannot modify admin account
     */
    public function test_cannot_modify_admin_account(): void
    {
        $admin1 = User::factory()->createOne(['role' => 'admin']);
        $admin1 = User::query()->whereKey($admin1->id)->firstOrFail();
        $admin2 = User::factory()->createOne(['role' => 'admin']);
        $admin2 = User::query()->whereKey($admin2->id)->firstOrFail();

        $response = $this->actingAs($admin1)
                         ->postJson("/api/admin/users/{$admin2->id}/approve");

        $response->assertStatus(403)
                 ->assertJson([
                     'success' => false,
                     'message' => 'Cannot modify admin accounts.',
                 ]);
    }

    public function test_admin_can_promote_user_to_admin(): void
    {
        $admin = User::factory()->createOne([
            'role' => 'admin',
            'password' => Hash::make('secret123')
        ]);
        $admin = User::query()->whereKey($admin->id)->firstOrFail();
        $alumniUser = User::factory()->createOne(['role' => 'alumni']);
        $alumniUser = User::query()->whereKey($alumniUser->id)->firstOrFail();

        $response = $this->actingAs($admin)
                         ->postJson("/api/admin/users/{$alumniUser->id}/role", [
                             'role' => 'admin',
                             'admin_password' => 'secret123'
                         ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'User role updated.',
                 ]);

        $this->assertDatabaseHas('users', [
            'id' => $alumniUser->id,
            'role' => 'admin',
        ]);
    }

    public function test_admin_cannot_remove_own_admin_privileges(): void
    {
        $admin = User::factory()->createOne([
            'role' => 'admin',
            'password' => Hash::make('secret123')
        ]);
        $admin = User::query()->whereKey($admin->id)->firstOrFail();

        $response = $this->actingAs($admin)
                         ->postJson("/api/admin/users/{$admin->id}/role", [
                             'role' => 'alumni',
                             'admin_password' => 'secret123'
                         ]);

        $response->assertStatus(403)
                 ->assertJson([
                     'success' => false,
                     'message' => 'You cannot remove your own admin privileges.',
                 ]);

        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
            'role' => 'admin',
        ]);
    }

    public function test_admin_role_change_requires_valid_password(): void
    {
        $admin = User::factory()->createOne([
            'role' => 'admin',
            'password' => Hash::make('secret123')
        ]);
        $admin = User::query()->whereKey($admin->id)->firstOrFail();
        $alumniUser = User::factory()->createOne(['role' => 'alumni']);
        $alumniUser = User::query()->whereKey($alumniUser->id)->firstOrFail();

        $response = $this->actingAs($admin)
                         ->postJson("/api/admin/users/{$alumniUser->id}/role", [
                             'role' => 'admin',
                             'admin_password' => 'wrongpass'
                         ]);

        $response->assertStatus(403)
                 ->assertJson([
                     'success' => false,
                     'message' => 'Invalid admin password.',
                 ]);

        $this->assertDatabaseHas('users', [
            'id' => $alumniUser->id,
            'role' => 'alumni',
        ]);
    }
}
