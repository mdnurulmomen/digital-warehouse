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
            'merchant_product_variation_id' => $this->merchant_product_variation_id,
            'has_serials' => $this->productStock->merchantProduct->product->has_serials,
            // 'variation' => $this->merchantProductVariation->productVariation->variation,
            'variation' => $this->merchantProductVariation->productVariation->variation->variationParent ? (new MerchantProductParentVariationResource($this->merchantProductVariation->productVariation->variation->variationParent))->subVariation($this->merchantProductVariation->productVariation->variation) : new MerchantProductParentVariationResource($this->merchantProductVariation->productVariation->variation),
            'serials' => $this->when($this->productStock->merchantProduct->product->has_serials, ProductVariationSerialResource::collection($this->serials)),
        ];
    }
}
