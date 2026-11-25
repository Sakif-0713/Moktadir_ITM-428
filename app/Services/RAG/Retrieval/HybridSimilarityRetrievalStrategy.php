<?php

namespace App\Services\RAG\Retrieval;

use App\Enums\RetrievalStrategyEnum;
use Illuminate\Support\Collection;

class HybridSimilarityRetrievalStrategy extends LexicalOverlapRetrievalStrategy
{
    public function key(): string
    {
        return RetrievalStrategyEnum::HYBRID_SIMILARITY->value;
    }

    public function retrieve(string $query, Collection $chunks, int $topK = 3): Collection
    {
        if ($chunks->isEmpty() || trim($query) === '') {
            return collect();
        }

        $queryTerms = $this->tokenize($query);
        $queryTrigrams = $this->trigrams(strtolower($query));

        return $chunks
            ->map(function ($chunk) use ($queryTerms, $queryTrigrams) {
                $content = (string) $chunk->content;
                $chunkTerms = $this->tokenize($content);

                $lexicalScore = empty($queryTerms)
                    ? 0.0
                    : (count(array_intersect($queryTerms, $chunkTerms)) / count($queryTerms)) * 100.0;

                $chunkTrigrams = $this->trigrams(strtolower($content));
                $trigramUnion = count(array_unique(array_merge($queryTrigrams, $chunkTrigrams)));
                $trigramJaccard = $trigramUnion > 0
                    ? (count(array_intersect($queryTrigrams, $chunkTrigrams)) / $trigramUnion) * 100.0
                    : 0.0;

                $hybridScore = round(min(100.0, ($lexicalScore * 0.65) + ($trigramJaccard * 0.35)), 2);
                $chunk->setAttribute('relevance_score', $hybridScore);

                return $chunk;
            })
            ->sortByDesc('relevance_score')
            ->take(max(1, $topK))
            ->values();
    }

    /**
     * @return array<int, string>
     */
    private function trigrams(string $text): array
    {
        $clean = preg_replace('/\s+/', ' ', trim($text)) ?? '';
        $len = strlen($clean);
        if ($len < 3) {
            return $clean === '' ? [] : [$clean];
        }

        $grams = [];
        for ($i = 0; $i <= $len - 3; $i++) {
            $grams[] = substr($clean, $i, 3);
        }

        return array_values(array_unique($grams));
    }
}
