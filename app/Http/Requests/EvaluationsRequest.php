<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;

// Use the ApiResponserTrait to get a standard response in whole application
use App\Traits\ApiResponserTrait;

class EvaluationsRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        return [

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
               [ 
               "code" => Response::HTTP_NOT_FOUND,
               "data" => null,
               "message" => 'Validation errors',
               "errors" => $validator->errors(),
               "pastId" => null,
               "dataIn" => $this->all()
               ],
               Response::HTTP_NOT_FOUND
            )
        );
    }

    public function messages()
    {
        return [

        ];
    }
}
