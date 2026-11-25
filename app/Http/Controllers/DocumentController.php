<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDocumentRequest;
use App\Models\Document;
use App\Models\Workspace;
use App\Services\Facades\EvaluationFacadeInterface;
use App\Services\Versioning\MementoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DocumentController extends Controller
{
    public function __construct(
        private readonly EvaluationFacadeInterface $evaluationFacade,
        private readonly MementoService $mementoService
    ) {}

    public function store(StoreDocumentRequest $request, Workspace $workspace): RedirectResponse
    {
        $content = $request->hasFile('document_file')
            ? (string) file_get_contents($request->file('document_file')->getRealPath())
            : (string) $request->input('raw_content');

        $sourceType = $request->hasFile('document_file') ? 'upload' : 'paste';

        $document = Document::create([
            'workspace_id' => $workspace->id,
            'title' => $request->input('title'),
            'source_type' => $sourceType,
            'raw_content' => $content,
            'content_hash' => hash('sha256', $content),
        ]);

        $this->mementoService->captureDocumentSnapshot(
            $document,
            $content,
            $request->input('change_notes') ?? 'Initial ingestion',
            Auth::id()
        );

        $this->evaluationFacade->ingestDocumentChunks(
            $document,
            $request->input('chunking_strategy'),
            ['chunk_size' => $request->input('chunk_size')]
        );

        return redirect()
            ->route('documents.show', [$workspace, $document])
            ->with('success', 'Document ingested and chunked.');
    }

    public function show(Workspace $workspace, Document $document): View
    {
        $document->load(['chunks', 'versions']);

        return view('documents.show', compact('workspace', 'document'));
    }

    public function update(StoreDocumentRequest $request, Workspace $workspace, Document $document): RedirectResponse
    {
        $content = $request->hasFile('document_file')
            ? (string) file_get_contents($request->file('document_file')->getRealPath())
            : (string) $request->input('raw_content');

        $document->update(['title' => $request->input('title')]);

        $this->mementoService->captureDocumentSnapshot(
            $document,
            $content,
            $request->input('change_notes') ?? 'Updated document content',
            Auth::id()
        );

        $this->evaluationFacade->ingestDocumentChunks(
            $document,
            $request->input('chunking_strategy'),
            ['chunk_size' => $request->input('chunk_size')]
        );

        return redirect()
            ->route('documents.show', [$workspace, $document])
            ->with('success', 'Document updated, snapshot saved, and chunks regenerated.');
    }

    public function rechunk(Request $request, Workspace $workspace, Document $document): RedirectResponse
    {
        $strategy = (string) $request->input('chunking_strategy', 'fixed_size');
        $chunkSize = (int) $request->input('chunk_size', config('rag.default_chunk_size'));

        $this->evaluationFacade->ingestDocumentChunks($document, $strategy, ['chunk_size' => $chunkSize]);

        return redirect()
            ->route('documents.show', [$workspace, $document])
            ->with('success', "Chunks regenerated using {$strategy}.");
    }

    public function destroy(Workspace $workspace, Document $document): RedirectResponse
    {
        $document->delete();

        return redirect()
            ->route('workspaces.show', $workspace)
            ->with('success', 'Document deleted.');
    }
}
