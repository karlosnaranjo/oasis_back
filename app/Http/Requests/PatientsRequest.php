<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;

// Use the ApiResponserTrait to get a standard response in whole application
use App\Traits\ApiResponserTrait;

class PatientsRequest extends FormRequest
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
			"address1" => "required",
			"cellphone" => "required",
			"email" => "required",
			"health_insurance" => "required",
			"level_of_education" => "required",
			"admission_date" => "required",
			"responsible_adult" => "required",
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
			"address1.required" => "Direccion 1 es requerido",
			"cellphone.required" => "Celular es requerido",
			"email.required" => "E-Mail es requerido",
			"health_insurance.required" => "EPS es requerido",
			"level_of_education.required" => "Escolaridad es requerido",
			"admission_date.required" => "Fecha de Ingreso es requerido",
			"responsible_adult.required" => "Acudiente es requerido",
        ];
    }
}
