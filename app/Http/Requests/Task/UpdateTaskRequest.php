<?php

namespace App\Http\Requests\Task;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'subject_id' => [
                'required',
                Rule::exists('subjects', 'id')->where('user_id', $this->user()->id),
            ],
            'priority' => ['required', Rule::in(['low', 'medium', 'high'])],
            'difficulty' => ['required', Rule::in(['easy', 'medium', 'hard'])],
            'estimated_minutes' => ['nullable', 'integer', 'min:1', 'max:1440'],
            'deadline' => ['nullable', 'date'],
            'status' => ['required', Rule::in(['pending', 'in_progress', 'completed'])],
        ];
    }
}