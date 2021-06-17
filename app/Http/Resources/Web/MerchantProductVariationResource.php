<?php

namespace App\Http\Resources\Web;

use Illuminate\Http\Resources\Json\JsonResource;

class MerchantProductVariationResource extends JsonResource
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
            'sku' => $this->sku,
            'preview' => $this->preview,
            'selling_price' => $this->selling_price,
            'variation_immutability' => $this->variation_immutability,
            'variation' => $this->productVariation->variation,
            'serials' => $this->when($this->merchantProduct->product->has_serials && $this->merchantProduct->product->has_variations, ProductVariationSerialResource::collection($this->serials))
        ];
    }
}
