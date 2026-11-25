<?php

namespace App\Services\Versioning;

use App\Models\Document;
use App\Models\DocumentVersion;
use App\Models\PromptTemplate;
use App\Models\PromptVersion;
use Illuminate\Support\Facades\DB;

/**
 * Caretaker in the Memento Pattern.
 * Captures immutable snapshots for PromptTemplates and Documents and supports safe rollback.
 */
class MementoService
{
    /**
     * Extract {{variable}} placeholders from system and user prompts.
     *
     * @return array<int, string>
     */
    public function extractVariables(string $systemPrompt, string $userPrompt): array
    {
        preg_match_all('/\{\{\s*([a-zA-Z0-9_]+)\s*\}\}/', $systemPrompt . "\n" . $userPrompt, $matches);

        return array_values(array_unique($matches[1] ?? []));
    }

    public function capturePromptSnapshot(
        PromptTemplate $template,
        string $systemPrompt,
        string $userPrompt,
        ?string $changeNotes = null,
        ?int $userId = null
    ): PromptVersion {
        return DB::transaction(function () use ($template, $systemPrompt, $userPrompt, $changeNotes, $userId) {
            $nextVersion = ((int) $template->versions()->max('version_number')) + 1;

            $version = PromptVersion::create([
                'prompt_template_id' => $template->id,
                'version_number' => $nextVersion,
                'system_prompt' => $systemPrompt,
                'user_prompt' => $userPrompt,
                'detected_variables' => $this->extractVariables($systemPrompt, $userPrompt),
                'change_notes' => $changeNotes ?? "Snapshot v{$nextVersion}",
                'created_by' => $userId,
                'created_at' => now(),
            ]);

            $template->update(['current_version_id' => $version->id]);

            return $version;
        });
    }

    public function restorePromptVersion(PromptTemplate $template, int $versionNumber, ?int $userId = null): PromptVersion
    {
        $target = $template->versions()
            ->where('version_number', $versionNumber)
            ->firstOrFail();

        return $this->capturePromptSnapshot(
            $template,
            $target->system_prompt,
            $target->user_prompt,
            "Rollback to v{$versionNumber}",
            $userId
        );
    }

    public function captureDocumentSnapshot(
        Document $document,
        string $rawContent,
        ?string $changeNotes = null,
        ?int $userId = null
    ): DocumentVersion {
        return DB::transaction(function () use ($document, $rawContent, $changeNotes, $userId) {
            $nextVersion = ((int) $document->versions()->max('version_number')) + 1;

            $version = DocumentVersion::create([
                'document_id' => $document->id,
                'version_number' => $nextVersion,
                'raw_content' => $rawContent,
                'change_notes' => $changeNotes ?? "Document revision v{$nextVersion}",
                'created_by' => $userId,
                'created_at' => now(),
            ]);

            $document->update([
                'raw_content' => $rawContent,
                'content_hash' => hash('sha256', $rawContent),
                'current_version_id' => $version->id,
            ]);

            return $version;
        });
    }
}
