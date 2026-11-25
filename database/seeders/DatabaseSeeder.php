<?php

namespace Database\Seeders;

use App\Enums\ChunkingStrategyEnum;
use App\Enums\EvaluationRubricEnum;
use App\Enums\RetrievalStrategyEnum;
use App\Models\Document;
use App\Models\PromptTemplate;
use App\Models\RefactorLog;
use App\Models\TestRun;
use App\Models\User;
use App\Models\Workspace;
use App\Services\Facades\EvaluationFacade;
use App\Services\Versioning\MementoService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $instructor = User::firstOrCreate(
            ['email' => 'evaluator@zums.edu'],
            [
                'name' => 'Dr. Evaluator (Guest Demo)',
                'password' => Hash::make('demo-password'),
                'role' => 'evaluator',
            ]
        );

        $student = User::firstOrCreate(
            ['email' => 'moktadir@zums.edu'],
            [
                'name' => 'Moktadir (ITM 428)',
                'password' => Hash::make('student-password'),
                'role' => 'student',
            ]
        );

        $workspace = Workspace::firstOrCreate(
            ['slug' => 'itm-428-rag-demo'],
            [
                'user_id' => $instructor->id,
                'name' => 'ITM 427/428 RAG Evaluation Lab',
                'description' => 'Pre-seeded public demo workspace for evaluating chunking, retrieval, prompt versioning, and grounding rubrics.',
                'role_metadata' => [
                    'owner' => 'Dr. Evaluator',
                    'contributors' => ['Moktadir'],
                    'course' => 'ITM 427 / ITM 428',
                ],
                'is_public_demo' => true,
            ]
        );

        $memento = app(MementoService::class);
        $facade = app(EvaluationFacade::class);

        // 1. Seed Documents & Versions & Chunks
        if ($workspace->documents()->count() === 0) {
            $doc1Text = "# Software Testing & Boundary Value Analysis\n\n"
                . "Boundary Value Analysis (BVA) is a black-box test design technique that focuses on values at the edges of equivalence partitions. "
                . "Defects frequently cluster around boundaries such as zero-length inputs, maximum string lengths, null/empty payloads, and off-by-one loop limits.\n\n"
                . "In RAG pipelines, boundary cases include empty documents, oversized paragraphs exceeding token limits, missing template variables like {{context}}, and external LLM API timeouts. "
                . "Robust systems must handle these gracefully using deterministic fallbacks and explicit validation rules.\n\n"
                . "# Regression Testing & Smoke Suites\n\n"
                . "Smoke tests verify that critical system paths—such as authentication, database connectivity, and primary evaluation routes—function after deployment. "
                . "Regression suites compare new prompt versions against baseline evaluation scores to prevent quality degradation.";

            $doc1 = Document::create([
                'workspace_id' => $workspace->id,
                'title' => 'Software Testing & QA Handbook',
                'source_type' => 'paste',
                'raw_content' => $doc1Text,
                'content_hash' => hash('sha256', $doc1Text),
            ]);
            $memento->captureDocumentSnapshot($doc1, $doc1Text, 'Initial QA handbook ingestion', $student->id);
            $facade->ingestDocumentChunks($doc1, ChunkingStrategyEnum::PARAGRAPH_BOUNDARY->value);

            $doc2Text = "# Design Patterns in Enterprise Laravel\n\n"
                . "The Facade Pattern provides a unified, simplified interface to a complex subsystem. In this workbench, EvaluationFacade hides chunking, top-k retrieval, prompt interpolation, LLM invocation, deterministic fallback, and rubric scoring behind a single method call.\n\n"
                . "The Strategy Pattern defines a family of interchangeable algorithms—such as Fixed-Size vs. Paragraph-Boundary chunking, Lexical vs. Hybrid retrieval, and Grounding vs. Completeness evaluation rubrics—selectable at runtime per test run.\n\n"
                . "The Memento Pattern captures and externalizes an object's internal state without violating encapsulation, enabling immutable PromptVersion snapshots and safe rollback.";

            $doc2 = Document::create([
                'workspace_id' => $workspace->id,
                'title' => 'RAG Architecture & Design Patterns Guide',
                'source_type' => 'paste',
                'raw_content' => $doc2Text,
                'content_hash' => hash('sha256', $doc2Text),
            ]);
            $memento->captureDocumentSnapshot($doc2, $doc2Text, 'Initial architecture guide', $student->id);
            $facade->ingestDocumentChunks($doc2, ChunkingStrategyEnum::PARAGRAPH_BOUNDARY->value);
        }

        // 2. Seed Prompt Templates & Memento Versions
        if ($workspace->promptTemplates()->count() === 0) {
            $template = PromptTemplate::create([
                'workspace_id' => $workspace->id,
                'title' => 'Strict Context-Grounded Q&A',
                'description' => 'Answers user queries strictly using retrieved knowledge base context with {{tone}} formatting.',
            ]);

            $v1 = $memento->capturePromptSnapshot(
                $template,
                'You are a helpful software engineering assistant. Answer based on context.',
                "Context:\n{{context}}\n\nQuestion: {{query}}",
                'v1: Baseline prompt without strict citation rules',
                $student->id
            );

            $v2 = $memento->capturePromptSnapshot(
                $template,
                'You are a strict RAG evaluator. Use ONLY the provided context. Tone: {{tone}}. Cite sources clearly and avoid unsupported claims.',
                "### Retrieved Context\n{{context}}\n\n### Query\n{{query}}\n\nProvide a structured, grounded response.",
                'v2: Added {{tone}} variable, strict grounding instruction, and structured formatting',
                $student->id
            );

            // 3. Seed Comparative TestRun (v1 vs v2 side-by-side)
            $testRun = TestRun::create([
                'workspace_id' => $workspace->id,
                'name' => 'Baseline vs. Strict Grounding Comparison',
                'query_text' => 'How do Facade, Strategy, and Boundary Value Analysis work together in this RAG workbench?',
                'variables_payload' => ['tone' => 'academic and concise'],
                'chunking_strategy' => ChunkingStrategyEnum::PARAGRAPH_BOUNDARY->value,
                'retrieval_strategy' => RetrievalStrategyEnum::HYBRID_SIMILARITY->value,
                'evaluation_rubric' => EvaluationRubricEnum::GROUNDING_FAITHFULNESS->value,
            ]);

            $facade->executeTestRun($testRun, [$v1->id, $v2->id]);
        }

        // 4. Seed Refactoring & Bug-Fix Logs
        if ($workspace->refactorLogs()->count() === 0) {
            $logs = [
                [
                    'sprint_phase' => 'Phase 1',
                    'component_modified' => 'EvaluationFacade',
                    'design_pattern_applied' => 'Facade Pattern',
                    'issue_resolved' => 'TestRunController previously coupled chunking, retrieval, and scoring logic directly.',
                    'resolution_summary' => 'Extracted orchestration into EvaluationFacade with DeterministicMockProvider fallback.',
                    'commit_ref' => 'feat/facade',
                ],
                [
                    'sprint_phase' => 'Phase 2',
                    'component_modified' => 'Chunking & Retrieval Services',
                    'design_pattern_applied' => 'Strategy Pattern',
                    'issue_resolved' => 'Hardcoded fixed-size text splitting caused mid-sentence context breaks.',
                    'resolution_summary' => 'Introduced ChunkingStrategyInterface, RetrievalStrategyInterface, and EvaluationRubricStrategyInterface.',
                    'commit_ref' => 'feat/strategy',
                ],
                [
                    'sprint_phase' => 'Phase 2',
                    'component_modified' => 'MementoService',
                    'design_pattern_applied' => 'Memento Pattern',
                    'issue_resolved' => 'Editing prompt templates overwrote previous states, breaking test run reproducibility.',
                    'resolution_summary' => 'Added immutable PromptVersion and DocumentVersion snapshots with one-click rollback.',
                    'commit_ref' => 'feat/memento',
                ],
                [
                    'sprint_phase' => 'Phase 3',
                    'component_modified' => 'EvaluationFacade::interpolate',
                    'design_pattern_applied' => 'Boundary Guard',
                    'issue_resolved' => 'Missing {{variables}} in user payloads left raw curly braces or threw undefined key notices.',
                    'resolution_summary' => 'Replaced missing variables deterministically with [missing:var] markers and added unit tests.',
                    'commit_ref' => 'fix/bva-vars',
                ],
            ];

            foreach ($logs as $log) {
                RefactorLog::create(array_merge($log, ['workspace_id' => $workspace->id]));
            }
        }
    }
}
