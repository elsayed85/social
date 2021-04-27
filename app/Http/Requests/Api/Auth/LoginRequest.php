<?php

namespace App\Http\Requests\Api\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'login_key' => ['required', 'max:255'], // username or email
            'password' => ['required', 'max:50', Password::min(6)
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()],
        ];
    }
}
