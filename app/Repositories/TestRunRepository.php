<?php

namespace App\Repositories;

use App\Models\TestRun;
use Illuminate\Database\Eloquent\Collection;

class TestRunRepository
{
    public function forWorkspace(int $workspaceId): Collection
    {
        return TestRun::where('workspace_id', $workspaceId)
            ->with(['results.evaluationScore', 'results.promptVersion.template'])
            ->latest()
            ->get();
    }
}
