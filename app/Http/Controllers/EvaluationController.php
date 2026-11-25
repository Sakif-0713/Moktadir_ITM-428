<?php

namespace App\Http\Controllers;

use App\Models\EvaluationScore;
use App\Models\Workspace;
use Illuminate\View\View;

class EvaluationController extends Controller
{
    public function index(Workspace $workspace): View
    {
        $scores = EvaluationScore::query()
            ->whereHas('result.testRun', fn ($q) => $q->where('workspace_id', $workspace->id))
            ->with(['result.testRun', 'result.promptVersion.template'])
            ->latest()
            ->get();

        $summary = [
            'total' => $scores->count(),
            'pass_rate' => $scores->count() > 0
                ? round(($scores->where('passed_regression', true)->count() / $scores->count()) * 100, 1)
                : 0.0,
            'avg_overall' => round((float) $scores->avg('overall_score'), 2),
            'avg_grounding' => round((float) $scores->avg('grounding_score'), 2),
        ];

        return view('evaluations.index', compact('workspace', 'scores', 'summary'));
    }
}
