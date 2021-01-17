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
            'initial_quantity' => $this->initial_quantity,
            'available_quantity' => $this->available_quantity,
            'requested_quantity' => $this->nonDispatchedRequests->sum('quantity'),
            'price' => $this->price,
            'variation' => $this->variation,
        ];
    }
}
