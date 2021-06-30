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
            // 'warehouse' => $this->warehouse,
            'available_quantity' => $this->available_quantity ?? 0,
            'has_serials' => $this->merchantProduct->product->has_serials,
            'has_variations' => $this->merchantProduct->product->has_variations,
            'has_approval' => $this->stock->has_approval,
            // 'quantity_type' => $this->product->quantity_type,
            'serials' => $this->when($this->merchantProduct->product->has_serials && ! $this->merchantProduct->product->has_variations, ProductSerialResource::collection($this->serials)),
            'variations' => $this->when($this->merchantProduct->product->has_variations, ProductVariationStockResource::collection($this->variations->loadMissing('productVariation.variation'))),
            'addresses' => new ProductAddressCollection($this->addresses),
            'keeper' => $this->stock->keeper,
            'approver' => $this->when($this->stock->has_approval, $this->stock->approver),    // -1 / 1
            // 'created_at' => $this->created_at->diffForHumans(),
            // 'updated_at' => $this->updated_at->diffForHumans(),
        ];
    }
}
