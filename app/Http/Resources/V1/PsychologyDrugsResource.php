<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class PsychologyDrugsResource extends JsonResource
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
			'psychology_id' => $this->psychology_id,
			'drug_id' => $this->drug_id,
			'start_age' => $this->start_age,
			'frecuency_of_consumption' => $this->frecuency_of_consumption,
			'maximum_abstinence' => $this->maximum_abstinence,
			'consumption_date' => $this->consumption_date,
            'drugs' => $this->whenLoaded('drugs', function () {
                return new DrugsResource($this->drugs);
            }),
        ];
    }
}
