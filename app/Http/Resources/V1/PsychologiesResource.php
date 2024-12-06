<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class PsychologiesResource extends JsonResource
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
			'issue_date' => $this->issue_date,
			'patient_id' => $this->patient_id,
			'reason_of_visit' => $this->reason_of_visit,
			'family_history' => $this->family_history,
			'work_history' => $this->work_history,
			'personal_history' => $this->personal_history,
			'addiction_history' => $this->addiction_history,
			'way_administration' => $this->way_administration,
			'other_substances' => $this->other_substances,
			'highest_substance' => $this->highest_substance,
			'current_consumption' => $this->current_consumption,
			'addictive_behavior' => $this->addictive_behavior,
			'previous_treatment' => $this->previous_treatment,
			'place_treatment' => $this->place_treatment,
			'mental_illness' => $this->mental_illness,
			'suicidal_thinking' => $this->suicidal_thinking,
			'homicidal_attempts' => $this->homicidal_attempts,
			'language' => $this->language,
			'orientation' => $this->orientation,
			'memory' => $this->memory,
			'mood' => $this->mood,
			'feeding' => $this->feeding,
			'sleep' => $this->sleep,
			'medication' => $this->medication,
			'legal_issues' => $this->legal_issues,
			'defense_mechanism' => $this->defense_mechanism,
			'another_difficulty' => $this->another_difficulty,
			'expectation' => $this->expectation,
			'diagnostic_impression' => $this->diagnostic_impression,
			'intervention' => $this->intervention,
			'comments' => $this->comments,
			'employee_id' => $this->employee_id,
			'status' => $this->status,
            'employees' => $this->whenLoaded('employees', function () {
                return new EmployeesResource($this->employees);
            }),
        ];
    }
}
