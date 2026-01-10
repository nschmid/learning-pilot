<?php

namespace App\Http\Requests;

use App\Enums\QuestionType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreQuestionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->isInstructor() || $this->user()->isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $questionType = QuestionType::tryFrom($this->input('question_type'));

        $rules = [
            'assessment_id' => ['required', 'exists:assessments,id'],
            'question_type' => ['required', Rule::enum(QuestionType::class)],
            'question_text' => ['required', 'string', 'min:5', 'max:5000'],
            'question_image' => ['nullable', 'image', 'max:5120'],
            'explanation' => ['nullable', 'string', 'max:2000'],
            'points' => ['required', 'integer', 'min:1', 'max:100'],
            'position' => ['nullable', 'integer', 'min:0'],
            'metadata' => ['nullable', 'array'],
        ];

        // Validate options for choice-based questions
        if ($questionType?->hasOptions()) {
            $rules['options'] = ['required', 'array', 'min:2', 'max:10'];
            $rules['options.*.option_text'] = ['required', 'string', 'max:1000'];
            $rules['options.*.is_correct'] = ['boolean'];
            $rules['options.*.feedback'] = ['nullable', 'string', 'max:500'];
        }

        // For matching questions, validate pairs
        if ($questionType === QuestionType::Matching) {
            $rules['metadata.pairs'] = ['required', 'array', 'min:2', 'max:10'];
            $rules['metadata.pairs.*.left'] = ['required', 'string', 'max:500'];
            $rules['metadata.pairs.*.right'] = ['required', 'string', 'max:500'];
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'assessment_id.required' => __('Bitte wähle eine Prüfung.'),
            'assessment_id.exists' => __('Die gewählte Prüfung existiert nicht.'),
            'question_type.required' => __('Bitte wähle einen Fragetyp.'),
            'question_text.required' => __('Bitte gib eine Frage ein.'),
            'question_text.min' => __('Die Frage muss mindestens 5 Zeichen lang sein.'),
            'points.required' => __('Bitte gib die Punktzahl ein.'),
            'points.min' => __('Die Punktzahl muss mindestens 1 sein.'),
            'options.required' => __('Bitte gib Antwortoptionen ein.'),
            'options.min' => __('Es müssen mindestens 2 Antwortoptionen vorhanden sein.'),
            'options.*.option_text.required' => __('Bitte gib einen Antworttext ein.'),
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $questionType = QuestionType::tryFrom($this->input('question_type'));
            $options = $this->input('options', []);

            // Ensure at least one correct answer for choice questions
            if ($questionType?->hasOptions()) {
                $hasCorrect = collect($options)->contains(fn ($opt) => $opt['is_correct'] ?? false);

                if (!$hasCorrect) {
                    $validator->errors()->add('options', __('Es muss mindestens eine richtige Antwort markiert sein.'));
                }

                // For single choice, ensure only one correct answer
                if ($questionType === QuestionType::SingleChoice) {
                    $correctCount = collect($options)->filter(fn ($opt) => $opt['is_correct'] ?? false)->count();
                    if ($correctCount > 1) {
                        $validator->errors()->add('options', __('Bei Einzelauswahl darf nur eine Antwort als richtig markiert sein.'));
                    }
                }
            }
        });
    }
}
