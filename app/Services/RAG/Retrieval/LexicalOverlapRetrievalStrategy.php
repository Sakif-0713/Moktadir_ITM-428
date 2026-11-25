<?php

namespace App\Services\RAG\Retrieval;

use App\Enums\RetrievalStrategyEnum;
use Illuminate\Support\Collection;

class LexicalOverlapRetrievalStrategy implements RetrievalStrategyInterface
{
    public function key(): string
    {
        return RetrievalStrategyEnum::LEXICAL_OVERLAP->value;
    }

    public function retrieve(string $query, Collection $chunks, int $topK = 3): Collection
    {
        if ($chunks->isEmpty() || trim($query) === '') {
            return collect();
        }

        $queryTerms = $this->tokenize($query);
        if (empty($queryTerms)) {
            return $chunks->take($topK)->map(function ($chunk) {
                $chunk->setAttribute('relevance_score', 0.0);

                return $chunk;
            });
        }

        return $chunks
            ->map(function ($chunk) use ($queryTerms) {
                $chunkTerms = $this->tokenize((string) $chunk->content);
                if (empty($chunkTerms)) {
                    $chunk->setAttribute('relevance_score', 0.0);

                    return $chunk;
                }

                $matches = count(array_intersect($queryTerms, $chunkTerms));
                $coverage = $matches / count($queryTerms);
                $density = $matches / max(1, sqrt(count($chunkTerms)));
                $score = round(min(100.0, ($coverage * 75.0) + ($density * 25.0)), 2);

                $chunk->setAttribute('relevance_score', $score);

                return $chunk;
            })
            ->sortByDesc('relevance_score')
            ->take(max(1, $topK))
            ->values();
    }

    /**
     * @return array<int, string>
     */
    protected function tokenize(string $text): array
    {
        $clean = strtolower(preg_replace('/[^a-zA-Z0-9\s]/', ' ', $text) ?? '');
        $words = preg_split('/\s+/', trim($clean)) ?: [];
        $stopwords = ['the', 'a', 'an', 'is', 'are', 'in', 'on', 'at', 'to', 'for', 'of', 'and', 'or', 'with', 'by'];

        return array_values(array_unique(array_filter(
            $words,
            fn (string $w) => strlen($w) > 1 && ! in_array($w, $stopwords, true)
        )));
    }
}
