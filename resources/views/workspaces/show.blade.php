@extends('layouts.app')

@section('title', $workspace->name . ' — Workbench')

@section('content')
<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
        <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
            <a href="{{ route('workspaces.index') }}" class="hover:underline">&larr; All Workspaces</a>
            <span>/</span>
            <span>{{ $workspace->slug }}</span>
        </div>
        <h1 class="text-2xl font-bold text-slate-900">{{ $workspace->name }}</h1>
        <p class="text-sm text-slate-600">{{ $workspace->description }}</p>
    </div>

    <div class="flex flex-wrap gap-2">
        <a href="{{ route('evaluations.index', $workspace) }}"
           class="px-3 py-2 rounded-lg bg-white border border-slate-300 hover:bg-slate-50 text-xs font-semibold text-slate-700">
            QA &amp; Metrics Inspector
        </a>
        <a href="{{ route('refactor-logs.index', $workspace) }}"
           class="px-3 py-2 rounded-lg bg-white border border-slate-300 hover:bg-slate-50 text-xs font-semibold text-slate-700">
            SE Refactor Log ({{ $workspace->refactorLogs->count() }})
        </a>
    </div>
</div>

<div x-data="{ tab: 'runner' }" class="space-y-6">
    <div class="border-b border-slate-200 flex flex-wrap gap-2">
        <button @click="tab = 'runner'" :class="tab === 'runner' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-slate-500 hover:text-slate-700'"
                class="px-4 py-2.5 text-sm font-semibold border-b-2 transition">
            Side-by-Side Test Runner ({{ $workspace->testRuns->count() }})
        </button>
        <button @click="tab = 'documents'" :class="tab === 'documents' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-slate-500 hover:text-slate-700'"
                class="px-4 py-2.5 text-sm font-semibold border-b-2 transition">
            Documents &amp; Chunks ({{ $workspace->documents->count() }})
        </button>
        <button @click="tab = 'prompts'" :class="tab === 'prompts' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-slate-500 hover:text-slate-700'"
                class="px-4 py-2.5 text-sm font-semibold border-b-2 transition">
            Prompt Templates ({{ $workspace->promptTemplates->count() }})
        </button>
    </div>

    <!-- TAB 1: TEST RUNNER & HISTORY -->
    <div x-show="tab === 'runner'" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
            <h2 class="text-base font-semibold text-slate-900 mb-4">Execute Comparative RAG Test Run</h2>
            <form action="{{ route('test-runs.store', $workspace) }}" method="POST" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1">Run Name</label>
                        <input type="text" name="name" required value="Prompt & Strategy Comparison"
                               class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1">Optional Variables (JSON)</label>
                        <input type="text" name="variables_json" value='{"tone": "concise and technical"}'
                               class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm font-mono">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-700 mb-1">Evaluation Query</label>
                    <textarea name="query_text" rows="2" required placeholder="Ask a question grounded in your workspace documents..."
                              class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">How do Boundary Value Analysis and the Strategy Pattern improve RAG evaluation?</textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1">Chunking Strategy</label>
                        <select name="chunking_strategy" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                            @foreach(\App\Enums\ChunkingStrategyEnum::cases() as $case)
                                <option value="{{ $case->value }}">{{ $case->label() }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1">Retrieval Strategy</label>
                        <select name="retrieval_strategy" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                            @foreach(\App\Enums\RetrievalStrategyEnum::cases() as $case)
                                <option value="{{ $case->value }}">{{ $case->label() }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1">Evaluation Rubric</label>
                        <select name="evaluation_rubric" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                            @foreach(\App\Enums\EvaluationRubricEnum::cases() as $case)
                                <option value="{{ $case->value }}">{{ $case->label() }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-700 mb-2">Select Prompt Versions to Compare Side-by-Side (1–4)</label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        @foreach($workspace->promptTemplates as $tpl)
                            @foreach($tpl->versions as $ver)
                                <label class="flex items-center gap-2 p-2.5 rounded-lg border border-slate-200 hover:bg-slate-50 text-xs cursor-pointer">
                                    <input type="checkbox" name="prompt_version_ids[]" value="{{ $ver->id }}" checked
                                           class="rounded border-slate-300 text-indigo-600">
                                    <span>
                                        <strong>{{ $tpl->title }}</strong> (v{{ $ver->version_number }})
                                        <span class="block text-slate-500 truncate">{{ $ver->change_notes }}</span>
                                    </span>
                                </label>
                            @endforeach
                        @endforeach
                    </div>
                </div>

                <button type="submit" class="px-5 py-2.5 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold shadow-sm transition">
                    Run Evaluation via EvaluationFacade
                </button>
            </form>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm h-fit">
            <h2 class="text-base font-semibold text-slate-900 mb-3">Recent Test Runs</h2>
            <div class="space-y-3">
                @forelse($workspace->testRuns->sortByDesc('id') as $run)
                    <a href="{{ route('test-runs.show', [$workspace, $run]) }}"
                       class="block p-3 rounded-lg border border-slate-200 hover:border-indigo-400 transition">
                        <div class="flex items-center justify-between">
                            <span class="font-semibold text-sm text-indigo-700">{{ $run->name }}</span>
                            <span class="text-[11px] px-2 py-0.5 rounded-full bg-slate-100 text-slate-700 uppercase font-mono">
                                {{ $run->status }}
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 mt-1 line-clamp-2">{{ $run->query_text }}</p>
                        <div class="mt-2 text-[11px] text-slate-400 flex justify-between">
                            <span>{{ $run->results->count() }} compared</span>
                            <span>{{ $run->created_at->diffForHumans() }}</span>
                        </div>
                    </a>
                @empty
                    <p class="text-xs text-slate-500">No test runs yet.</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- TAB 2: DOCUMENTS & CHUNKS -->
    <div x-show="tab === 'documents'" x-cloak class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-3">
            @forelse($workspace->documents as $doc)
                <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm flex items-center justify-between">
                    <div>
                        <a href="{{ route('documents.show', [$workspace, $doc]) }}" class="font-semibold text-indigo-700 hover:underline">
                            {{ $doc->title }}
                        </a>
                        <p class="text-xs text-slate-500 mt-1">
                            Source: {{ $doc->source_type }} &bull; Chunks: <strong>{{ $doc->chunk_count }}</strong> &bull;
                            SHA256: <code class="text-[11px]">{{ substr($doc->content_hash, 0, 12) }}...</code>
                        </p>
                    </div>
                    <a href="{{ route('documents.show', [$workspace, $doc]) }}"
                       class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-xs font-semibold text-slate-700">
                        Inspect Chunks
                    </a>
                </div>
            @empty
                <div class="bg-white rounded-xl border border-dashed border-slate-300 p-6 text-center text-sm text-slate-500">
                    No documents in this workspace yet.
                </div>
            @endforelse
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm h-fit">
            <h2 class="text-base font-semibold text-slate-900 mb-3">Ingest Knowledge Document</h2>
            <form action="{{ route('documents.store', $workspace) }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-medium text-slate-700 mb-1">Document Title</label>
                    <input type="text" name="title" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-700 mb-1">Upload File (.txt / .md)</label>
                    <input type="file" name="document_file" class="w-full text-xs text-slate-600">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-700 mb-1">Or Paste Raw Content</label>
                    <textarea name="raw_content" rows="4" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"></textarea>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-700 mb-1">Chunking Strategy</label>
                    <select name="chunking_strategy" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                        @foreach(\App\Enums\ChunkingStrategyEnum::cases() as $case)
                            <option value="{{ $case->value }}">{{ $case->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="w-full py-2 px-4 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold">
                    Ingest &amp; Generate Chunks
                </button>
            </form>
        </div>
    </div>

    <!-- TAB 3: PROMPT TEMPLATES -->
    <div x-show="tab === 'prompts'" x-cloak class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-3">
            @forelse($workspace->promptTemplates as $tpl)
                <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm flex items-center justify-between">
                    <div>
                        <a href="{{ route('prompts.show', [$workspace, $tpl]) }}" class="font-semibold text-indigo-700 hover:underline">
                            {{ $tpl->title }}
                        </a>
                        <p class="text-xs text-slate-500 mt-1">{{ $tpl->description }}</p>
                        @if($tpl->currentVersion)
                            <div class="mt-2 flex flex-wrap gap-1.5">
                                <span class="text-[11px] px-2 py-0.5 rounded bg-indigo-50 text-indigo-700 font-mono">
                                    v{{ $tpl->currentVersion->version_number }}
                                </span>
                                @foreach($tpl->currentVersion->detected_variables ?? [] as $var)
                                    <span class="text-[11px] px-2 py-0.5 rounded bg-amber-50 text-amber-800 font-mono">
                                        @{{ $var }}
                                    </span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    <a href="{{ route('prompts.show', [$workspace, $tpl]) }}"
                       class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-xs font-semibold text-slate-700">
                        Edit &amp; History
                    </a>
                </div>
            @empty
                <div class="bg-white rounded-xl border border-dashed border-slate-300 p-6 text-center text-sm text-slate-500">
                    No prompt templates yet.
                </div>
            @endforelse
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm h-fit">
            <h2 class="text-base font-semibold text-slate-900 mb-3">New Prompt Template</h2>
            <form action="{{ route('prompts.store', $workspace) }}" method="POST" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-medium text-slate-700 mb-1">Template Title</label>
                    <input type="text" name="title" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-700 mb-1">System Prompt</label>
                    <textarea name="system_prompt" rows="2" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm font-mono">You are a strict RAG assistant. Tone: @{{tone}}.</textarea>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-700 mb-1">User Prompt</label>
                    <textarea name="user_prompt" rows="3" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm font-mono">Context:
@{{context}}

Question: @{{query}}</textarea>
                </div>
                <button type="submit" class="w-full py-2 px-4 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold">
                    Create Template (v1)
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
