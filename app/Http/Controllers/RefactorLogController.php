<?php

namespace App\Http\Controllers;

use App\Models\RefactorLog;
use App\Models\Workspace;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RefactorLogController extends Controller
{
    public function index(Workspace $workspace): View
    {
        $logs = $workspace->refactorLogs()->latest()->get();

        return view('refactor-logs.index', compact('workspace', 'logs'));
    }

    public function store(Request $request, Workspace $workspace): RedirectResponse
    {
        $validated = $request->validate([
            'sprint_phase' => ['required', 'string', 'max:50'],
            'component_modified' => ['required', 'string', 'max:120'],
            'design_pattern_applied' => ['required', 'string', 'max:100'],
            'issue_resolved' => ['required', 'string', 'max:1000'],
            'resolution_summary' => ['required', 'string', 'max:1000'],
            'commit_ref' => ['nullable', 'string', 'max:40'],
        ]);

        RefactorLog::create(array_merge($validated, ['workspace_id' => $workspace->id]));

        return redirect()
            ->route('refactor-logs.index', $workspace)
            ->with('success', 'Refactoring log entry recorded.');
    }
}
