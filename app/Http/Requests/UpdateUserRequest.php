<?php

namespace App\Http\Requests;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->route('user');

        if ($user instanceof User) {
            return $this->user()->can('update', $user);
        }

        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $userId = $this->route('user')?->id;

        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => ['sometimes', 'required', 'string', 'email', 'max:255', 'unique:users,email,'.$userId],
            'password' => ['nullable', 'string', Password::defaults()],
            'role' => ['sometimes', 'required', Rule::enum(UserRole::class)],
            'team_id' => ['nullable', 'exists:teams,id'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => __('Bitte gib einen Namen ein.'),
            'email.required' => __('Bitte gib eine E-Mail-Adresse ein.'),
            'email.email' => __('Bitte gib eine gültige E-Mail-Adresse ein.'),
            'email.unique' => __('Diese E-Mail-Adresse wird bereits verwendet.'),
            'role.required' => __('Bitte wähle eine Rolle.'),
        ];
    }
}
