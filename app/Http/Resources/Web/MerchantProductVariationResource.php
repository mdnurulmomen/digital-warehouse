<?php

namespace App\Http\Resources\Web;

use App\Models\ProductVariationStock;
use Illuminate\Http\Resources\Json\JsonResource;

class MerchantProductVariationResource extends JsonResource
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
        return [
            'id' => $this->id,
            'sku' => $this->sku,
            'preview' => $this->preview,
            'selling_price' => $this->selling_price,
            'product_variation_id' => $this->product_variation_id,
            'merchant_product_id' => $this->merchant_product_id,
            'variation_immutability' => $this->variation_immutability,
            
            'previous_quantity' => isset(self::$dateFrom) ? (int) ProductVariationStock::where('merchant_product_variation_id', $this->id)
            ->whereHas('productStock', function ($query) {
                $query->whereHas('stock', function ($q) {
                    $q->where('has_approval', 1)
                    ->whereDate('created_at', '<', self::$dateFrom);
                });
            })
            ->sum('available_quantity') : 0,

            'stock_total_cost' => $this->stocks->sum(function ($productVariationStock) {
                return $productVariationStock->stock_quantity * $productVariationStock->unit_buying_price;
            }),

            'available_quantity' => /*$this->when($this->relationLoaded('latestStock'), $this->latestStock->available_quantity ?? 0)*/ $this->relationLoaded('stocks') ? $this->stocks->sum('available_quantity') : $this->available_quantity ?? 0,
            'requested_quantity' => $this->when($this->relationLoaded('nonDispatchedRequests'), $this->nonDispatchedRequests->sum('quantity')),
            'variation' => $this->productVariation->variation, 
            'serials' => $this->when($this->merchantProduct->product->has_serials && $this->merchantProduct->product->has_variations, ProductVariationSerialResource::collection($this->serials))
        ];
    }
}
