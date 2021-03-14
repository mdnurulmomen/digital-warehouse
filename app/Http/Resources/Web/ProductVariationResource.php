<?php

namespace App\Http\Resources\Web;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductVariationResource extends JsonResource
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
            'price' => $this->price,
            'variation_immutability' => $this->variation_immutability,
            'variation' => $this->variation,
        ];
    }
}
