<?php

namespace App\Services\RAG\Chunking;

use App\Enums\ChunkingStrategyEnum;

class ParagraphBoundaryChunkingStrategy implements ChunkingStrategyInterface
{
    public function key(): string
    {
        return ChunkingStrategyEnum::PARAGRAPH_BOUNDARY->value;
    }

    public function chunk(string $content, array $options = []): array
    {
        $normalized = trim(preg_replace("/\r\n|\r/", "\n", $content) ?? '');
        if ($normalized === '') {
            return [];
        }

        $maxSize = max(100, (int) ($options['chunk_size'] ?? config('rag.default_chunk_size', 450)));
        $paragraphs = preg_split("/\n{2,}/", $normalized) ?: [$normalized];

        $chunks = [];
        $buffer = '';
        $index = 0;

        foreach ($paragraphs as $paragraph) {
            $paragraph = trim($paragraph);
            if ($paragraph === '') {
                continue;
            }

            if (mb_strlen($paragraph) > $maxSize) {
                if ($buffer !== '') {
                    $chunks[] = $this->buildChunk($index++, $buffer);
                    $buffer = '';
                }

                // Split oversized paragraph by sentence boundaries
                $sentences = preg_split('/(?<=[.!?])\s+/', $paragraph) ?: [$paragraph];
                foreach ($sentences as $sentence) {
                    $sentence = trim($sentence);
                    if ($sentence === '') {
                        continue;
                    }
                    if ($buffer !== '' && mb_strlen($buffer . ' ' . $sentence) > $maxSize) {
                        $chunks[] = $this->buildChunk($index++, $buffer);
                        $buffer = $sentence;
                    } else {
                        $buffer = $buffer === '' ? $sentence : $buffer . ' ' . $sentence;
                    }
                }
                continue;
            }

            $candidate = $buffer === '' ? $paragraph : $buffer . "\n\n" . $paragraph;
            if (mb_strlen($candidate) > $maxSize && $buffer !== '') {
                $chunks[] = $this->buildChunk($index++, $buffer);
                $buffer = $paragraph;
            } else {
                $buffer = $candidate;
            }
        }

        if ($buffer !== '') {
            $chunks[] = $this->buildChunk($index, $buffer);
        }

        return $chunks;
    }

    /**
     * @return array{chunk_index: int, content: string, token_count: int, metadata: array<string, mixed>}
     */
    private function buildChunk(int $index, string $text): array
    {
        return [
            'chunk_index' => $index,
            'content' => $text,
            'token_count' => max(1, (int) ceil(str_word_count($text) * 1.3)),
            'metadata' => [
                'strategy' => $this->key(),
                'boundary_preserved' => true,
                'char_length' => mb_strlen($text),
            ],
        ];
    }
}
