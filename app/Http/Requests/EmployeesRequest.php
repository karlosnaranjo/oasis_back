<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;

// Use the ApiResponserTrait to get a standard response in whole application
use App\Traits\ApiResponserTrait;

class EmployeesRequest extends FormRequest
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
			"code" => "required",
			"name" => "required",
			"gender" => "required",
			"marital_status" => "required",
			"address1" => "required",
			"address2" => "required",
			"phone" => "required",
			"cellphone" => "required",
			"email" => "required",
			"job_title" => "required",
			"status" => "required",
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
			"code.required" => "Numero es requerido",
			"name.required" => "Nombres es requerido",
			"gender.required" => "Genero es requerido",
			"marital_status.required" => "Estado Civil es requerido",
			"address1.required" => "Direccion 1 es requerido",
			"address2.required" => "Direccion 2 es requerido",
			"phone.required" => "Telefono es requerido",
			"cellphone.required" => "Celular es requerido",
			"email.required" => "E-Mail es requerido",
			"job_title.required" => "Cargo es requerido",
			"status.required" => "Estado es requerido",
        ];
    }
}
