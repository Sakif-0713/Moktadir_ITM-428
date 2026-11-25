<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EvaluationScore extends Model
{
    protected $fillable = [
        'test_run_result_id',
        'rubric_type',
        'grounding_score',
        'completeness_score',
        'retrieval_relevance',
        'overall_score',
        'passed_regression',
        'evaluator_notes',
    ];

    protected function casts(): array
    {
        return [
            'grounding_score' => 'float',
            'completeness_score' => 'float',
            'retrieval_relevance' => 'float',
            'overall_score' => 'float',
            'passed_regression' => 'boolean',
        ];
    }

    public function result(): BelongsTo
    {
        return $this->belongsTo(TestRunResult::class, 'test_run_result_id');
    }
}
