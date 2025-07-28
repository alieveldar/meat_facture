<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

/**
 *
 */
class RegisterRequest extends FormRequest
{
    /**
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|unique:users|regex:/^\+?[1-9]\d{1,14}$/',
            'address' => 'nullable|string',
            'password' => [
                'required',
                'confirmed',
                Password::min(8)->mixedCase()->symbols(),
            ],
        ];
    }
}
