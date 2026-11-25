@extends('layouts.app')

@section('title', $prompt->title . ' — Prompt Studio')

@section('content')
<div class="mb-6">
    <div class="text-xs text-slate-500 mb-1">
        <a href="{{ route('workspaces.show', $workspace) }}" class="hover:underline">&larr; {{ $workspace->name }}</a>
    </div>
    <h1 class="text-2xl font-bold text-slate-900">{{ $prompt->title }}</h1>
    <p class="text-sm text-slate-600">{{ $prompt->description }}</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
        <h2 class="text-base font-semibold text-slate-900 mb-4">
            Edit Prompt &amp; Create New Memento Snapshot
            @if($prompt->currentVersion)
                <span class="ml-2 text-xs px-2 py-0.5 rounded bg-indigo-100 text-indigo-700 font-mono">
                    Active: v{{ $prompt->currentVersion->version_number }}
                </span>
            @endif
        </h2>

        <form action="{{ route('prompts.update', [$workspace, $prompt]) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-xs font-medium text-slate-700 mb-1">Title</label>
                <input type="text" name="title" value="{{ $prompt->title }}" required
                       class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-700 mb-1">Description</label>
                <input type="text" name="description" value="{{ $prompt->description }}"
                       class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-700 mb-1">System Prompt</label>
                <textarea name="system_prompt" rows="3" required
                          class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm font-mono">{{ $prompt->currentVersion?->system_prompt }}</textarea>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-700 mb-1">User Prompt (supports @{{context}}, @{{query}}, custom @{{variables}})</label>
                <textarea name="user_prompt" rows="5" required
                          class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm font-mono">{{ $prompt->currentVersion?->user_prompt }}</textarea>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-700 mb-1">Change Notes</label>
                <input type="text" name="change_notes" placeholder="Describe what changed in this version..."
                       class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
            </div>
            <button type="submit" class="px-4 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold">
                Save New Snapshot
            </button>
        </form>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm h-fit">
        <h2 class="text-base font-semibold text-slate-900 mb-3">Memento Version History</h2>
        <div class="space-y-3">
            @foreach($prompt->versions as $ver)
                <div class="p-3 rounded-lg border {{ $prompt->current_version_id === $ver->id ? 'border-indigo-400 bg-indigo-50/40' : 'border-slate-200' }}">
                    <div class="flex items-center justify-between">
                        <span class="font-mono font-semibold text-xs text-indigo-700">v{{ $ver->version_number }}</span>
                        <span class="text-[11px] text-slate-400">{{ $ver->created_at->format('M d, H:i') }}</span>
                    </div>
                    <p class="text-xs text-slate-700 mt-1">{{ $ver->change_notes }}</p>
                    <div class="mt-2 flex flex-wrap gap-1">
                        @foreach($ver->detected_variables ?? [] as $var)
                            <span class="text-[10px] px-1.5 py-0.5 rounded bg-amber-100 text-amber-800 font-mono">@{{ $var }}</span>
                        @endforeach
                    </div>
                    @if($prompt->current_version_id !== $ver->id)
                        <form action="{{ route('prompts.rollback', [$workspace, $prompt, $ver->version_number]) }}" method="POST" class="mt-2">
                            @csrf
                            <button type="submit" class="text-xs font-semibold text-indigo-600 hover:underline">
                                Restore this Memento &rarr;
                            </button>
                        </form>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
