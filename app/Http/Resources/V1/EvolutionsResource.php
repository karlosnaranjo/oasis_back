<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class EvolutionsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
			'code' => $this->code,
			'patient_id' => $this->patient_id,
			'employee_id' => $this->employee_id,
			'date_of_evolution' => $this->date_of_evolution,
			'area' => $this->area,
			'comments' => $this->comments,
			'status' => $this->status,
            'employees' => $this->whenLoaded('employees', function () {
                return new EmployeesResource($this->employees);
            }),
        ];
    }
}
