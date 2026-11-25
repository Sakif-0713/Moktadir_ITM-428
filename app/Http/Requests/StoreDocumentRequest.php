<?php

namespace App\Http\Requests;

use App\Enums\ChunkingStrategyEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'min:2', 'max:200'],
            'raw_content' => ['nullable', 'string', 'max:250000'],
            'document_file' => ['nullable', 'file', 'mimes:txt,md,csv,json', 'max:2048'],
            'chunking_strategy' => ['required', Rule::in(array_column(ChunkingStrategyEnum::cases(), 'value'))],
            'chunk_size' => ['nullable', 'integer', 'min:50', 'max:4000'],
            'change_notes' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if (! $this->hasFile('document_file') && trim((string) $this->input('raw_content')) === '') {
                $validator->errors()->add('raw_content', 'Provide either document text or upload a text/markdown file.');
            }
        });
    }
}
