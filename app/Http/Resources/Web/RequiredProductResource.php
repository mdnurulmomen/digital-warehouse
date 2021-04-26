<?php

namespace App\Http\Resources\Web;

use Illuminate\Http\Resources\Json\JsonResource;

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
            'available_quantity' => $this->product->latestStock->available_quantity,
            'has_variations' => $this->has_variations,
            'has_serials' => $this->has_serials,
            'serials' => $this->when($this->has_serials && ! $this->has_variations, $this->serials->loadMissing('serial')->pluck('serial')->pluck('serial_no')),
            'requisition_id' => $this->requisition_id,
            'variations' => $this->when($this->has_variations, RequiredProductVariationResource::collection($this->variations)),
            'addresses' => new ProductAddressCollection($this->product->addresses),
        ];
    }
}
