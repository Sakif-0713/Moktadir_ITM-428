<?php

namespace App\Services\RAG\Evaluation;

use App\Enums\EvaluationRubricEnum;
use Illuminate\Support\Collection;

class GroundingFaithfulnessStrategy implements EvaluationRubricStrategyInterface
{
    public function key(): string
    {
        return EvaluationRubricEnum::GROUNDING_FAITHFULNESS->value;
    }

    public function evaluate(string $query, string $generatedOutput, Collection $retrievedChunks): array
    {
        $contextText = $retrievedChunks->pluck('content')->implode(' ');
        $outputTokens = $this->significantWords($generatedOutput);
        $contextTokens = $this->significantWords($contextText);
        $queryTokens = $this->significantWords($query);

        $groundingScore = 0.0;
        if (! empty($outputTokens) && ! empty($contextTokens)) {
            $groundedMatches = count(array_intersect($outputTokens, $contextTokens));
            $groundingScore = round(min(100.0, ($groundedMatches / count($outputTokens)) * 115.0), 2);
        }

        $queryCoverage = (! empty($queryTokens) && ! empty($outputTokens))
            ? min(100.0, (count(array_intersect($queryTokens, $outputTokens)) / count($queryTokens)) * 100.0)
            : 0.0;
        $lengthFactor = min(100.0, (str_word_count($generatedOutput) / 35) * 100.0);
        $completenessScore = round(($queryCoverage * 0.6) + ($lengthFactor * 0.4), 2);

        $retrievalRelevance = $retrievedChunks->isNotEmpty()
            ? round((float) $retrievedChunks->avg('relevance_score'), 2)
            : 0.0;

        // Grounding rubric prioritizes faithfulness (60%) over completeness (25%) and retrieval (15%)
        $overallScore = round(($groundingScore * 0.60) + ($completenessScore * 0.25) + ($retrievalRelevance * 0.15), 2);
        $threshold = (float) config('rag.regression_pass_threshold', 65.0);

        return [
            'grounding_score' => $groundingScore,
            'completeness_score' => $completenessScore,
            'retrieval_relevance' => $retrievalRelevance,
            'overall_score' => $overallScore,
            'passed_regression' => $overallScore >= $threshold,
            'evaluator_notes' => sprintf(
                'Grounding rubric evaluated %d output terms against %d retrieved chunks (threshold: %.1f%%).',
                count($outputTokens),
                $retrievedChunks->count(),
                $threshold
            ),
        ];
    }

    /**
     * @return array<int, string>
     */
    private function significantWords(string $text): array
    {
        $clean = strtolower(preg_replace('/[^a-zA-Z0-9\s]/', ' ', $text) ?? '');
        $words = preg_split('/\s+/', trim($clean)) ?: [];
        $stopwords = ['the', 'a', 'an', 'is', 'are', 'was', 'were', 'in', 'on', 'at', 'to', 'for', 'of', 'and', 'or', 'with', 'this', 'that'];

        return array_values(array_unique(array_filter(
            $words,
            fn (string $w) => strlen($w) > 2 && ! in_array($w, $stopwords, true)
        )));
    }
}
