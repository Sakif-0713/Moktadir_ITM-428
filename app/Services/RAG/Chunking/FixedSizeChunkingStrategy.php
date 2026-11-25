<?php

namespace App\Services\RAG\Chunking;

use App\Enums\ChunkingStrategyEnum;

class FixedSizeChunkingStrategy implements ChunkingStrategyInterface
{
    public function key(): string
    {
        return ChunkingStrategyEnum::FIXED_SIZE->value;
    }

    public function chunk(string $content, array $options = []): array
    {
        $normalized = trim(preg_replace("/\r\n|\r/", "\n", $content) ?? '');
        if ($normalized === '') {
            return [];
        }

        $chunkSize = max(50, (int) ($options['chunk_size'] ?? config('rag.default_chunk_size', 450)));
        $overlap = max(0, min($chunkSize - 10, (int) ($options['overlap'] ?? config('rag.default_chunk_overlap', 60))));

        $length = mb_strlen($normalized);
        $chunks = [];
        $offset = 0;
        $index = 0;

        while ($offset < $length) {
            $slice = trim(mb_substr($normalized, $offset, $chunkSize));
            if ($slice !== '') {
                $chunks[] = [
                    'chunk_index' => $index++,
                    'content' => $slice,
                    'token_count' => $this->estimateTokens($slice),
                    'metadata' => [
                        'strategy' => $this->key(),
                        'char_start' => $offset,
                        'char_length' => mb_strlen($slice),
                        'overlap' => $overlap,
                    ],
                ];
            }

            if ($offset + $chunkSize >= $length) {
                break;
            }

            $offset += ($chunkSize - $overlap);
        }

        return $chunks;
    }

    private function estimateTokens(string $text): int
    {
        return max(1, (int) ceil(str_word_count($text) * 1.3));
    }
}
