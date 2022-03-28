<?php

namespace App\Http\Resources\Web;

use Illuminate\Http\Resources\Json\JsonResource;

class StockProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // return parent::toArray($request);

        $product = $this->merchantProduct->product;

        return [
            'id' => $this->id,
            'stock_code' => $this->stock_code,
            'stock_quantity' => $this->stock_quantity ?? 0,
            'available_quantity' => $this->when(! $product->has_variations, $this->available_quantity ?? 0),
            'unit_buying_price' => $this->when(! $product->has_variations, $this->unit_buying_price ?? 0.0),
            'manufactured_at' => $this->when(! $product->has_variations, $this->manufactured_at),
            'expired_at' => $this->when(! $product->has_variations, $this->expired_at),
            'merchant_product_id' => $this->merchant_product_id,
            'has_serials' => $product->has_serials,
            'has_variations' => $product->has_variations,
            'quantity_type' => $product->quantity_type,
            'serials' => $this->when($product->has_serials && ! $product->has_variations, ProductSerialResource::collection($this->serials)),
            'variations' => $this->when($product->has_variations, ProductVariationStockResource::collection($this->variations->loadMissing('merchantProductVariation.productVariation.variation'))),
            'addresses' => new ProductAddressCollection($this->addresses),
            'merchant_product' => $this->merchantProduct->loadMissing(['product.category', 'manufacturer']),
        ];
    }
}
