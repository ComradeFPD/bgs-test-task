<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddUserToActivity extends FormRequest
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
            'name' => 'required|string|min:4|max:255',
            'surname' => 'required|string|min:4|max:255',
            'patronymic' => 'required|string|min:2|max:255',
            'email' => 'required|unique:users|email',
            'activity_id' => 'required|exists:activities,id'
        ];
    }
}
