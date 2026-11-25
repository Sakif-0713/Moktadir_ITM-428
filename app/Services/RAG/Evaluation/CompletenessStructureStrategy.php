<?php

namespace App\Services\RAG\Evaluation;

use App\Enums\EvaluationRubricEnum;
use Illuminate\Support\Collection;

class CompletenessStructureStrategy implements EvaluationRubricStrategyInterface
{
    public function key(): string
    {
        return EvaluationRubricEnum::COMPLETENESS_STRUCTURE->value;
    }

    public function evaluate(string $query, string $generatedOutput, Collection $retrievedChunks): array
    {
        $wordCount = str_word_count($generatedOutput);
        $hasStructure = preg_match('/(\n[-*]|\n\d+\.|#{1,3}\s|\*\*)/', $generatedOutput) === 1;

        $lengthScore = min(100.0, ($wordCount / 60) * 100.0);
        $structureBonus = $hasStructure ? 20.0 : 0.0;
        $completenessScore = round(min(100.0, ($lengthScore * 0.8) + $structureBonus), 2);

        $groundingBase = (new GroundingFaithfulnessStrategy())->evaluate($query, $generatedOutput, $retrievedChunks);
        $groundingScore = $groundingBase['grounding_score'];
        $retrievalRelevance = $groundingBase['retrieval_relevance'];

        // Completeness rubric prioritizes completeness & structure (55%)
        $overallScore = round(($completenessScore * 0.55) + ($groundingScore * 0.30) + ($retrievalRelevance * 0.15), 2);
        $threshold = (float) config('rag.regression_pass_threshold', 65.0);

        return [
            'grounding_score' => $groundingScore,
            'completeness_score' => $completenessScore,
            'retrieval_relevance' => $retrievalRelevance,
            'overall_score' => $overallScore,
            'passed_regression' => $overallScore >= $threshold,
            'evaluator_notes' => sprintf(
                'Completeness rubric: %d words, structured formatting=%s (threshold: %.1f%%).',
                $wordCount,
                $hasStructure ? 'yes' : 'no',
                $threshold
            ),
        ];
    }
}
