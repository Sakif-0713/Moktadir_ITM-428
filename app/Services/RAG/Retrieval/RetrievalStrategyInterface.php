<?php

namespace App\Services\RAG\Retrieval;

use Illuminate\Support\Collection;

interface RetrievalStrategyInterface
{
    /**
     * Rank and return top-K relevant DocumentChunk models with an attached relevance_score attribute.
     *
     * @param  string  $query
     * @param  Collection<int, \App\Models\DocumentChunk>  $chunks
     * @param  int  $topK
     * @return Collection<int, \App\Models\DocumentChunk>
     */
    public function retrieve(string $query, Collection $chunks, int $topK = 3): Collection;

    public function key(): string;
}
