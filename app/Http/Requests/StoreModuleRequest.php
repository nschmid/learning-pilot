<?php

namespace App\Http\Requests;

use App\Enums\UnlockCondition;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreModuleRequest extends FormRequest
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
            'learning_path_id' => ['required', 'exists:learning_paths,id'],
            'title' => ['required', 'string', 'min:2', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'position' => ['nullable', 'integer', 'min:1'],
            'unlock_condition' => ['nullable', Rule::enum(UnlockCondition::class)],
            'unlock_value' => ['nullable', 'integer', 'min:0'],
            'is_required' => ['boolean'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'learning_path_id.required' => __('Bitte wähle einen Lernpfad.'),
            'learning_path_id.exists' => __('Der gewählte Lernpfad existiert nicht.'),
            'title.required' => __('Bitte gib einen Titel ein.'),
            'title.min' => __('Der Titel muss mindestens 2 Zeichen lang sein.'),
        ];
    }
}
