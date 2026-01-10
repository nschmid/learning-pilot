<?php

namespace App\Http\Requests;

use App\Enums\TaskType;
use App\Models\Task;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $task = $this->route('task');

        if ($task instanceof Task) {
            return $this->user()->can('update', $task);
        }

        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'task_type' => ['sometimes', 'required', Rule::enum(TaskType::class)],
            'title' => ['sometimes', 'required', 'string', 'min:2', 'max:255'],
            'instructions' => ['sometimes', 'required', 'string', 'min:10', 'max:10000'],
            'max_points' => ['sometimes', 'required', 'integer', 'min:1', 'max:1000'],
            'due_days' => ['nullable', 'integer', 'min:1', 'max:365'],
            'allow_late' => ['boolean'],
            'allow_resubmit' => ['boolean'],
            'rubric' => ['nullable', 'array'],
            'rubric.*.criterion' => ['required_with:rubric', 'string', 'max:255'],
            'rubric.*.points' => ['required_with:rubric', 'integer', 'min:0'],
            'rubric.*.description' => ['nullable', 'string', 'max:500'],
            'allowed_file_types' => ['nullable', 'array'],
            'allowed_file_types.*' => ['string', 'max:10'],
            'max_file_size_mb' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'task_type.required' => __('Bitte wähle einen Aufgabentyp.'),
            'title.required' => __('Bitte gib einen Titel ein.'),
            'title.min' => __('Der Titel muss mindestens 2 Zeichen lang sein.'),
            'instructions.required' => __('Bitte gib eine Aufgabenbeschreibung ein.'),
            'instructions.min' => __('Die Aufgabenbeschreibung muss mindestens 10 Zeichen lang sein.'),
            'max_points.required' => __('Bitte gib die maximale Punktzahl ein.'),
            'max_points.min' => __('Die Punktzahl muss mindestens 1 sein.'),
            'max_points.max' => __('Die Punktzahl darf maximal 1000 sein.'),
        ];
    }
}
