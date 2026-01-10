<?php

namespace App\Http\Requests;

use App\Enums\MaterialType;
use App\Enums\VideoSourceType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMaterialRequest extends FormRequest
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
        $materialType = MaterialType::tryFrom($this->input('material_type'));

        $rules = [
            'step_id' => ['required', 'exists:learning_steps,id'],
            'material_type' => ['required', Rule::enum(MaterialType::class)],
            'title' => ['required', 'string', 'min:2', 'max:255'],
            'position' => ['nullable', 'integer', 'min:0'],
            'metadata' => ['nullable', 'array'],
        ];

        // Content rules based on type
        if ($materialType === MaterialType::Text) {
            $rules['content'] = ['required', 'string', 'min:10', 'max:100000'];
        } elseif ($materialType === MaterialType::Link) {
            $rules['external_url'] = ['required', 'url', 'max:2000'];
            $rules['content'] = ['nullable', 'string', 'max:2000'];
        } elseif ($materialType === MaterialType::Video) {
            // Either file upload or external embed
            $rules['video_source_type'] = ['nullable', Rule::enum(VideoSourceType::class)];
            $rules['video_source_id'] = ['nullable', 'string', 'max:255'];
            $rules['external_url'] = ['nullable', 'url', 'max:2000'];
            $rules['file'] = ['nullable', 'file', 'mimes:mp4,webm,mov', 'max:512000']; // 500MB
            $rules['duration_seconds'] = ['nullable', 'integer', 'min:1'];
        } elseif ($materialType === MaterialType::Audio) {
            $rules['file'] = ['required', 'file', 'mimes:mp3,wav,ogg', 'max:102400']; // 100MB
            $rules['duration_seconds'] = ['nullable', 'integer', 'min:1'];
        } elseif ($materialType === MaterialType::Pdf) {
            $rules['file'] = ['required', 'file', 'mimes:pdf', 'max:51200']; // 50MB
        } elseif ($materialType === MaterialType::Image) {
            $rules['file'] = ['required', 'image', 'max:10240']; // 10MB
            $rules['content'] = ['nullable', 'string', 'max:500']; // Alt text
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'step_id.required' => __('Bitte wähle einen Schritt.'),
            'step_id.exists' => __('Der gewählte Schritt existiert nicht.'),
            'material_type.required' => __('Bitte wähle einen Materialtyp.'),
            'title.required' => __('Bitte gib einen Titel ein.'),
            'title.min' => __('Der Titel muss mindestens 2 Zeichen lang sein.'),
            'content.required' => __('Bitte gib Inhalt ein.'),
            'content.min' => __('Der Inhalt muss mindestens 10 Zeichen lang sein.'),
            'external_url.required' => __('Bitte gib eine URL ein.'),
            'external_url.url' => __('Bitte gib eine gültige URL ein.'),
            'file.required' => __('Bitte lade eine Datei hoch.'),
            'file.mimes' => __('Ungültiger Dateityp. Erlaubt sind: :values'),
            'file.max' => __('Die Datei ist zu groß.'),
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $materialType = MaterialType::tryFrom($this->input('material_type'));

            // For video, require either file or embed source
            if ($materialType === MaterialType::Video) {
                $hasFile = $this->hasFile('file');
                $hasEmbed = $this->filled('video_source_type') && $this->filled('video_source_id');
                $hasUrl = $this->filled('external_url');

                if (!$hasFile && !$hasEmbed && !$hasUrl) {
                    $validator->errors()->add('file', __('Bitte lade ein Video hoch oder gib eine Embed-URL ein.'));
                }
            }
        });
    }
}
