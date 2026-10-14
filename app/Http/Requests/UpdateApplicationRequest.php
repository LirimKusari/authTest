<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateApplicationRequest extends FormRequest
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
            'cover_letter' => ['sometimes', 'string'],
            'resume_url' => ['sometimes', 'nullable', 'url', 'max:255'],
            'status' => ['sometimes', Rule::in(['submitted', 'reviewing', 'rejected', 'hired'])],
            'internal_notes' => ['sometimes', 'nullable', 'string'],
            'score' => ['sometimes', 'nullable', 'integer', 'min:1', 'max:10'],
        ];
    }
}
