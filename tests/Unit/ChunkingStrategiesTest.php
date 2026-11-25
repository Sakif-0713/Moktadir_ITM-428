<?php

namespace Tests\Unit;

use App\Services\RAG\Chunking\FixedSizeChunkingStrategy;
use App\Services\RAG\Chunking\ParagraphBoundaryChunkingStrategy;
use Tests\TestCase;

class ChunkingStrategiesTest extends TestCase
{
    public function test_fixed_size_chunking_splits_with_overlap(): void
    {
        $strategy = new FixedSizeChunkingStrategy();
        $text = str_repeat('Software engineering testing quality assurance. ', 20);

        $chunks = $strategy->chunk($text, ['chunk_size' => 120, 'overlap' => 20]);

        $this->assertGreaterThan(3, count($chunks));
        $this->assertSame(0, $chunks[0]['chunk_index']);
        $this->assertSame(1, $chunks[1]['chunk_index']);
        $this->assertGreaterThan(0, $chunks[0]['token_count']);
    }

    public function test_paragraph_boundary_chunking_preserves_paragraphs(): void
    {
        $strategy = new ParagraphBoundaryChunkingStrategy();
        $text = "Paragraph one explains how the Facade Pattern simplifies complex RAG orchestration pipelines.\n\n"
            . "Paragraph two explains how the Strategy Pattern swaps chunking and retrieval algorithms.\n\n"
            . "Paragraph three explains how the Memento Pattern snapshots immutable prompt versions.";

        $chunks = $strategy->chunk($text, ['chunk_size' => 100]);

        $this->assertCount(3, $chunks);
        $this->assertStringContainsString('Facade', $chunks[0]['content']);
        $this->assertStringContainsString('Strategy', $chunks[1]['content']);
        $this->assertStringContainsString('Memento', $chunks[2]['content']);
    }

    public function test_chunking_returns_empty_array_for_empty_or_whitespace_text(): void
    {
        $fixed = new FixedSizeChunkingStrategy();
        $boundary = new ParagraphBoundaryChunkingStrategy();

        $this->assertSame([], $fixed->chunk('   '));
        $this->assertSame([], $boundary->chunk("\n\n  \t "));
    }
}
