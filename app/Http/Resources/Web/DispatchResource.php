<?php

namespace App\Http\Resources\Web;

use Illuminate\Http\Resources\Json\JsonResource;

class DispatchResource extends JsonResource
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
            'requisition_id' => $this->requisition_id,
            'released_at' => $this->released_at->diffForHumans(),
            'requisition' => new DispatchedRequisitionResource($this->requisition),
            'products' => DispatchedProductResource::collection($this->products),
            // 'products' => RequiredProductResource::collection($this->products),
            'delivery' =>  $this->when($this->delivery, $this->delivery),
            'agent' => $this->when($this->return, $this->return),
        ];
    }
}
