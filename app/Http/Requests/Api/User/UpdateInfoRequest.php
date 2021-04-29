<?php

namespace App\Http\Requests\Api\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInfoRequest extends FormRequest
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
            "name" => ['sometimes', 'min:3', 'max:50', 'string'],
            'username' => ['sometimes', 'min:3', 'max:100', 'unique:users,username,' . auth()->id() . ',id'],
            'email' => ['sometimes', 'email', 'max:255', 'unique:users,email,' . auth()->id() . ',id']
        ];
    }
}
