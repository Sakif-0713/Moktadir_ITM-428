<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePromptTemplateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'min:2', 'max:150'],
            'description' => ['nullable', 'string', 'max:1000'],
            'system_prompt' => ['required', 'string', 'min:5', 'max:10000'],
            'user_prompt' => ['required', 'string', 'min:5', 'max:10000'],
            'change_notes' => ['nullable', 'string', 'max:255'],
        ];
    }
}
