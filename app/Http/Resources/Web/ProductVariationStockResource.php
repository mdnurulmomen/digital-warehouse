<?php

namespace App\Http\Resources\Web;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductVariationStockResource extends JsonResource
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
            'stock_quantity' => $this->stock_quantity ?? 0,
            'available_quantity' => $this->available_quantity ?? 0,
            'has_serials' => $this->has_serials,
            'variation' => $this->productVariation->variation,
            'serials' => $this->when($this->has_serials, ProductVariationSerialResource::collection($this->serials)),
        ];
    }
}
