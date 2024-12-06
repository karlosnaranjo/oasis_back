<?php

namespace App\Traits;

trait ApiResponserTrait{
    /**
    *        @parameter(
    *           name="status",
    *           description="Schedule status, Active or Inactive",
    *           paramType="query",
    *           defaultValue="Active",
    *           @allowableValues(valueType="LIST", values="['Active', 'Inactive']"),
    *           required="true",
    *           allowMultiple=false,
    *           dataType="string"
    *         )
    */

    /**
     * @OA\Schema(
     *      schema="successResponse",
     *      @OA\Property(property="status_code", type="integer", description="Status code, HTTP Response", example="200"),
     *      @OA\Property(property="response", type="object", description="Response content",
     *          @OA\Property(property="success", type="boolean", description="Response result status", example="true"),
     *          @OA\Property(property="data",type="object",  description="Requested data from database",),
     *          @OA\Property(property="message",type="string",  description="Response message", example="Record updated successfully")
     *      ),
     *      @OA\Property(property="request", type="object", description="Request content",
     *          @OA\Property(property="past-id", type="integer", description="Id sent into request", example="1"),
     *          @OA\Property(property="dataIn",type="object",  description="Data JSON sent into request"),
     *      ),
     * )
     */



    protected function successResponse($code = 200, $data = null, $message = null, $pastId = null, $dataIn = null)
	{
        return response()->json(
        $response = [
            'status_code' => $code,
            'response' => [
                'success'   => true,
                'data' => $data,
                'message'   => $message
            ],
            'request' => [
                'past-id' => $pastId,
                'dataIn' => $dataIn
            ]
        ],
        $code);

	}

        /**
     * @OA\Schema(
     *      schema="errorResponse",
     *      @OA\Property(property="status_code", type="integer", description="Status code, HTTP Response", example="404"),
     *      @OA\Property(property="response", type="object", description="Response content",
     *          @OA\Property(property="success", type="boolean", description="Response result status", example="false"),
     *          @OA\Property(property="data",type="object",  description="Requested data from database",),
     *          @OA\Property(property="message",type="string",  description="Response message", example="Record not found"),
     *          @OA\Property(property="error",type="object",  description="Validation error messages for Data sent request")
     *      ),
     *      @OA\Property(property="request", type="object", description="Request content",
     *          @OA\Property(property="past-id", type="integer", description="Id sent into request", example="5"),
     *          @OA\Property(property="dataIn",type="object",  description="Data JSON sent into request"),
     *      ),
     * )
     */
	protected function errorResponse($code = 404, $data = null, $message = null, $errors = null, $pastId = null, $dataIn = null)
	{
		return response()->json(
            $response = [
                'status_code' => $code,
                'response' => [
                    'success' => false,
                    'data' => $data,
                    'message' => $message,
                    'errors' => $errors
                ],
                'request' => [
                    'past-id' => $pastId,
                    'dataIn' => $dataIn
                ]
            ],
            $code);
	}

}
