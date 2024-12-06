<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class PatientsResource extends JsonResource
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
			'health_insurance' => $this->health_insurance,
			'level_of_education' => $this->level_of_education,
			'admission_date' => $this->admission_date,
			'second_date' => $this->second_date,
			'third_date' => $this->third_date,
			'responsible_adult' => $this->responsible_adult,
			'responsible_adult_code' => $this->responsible_adult_code,
			'relationship' => $this->relationship,
			'responsible_adult_phone' => $this->responsible_adult_phone,
			'responsible_adult_cellphone' => $this->responsible_adult_cellphone,
			'drug_id' => $this->drug_id,
			'orientation' => $this->orientation,
			'body_language' => $this->body_language,
			'ideation' => $this->ideation,
			'delusions' => $this->delusions,
			'hallucinations' => $this->hallucinations,
			'eating_problems' => $this->eating_problems,
			'treatment_motivations' => $this->treatment_motivations,
			'end_date' => $this->end_date,
			'cause_of_end' => $this->cause_of_end,
			'end_date_second' => $this->end_date_second,
			'cause_of_end_second' => $this->cause_of_end_second,
			'end_date_third' => $this->end_date_third,
			'cause_of_end_third' => $this->cause_of_end_third,
			'comments' => $this->comments,
			'employee_id' => $this->employee_id,
			'status' => $this->status,
            'employees' => $this->whenLoaded('employees', function () {
                return new EmployeesResource($this->employees);
            }),
        ];
    }
}
