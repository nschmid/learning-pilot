<?php

namespace App\Http\Requests;

use App\Models\TaskSubmission;
use Illuminate\Foundation\Http\FormRequest;

class ReviewSubmissionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $submission = $this->route('submission');

        if ($submission instanceof TaskSubmission) {
            return $this->user()->can('review', $submission);
        }

        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $submission = $this->route('submission');
        $maxPoints = $submission?->task?->max_points ?? 100;

        return [
            'status' => ['required', 'in:approved,rejected,revision_requested'],
            'score' => ['nullable', 'integer', 'min:0', 'max:'.$maxPoints],
            'feedback' => ['nullable', 'string', 'max:5000'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'status.required' => __('Bitte wähle einen Status.'),
            'status.in' => __('Ungültiger Status.'),
            'score.min' => __('Die Punktzahl muss mindestens 0 sein.'),
            'score.max' => __('Die Punktzahl darf maximal :max sein.'),
            'feedback.max' => __('Das Feedback darf maximal 5.000 Zeichen lang sein.'),
        ];
    }
}
