<?php

namespace App\Http\Requests;

use App\Enums\UnlockCondition;
use App\Models\Module;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateModuleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $module = $this->route('module');

        if ($module instanceof Module) {
            return $this->user()->can('update', $module);
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
            'position' => ['nullable', 'integer', 'min:0'],
            'unlock_condition' => ['sometimes', Rule::enum(UnlockCondition::class)],
            'unlock_value' => ['nullable', 'integer', 'min:0', 'max:100'],
            'is_required' => ['boolean'],
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
            'description.max' => __('Die Beschreibung darf maximal 2000 Zeichen lang sein.'),
            'unlock_value.max' => __('Der Entsperrwert darf maximal 100 sein.'),
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $condition = $this->input('unlock_condition');
            $value = $this->input('unlock_value');

            // If unlock condition is completion_percent, unlock_value is required
            if ($condition === UnlockCondition::CompletionPercent->value && $value === null) {
                $validator->errors()->add('unlock_value', __('Bitte gib den erforderlichen Fortschritt in Prozent ein.'));
            }
        });
    }
}
