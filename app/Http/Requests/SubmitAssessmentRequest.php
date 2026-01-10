<?php

namespace App\Http\Requests;

use App\Models\Assessment;
use App\Models\AssessmentAttempt;
use Illuminate\Foundation\Http\FormRequest;

class SubmitAssessmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $attempt = $this->route('attempt');

        if ($attempt instanceof AssessmentAttempt) {
            // User must own the attempt and it must not be completed
            return $attempt->enrollment->user_id === $this->user()->id
                && $attempt->completed_at === null;
        }

        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $attempt = $this->route('attempt');
        $assessment = $attempt?->assessment;

        $rules = [
            'answers' => ['required', 'array'],
        ];

        // Validate each answer based on question type
        if ($assessment) {
            foreach ($assessment->questions as $question) {
                $key = "answers.{$question->id}";

                if ($question->question_type->hasOptions()) {
                    if ($question->question_type->value === 'multiple_choice') {
                        $rules[$key] = ['nullable', 'array'];
                        $rules["{$key}.*"] = ['exists:answer_options,id'];
                    } else {
                        $rules[$key] = ['nullable', 'exists:answer_options,id'];
                    }
                } elseif ($question->question_type->value === 'text') {
                    $rules[$key] = ['nullable', 'string', 'max:5000'];
                } elseif ($question->question_type->value === 'matching') {
                    $rules[$key] = ['nullable', 'array'];
                }
            }
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'answers.required' => __('Bitte beantworte die Fragen.'),
            'answers.array' => __('Ungültiges Antwortformat.'),
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $attempt = $this->route('attempt');

            if ($attempt) {
                // Check if time limit exceeded
                if ($attempt->assessment->hasTimeLimit()) {
                    $startedAt = $attempt->started_at;
                    $timeLimit = $attempt->assessment->time_limit_minutes;
                    $deadline = $startedAt->addMinutes($timeLimit);

                    if (now()->gt($deadline)) {
                        $validator->errors()->add('answers', __('Die Zeit für diese Prüfung ist abgelaufen.'));
                    }
                }

                // Check if attempt is already submitted
                if ($attempt->completed_at !== null) {
                    $validator->errors()->add('answers', __('Diese Prüfung wurde bereits abgegeben.'));
                }
            }
        });
    }
}
