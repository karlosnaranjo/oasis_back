<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class EvaluationsResource extends JsonResource
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
			'creation_date' => $this->creation_date,
			'phase_id' => $this->phase_id,
			'target_id' => $this->target_id,
			'start_date' => $this->start_date,
			'end_date' => $this->end_date,
			'clinical_team' => $this->clinical_team,
			'achievement' => $this->achievement,
			'strategy' => $this->strategy,
			'requirement' => $this->requirement,
			'test' => $this->test,
			'status' => $this->status,
            'targets' => $this->whenLoaded('targets', function () {
                return new TargetsResource($this->targets);
            }),
        ];
    }
}
