<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExecuteTestRunRequest;
use App\Models\DocumentChunk;
use App\Models\TestRun;
use App\Models\Workspace;
use App\Services\Facades\EvaluationFacadeInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TestRunController extends Controller
{
    public function __construct(
        private readonly EvaluationFacadeInterface $evaluationFacade
    ) {}

    public function store(ExecuteTestRunRequest $request, Workspace $workspace): RedirectResponse
    {
        $data = $request->validated();
        $variables = [];

        if (! empty($data['variables_json'])) {
            $decoded = json_decode($data['variables_json'], true);
            if (is_array($decoded)) {
                $variables = $decoded;
            }
        }

        $testRun = TestRun::create([
            'workspace_id' => $workspace->id,
            'name' => $data['name'],
            'query_text' => $data['query_text'],
            'variables_payload' => $variables,
            'chunking_strategy' => $data['chunking_strategy'],
            'retrieval_strategy' => $data['retrieval_strategy'],
            'evaluation_rubric' => $data['evaluation_rubric'],
        ]);

        $this->evaluationFacade->executeTestRun($testRun, $data['prompt_version_ids']);

        return redirect()
            ->route('test-runs.show', [$workspace, $testRun])
            ->with('success', 'Test run executed and evaluated.');
    }

    public function show(Workspace $workspace, TestRun $testRun): View
    {
        $testRun->load([
            'results.evaluationScore',
            'results.promptVersion.template',
        ]);

        $chunkIds = $testRun->results->flatMap(fn ($r) => $r->retrieved_chunk_ids ?? [])->unique()->all();
        $chunksById = DocumentChunk::with('document')->whereIn('id', $chunkIds)->get()->keyBy('id');

        return view('test-runs.show', compact('workspace', 'testRun', 'chunksById'));
    }
}
