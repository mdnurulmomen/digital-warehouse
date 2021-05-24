<?php

namespace App\Http\Resources\Web;

use Illuminate\Http\Resources\Json\JsonResource;

class ManagerProductVariationResource extends JsonResource
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
            'sku' => $this->sku,
            'available_quantity' => $this->latestStock->available_quantity ?? 0,
            'requested_quantity' => $this->nonDispatchedRequests->sum('quantity'),
            'dispatched_quantity' => $this->dispatchedRequests->sum('quantity'),
            'price' => $this->price,
            'variation_immutability' => $this->variation_immutability,
            'variation' => $this->variation,
            'serials' => $this->when($this->product->has_serials && $this->product->has_variations, ProductVariationSerialResource::collection($this->serials))
        ];
    }
}
