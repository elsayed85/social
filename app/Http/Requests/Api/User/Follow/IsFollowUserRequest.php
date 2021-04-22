<?php

namespace App\Http\Requests\Api\User\Follow;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class IsFollowUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return  !auth()->user()->is($this->user2);
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

    protected function prepareForValidation()
    {
        $this->merge(['user2' => User::find($this->user_id)]);
    }
}
