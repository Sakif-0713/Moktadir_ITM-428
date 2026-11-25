<?php

namespace App\Repositories;

use App\Models\Document;
use Illuminate\Database\Eloquent\Collection;

class DocumentRepository
{
    public function forWorkspace(int $workspaceId): Collection
    {
        return Document::where('workspace_id', $workspaceId)
            ->withCount('chunks')
            ->latest()
            ->get();
    }
}
