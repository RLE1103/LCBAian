<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\JobPost;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JobPostTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test user can create job post
     */
    public function test_user_can_create_job_post(): void
    {
        $user = $this->createUser();

        $response = $this->actingAs($user)
                         ->postJson('/api/job-posts', [
                             'title' => 'Software Developer',
                             'description' => 'We are hiring',
                             'location' => 'Manila',
                             'work_type' => 'full_time',
                             'status' => 'pending',
                         ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('job_posts', [
            'title' => 'Software Developer',
            'status' => 'pending',
        ]);
    }

    /**
     * Test admin can approve job post
     */
    public function test_admin_can_approve_job_post(): void
    {
        $admin = $this->createUser(['role' => 'admin']);
        $job = JobPost::factory()->create(['status' => 'pending']);

        $response = $this->actingAs($admin)
                         ->postJson("/api/admin/jobs/{$job->job_id}/approve");

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);

        $this->assertDatabaseHas('job_posts', [
            'job_id' => $job->job_id,
            'status' => 'approved',
        ]);
    }

    /**
     * Test admin can reject job post
     */
    public function test_admin_can_reject_job_post(): void
    {
        $admin = $this->createUser(['role' => 'admin']);
        $job = JobPost::factory()->create(['status' => 'pending']);

        $response = $this->actingAs($admin)
                         ->postJson("/api/admin/jobs/{$job->job_id}/reject", [
                             'reason' => 'Inappropriate content'
                         ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('job_posts', [
            'job_id' => $job->job_id,
            'status' => 'rejected',
        ]);
    }

    /**
     * Test non-admin cannot approve jobs
     */
    public function test_non_admin_cannot_approve_job(): void
    {
        $user = $this->createUser(['role' => 'alumni']);
        $job = JobPost::factory()->create(['status' => 'pending']);

        $response = $this->actingAs($user)
                         ->postJson("/api/admin/jobs/{$job->job_id}/approve");

        $response->assertStatus(403);
    }

    /**
     * Test job post requires authentication
     */
    public function test_job_post_requires_authentication(): void
    {
        $response = $this->postJson('/api/job-posts', [
            'title' => 'Test Job',
            'description' => 'Test',
        ]);

        $response->assertStatus(401);
    }

    private function createUser(array $overrides = []): User
    {
        return User::factory()->createOne($overrides);
    }
}
