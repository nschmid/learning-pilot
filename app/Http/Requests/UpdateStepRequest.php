<?php

namespace App\Http\Requests;

use App\Enums\StepType;
use App\Models\LearningStep;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStepRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $step = $this->route('step');

        if ($step instanceof LearningStep) {
            return $this->user()->can('update', $step);
        }

        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'required', 'string', 'min:2', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'step_type' => ['sometimes', 'required', Rule::enum(StepType::class)],
            'position' => ['nullable', 'integer', 'min:0'],
            'points_value' => ['nullable', 'integer', 'min:0', 'max:1000'],
            'estimated_minutes' => ['nullable', 'integer', 'min:1', 'max:600'],
            'is_required' => ['boolean'],
            'is_preview' => ['boolean'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.required' => __('Bitte gib einen Titel ein.'),
            'title.min' => __('Der Titel muss mindestens 2 Zeichen lang sein.'),
            'step_type.required' => __('Bitte wähle einen Schritttyp.'),
            'points_value.max' => __('Die Punktzahl darf maximal 1000 sein.'),
            'estimated_minutes.min' => __('Die geschätzte Zeit muss mindestens 1 Minute sein.'),
            'estimated_minutes.max' => __('Die geschätzte Zeit darf maximal 600 Minuten (10 Stunden) sein.'),
        ];
    }
}
