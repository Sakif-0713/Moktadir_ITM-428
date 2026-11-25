<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePromptTemplateRequest;
use App\Models\PromptTemplate;
use App\Models\Workspace;
use App\Services\Versioning\MementoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PromptTemplateController extends Controller
{
    public function __construct(
        private readonly MementoService $mementoService
    ) {}

    public function store(StorePromptTemplateRequest $request, Workspace $workspace): RedirectResponse
    {
        $data = $request->validated();

        $template = PromptTemplate::create([
            'workspace_id' => $workspace->id,
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
        ]);

        $this->mementoService->capturePromptSnapshot(
            $template,
            $data['system_prompt'],
            $data['user_prompt'],
            $data['change_notes'] ?? 'Initial prompt version',
            Auth::id()
        );

        return redirect()
            ->route('prompts.show', [$workspace, $template])
            ->with('success', 'Prompt template created with Memento snapshot v1.');
    }

    public function show(Workspace $workspace, PromptTemplate $prompt): View
    {
        $prompt->load(['versions', 'currentVersion']);

        return view('prompts.show', compact('workspace', 'prompt'));
    }

    public function update(StorePromptTemplateRequest $request, Workspace $workspace, PromptTemplate $prompt): RedirectResponse
    {
        $data = $request->validated();

        $prompt->update([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
        ]);

        $version = $this->mementoService->capturePromptSnapshot(
            $prompt,
            $data['system_prompt'],
            $data['user_prompt'],
            $data['change_notes'] ?? 'Updated prompt template',
            Auth::id()
        );

        return redirect()
            ->route('prompts.show', [$workspace, $prompt])
            ->with('success', "Saved new Memento snapshot v{$version->version_number}.");
    }

    public function rollback(Workspace $workspace, PromptTemplate $prompt, int $versionNumber): RedirectResponse
    {
        $restored = $this->mementoService->restorePromptVersion($prompt, $versionNumber, Auth::id());

        return redirect()
            ->route('prompts.show', [$workspace, $prompt])
            ->with('success', "Rolled back to v{$versionNumber} (recorded as v{$restored->version_number}).");
    }

    public function destroy(Workspace $workspace, PromptTemplate $prompt): RedirectResponse
    {
        $prompt->delete();

        return redirect()
            ->route('workspaces.show', $workspace)
            ->with('success', 'Prompt template deleted.');
    }
}
