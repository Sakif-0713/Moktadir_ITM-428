<?php

namespace App\Http\Requests;

use App\Enums\ChunkingStrategyEnum;
use App\Enums\EvaluationRubricEnum;
use App\Enums\RetrievalStrategyEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ExecuteTestRunRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:3', 'max:150'],
            'query_text' => ['required', 'string', 'min:3', 'max:5000'],
            'prompt_version_ids' => ['required', 'array', 'min:1', 'max:4'],
            'prompt_version_ids.*' => ['integer', 'exists:prompt_versions,id'],
            'chunking_strategy' => ['required', Rule::in(array_column(ChunkingStrategyEnum::cases(), 'value'))],
            'retrieval_strategy' => ['required', Rule::in(array_column(RetrievalStrategyEnum::cases(), 'value'))],
            'evaluation_rubric' => ['required', Rule::in(array_column(EvaluationRubricEnum::cases(), 'value'))],
            'variables_json' => ['nullable', 'string', 'max:5000'],
        ];
    }
}
