<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class InitFormRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'userId' => ['nullable', 'integer'],
        ];
    }
}
