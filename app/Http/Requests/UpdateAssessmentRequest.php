<?php

namespace App\Http\Requests;

use App\Enums\AssessmentType;
use App\Models\Assessment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAssessmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $assessment = $this->route('assessment');

        if ($assessment instanceof Assessment) {
            return $this->user()->can('update', $assessment);
        }

        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'assessment_type' => ['sometimes', 'required', Rule::enum(AssessmentType::class)],
            'title' => ['sometimes', 'required', 'string', 'min:2', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'instructions' => ['nullable', 'string', 'max:5000'],
            'time_limit_minutes' => ['nullable', 'integer', 'min:1', 'max:480'],
            'passing_score_percent' => ['sometimes', 'required', 'integer', 'min:0', 'max:100'],
            'max_attempts' => ['nullable', 'integer', 'min:1', 'max:100'],
            'shuffle_questions' => ['boolean'],
            'shuffle_answers' => ['boolean'],
            'show_correct_answers' => ['boolean'],
            'show_score_immediately' => ['boolean'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'assessment_type.required' => __('Bitte wähle einen Prüfungstyp.'),
            'title.required' => __('Bitte gib einen Titel ein.'),
            'title.min' => __('Der Titel muss mindestens 2 Zeichen lang sein.'),
            'passing_score_percent.required' => __('Bitte gib eine Bestehensgrenze ein.'),
            'passing_score_percent.min' => __('Die Bestehensgrenze muss mindestens 0% sein.'),
            'passing_score_percent.max' => __('Die Bestehensgrenze darf maximal 100% sein.'),
            'time_limit_minutes.min' => __('Das Zeitlimit muss mindestens 1 Minute sein.'),
            'time_limit_minutes.max' => __('Das Zeitlimit darf maximal 480 Minuten (8 Stunden) sein.'),
        ];
    }
}
