<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string|in:alumni',
            'program' => 'nullable|string|max:255',
            'batch' => 'nullable|integer|min:1990|max:' . date('Y'),
            'highest_educational_attainment' => 'nullable|string|in:elementary,high_school,senior_high,bachelors,masters,doctorate',
        ];
    }
}
