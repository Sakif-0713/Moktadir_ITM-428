<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class TestRunResult extends Model
{
    protected $fillable = [
        'test_run_id',
        'prompt_version_id',
        'model_identifier',
        'rendered_prompt',
        'retrieved_chunk_ids',
        'generated_output',
        'latency_ms',
        'prompt_tokens',
        'completion_tokens',
        'is_fallback',
    ];

    protected function casts(): array
    {
        return [
            'retrieved_chunk_ids' => 'array',
            'is_fallback' => 'boolean',
        ];
    }

    public function testRun(): BelongsTo
    {
        return $this->belongsTo(TestRun::class);
    }

    public function promptVersion(): BelongsTo
    {
        return $this->belongsTo(PromptVersion::class);
    }

    public function evaluationScore(): HasOne
    {
        return $this->hasOne(EvaluationScore::class);
    }
}
