<?php

namespace App\Services\RAG\Evaluation;

use Illuminate\Support\Collection;

interface EvaluationRubricStrategyInterface
{
    /**
     * Evaluate generated output against query and retrieved context chunks.
     *
     * @param  string  $query
     * @param  string  $generatedOutput
     * @param  Collection<int, \App\Models\DocumentChunk>  $retrievedChunks
     * @return array{grounding_score: float, completeness_score: float, retrieval_relevance: float, overall_score: float, passed_regression: bool, evaluator_notes: string}
     */
    public function evaluate(string $query, string $generatedOutput, Collection $retrievedChunks): array;

    public function key(): string;
}
