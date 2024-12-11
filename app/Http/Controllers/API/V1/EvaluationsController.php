<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\API\ApiController;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;
use App\Traits\IndexTrait;

// Models
use App\Models\Evaluations;
use App\Models\Patients;
use App\Models\Phases;
use App\Models\Targets;

// Repositories
use App\Http\Repositories\EvaluationsRespository;

// Resources
use App\Http\Resources\V1\EvaluationsResource;



class EvaluationsController extends ApiController
{
    use IndexTrait;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $repo = new EvaluationsRespository();
        return $repo->getEvaluationsList($request);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $dataIn = $request->all();
        $dataIn['status'] = true;

        // insert the new record into the database
        $result = Evaluations::create($dataIn);

        // send a successful response
        return $this->successResponse(
            $code = Response::HTTP_CREATED,
            $data = new EvaluationsResource($result),
            $message = 'Record created successfully.',
            $pastId = null,
            $dataIn
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\{Evaluations}  $Evaluations
     * @return \Illuminate\Http\Response
     */
    public function update($id, Request $request)
    {
        // Find the id into the database using its model
        $result = Evaluations::find($id);
        // if not found, return a 404 response
        if (is_null($result)) {
            // send an error response
            return $this->errorResponse(
                $code = Response::HTTP_NOT_FOUND,
                $data = null,
                $message = 'Record not found',
                $errors = null,
                $pastId = $id,
                $dataIn = $request->all()
            );
        } else {

            // We have to be sure that the variables contain its values before to assign them
            $result->update($request->all());

            // send a successful response
            return $this->successResponse(
                $code = Response::HTTP_OK,
                $data = new EvaluationsResource($result),
                $message = 'Record updated successfully.',
                $pastId = $id,
                $dataIn = $request->all()
            );
        }
    }

    /**
     * Update or create the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\{Evaluations}  $Evaluations
     * @return \Illuminate\Http\Response
     */
    public function updateOrCreate($id, Request $request)
    {
        // Find the parent id from the request
        $parentId = $request->get('');
        // Retrieve the record based on parent id
        $result = Evaluations::where('', $parentId)->first(); 

        // if not found, create a new record
        if (is_null($result)) {
            $dataIn = $request->all();
            $dataIn['status'] = true;

            // insert the new record into the database
            $result = Evaluations::create($dataIn);
            $message = 'Record created successfully.';
        } else {
            $result->update($request->all());
            $message = 'Record updated successfully.';
        }
        // send a successful response
        return $this->successResponse(
            $code = Response::HTTP_OK,
            $data = new EvaluationsResource($result),
            $message,
            $pastId = $id,
            $dataIn = $request->all()
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\{Evaluations}  $seg_Evaluations
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
    // Find the id into the database using its model
        $result = Evaluations::find($id);

        // if not found, return a 404 response
        if (is_null($result)) {
            // send an error response
            return $this->errorResponse(
                $code = Response::HTTP_NOT_FOUND,
                $data = null,
                $message = 'Record not found to delete.',
                $errors = null,
                $pastId = $id,
                $dataIn = null
            );
        } else {
            // we can save the result's main description to send a better response to the user
            $description = $result->name ?? '';

            // delete the record and send a successful response
            $result->delete();

            // send a successful response
            return $this->successResponse(
                $code = Response::HTTP_OK,
                $data = null,
                $message = 'The record ' . $description . ' has been deleted successfully',
                $pastId = $id,
                $dataIn = null
            );
        }
    }

    /**
     * Change status for the specified resource from storage.
     *
     * @param  \App\Models\{Evaluations} 
     * @return \Illuminate\Http\Response
     */
    public function changeStatus($id)
    {
        // Find the id into the database using its model
        $result = Evaluations::find($id);

        // if not found, return a 404 response
        if (is_null($result)) {
            // send an error response
            return $this->errorResponse(
                $code = Response::HTTP_NOT_FOUND,
                $data = null,
                $message = 'Record not found to change status.',
                $errors = null,
                $pastId = $id,
                $dataIn = null
            );
        } else {
            // we need to flip the status value
            $result->status = !$result->status;

            // save the record and send a successful response
            $result->save();

            // send a successful response
            return $this->successResponse(
                $code = Response::HTTP_OK,
                $data = new EvaluationsResource($result),
                $message = 'Status changed successfully.',
                $pastId = $id,
                $dataIn = null
            );
        }
    }

    public function initForm(Request $request)
    {
        //tabla de principal debe devolver sus campos sin hacer join de los foreingkey
        $id = $request->get('id');
        $data =  ((isset($id) and !is_null($id)) ? Evaluations::where('id', '=', $id)->first()
            : new Evaluations());

        //por cada FK que tenga la tabla principal hacer una consulta independiente a esa maestra con los campos
        //ID y Nombre
		$patient = Patients::select('id','name')->get();
		$phase = Phases::select('id','name')->get();
		$target = Targets::select('id','name')->get();

        $respuesta = [
			"evaluations" => new EvaluationsResource($data),
			"patient" => $patient,
			"phase" => $phase,
			"target" => $target,
        ];
        return response($respuesta, Response::HTTP_OK);
    }

    public function targetsByPhase(Request $request)
    {
        $phaseId = $request->get('phase_id');
        $targets = Targets::where('phase_id', $phaseId)
            ->select('id', 'name')
            ->get();

        return response([
            'targets' => $targets
        ], Response::HTTP_OK);
    }
}

