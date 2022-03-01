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
        $merchantProductVariation = $this->merchantProductVariation;
        $requiredProduct = $this->requiredProduct;
        $product = $requiredProduct->merchantProduct->product;

        return [
            'id' => $this->id,
            'merchant_product_variation_id' => $this->merchant_product_variation_id,
            'variation_name' => $merchantProductVariation->productVariation->variation->name,
            'quantity' => $this->quantity,
            'has_serials' => $product->has_serials,
            'serials' => $this->when($product->has_serials, $this->serials->loadMissing('serial')),
            'available_quantity' => /*$merchantProductVariation->latestStock->available_quantity*/ $merchantProductVariation->available_quantity,
        ];
    }
}
