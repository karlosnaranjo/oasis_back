<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
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
            'email' => ['string', 'email', 'max:255'],
            'role_id' => ['integer', 'required'],
            'password' => ['string', 'min:6']
        ];
    }
}
