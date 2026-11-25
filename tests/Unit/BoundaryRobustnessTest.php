<?php

namespace Tests\Unit;

use App\Services\RAG\Chunking\FixedSizeChunkingStrategy;
use App\Services\RAG\Providers\GeminiProvider;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class BoundaryRobustnessTest extends TestCase
{
    public function test_zero_or_tiny_chunk_size_clamps_safely_without_infinite_loop(): void
    {
        $strategy = new FixedSizeChunkingStrategy();
        $chunks = $strategy->chunk(str_repeat('ABC ', 60), ['chunk_size' => 0, 'overlap' => 999]);

        $this->assertNotEmpty($chunks);
        $this->assertLessThanOrEqual(25, count($chunks));
    }

    public function test_gemini_provider_falls_back_deterministically_on_http_error(): void
    {
        config()->set('rag.gemini.api_key', 'fake-test-key');
        config()->set('rag.force_mock_fallback', false);

        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response(['error' => 'Rate limited'], 429),
        ]);

        $provider = new GeminiProvider();
        $result = $provider->generate('System prompt', 'User query', [
            'query' => 'What is BVA?',
            'chunks' => ['BVA tests boundary values.'],
            'prompt_version' => 1,
        ]);

        $this->assertTrue($result['is_fallback']);
        $this->assertSame('deterministic-mock-v1', $result['model']);
        $this->assertStringContainsString('BVA tests boundary values', $result['output']);
    }
}
