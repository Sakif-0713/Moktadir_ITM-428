<?php

namespace App\Repositories;

use App\Models\Workspace;
use Illuminate\Database\Eloquent\Collection;

class WorkspaceRepository
{
    public function allForUser(?int $userId): Collection
    {
        return Workspace::query()
            ->when($userId, fn ($q) => $q->where('user_id', $userId)->orWhere('is_public_demo', true))
            ->withCount(['documents', 'promptTemplates', 'testRuns'])
            ->latest()
            ->get();
    }

    public function findWithRelations(int $id): Workspace
    {
        return Workspace::with([
            'documents.chunks',
            'promptTemplates.currentVersion',
            'testRuns.results.evaluationScore',
            'refactorLogs',
        ])->findOrFail($id);
    }
}
