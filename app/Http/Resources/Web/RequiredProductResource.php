<?php

namespace App\Http\Resources\Web;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Web\RequiredProductVariationResource;

class RequiredProductResource extends JsonResource
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
            'product_id' => $this->product_id,
            'product_name' => $this->product->name,
            'quantity' => $this->quantity,
            'available_quantity' => $this->product->available_quantity,
            'has_variations' => $this->has_variations,
            'requisition_id' => $this->requisition_id,
            'variations' => $this->when($this->has_variations, RequiredProductVariationResource::collection($this->variations)),
        ];
    }
}
