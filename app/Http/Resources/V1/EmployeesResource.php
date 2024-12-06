<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class EmployeesResource extends JsonResource
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
			'document_type' => $this->document_type,
			'code' => $this->code,
			'name' => $this->name,
			'image' => $this->image,
			'gender' => $this->gender,
			'marital_status' => $this->marital_status,
			'date_of_birth' => $this->date_of_birth,
			'address1' => $this->address1,
			'address2' => $this->address2,
			'phone' => $this->phone,
			'cellphone' => $this->cellphone,
			'email' => $this->email,
			'job_title' => $this->job_title,
			'status' => $this->status,
            
        ];
    }
}
