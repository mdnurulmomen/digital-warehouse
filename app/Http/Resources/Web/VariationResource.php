<?php

namespace App\Http\Resources\Web;

use Illuminate\Http\Resources\Json\JsonResource;

class VariationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->whenLoaded('type'),
            'deleted_at' => $this->deleted_at,
            // 'parent' => new VariationResource($this->parent ? $this->parent->loadMissing('parent') : NULL),
            'parent' => $this->when($this->whenLoaded('parent'), new VariationResource($this->parent ? $this->parent->loadMissing('parent') : NULL)),
            'children' => $this->whenLoaded('nestedChilds'),
        ];
    }
}
