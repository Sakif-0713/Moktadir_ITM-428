@extends('layouts.app')

@section('title', 'Workspaces — RAG Evaluation Workbench')

@section('content')
<div class="mb-6 rounded-2xl border border-slate-200/80 p-6 shadow-xs flex flex-col md:flex-row md:items-center md:justify-between gap-4"
     style="background: radial-gradient(at 15% 20%, rgba(191, 219, 254, 0.45) 0px, transparent 55%), radial-gradient(at 85% 30%, rgba(251, 207, 232, 0.38) 0px, transparent 55%), #ffffff;">
    <div>
        <span class="text-xs font-bold uppercase tracking-wider text-blue-600">PromptLab Workspaces</span>
        <h1 class="text-2xl font-extrabold text-slate-900 mt-1">Evaluation Workspaces</h1>
        <p class="text-sm text-slate-600 mt-1">
            Manage knowledge bases, prompt version snapshots, and side-by-side RAG evaluation runs.
        </p>
    </div>

    <div class="flex items-center gap-3">
        <a href="{{ route('demo.guest-login.get') }}"
           class="inline-flex items-center px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold shadow-sm shadow-blue-600/20 transition">
            Launch 1-Click Instructor Demo
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-4">
        @forelse($workspaces as $ws)
            <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm hover:border-indigo-300 transition">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('workspaces.show', $ws) }}" class="text-lg font-semibold text-indigo-700 hover:underline">
                                {{ $ws->name }}
                            </a>
                            @if($ws->is_public_demo)
                                <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-emerald-100 text-emerald-800">Public Demo</span>
                            @endif
                        </div>
                        <p class="text-sm text-slate-600 mt-1">{{ $ws->description ?? 'No description provided.' }}</p>
                    </div>
                    <a href="{{ route('workspaces.show', $ws) }}"
                       class="shrink-0 px-3 py-1.5 rounded-lg bg-indigo-50 text-indigo-700 hover:bg-indigo-100 text-xs font-semibold">
                        Open Workbench &rarr;
                    </a>
                </div>

                <div class="mt-4 pt-3 border-t border-slate-100 flex flex-wrap gap-4 text-xs text-slate-500">
                    <span><strong>{{ $ws->documents_count }}</strong> Documents</span>
                    <span><strong>{{ $ws->prompt_templates_count }}</strong> Prompt Templates</span>
                    <span><strong>{{ $ws->test_runs_count }}</strong> Test Runs</span>
                    <span>Owner: <strong>{{ $ws->role_metadata['owner'] ?? 'Evaluator' }}</strong></span>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-xl border border-dashed border-slate-300 p-8 text-center text-slate-500">
                No workspaces found. Create one on the right or click <strong>Launch 1-Click Instructor Demo</strong>.
            </div>
        @endforelse
    </div>

    <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm h-fit">
        <h2 class="text-base font-semibold text-slate-900 mb-3">Create New Workspace</h2>
        <form action="{{ route('workspaces.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-medium text-slate-700 mb-1">Workspace Name</label>
                <input type="text" name="name" required placeholder="e.g., ITM 428 Sprint 2 Eval"
                       class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-700 mb-1">Description</label>
                <textarea name="description" rows="3" placeholder="Purpose of this knowledge base & prompt evaluation..."
                          class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none"></textarea>
            </div>
            <label class="flex items-center gap-2 text-xs text-slate-700">
                <input type="checkbox" name="is_public_demo" value="1" class="rounded border-slate-300 text-indigo-600">
                Allow public demo access
            </label>
            <button type="submit" class="w-full py-2 px-4 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold transition">
                Create Workspace
            </button>
        </form>
    </div>
</div>
@endsection
