<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GenerateAiPlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'goal'            => ['required', 'string', 'max:200'],
            'plan_type'       => ['required', 'in:single,multi'],
            'session_minutes' => ['required_if:plan_type,single', 'nullable', 'integer', 'min:5', 'max:480'],
            'deadline'        => ['required_if:plan_type,multi', 'nullable', 'date', 'after_or_equal:today'],
            'daily_minutes'   => ['required_if:plan_type,multi', 'nullable', 'integer', 'min:10', 'max:600'],
            'subject_id'      => ['nullable', Rule::exists('subjects', 'id')->where('user_id', auth()->id())],
            'topics'          => ['nullable', 'string', 'max:1000'],
            'knowledge_level' => ['required', 'in:beginner,intermediate,advanced'],
        ];
    }
}