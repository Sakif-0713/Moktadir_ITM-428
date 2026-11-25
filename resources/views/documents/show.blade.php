@extends('layouts.app')

@section('title', $document->title . ' — Chunks & Versions')

@section('content')
<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
        <div class="text-xs text-slate-500 mb-1">
            <a href="{{ route('workspaces.show', $workspace) }}" class="hover:underline">&larr; {{ $workspace->name }}</a>
        </div>
        <h1 class="text-2xl font-bold text-slate-900">{{ $document->title }}</h1>
        <p class="text-xs text-slate-500 mt-1">
            Total Chunks: <strong>{{ $document->chunks->count() }}</strong> &bull;
            Versions: <strong>{{ $document->versions->count() }}</strong>
        </p>
    </div>

    <form action="{{ route('documents.rechunk', [$workspace, $document]) }}" method="POST" class="flex items-center gap-2">
        @csrf
        <select name="chunking_strategy" class="rounded-lg border border-slate-300 px-3 py-2 text-xs">
            @foreach(\App\Enums\ChunkingStrategyEnum::cases() as $case)
                <option value="{{ $case->value }}">{{ $case->label() }}</option>
            @endforeach
        </select>
        <input type="number" name="chunk_size" value="450" min="50" max="4000"
               class="w-24 rounded-lg border border-slate-300 px-2 py-2 text-xs" title="Chunk Size (chars)">
        <button type="submit" class="px-3 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold">
            Re-Chunk
        </button>
    </form>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-3">
        <h2 class="text-base font-semibold text-slate-900">Generated Chunks</h2>
        @foreach($document->chunks as $chunk)
            <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm">
                <div class="flex items-center justify-between text-xs text-slate-500 mb-2">
                    <span class="font-mono font-semibold text-indigo-700">Chunk #{{ $chunk->chunk_index }}</span>
                    <span>Strategy: <strong>{{ $chunk->strategy_used }}</strong> &bull; ~{{ $chunk->token_count }} tokens</span>
                </div>
                <pre class="whitespace-pre-wrap text-xs text-slate-700 bg-slate-50 p-3 rounded-lg border border-slate-100 font-sans">{{ $chunk->content }}</pre>
            </div>
        @endforeach
    </div>

    <div class="space-y-4">
        <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm">
            <h2 class="text-sm font-semibold text-slate-900 mb-3">Document Memento Versions</h2>
            <div class="space-y-2">
                @foreach($document->versions as $ver)
                    <div class="p-2.5 rounded-lg border border-slate-200 text-xs">
                        <div class="flex justify-between font-semibold">
                            <span>v{{ $ver->version_number }}</span>
                            <span class="text-slate-400">{{ $ver->created_at->format('M d, H:i') }}</span>
                        </div>
                        <p class="text-slate-600 mt-1">{{ $ver->change_notes }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
