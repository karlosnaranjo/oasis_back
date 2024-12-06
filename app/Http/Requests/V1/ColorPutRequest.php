<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Symfony\Component\HttpFoundation\Response;

// Use the ApiResponserTrait to get a standard response in whole application
use App\Traits\ApiResponserTrait;

class ColorPutRequest extends FormRequest
{
    use ApiResponserTrait;
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'code' => 'required|string|unique:colors,code,' . $this->route('id') . ',id',
            'description' => 'required|string',
            'color' => 'string',
        ];
    }

    /**
     * When validation fails, show the response with the validation errors
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            $this->errorResponse(
                $code = Response::HTTP_UNPROCESSABLE_ENTITY,
                $data = null,
                $message = 'Validation errors',
                $errors = $validator->errors(),
                $pastId = $this->id,
                $dataIn = $this->all()
            )
        );
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'code.required' => 'Code is required',
            'code.string' => 'You must to specify a string code',
            'code.unique' => 'the code must be unique, there is already a code ' . $this->code . ' for another record',

            'description.required' => 'Description is required',
            'description.string' => 'You must to specify a string value for description',

            'color.string' => 'You must to specify a string value for color',
        ];
    }
}
