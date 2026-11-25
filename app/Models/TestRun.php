<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TestRun extends Model
{
    protected $fillable = [
        'workspace_id',
        'name',
        'query_text',
        'variables_payload',
        'chunking_strategy',
        'retrieval_strategy',
        'evaluation_rubric',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'variables_payload' => 'array',
        ];
    }

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function results(): HasMany
    {
        return $this->hasMany(TestRunResult::class);
    }
}
