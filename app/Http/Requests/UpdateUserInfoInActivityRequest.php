<?php

namespace App\Http\Requests;

use http\Client\Curl\User;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUserInfoInActivityRequest extends FormRequest
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
        $user = \App\Models\User::find($this->request->get('user_id'));
        return [
            'user_id' => 'required|exists:users,id',
            'name' => 'string|min:4|max:255',
            'surname' => 'string|min:4|max:255',
            'patronymic' => 'string|min:2|max:255',
            'email' => "unique:users,email,{$user->id}|email",
        ];
    }
}
