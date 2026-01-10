<?php

namespace App\Http\Requests;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', Password::defaults()],
            'role' => ['required', Rule::enum(UserRole::class)],
            'team_id' => ['nullable', 'exists:teams,id'],
            'is_active' => ['boolean'],
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
            'password.required' => __('Bitte gib ein Passwort ein.'),
            'role.required' => __('Bitte wähle eine Rolle.'),
        ];
    }
}
