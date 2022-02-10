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
        $product = $this->product;

        return [
            'id' => $this->id,
            'name' => $product->name,
            'sku' => $this->sku,
            'preview' => $this->preview,
            'manufacturer_id' => $this->when($this->manufacturer_id, $this->manufacturer_id),
            'manufacturer' => $this->when($this->manufacturer_id, $this->manufacturer),
            'description' => $this->description,
            'warning_quantity' => $this->warning_quantity,
            // 'selling_price' => $this->selling_price,
            'discount' => $this->discount,
            'product_id' => $this->product_id,
            'merchant_id' => $this->merchant_id,
            'created_at' => $this->created_at,
            'merchant' => $this->whenLoaded('merchant'),
            'product' => new ProductResource($product),
            'has_serials' => $product->has_serials,
            'has_variations' => $product->has_variations,
            'available_quantity' => $this->when($this->relationLoaded('latestStock'), $this->latestStock->available_quantity ?? 0),
            'requested_quantity' => $this->when($this->relationLoaded('nonDispatchedRequests'), $this->nonDispatchedRequests->sum('quantity')),
            'dispatched_quantity' => $this->when($this->relationLoaded('dispatchedRequests'), $this->dispatchedRequests->sum('quantity')),
            'variations' => $this->when($product->has_variations, MerchantProductVariationResource::collection($this->variations)),
            'product_immutability' => $this->product_immutability,
            'serials' => $this->when($product->has_serials && ! $product->has_variations, ProductSerialResource::collection($this->serials)),
            'addresses' => new ProductAddressCollection($this->whenLoaded('addresses')),
            // 'product' => $product,
            // 'requests' => $this->requests,
            // 'stocks' => $this->stocks,
        ];
    }
}
