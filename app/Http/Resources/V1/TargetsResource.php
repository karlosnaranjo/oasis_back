<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class TargetsResource extends JsonResource
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
			'name' => $this->name,
			'phase_id' => $this->phase_id,
			'status' => $this->status,
            'phases' => $this->whenLoaded('phases', function () {
                return new PhasesResource($this->phases);
            }),
        ];
    }
}
