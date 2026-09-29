<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GenerateAiPlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'subject_id' => ['required', 'exists:subjects,id'],
            'exam_name' => ['required', 'string', 'max:255'],
            'exam_date' => ['required', 'date', 'after:today'],
            'daily_minutes' => ['required', 'integer', 'min:15', 'max:600'],
            'topics' => ['required', 'string', 'max:1000'],
            'knowledge_level' => ['required', 'in:beginner,intermediate,advanced'],
        ];
    }
}