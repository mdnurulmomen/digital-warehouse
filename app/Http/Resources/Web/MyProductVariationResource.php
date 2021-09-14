<?php

namespace App\Http\Resources\Web;

use Illuminate\Http\Resources\Json\JsonResource;

class MyProductVariationResource extends JsonResource
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
            'selling_price' => $this->selling_price,
            'available_quantity' => $this->latestStock->available_quantity ?? 0,
            'requested_quantity' => $this->nonDispatchedRequests->sum('quantity'),
            'dispatched_quantity' => $this->dispatchedRequests->sum('quantity'),
            // 'variation_immutability' => $this->variation_immutability,
            'variation' => $this->productVariation->variation,

            'serials' => $this->when($this->product->has_serials && $this->product->has_variations, $this->serials()->where('has_requisitions', false)->where('has_dispatched', false)->whereHas('variationStock.productStock.stock', function ($query) {
                    $query->where('has_approval', 1);
                })->get()->pluck('serial_no')),

            'dispatched_serials' => $this->when($this->product->has_serials && $this->product->has_variations, $this->serials()->where('has_requisitions', true)->where('has_dispatched', true)->whereHas('variationStock.productStock.stock', function ($query) {
                    $query->where('has_approval', 1);
                })->get()->pluck('serial_no'))
        ];
    }
}
