<?php

namespace App\Http\Requests;

use App\Enums\Difficulty;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLearningPathRequest extends FormRequest
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
            'title' => ['required', 'string', 'min:3', 'max:255'],
            'description' => ['required', 'string', 'min:10', 'max:5000'],
            'objectives' => ['array', 'min:1'],
            'objectives.*' => ['nullable', 'string', 'max:500'],
            'difficulty' => ['required', Rule::enum(Difficulty::class)],
            'category_id' => ['nullable', 'exists:categories,id'],
            'tag_ids' => ['array'],
            'tag_ids.*' => ['exists:tags,id'],
            'estimated_hours' => ['nullable', 'integer', 'min:1', 'max:500'],
            'thumbnail' => ['nullable', 'image', 'max:2048'],
            'is_published' => ['boolean'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.required' => __('Bitte gib einen Titel ein.'),
            'title.min' => __('Der Titel muss mindestens 3 Zeichen lang sein.'),
            'description.required' => __('Bitte gib eine Beschreibung ein.'),
            'description.min' => __('Die Beschreibung muss mindestens 10 Zeichen lang sein.'),
            'objectives.min' => __('Bitte gib mindestens ein Lernziel an.'),
            'difficulty.required' => __('Bitte wähle einen Schwierigkeitsgrad.'),
            'thumbnail.image' => __('Die Datei muss ein Bild sein.'),
            'thumbnail.max' => __('Das Bild darf maximal 2MB groß sein.'),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'title' => __('Titel'),
            'description' => __('Beschreibung'),
            'objectives' => __('Lernziele'),
            'difficulty' => __('Schwierigkeitsgrad'),
            'category_id' => __('Kategorie'),
            'tag_ids' => __('Tags'),
            'estimated_hours' => __('Geschätzte Dauer'),
            'thumbnail' => __('Vorschaubild'),
        ];
    }
}
