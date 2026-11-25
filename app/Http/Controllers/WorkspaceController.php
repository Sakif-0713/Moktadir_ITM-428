<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWorkspaceRequest;
use App\Models\User;
use App\Models\Workspace;
use App\Repositories\WorkspaceRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class WorkspaceController extends Controller
{
    public function __construct(
        private readonly WorkspaceRepository $workspaces
    ) {}

    public function index(): View
    {
        $userId = Auth::id();
        $workspaces = $this->workspaces->allForUser($userId);

        return view('workspaces.index', compact('workspaces'));
    }

    public function store(StoreWorkspaceRequest $request): RedirectResponse
    {
        $userId = Auth::id() ?? User::firstOrFail()->id;
        $data = $request->validated();

        $workspace = Workspace::create([
            'user_id' => $userId,
            'name' => $data['name'],
            'slug' => Str::slug($data['name']) . '-' . Str::lower(Str::random(5)),
            'description' => $data['description'] ?? null,
            'role_metadata' => [
                'owner' => Auth::user()?->name ?? 'Evaluator',
                'role' => Auth::user()?->role ?? 'evaluator',
            ],
            'is_public_demo' => (bool) ($data['is_public_demo'] ?? false),
        ]);

        return redirect()
            ->route('workspaces.show', $workspace)
            ->with('success', 'Workspace created successfully.');
    }

    public function show(Workspace $workspace): View
    {
        $workspace = $this->workspaces->findWithRelations($workspace->id);

        return view('workspaces.show', compact('workspace'));
    }

    public function update(StoreWorkspaceRequest $request, Workspace $workspace): RedirectResponse
    {
        $workspace->update($request->validated());

        return redirect()
            ->route('workspaces.show', $workspace)
            ->with('success', 'Workspace updated.');
    }

    public function destroy(Workspace $workspace): RedirectResponse
    {
        $workspace->delete();

        return redirect()
            ->route('workspaces.index')
            ->with('success', 'Workspace deleted.');
    }
}
