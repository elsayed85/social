<?php

namespace App\Http\Requests\Api\User\Block;

use Illuminate\Foundation\Http\FormRequest;

class IsMutualBlockMeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return  auth()->id() != $this->user_id;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            "user_id" => ['required', 'integer', 'exists:users,id']
        ];
    }
}
