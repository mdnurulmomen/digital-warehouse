<?php

namespace App\Http\Resources\Web;

use Illuminate\Http\Resources\Json\JsonResource;

class RequiredProductVariationResource extends JsonResource
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
            'merchant_product_variation_id' => $this->merchant_product_variation_id,
            'variation_name' => $this->merchantProductVariation->productVariation->variation->name,
            'quantity' => $this->quantity,
            'has_serials' => $this->requiredProduct->merchantProduct->product->has_serials,
            'serials' => $this->when($this->requiredProduct->merchantProduct->product->has_serials, $this->serials->loadMissing('serial')),
            'available_quantity' => $this->merchantProductVariation->latestStock->available_quantity,
        ];
    }
}
