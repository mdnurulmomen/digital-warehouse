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
            'created_at' => $this->created_at->diffForHumans(),
            // 'quantity_type' => $this->product->quantity_type,
            // 'has_variations' => $this->product->has_variations,
            'variations' => $this->when($this->product->has_variations, ProductVariationStockResource::collection($this->variations->loadMissing('productVariation.variation'))),
            'addresses' => new ProductAddressCollection($this->addresses),
        ];
    }
}
