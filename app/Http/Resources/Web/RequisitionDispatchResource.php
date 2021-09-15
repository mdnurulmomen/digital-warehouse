<?php

namespace App\Http\Resources\Web;

use Illuminate\Http\Resources\Json\JsonResource;

class RequisitionDispatchResource extends JsonResource
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
            'released_at' => $this->released_at->format('Y.m.d H:i:s'),
            'has_approval' => $this->has_approval,
            'updater' => $this->when($this->has_approval, $this->updater),
            'updated_at' => $this->when($this->has_approval, $this->updated_at ? $this->updated_at->format('Y.m.d H:i:s') : NULL),
            'requisition_id' => $this->requisition_id,
            // 'requisition' => new RequisitionResource($this->requisition),
            // 'products' => DispatchedProductResource::collection($this->products),
            // 'products' => RequiredProductResource::collection($this->products),
            'delivery' =>  $this->when($this->delivery, $this->delivery),
            'agent' => $this->when($this->return, $this->return),
        ];
    }
}
