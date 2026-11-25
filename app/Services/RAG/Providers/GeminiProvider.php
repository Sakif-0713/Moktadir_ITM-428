<?php

namespace App\Services\RAG\Providers;

use Illuminate\Support\Facades\Http;
use Throwable;

class GeminiProvider implements LLMProviderInterface
{
    public function __construct(
        private readonly DeterministicMockProvider $fallbackProvider = new DeterministicMockProvider()
    ) {}

    public function generate(string $systemPrompt, string $userPrompt, array $contextMeta = []): array
    {
        $apiKey = config('rag.gemini.api_key');
        $forceMock = (bool) config('rag.force_mock_fallback', false);

        if ($forceMock || empty($apiKey)) {
            return $this->fallbackProvider->generate($systemPrompt, $userPrompt, $contextMeta);
        }

        $model = (string) config('rag.gemini.model', 'gemini-2.0-flash');
        $timeout = (int) config('rag.gemini.timeout_seconds', 15);
        $start = hrtime(true);

        try {
            $response = Http::timeout($timeout)
                ->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}", [
                    'systemInstruction' => [
                        'parts' => [['text' => $systemPrompt]],
                    ],
                    'contents' => [
                        ['parts' => [['text' => $userPrompt]]],
                    ],
                ]);

            if ($response->failed()) {
                return $this->fallbackProvider->generate($systemPrompt, $userPrompt, $contextMeta);
            }

            $data = $response->json();
            $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;

            if (! is_string($text) || trim($text) === '') {
                return $this->fallbackProvider->generate($systemPrompt, $userPrompt, $contextMeta);
            }

            $latencyMs = max(1, (int) round((hrtime(true) - $start) / 1_000_000));
            $usage = $data['usageMetadata'] ?? [];

            return [
                'output' => $text,
                'model' => $model,
                'latency_ms' => $latencyMs,
                'prompt_tokens' => (int) ($usage['promptTokenCount'] ?? ceil(str_word_count($systemPrompt . ' ' . $userPrompt) * 1.3)),
                'completion_tokens' => (int) ($usage['candidatesTokenCount'] ?? ceil(str_word_count($text) * 1.3)),
                'is_fallback' => false,
            ];
        } catch (Throwable) {
            return $this->fallbackProvider->generate($systemPrompt, $userPrompt, $contextMeta);
        }
    }
}
