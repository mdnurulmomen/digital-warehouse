<?php

namespace App\Http\Resources\Web;

use Illuminate\Http\Resources\Json\JsonResource;

class RequiredProductVariationResource extends JsonResource
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
            'product_variation_id' => $this->product_variation_id,
            'variation_name' => $this->productVariation->variation->name,
            'quantity' => $this->quantity,
            'has_serials' => $this->has_serials,
            'serials' => $this->when($this->has_serials, $this->serials->loadMissing('serial')->pluck('serial')->pluck('serial_no')),
            'available_quantity' => $this->productVariation->latestStock->available_quantity,
        ];
    }
}
