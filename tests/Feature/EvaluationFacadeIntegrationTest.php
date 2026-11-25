<?php

namespace Tests\Feature;

use App\Enums\ChunkingStrategyEnum;
use App\Enums\EvaluationRubricEnum;
use App\Enums\RetrievalStrategyEnum;
use App\Models\Document;
use App\Models\PromptTemplate;
use App\Models\TestRun;
use App\Models\User;
use App\Models\Workspace;
use App\Services\Facades\EvaluationFacade;
use App\Services\Versioning\MementoService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EvaluationFacadeIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_full_rag_pipeline_and_memento_rollback(): void
    {
        $user = User::factory()->create(['role' => 'evaluator']);
        $workspace = Workspace::create([
            'user_id' => $user->id,
            'name' => 'Integration Lab',
            'slug' => 'integration-lab',
        ]);

        $facade = app(EvaluationFacade::class);
        $memento = app(MementoService::class);

        $doc = Document::create([
            'workspace_id' => $workspace->id,
            'title' => 'Facade Guide',
            'source_type' => 'paste',
            'raw_content' => 'EvaluationFacade coordinates chunking, retrieval, fallback execution, and rubric scoring.',
            'content_hash' => hash('sha256', 'test'),
        ]);
        $facade->ingestDocumentChunks($doc, ChunkingStrategyEnum::PARAGRAPH_BOUNDARY->value);

        $template = PromptTemplate::create([
            'workspace_id' => $workspace->id,
            'title' => 'QA Prompt',
        ]);

        $v1 = $memento->capturePromptSnapshot($template, 'System v1', 'Context: {{context}} Query: {{query}}', 'v1', $user->id);
        $v2 = $memento->capturePromptSnapshot($template, 'System v2 {{missing_var}}', 'Context: {{context}} Query: {{query}}', 'v2', $user->id);

        $testRun = TestRun::create([
            'workspace_id' => $workspace->id,
            'name' => 'Compare v1 vs v2',
            'query_text' => 'What does EvaluationFacade coordinate?',
            'chunking_strategy' => ChunkingStrategyEnum::PARAGRAPH_BOUNDARY->value,
            'retrieval_strategy' => RetrievalStrategyEnum::LEXICAL_OVERLAP->value,
            'evaluation_rubric' => EvaluationRubricEnum::GROUNDING_FAITHFULNESS->value,
        ]);

        $results = $facade->executeTestRun($testRun, [$v1->id, $v2->id]);

        $this->assertCount(2, $results);
        $this->assertSame('completed', $testRun->fresh()->status);
        $this->assertStringContainsString('[missing:missing_var]', $results[1]->rendered_prompt);
        $this->assertNotNull($results[0]->evaluationScore);

        // Verify Memento rollback creates v3 matching v1
        $v3 = $memento->restorePromptVersion($template, 1, $user->id);
        $this->assertSame(3, $v3->version_number);
        $this->assertSame('System v1', $v3->system_prompt);
    }
}
