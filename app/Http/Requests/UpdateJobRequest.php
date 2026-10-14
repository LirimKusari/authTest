<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateJobRequest extends FormRequest
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
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'string'],
            'location' => ['sometimes', 'string', 'max:255'],
            'employment_type' => ['sometimes', Rule::in(['full_time', 'part_time', 'contract'])],
            'status' => ['sometimes', Rule::in(['draft', 'published', 'closed'])],
            'salary_min' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'salary_max' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'hiring_budget' => ['sometimes', 'nullable', 'integer', 'min:0'],
        ];
    }
}
