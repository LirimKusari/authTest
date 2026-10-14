<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'cover_letter' => ['required', 'string'],
            'resume_url' => ['sometimes', 'nullable', 'url', 'max:255'],
            'status' => ['sometimes', 'string'],
            'internal_notes' => ['sometimes', 'nullable', 'string'],
            'score' => ['sometimes', 'nullable', 'integer', 'min:1', 'max:10'],
        ];
    }
}
