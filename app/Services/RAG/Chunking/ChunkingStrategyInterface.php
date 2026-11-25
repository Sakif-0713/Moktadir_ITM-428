<?php

namespace App\Services\RAG\Chunking;

interface ChunkingStrategyInterface
{
    /**
     * Split raw document content into indexed chunks.
     *
     * @param  string  $content
     * @param  array<string, mixed>  $options
     * @return array<int, array{chunk_index: int, content: string, token_count: int, metadata: array<string, mixed>}>
     */
    public function chunk(string $content, array $options = []): array;

    public function key(): string;
}
