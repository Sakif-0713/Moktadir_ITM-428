<?php

namespace App\Services\RAG\Providers;

class DeterministicMockProvider implements LLMProviderInterface
{
    public function generate(string $systemPrompt, string $userPrompt, array $contextMeta = []): array
    {
        $start = hrtime(true);

        $query = trim((string) ($contextMeta['query'] ?? ''));
        $chunks = $contextMeta['chunks'] ?? [];
        $promptVersion = $contextMeta['prompt_version'] ?? 1;

        $snippets = [];
        foreach (array_slice($chunks, 0, 3) as $idx => $chunkText) {
            $trimmed = trim(mb_substr((string) $chunkText, 0, 220));
            if ($trimmed !== '') {
                $snippets[] = sprintf('- **[Source %d]:** %s', $idx + 1, $trimmed);
            }
        }

        if (empty($snippets)) {
            $output = sprintf(
                "### Evaluation Response (Deterministic Fallback — Prompt v%d)\n\n"
                . "**Query:** %s\n\n"
                . "No matching knowledge base chunks were retrieved for this query. "
                . "Please ingest relevant documents or switch to a broader retrieval strategy.",
                $promptVersion,
                $query !== '' ? $query : 'N/A'
            );
        } else {
            $output = sprintf(
                "### Grounded Synthesis (Deterministic Fallback — Prompt v%d)\n\n"
                . "**Addressing Query:** %s\n\n"
                . "Based on the retrieved knowledge base context:\n%s\n\n"
                . "**Conclusion:** The retrieved evidence directly supports the query requirements under strict context-grounding constraints.",
                $promptVersion,
                $query !== '' ? $query : 'General evaluation',
                implode("\n", $snippets)
            );
        }

        $latencyMs = max(12, (int) round((hrtime(true) - $start) / 1_000_000) + 18);
        $promptTokens = max(1, (int) ceil(str_word_count($systemPrompt . ' ' . $userPrompt) * 1.3));
        $completionTokens = max(1, (int) ceil(str_word_count($output) * 1.3));

        return [
            'output' => $output,
            'model' => 'deterministic-mock-v1',
            'latency_ms' => $latencyMs,
            'prompt_tokens' => $promptTokens,
            'completion_tokens' => $completionTokens,
            'is_fallback' => true,
        ];
    }
}
