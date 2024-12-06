<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class PsychologyRelativesResource extends JsonResource
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
			'name' => $this->name,
			'relative_id' => $this->relative_id,
			'age' => $this->age,
			'relationship_type' => $this->relationship_type,
            'relatives' => $this->whenLoaded('relatives', function () {
                return new RelativesResource($this->relatives);
            }),
        ];
    }
}
