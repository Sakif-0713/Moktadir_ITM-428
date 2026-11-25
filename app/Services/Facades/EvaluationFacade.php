<?php

namespace App\Services\Facades;

use App\Enums\ChunkingStrategyEnum;
use App\Enums\EvaluationRubricEnum;
use App\Enums\RetrievalStrategyEnum;
use App\Enums\TestRunStatusEnum;
use App\Models\Document;
use App\Models\DocumentChunk;
use App\Models\EvaluationScore;
use App\Models\PromptVersion;
use App\Models\TestRun;
use App\Models\TestRunResult;
use App\Services\RAG\Chunking\ChunkingStrategyInterface;
use App\Services\RAG\Chunking\FixedSizeChunkingStrategy;
use App\Services\RAG\Chunking\ParagraphBoundaryChunkingStrategy;
use App\Services\RAG\Evaluation\CompletenessStructureStrategy;
use App\Services\RAG\Evaluation\EvaluationRubricStrategyInterface;
use App\Services\RAG\Evaluation\GroundingFaithfulnessStrategy;
use App\Services\RAG\Providers\GeminiProvider;
use App\Services\RAG\Providers\LLMProviderInterface;
use App\Services\RAG\Retrieval\HybridSimilarityRetrievalStrategy;
use App\Services\RAG\Retrieval\LexicalOverlapRetrievalStrategy;
use App\Services\RAG\Retrieval\RetrievalStrategyInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Facade Pattern: Unified entry point for chunking, retrieval, LLM execution
 * with deterministic fallback, and automated rubric evaluation.
 */
class EvaluationFacade implements EvaluationFacadeInterface
{
    public function __construct(
        private readonly ?LLMProviderInterface $llmProvider = null
    ) {}

    public function ingestDocumentChunks(Document $document, string $strategyKey, array $options = []): Collection
    {
        $strategy = $this->resolveChunkingStrategy($strategyKey);
        $generated = $strategy->chunk((string) $document->raw_content, $options);

        return DB::transaction(function () use ($document, $strategy, $generated) {
            $document->chunks()->delete();

            $created = collect();
            foreach ($generated as $item) {
                $created->push(DocumentChunk::create([
                    'document_id' => $document->id,
                    'chunk_index' => $item['chunk_index'],
                    'content' => $item['content'],
                    'token_count' => $item['token_count'],
                    'strategy_used' => $strategy->key(),
                    'metadata' => $item['metadata'],
                ]));
            }

            $document->update(['chunk_count' => $created->count()]);

            return $created;
        });
    }

    public function executeTestRun(TestRun $testRun, array $promptVersionIds): Collection
    {
        $testRun->update(['status' => TestRunStatusEnum::RUNNING->value]);

        try {
            $workspace = $testRun->workspace()->with('documents.chunks')->firstOrFail();
            $allChunks = $workspace->documents->flatMap(fn (Document $doc) => $doc->chunks);

            $retrievalStrategy = $this->resolveRetrievalStrategy($testRun->retrieval_strategy);
            $rubricStrategy = $this->resolveRubricStrategy($testRun->evaluation_rubric);
            $provider = $this->llmProvider ?? new GeminiProvider();

            $topK = (int) config('rag.default_top_k', 3);
            $retrievedChunks = $retrievalStrategy->retrieve($testRun->query_text, $allChunks, $topK);

            $contextBlock = $retrievedChunks->isEmpty()
                ? '[No matching knowledge base context retrieved]'
                : $retrievedChunks->map(fn ($c, $i) => '[' . ($i + 1) . '] ' . $c->content)->implode("\n\n");

            $promptVersions = PromptVersion::whereIn('id', $promptVersionIds)->get();
            $results = collect();

            foreach ($promptVersions as $version) {
                $variables = array_merge([
                    'query' => $testRun->query_text,
                    'context' => $contextBlock,
                ], $testRun->variables_payload ?? []);

                $renderedSystem = $this->interpolate($version->system_prompt, $variables);
                $renderedUser = $this->interpolate($version->user_prompt, $variables);

                $generation = $provider->generate($renderedSystem, $renderedUser, [
                    'query' => $testRun->query_text,
                    'chunks' => $retrievedChunks->pluck('content')->all(),
                    'prompt_version' => $version->version_number,
                ]);

                $result = TestRunResult::create([
                    'test_run_id' => $testRun->id,
                    'prompt_version_id' => $version->id,
                    'model_identifier' => $generation['model'],
                    'rendered_prompt' => "SYSTEM:\n{$renderedSystem}\n\nUSER:\n{$renderedUser}",
                    'retrieved_chunk_ids' => $retrievedChunks->pluck('id')->values()->all(),
                    'generated_output' => $generation['output'],
                    'latency_ms' => $generation['latency_ms'],
                    'prompt_tokens' => $generation['prompt_tokens'],
                    'completion_tokens' => $generation['completion_tokens'],
                    'is_fallback' => $generation['is_fallback'],
                ]);

                $scoreData = $rubricStrategy->evaluate(
                    $testRun->query_text,
                    $generation['output'],
                    $retrievedChunks
                );

                EvaluationScore::create(array_merge($scoreData, [
                    'test_run_result_id' => $result->id,
                    'rubric_type' => $rubricStrategy->key(),
                ]));

                $results->push($result->load('evaluationScore', 'promptVersion.template'));
            }

            $testRun->update(['status' => TestRunStatusEnum::COMPLETED->value]);

            return $results;
        } catch (Throwable $e) {
            $testRun->update(['status' => TestRunStatusEnum::FAILED->value]);
            throw $e;
        }
    }

    public function resolveChunkingStrategy(string $key): ChunkingStrategyInterface
    {
        return match ($key) {
            ChunkingStrategyEnum::PARAGRAPH_BOUNDARY->value => new ParagraphBoundaryChunkingStrategy(),
            default => new FixedSizeChunkingStrategy(),
        };
    }

    public function resolveRetrievalStrategy(string $key): RetrievalStrategyInterface
    {
        return match ($key) {
            RetrievalStrategyEnum::HYBRID_SIMILARITY->value => new HybridSimilarityRetrievalStrategy(),
            default => new LexicalOverlapRetrievalStrategy(),
        };
    }

    public function resolveRubricStrategy(string $key): EvaluationRubricStrategyInterface
    {
        return match ($key) {
            EvaluationRubricEnum::COMPLETENESS_STRUCTURE->value => new CompletenessStructureStrategy(),
            default => new GroundingFaithfulnessStrategy(),
        };
    }

    /**
     * Safely interpolate {{variable}} placeholders, replacing missing variables with a readable marker.
     *
     * @param  array<string, mixed>  $variables
     */
    private function interpolate(string $template, array $variables): string
    {
        return (string) preg_replace_callback('/\{\{\s*([a-zA-Z0-9_]+)\s*\}\}/', function (array $matches) use ($variables) {
            $key = $matches[1];
            if (array_key_exists($key, $variables) && $variables[$key] !== null && $variables[$key] !== '') {
                return (string) $variables[$key];
            }

            return "[missing:{$key}]";
        }, $template);
    }
}
