<?php

namespace App\Http\Resources\Web;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductStockResource extends JsonResource
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
            // 'name' => $this->product->name,
            'stock_quantity' => $this->stock_quantity ?? 0,
            'available_quantity' => $this->available_quantity ?? 0,
            'has_serials' => $this->has_serials,
            'has_variations' => $this->has_variations,
            // 'quantity_type' => $this->product->quantity_type,
            'serials' => $this->when($this->has_serials, $this->serials->pluck('serial_no')),
            'variations' => $this->when($this->has_variations, ProductVariationStockResource::collection($this->variations->loadMissing('productVariation.variation'))),
            'addresses' => new ProductAddressCollection($this->addresses),
            'created_at' => $this->created_at->diffForHumans(),
        ];
    }
}
