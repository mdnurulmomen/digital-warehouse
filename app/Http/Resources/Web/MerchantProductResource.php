<?php

namespace App\Http\Resources\Web;

use App\Models\ProductStock;
use Illuminate\Http\Resources\Json\JsonResource;

class MerchantProductResource extends JsonResource
{
    private static $dateFrom;

    public static function customCollection($resource, $value): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        self::$dateFrom = $value;
        return parent::collection($resource);
    }

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
            'sku' => $this->when($this->sku, $this->sku),
            'upc' => $this->when($this->upc, $this->upc),
            'preview' => $this->preview,
            'manufacturer_id' => $this->when($this->manufacturer_id, $this->manufacturer_id),
            'manufacturer' => $this->when($this->manufacturer_id, $this->manufacturer),
            'description' => $this->description,
            'warning_quantity' => $this->warning_quantity,
            'selling_price' => $this->selling_price,
            'discount' => $this->discount,
            'product_id' => $this->product_id,
            'merchant_id' => $this->merchant_id,
            'created_at' => $this->created_at,
            'merchant' => $this->whenLoaded('merchant'),
            'product' => new ProductResource($product),
            'has_serials' => $product->has_serials,
            'has_variations' => $product->has_variations,

            'previous_quantity' => isset(self::$dateFrom) ? (int) ProductStock::where('merchant_product_id', $this->id)
            ->whereHas('stock', function ($query) {
                $query->where('has_approval', 1)
                ->whereDate('created_at', '<', self::$dateFrom);
            })
            ->sum('available_quantity') : 0,

            'available_quantity' => /*$this->when($this->relationLoaded('latestStock'), $this->latestStock->available_quantity ?? 0)*/ $this->relationLoaded('stocks') ? $this->stocks->sum('available_quantity') : $this->available_quantity ?? 0,
            
            'requested_quantity' => $this->when($this->relationLoaded('nonDispatchedRequests'), $this->nonDispatchedRequests->sum('quantity')),
            
            'dispatched_quantity' => $this->when($this->relationLoaded('dispatchedRequests'), $this->dispatchedRequests->sum('quantity')),

            'unit_max_price' => $this->when($this->relationLoaded('stocks'), $product->has_variations ? $this->variations->pluck('stocks')->collapse()->pluck('unit_buying_price')->max() : $this->stocks->pluck('unit_buying_price')->max()),

            'unit_min_price' => $this->when($this->relationLoaded('stocks'), $product->has_variations ? $this->variations->pluck('stocks')->collapse()->pluck('unit_buying_price')->min() : $this->stocks->pluck('unit_buying_price')->min()),

            'unit_avg_price' => $this->when($this->relationLoaded('stocks'), $product->has_variations ? $this->variations->pluck('stocks')->collapse()->pluck('unit_buying_price')->avg() : $this->stocks->pluck('unit_buying_price')->avg()),

            'stock_total_cost' => $product->has_variations ? $this->variations->sum(function ($productVariation) {
                return $productVariation->stocks->sum(function ($productVariationStock) {
                    return $productVariationStock->stock_quantity * $productVariationStock->unit_buying_price;
                });
            }) : $this->stocks->sum(function ($productStock) {
                return $productStock->stock_quantity * $productStock->unit_buying_price;
            }),

            'variations' => $this->when($product->has_variations, MerchantProductVariationResource::customCollection($this->variations, self::$dateFrom)),

            'product_immutability' => $this->product_immutability,
            'serials' => $this->when($product->has_serials && ! $product->has_variations, ProductSerialResource::collection($this->serials)),
            'addresses' => new ProductAddressCollection($this->whenLoaded('addresses')),
            // 'product' => $product,
            // 'requests' => $this->requests,
            // 'stocks' => $this->stocks,
        ];
    }
}
