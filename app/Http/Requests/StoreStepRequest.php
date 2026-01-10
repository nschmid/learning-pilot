<?php

namespace App\Http\Requests;

use App\Enums\StepType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreStepRequest extends FormRequest
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
        return [
            'module_id' => ['required', 'exists:modules,id'],
            'title' => ['required', 'string', 'min:2', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'step_type' => ['required', Rule::enum(StepType::class)],
            'position' => ['nullable', 'integer', 'min:1'],
            'points_value' => ['nullable', 'integer', 'min:0', 'max:1000'],
            'estimated_minutes' => ['nullable', 'integer', 'min:1', 'max:600'],
            'is_required' => ['boolean'],
            'is_previewable' => ['boolean'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'module_id.required' => __('Bitte wähle ein Modul.'),
            'module_id.exists' => __('Das gewählte Modul existiert nicht.'),
            'title.required' => __('Bitte gib einen Titel ein.'),
            'title.min' => __('Der Titel muss mindestens 2 Zeichen lang sein.'),
            'step_type.required' => __('Bitte wähle einen Schritttyp.'),
        ];
    }
}
