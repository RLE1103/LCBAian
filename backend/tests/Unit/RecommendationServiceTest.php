<?php

namespace Tests\Unit;

use App\Services\RecommendationService;
use App\Models\User;
use App\Models\JobPost;
use PHPUnit\Framework\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RecommendationServiceTest extends \Tests\TestCase
{
    use RefreshDatabase;

    /**
     * Test Jaccard similarity calculation
     */
    public function test_jaccard_similarity_calculation(): void
    {
        $set1 = ['PHP', 'Laravel', 'MySQL'];
        $set2 = ['PHP', 'Laravel', 'JavaScript'];

        $similarity = RecommendationService::calculateJaccard($set1, $set2);

        // Intersection: PHP, Laravel (2 items)
        // Union: PHP, Laravel, MySQL, JavaScript (4 items)
        // Expected: 2/4 = 0.5
        $this->assertEquals(0.5, $similarity);
    }

    /**
     * Test Jaccard similarity with identical sets
     */
    public function test_jaccard_similarity_identical_sets(): void
    {
        $set = ['PHP', 'Laravel', 'MySQL'];
        
        $similarity = RecommendationService::calculateJaccard($set, $set);

        $this->assertEquals(1.0, $similarity);
    }

    /**
     * Test Jaccard similarity with no overlap
     */
    public function test_jaccard_similarity_no_overlap(): void
    {
        $set1 = ['PHP', 'Laravel'];
        $set2 = ['Python', 'Django'];

        $similarity = RecommendationService::calculateJaccard($set1, $set2);

        $this->assertEquals(0.0, $similarity);
    }

    /**
     * Test Jaccard similarity with empty sets
     */
    public function test_jaccard_similarity_empty_sets(): void
    {
        $similarity = RecommendationService::calculateJaccard([], []);

        $this->assertEquals(0.0, $similarity);
    }

    /**
     * Test cosine similarity calculation
     */
    public function test_cosine_similarity_calculation(): void
    {
        $set1 = ['PHP', 'Laravel', 'MySQL'];
        $set2 = ['PHP', 'Laravel', 'JavaScript'];

        $similarity = RecommendationService::calculateCosine($set1, $set2);

        $this->assertEqualsWithDelta(0.6667, $similarity, 0.01);
    }

    /**
     * Test cosine similarity with identical vectors
     */
    public function test_cosine_similarity_identical_vectors(): void
    {
        $set = ['PHP', 'Laravel', 'MySQL'];

        $similarity = RecommendationService::calculateCosine($set, $set);

        $this->assertEqualsWithDelta(1.0, $similarity, 0.0001);
    }

    /**
     * Test similarity score calculation between user and job
     */
    public function test_calculate_similarity_score(): void
    {
        $user = User::factory()->create([
            'skills' => ['PHP', 'Laravel', 'MySQL'],
            'program' => 'BSCS',
            'industry' => 'Technology',
            'work_setup_preferences' => ['remote'],
            'employment_type_preferences' => ['full_time'],
            'industries_of_interest' => ['Technology'],
        ]);

        $job = JobPost::factory()->create([
            'required_skills' => ['PHP', 'Laravel'],
            'preferred_skills' => ['MySQL'],
            'work_type' => 'full_time',
            'industry' => 'Technology',
        ]);

        $score = RecommendationService::calculateSimilarityScore($user, $job);

        // Score should be > 0 since there's skill overlap
        $this->assertGreaterThan(0, $score);
        $this->assertLessThanOrEqual(1, $score);
    }

    /**
     * Test get recommended jobs returns sorted results
     */
    public function test_get_recommended_jobs_returns_sorted_results(): void
    {
        $user = User::factory()->create([
            'skills' => ['PHP', 'Laravel'],
            'program' => 'BSCS',
        ]);

        // Create jobs with varying relevance
        $highMatch = JobPost::factory()->create([
            'required_skills' => ['PHP', 'Laravel'],
            'status' => 'approved',
        ]);

        $lowMatch = JobPost::factory()->create([
            'required_skills' => ['Python'],
            'status' => 'approved',
        ]);

        $service = new RecommendationService();
        $recommendations = $service->getRecommendedJobs($user, 10);

        // Should return jobs sorted by similarity
        $this->assertGreaterThan(0, $recommendations->count());
        
        // First job should have higher similarity than subsequent ones
        if ($recommendations->count() > 1) {
            $this->assertGreaterThanOrEqual(
                $recommendations->last()->similarity_score,
                $recommendations->first()->similarity_score
            );
        }
    }
}
