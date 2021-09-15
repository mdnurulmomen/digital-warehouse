<?php

namespace App\Http\Resources\Web;

use Illuminate\Http\Resources\Json\JsonResource;

class MerchantProductResource extends JsonResource
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
            'sku' => $this->sku,
            'preview' => $this->preview,
            'description' => $this->description,
            'manufacturer_id' => $this->manufacturer_id,
            'manufacturer' => $this->manufacturer,
            'warning_quantity' => $this->warning_quantity,
            'selling_price' => $this->selling_price,
            'discount' => $this->discount,
            'product_id' => $this->product_id,
            'merchant_id' => $this->merchant_id,
            'created_at' => $this->created_at,
            'merchant' => $this->merchant,
            'product' => new ProductResource($this->product),
            'has_serials' => $this->product->has_serials,
            'has_variations' => $this->product->has_variations,
            'available_quantity' => $this->latestStock->available_quantity ?? 0,
            'requested_quantity' => $this->nonDispatchedRequests->sum('quantity'),
            'dispatched_quantity' => $this->dispatchedRequests->sum('quantity'),
            'variations' => $this->when($this->product->has_variations, MerchantProductVariationResource::collection($this->variations)),
            'product_immutability' => $this->product_immutability,
            'serials' => $this->when($this->product->has_serials && ! $this->product->has_variations, ProductSerialResource::collection($this->serials)),
            'addresses' => new ProductAddressCollection($this->addresses),
            // 'product' => $this->product,
            // 'requests' => $this->requests,
            // 'stocks' => $this->stocks,
        ];
    }
}
