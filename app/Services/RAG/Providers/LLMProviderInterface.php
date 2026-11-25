<?php

namespace App\Services\RAG\Providers;

interface LLMProviderInterface
{
    /**
     * Generate a completion response and return standardized telemetry.
     *
     * @param  string  $systemPrompt
     * @param  string  $userPrompt
     * @param  array<string, mixed>  $contextMeta
     * @return array{output: string, model: string, latency_ms: int, prompt_tokens: int, completion_tokens: int, is_fallback: bool}
     */
    public function generate(string $systemPrompt, string $userPrompt, array $contextMeta = []): array;
}
