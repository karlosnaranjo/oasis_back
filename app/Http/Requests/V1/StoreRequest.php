<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['required', 'string'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            // 'username' => ['required', 'string', 'unique:users,username'],
            'role_id' => ['required', 'integer'],
        ];
    }

    public function messages()
    {
        return [
            // 'username.unique' => 'El usuario ya se encuentra creado',
            'email.unique' => 'El correo ya se encuentra asociado a un usuario',
        ];
    }
}
