<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PromptVersion extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'prompt_template_id',
        'version_number',
        'system_prompt',
        'user_prompt',
        'detected_variables',
        'change_notes',
        'created_by',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'detected_variables' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(PromptTemplate::class, 'prompt_template_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
