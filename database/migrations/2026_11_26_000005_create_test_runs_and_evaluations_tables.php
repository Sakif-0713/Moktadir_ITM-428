<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('test_runs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $table->string('name', 150);
            $table->text('query_text');
            $table->json('variables_payload')->nullable();
            $table->string('chunking_strategy', 50)->default('fixed_size');
            $table->string('retrieval_strategy', 50)->default('lexical_overlap');
            $table->string('evaluation_rubric', 50)->default('grounding_faithfulness');
            $table->string('status', 30)->default('pending');
            $table->timestamps();
        });

        Schema::create('test_run_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('test_run_id')->constrained()->cascadeOnDelete();
            $table->foreignId('prompt_version_id')->constrained()->cascadeOnDelete();
            $table->string('model_identifier', 100);
            $table->longText('rendered_prompt');
            $table->json('retrieved_chunk_ids')->nullable();
            $table->longText('generated_output');
            $table->unsignedInteger('latency_ms')->default(0);
            $table->unsignedInteger('prompt_tokens')->default(0);
            $table->unsignedInteger('completion_tokens')->default(0);
            $table->boolean('is_fallback')->default(false);
            $table->timestamps();
        });

        Schema::create('evaluation_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('test_run_result_id')->constrained()->cascadeOnDelete();
            $table->string('rubric_type', 50);
            $table->decimal('grounding_score', 5, 2)->default(0);
            $table->decimal('completeness_score', 5, 2)->default(0);
            $table->decimal('retrieval_relevance', 5, 2)->default(0);
            $table->decimal('overall_score', 5, 2)->default(0);
            $table->boolean('passed_regression')->default(false);
            $table->text('evaluator_notes')->nullable();
            $table->timestamps();
        });

        Schema::create('refactor_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $table->string('sprint_phase', 50);
            $table->string('component_modified', 120);
            $table->string('design_pattern_applied', 100);
            $table->text('issue_resolved');
            $table->text('resolution_summary');
            $table->string('commit_ref', 40)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('refactor_logs');
        Schema::dropIfExists('evaluation_scores');
        Schema::dropIfExists('test_run_results');
        Schema::dropIfExists('test_runs');
    }
};
