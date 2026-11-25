@extends('layouts.app')

@section('title', 'Refactoring & Bug-Fix Log — ' . $workspace->name)

@section('content')
<div class="mb-6">
    <div class="text-xs text-slate-500 mb-1">
        <a href="{{ route('workspaces.show', $workspace) }}" class="hover:underline">&larr; {{ $workspace->name }}</a>
    </div>
    <h1 class="text-2xl font-bold text-slate-900">SE Refactoring &amp; Bug-Fix Log</h1>
    <p class="text-sm text-slate-600">Traceability log for ITM 427/428 design pattern introductions and defect resolutions.</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 bg-white rounded-xl border border-slate-200 shadow-sm overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200 text-xs">
            <thead class="bg-slate-50 text-slate-600 uppercase">
                <tr>
                    <th class="px-4 py-3 text-left">Phase</th>
                    <th class="px-4 py-3 text-left">Component</th>
                    <th class="px-4 py-3 text-left">Pattern / Fix</th>
                    <th class="px-4 py-3 text-left">Issue &amp; Resolution</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($logs as $log)
                    <tr>
                        <td class="px-4 py-3 font-semibold">{{ $log->sprint_phase }}</td>
                        <td class="px-4 py-3 font-mono text-indigo-700">{{ $log->component_modified }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-0.5 rounded bg-indigo-50 text-indigo-700 font-semibold">{{ $log->design_pattern_applied }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <p class="text-slate-800 font-medium">{{ $log->issue_resolved }}</p>
                            <p class="text-slate-500 mt-1">{{ $log->resolution_summary }}</p>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm h-fit">
        <h2 class="text-base font-semibold text-slate-900 mb-3">Record Refactor / Bug Fix</h2>
        <form action="{{ route('refactor-logs.store', $workspace) }}" method="POST" class="space-y-3">
            @csrf
            <input type="text" name="sprint_phase" required placeholder="Phase (e.g., Phase 3)" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
            <input type="text" name="component_modified" required placeholder="Component (e.g., EvaluationFacade)" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
            <input type="text" name="design_pattern_applied" required placeholder="Pattern (e.g., Strategy Pattern)" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
            <textarea name="issue_resolved" rows="2" required placeholder="Issue or code smell..." class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"></textarea>
            <textarea name="resolution_summary" rows="2" required placeholder="Resolution summary..." class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"></textarea>
            <button type="submit" class="w-full py-2 px-4 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold">
                Add Log Entry
            </button>
        </form>
    </div>
</div>
@endsection
