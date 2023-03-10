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
        $product = $this->productStock->merchantProduct->product;

        return [
            'id' => $this->id,
            'stock_code' => $this->stock_code,
            'stock_quantity' => $this->stock_quantity ?? 0,
            'available_quantity' => $this->available_quantity ?? 0,
            'unit_buying_price' => $this->unit_buying_price ?? 0.0,
            'manufactured_at' => $this->manufactured_at,
            'expired_at' => $this->expired_at,
            'has_serials' => $product->has_serials,
            'merchant_product_variation_id' => $this->merchant_product_variation_id,
            'variation' => $this->merchantProductVariation->productVariation->variation,
            'serials' => $this->when($product->has_serials, ProductVariationSerialResource::collection($this->serials)),
        ];
    }
}
