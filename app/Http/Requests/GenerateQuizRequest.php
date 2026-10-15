<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GenerateQuizRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'subject' => ['required', 'string', 'max:100'],
            'topic'   => ['nullable', 'string', 'max:150'],
            'level'   => ['required', 'in:beginner,intermediate,advanced'],
        ];
    }
}