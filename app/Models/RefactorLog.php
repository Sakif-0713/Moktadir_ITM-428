<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RefactorLog extends Model
{
    protected $fillable = [
        'workspace_id',
        'sprint_phase',
        'component_modified',
        'design_pattern_applied',
        'issue_resolved',
        'resolution_summary',
        'commit_ref',
    ];

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }
}
