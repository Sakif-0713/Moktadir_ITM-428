<?php

namespace Tests\Unit;

use App\Models\DocumentChunk;
use App\Services\RAG\Evaluation\CompletenessStructureStrategy;
use App\Services\RAG\Evaluation\GroundingFaithfulnessStrategy;
use App\Services\RAG\Retrieval\HybridSimilarityRetrievalStrategy;
use App\Services\RAG\Retrieval\LexicalOverlapRetrievalStrategy;
use Tests\TestCase;

class RetrievalAndRubricStrategiesTest extends TestCase
{
    public function test_lexical_and_hybrid_retrieval_rank_relevant_chunk_first(): void
    {
        $c1 = new DocumentChunk(['content' => 'Cooking recipes for pasta and tomato sauce.']);
        $c1->id = 1;
        $c2 = new DocumentChunk(['content' => 'Boundary Value Analysis tests edge cases in software engineering.']);
        $c2->id = 2;
        $chunks = collect([$c1, $c2]);

        $lexical = (new LexicalOverlapRetrievalStrategy())->retrieve('Boundary Value Analysis', $chunks, 2);
        $hybrid = (new HybridSimilarityRetrievalStrategy())->retrieve('Boundary Value Analysis', $chunks, 2);

        $this->assertSame(2, $lexical->first()->id);
        $this->assertSame(2, $hybrid->first()->id);
        $this->assertGreaterThan($lexical->last()->relevance_score, $lexical->first()->relevance_score);
    }

    public function test_grounding_rubric_scores_grounded_output_higher_than_hallucination(): void
    {
        $chunk = new DocumentChunk([
            'content' => 'EvaluationFacade encapsulates chunking, retrieval, and fallback scoring.',
            'relevance_score' => 90.0,
        ]);

        $rubric = new GroundingFaithfulnessStrategy();

        $grounded = $rubric->evaluate(
            'What does EvaluationFacade do?',
            'EvaluationFacade encapsulates chunking, retrieval, and fallback scoring.',
            collect([$chunk])
        );

        $hallucinated = $rubric->evaluate(
            'What does EvaluationFacade do?',
            'Quantum bananas orbit purple galaxies inside submarines.',
            collect([$chunk])
        );

        $this->assertGreaterThan($hallucinated['grounding_score'], $grounded['grounding_score']);
        $this->assertTrue($grounded['passed_regression']);
        $this->assertFalse($hallucinated['passed_regression']);
    }

    public function test_completeness_rubric_rewards_structured_response(): void
    {
        $chunk = new DocumentChunk(['content' => 'Strategy pattern allows interchangeable algorithms.', 'relevance_score' => 80.0]);
        $rubric = new CompletenessStructureStrategy();

        $structured = $rubric->evaluate(
            'Explain Strategy Pattern',
            "### Strategy Pattern\n- Defines a family of algorithms.\n- Makes them interchangeable at runtime.\n- Decouples client from concrete implementations in software engineering.",
            collect([$chunk])
        );

        $this->assertGreaterThan(50.0, $structured['completeness_score']);
    }
}
