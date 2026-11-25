<?php

namespace App\Services\Facades;

use App\Models\Document;
use App\Models\TestRun;
use Illuminate\Support\Collection;

interface EvaluationFacadeInterface
{
    /**
     * Chunk and persist chunks for a document using the chosen strategy.
     */
    public function ingestDocumentChunks(Document $document, string $strategyKey, array $options = []): Collection;

    /**
     * Execute a full RAG evaluation run across one or more PromptVersions.
     *
     * @param  array<int, int>  $promptVersionIds
     * @return Collection<int, \App\Models\TestRunResult>
     */
    public function executeTestRun(TestRun $testRun, array $promptVersionIds): Collection;
}
