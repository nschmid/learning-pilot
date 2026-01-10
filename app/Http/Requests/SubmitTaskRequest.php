<?php

namespace App\Http\Requests;

use App\Models\Task;
use Illuminate\Foundation\Http\FormRequest;

class SubmitTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $task = $this->route('task');

        if ($task instanceof Task) {
            return $this->user()->can('submit', $task);
        }

        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $task = $this->route('task');
        $rules = [
            'content' => ['nullable', 'string', 'max:50000'],
        ];

        // File validation if task allows files
        if ($task instanceof Task && $task->allow_file_upload) {
            $rules['files'] = ['nullable', 'array', 'max:'.($task->max_files ?? 5)];
            $rules['files.*'] = [
                'file',
                'max:'.($task->max_file_size ?? 10240),
            ];

            if ($task->allowed_file_types) {
                $rules['files.*'][] = 'mimes:'.implode(',', $task->allowed_file_types);
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
            'content.max' => __('Die Antwort darf maximal 50.000 Zeichen lang sein.'),
            'files.max' => __('Du kannst maximal :max Dateien hochladen.'),
            'files.*.max' => __('Jede Datei darf maximal :max KB groß sein.'),
            'files.*.mimes' => __('Ungültiger Dateityp. Erlaubt sind: :values'),
        ];
    }
}
