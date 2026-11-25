@extends('layouts.app')

@section('title', 'QA & Metrics Inspector — ' . $workspace->name)

@section('content')
<div class="mb-6">
    <div class="text-xs text-slate-500 mb-1">
        <a href="{{ route('workspaces.show', $workspace) }}" class="hover:underline">&larr; {{ $workspace->name }}</a>
    </div>
    <h1 class="text-2xl font-bold text-slate-900">QA &amp; Evaluation Metrics Inspector</h1>
</div>

<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm">
        <div class="text-xs text-slate-500">Total Evaluations</div>
        <div class="text-2xl font-bold text-slate-900 mt-1">{{ $summary['total'] }}</div>
    </div>
    <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm">
        <div class="text-xs text-slate-500">Regression Pass Rate</div>
        <div class="text-2xl font-bold text-emerald-600 mt-1">{{ $summary['pass_rate'] }}%</div>
    </div>
    <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm">
        <div class="text-xs text-slate-500">Avg Overall Quality</div>
        <div class="text-2xl font-bold text-indigo-600 mt-1">{{ $summary['avg_overall'] }}%</div>
    </div>
    <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm">
        <div class="text-xs text-slate-500">Avg Grounding Score</div>
        <div class="text-2xl font-bold text-slate-900 mt-1">{{ $summary['avg_grounding'] }}%</div>
    </div>
</div>

<div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-x-auto">
    <table class="min-w-full divide-y divide-slate-200 text-xs">
        <thead class="bg-slate-50 text-slate-600 uppercase">
            <tr>
                <th class="px-4 py-3 text-left">Test Run</th>
                <th class="px-4 py-3 text-left">Prompt Version</th>
                <th class="px-4 py-3 text-left">Rubric</th>
                <th class="px-4 py-3 text-right">Grounding</th>
                <th class="px-4 py-3 text-right">Completeness</th>
                <th class="px-4 py-3 text-right">Overall</th>
                <th class="px-4 py-3 text-center">Status</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @foreach($scores as $s)
                <tr>
                    <td class="px-4 py-3 font-medium text-indigo-700">
                        <a href="{{ route('test-runs.show', [$workspace, $s->result->testRun]) }}" class="hover:underline">
                            {{ $s->result->testRun->name }}
                        </a>
                    </td>
                    <td class="px-4 py-3">{{ $s->result->promptVersion?->template?->title }} (v{{ $s->result->promptVersion?->version_number }})</td>
                    <td class="px-4 py-3 font-mono">{{ $s->rubric_type }}</td>
                    <td class="px-4 py-3 text-right">{{ $s->grounding_score }}%</td>
                    <td class="px-4 py-3 text-right">{{ $s->completeness_score }}%</td>
                    <td class="px-4 py-3 text-right font-bold">{{ $s->overall_score }}%</td>
                    <td class="px-4 py-3 text-center">
                        <span class="px-2 py-0.5 rounded-full font-semibold {{ $s->passed_regression ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                            {{ $s->passed_regression ? 'PASS' : 'FAIL' }}
                        </span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
