<?php

namespace App\Http\Requests;

use App\Enums\MaterialType;
use App\Enums\VideoSourceType;
use App\Models\LearningMaterial;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMaterialRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $material = $this->route('material');

        if ($material instanceof LearningMaterial) {
            return $this->user()->can('update', $material->step);
        }

        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $material = $this->route('material');
        $materialType = MaterialType::tryFrom($this->input('material_type', $material?->material_type?->value));

        $rules = [
            'material_type' => ['sometimes', 'required', Rule::enum(MaterialType::class)],
            'title' => ['sometimes', 'required', 'string', 'min:2', 'max:255'],
            'position' => ['nullable', 'integer', 'min:0'],
            'metadata' => ['nullable', 'array'],
        ];

        // Content rules based on type
        if ($materialType === MaterialType::Text) {
            $rules['content'] = ['sometimes', 'required', 'string', 'min:10', 'max:100000'];
        } elseif ($materialType === MaterialType::Link) {
            $rules['external_url'] = ['sometimes', 'required', 'url', 'max:2000'];
            $rules['content'] = ['nullable', 'string', 'max:2000'];
        } elseif ($materialType === MaterialType::Video) {
            $rules['video_source_type'] = ['nullable', Rule::enum(VideoSourceType::class)];
            $rules['video_source_id'] = ['nullable', 'string', 'max:255'];
            $rules['external_url'] = ['nullable', 'url', 'max:2000'];
            $rules['file'] = ['nullable', 'file', 'mimes:mp4,webm,mov', 'max:512000'];
            $rules['duration_seconds'] = ['nullable', 'integer', 'min:1'];
        } elseif ($materialType === MaterialType::Audio) {
            $rules['file'] = ['nullable', 'file', 'mimes:mp3,wav,ogg', 'max:102400'];
            $rules['duration_seconds'] = ['nullable', 'integer', 'min:1'];
        } elseif ($materialType === MaterialType::Pdf) {
            $rules['file'] = ['nullable', 'file', 'mimes:pdf', 'max:51200'];
        } elseif ($materialType === MaterialType::Image) {
            $rules['file'] = ['nullable', 'image', 'max:10240'];
            $rules['content'] = ['nullable', 'string', 'max:500'];
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'material_type.required' => __('Bitte wähle einen Materialtyp.'),
            'title.required' => __('Bitte gib einen Titel ein.'),
            'title.min' => __('Der Titel muss mindestens 2 Zeichen lang sein.'),
            'content.required' => __('Bitte gib Inhalt ein.'),
            'content.min' => __('Der Inhalt muss mindestens 10 Zeichen lang sein.'),
            'external_url.required' => __('Bitte gib eine URL ein.'),
            'external_url.url' => __('Bitte gib eine gültige URL ein.'),
            'file.mimes' => __('Ungültiger Dateityp. Erlaubt sind: :values'),
            'file.max' => __('Die Datei ist zu groß.'),
        ];
    }
}
