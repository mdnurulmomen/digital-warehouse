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
        $stock = $this->stock;
        $product = $this->merchantProduct->product;

        return [
            'id' => $this->id,
            'stock_code' => $this->stock_code,
            'stock_quantity' => $this->stock_quantity ?? 0,
            'available_quantity' => /* $this->when(! $product->has_variations, $this->available_quantity ?? 0) */ $this->available_quantity ?? 0,
            'unit_buying_price' => $this->when(! $product->has_variations, $this->unit_buying_price ?? 0.0),
            'manufactured_at' => $this->when(! $product->has_variations, $this->manufactured_at),
            'expired_at' => $this->when(! $product->has_variations, $this->expired_at),
            'merchant_product_id' => $this->merchant_product_id,
            // 'name' => $this->product->name,
            'has_serials' => $product->has_serials,
            'has_variations' => $product->has_variations,
            'has_approval' => $stock->has_approval,
            // 'quantity_type' => $this->product->quantity_type,
            'serials' => $this->when($product->has_serials && ! $product->has_variations, ProductSerialResource::collection($this->serials)),
            'variations' => $this->when($product->has_variations, ProductVariationStockResource::collection($this->variations->loadMissing('merchantProductVariation.productVariation.variation'))),
            'invoice_no' => $stock->invoice_no,
            'warehouse' => (new MerchantWarehouseResource($stock->warehouse))->customResource($this->merchantProduct->merchant_id),
            'addresses' => $this->when($this->relationLoaded('addresses'), new ProductAddressCollection($this->addresses)),
            'keeper' => $stock->keeper,
            'approver' => $this->when($stock->has_approval, $stock->approver),    // -1 / 1
            'created_at' => $stock->created_at->format('Y.m.d H:i:s'),
            'updated_at' => $stock->updated_at->format('Y.m.d H:i:s'),
        ];
    }
}
