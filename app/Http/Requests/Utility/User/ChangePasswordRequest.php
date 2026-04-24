<?php

namespace App\Http\Requests\Utility\User;

use Illuminate\Foundation\Http\FormRequest;

class ChangePasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'password' => ['required', 'confirmed', 'min:8'],
            'password_confirmation' => ['required'],
        ];
    }
}
