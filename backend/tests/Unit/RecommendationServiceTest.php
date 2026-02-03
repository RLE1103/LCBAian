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

        $similarity = RecommendationService::jaccardSimilarity($set1, $set2);

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
        
        $similarity = RecommendationService::jaccardSimilarity($set, $set);

        $this->assertEquals(1.0, $similarity);
    }

    /**
     * Test Jaccard similarity with no overlap
     */
    public function test_jaccard_similarity_no_overlap(): void
    {
        $set1 = ['PHP', 'Laravel'];
        $set2 = ['Python', 'Django'];

        $similarity = RecommendationService::jaccardSimilarity($set1, $set2);

        $this->assertEquals(0.0, $similarity);
    }

    /**
     * Test Jaccard similarity with empty sets
     */
    public function test_jaccard_similarity_empty_sets(): void
    {
        $similarity = RecommendationService::jaccardSimilarity([], []);

        $this->assertEquals(0.0, $similarity);
    }

    /**
     * Test cosine similarity calculation
     */
    public function test_cosine_similarity_calculation(): void
    {
        // Test with simple vectors
        $vector1 = [1, 1, 0];
        $vector2 = [1, 0, 1];

        $similarity = RecommendationService::cosineSimilarity($vector1, $vector2);

        // Dot product: 1*1 + 1*0 + 0*1 = 1
        // Magnitude v1: sqrt(1 + 1 + 0) = sqrt(2) ≈ 1.414
        // Magnitude v2: sqrt(1 + 0 + 1) = sqrt(2) ≈ 1.414
        // Cosine: 1 / (1.414 * 1.414) ≈ 0.5
        $this->assertEqualsWithDelta(0.5, $similarity, 0.01);
    }

    /**
     * Test cosine similarity with identical vectors
     */
    public function test_cosine_similarity_identical_vectors(): void
    {
        $vector = [1, 2, 3];

        $similarity = RecommendationService::cosineSimilarity($vector, $vector);

        $this->assertEquals(1.0, $similarity);
    }

    /**
     * Test similarity score calculation between user and job
     */
    public function test_calculate_similarity_score(): void
    {
        $user = User::factory()->create([
            'skills' => json_encode(['PHP', 'Laravel', 'MySQL']),
            'program' => 'BSCS',
            'industry' => 'Technology',
            'career_preferences' => json_encode([
                'work_setup' => 'remote',
                'employment_type' => 'full_time',
            ]),
        ]);

        $job = JobPost::factory()->create([
            'required_skills' => json_encode(['PHP', 'Laravel']),
            'preferred_skills' => json_encode(['MySQL']),
            'work_type' => 'full_time',
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
            'skills' => json_encode(['PHP', 'Laravel']),
            'program' => 'BSCS',
        ]);

        // Create jobs with varying relevance
        $highMatch = JobPost::factory()->create([
            'required_skills' => json_encode(['PHP', 'Laravel']),
            'status' => 'approved',
        ]);

        $lowMatch = JobPost::factory()->create([
            'required_skills' => json_encode(['Python']),
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
