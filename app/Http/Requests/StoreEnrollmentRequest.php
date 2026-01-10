<?php

namespace App\Http\Requests;

use App\Models\LearningPath;
use Illuminate\Foundation\Http\FormRequest;

class StoreEnrollmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $path = $this->route('path');

        if ($path instanceof LearningPath) {
            return $this->user()->can('enroll', $path);
        }

        // Also check by ID from request body
        if ($this->has('learning_path_id')) {
            $path = LearningPath::find($this->input('learning_path_id'));
            return $path && $this->user()->can('enroll', $path);
        }

        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $userId = $this->user()->id;

        return [
            'learning_path_id' => [
                'required',
                'exists:learning_paths,id',
                // Ensure user is not already enrolled
                function ($attribute, $value, $fail) use ($userId) {
                    $exists = \App\Models\Enrollment::where('user_id', $userId)
                        ->where('learning_path_id', $value)
                        ->exists();

                    if ($exists) {
                        $fail(__('Du bist bereits für diesen Lernpfad eingeschrieben.'));
                    }
                },
            ],
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
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $pathId = $this->input('learning_path_id');
            $path = LearningPath::find($pathId);

            if ($path) {
                // Check if path is published
                if (!$path->is_published) {
                    $validator->errors()->add('learning_path_id', __('Dieser Lernpfad ist nicht veröffentlicht.'));
                }

                // Check prerequisites
                $user = $this->user();
                foreach ($path->prerequisites as $prerequisite) {
                    $completed = $user->enrollments()
                        ->where('learning_path_id', $prerequisite->id)
                        ->where('status', 'completed')
                        ->exists();

                    if (!$completed) {
                        $validator->errors()->add(
                            'learning_path_id',
                            __('Du musst zuerst ":path" abschließen.', ['path' => $prerequisite->title])
                        );
                    }
                }
            }
        });
    }
}
