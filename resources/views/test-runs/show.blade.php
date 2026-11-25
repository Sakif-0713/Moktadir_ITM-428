@extends('layouts.app')

@section('title', $testRun->name . ' — Side-by-Side Comparison')

@section('content')
<div class="mb-6">
    <div class="text-xs text-slate-500 mb-1">
        <a href="{{ route('workspaces.show', $workspace) }}" class="hover:underline">&larr; {{ $workspace->name }}</a>
    </div>
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2">
        <h1 class="text-2xl font-bold text-slate-900">{{ $testRun->name }}</h1>
        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 uppercase w-fit">
            {{ $testRun->status }}
        </span>
    </div>
    <div class="mt-3 p-4 rounded-xl bg-white border border-slate-200 text-sm">
        <p><strong>Query:</strong> {{ $testRun->query_text }}</p>
        <div class="mt-2 flex flex-wrap gap-4 text-xs text-slate-500">
            <span>Chunking: <strong class="text-slate-800">{{ $testRun->chunking_strategy }}</strong></span>
            <span>Retrieval: <strong class="text-slate-800">{{ $testRun->retrieval_strategy }}</strong></span>
            <span>Rubric: <strong class="text-slate-800">{{ $testRun->evaluation_rubric }}</strong></span>
        </div>
    </div>
</div>

<h2 class="text-base font-semibold text-slate-900 mb-3">Side-by-Side Run Comparison</h2>
<div class="grid grid-cols-1 lg:grid-cols-{{ max(1, min(2, $testRun->results->count())) }} gap-6 mb-8">
    @foreach($testRun->results as $res)
        @php $score = $res->evaluationScore; @endphp
        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div>
                        <span class="text-sm font-bold text-indigo-700">
                            {{ $res->promptVersion?->template?->title ?? 'Prompt' }} (v{{ $res->promptVersion?->version_number }})
                        </span>
                        <div class="text-xs text-slate-500 mt-0.5">
                            Model: <code class="font-mono">{{ $res->model_identifier }}</code>
                            @if($res->is_fallback)
                                <span class="ml-1 px-1.5 py-0.5 rounded bg-amber-100 text-amber-800 text-[10px] font-semibold">Deterministic Fallback</span>
                            @endif
                        </div>
                    </div>

                    @if($score)
                        <span class="px-2.5 py-1 rounded-lg text-xs font-bold {{ $score->passed_regression ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                            {{ $score->passed_regression ? 'PASS' : 'FAIL' }} ({{ number_format($score->overall_score, 1) }}%)
                        </span>
                    @endif
                </div>

                @if($score)
                    <div class="grid grid-cols-4 gap-2 my-3 text-center">
                        <div class="p-2 rounded bg-slate-50">
                            <div class="text-[10px] text-slate-500 uppercase">Grounding</div>
                            <div class="text-sm font-bold text-slate-900">{{ $score->grounding_score }}%</div>
                        </div>
                        <div class="p-2 rounded bg-slate-50">
                            <div class="text-[10px] text-slate-500 uppercase">Completeness</div>
                            <div class="text-sm font-bold text-slate-900">{{ $score->completeness_score }}%</div>
                        </div>
                        <div class="p-2 rounded bg-slate-50">
                            <div class="text-[10px] text-slate-500 uppercase">Retrieval</div>
                            <div class="text-sm font-bold text-slate-900">{{ $score->retrieval_relevance }}%</div>
                        </div>
                        <div class="p-2 rounded bg-slate-50">
                            <div class="text-[10px] text-slate-500 uppercase">Latency</div>
                            <div class="text-sm font-bold text-slate-900">{{ $res->latency_ms }} ms</div>
                        </div>
                    </div>
                @endif

                <div class="mt-3">
                    <div class="text-xs font-semibold text-slate-700 mb-1">Generated Output</div>
                    <pre class="whitespace-pre-wrap text-xs text-slate-800 bg-slate-50 p-3 rounded-lg border border-slate-100 font-sans">{{ $res->generated_output }}</pre>
                </div>
            </div>

            <div class="mt-4 pt-3 border-t border-slate-100 text-[11px] text-slate-500 flex justify-between">
                <span>Tokens: {{ $res->prompt_tokens }} prompt / {{ $res->completion_tokens }} completion</span>
                <span>{{ $score?->evaluator_notes }}</span>
            </div>
        </div>
    @endforeach
</div>

<div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
    <h2 class="text-base font-semibold text-slate-900 mb-3">Retrieved Context Chunks</h2>
    <div class="space-y-2">
        @php $displayedIds = $testRun->results->first()?->retrieved_chunk_ids ?? []; @endphp
        @forelse($displayedIds as $cid)
            @if(isset($chunksById[$cid]))
                @php $chunk = $chunksById[$cid]; @endphp
                <div class="p-3 rounded-lg bg-slate-50 border border-slate-200 text-xs">
                    <div class="font-semibold text-indigo-700 mb-1">
                        {{ $chunk->document?->title }} &mdash; Chunk #{{ $chunk->chunk_index }}
                    </div>
                    <p class="text-slate-700">{{ $chunk->content }}</p>
                </div>
            @endif
        @empty
            <p class="text-xs text-slate-500">No chunks were retrieved for this run.</p>
        @endforelse
    </div>
</div>
@endsection
