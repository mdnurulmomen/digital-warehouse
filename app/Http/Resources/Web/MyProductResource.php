<?php

namespace App\Http\Resources\Web;

use Illuminate\Http\Resources\Json\JsonResource;

class MyProductResource extends JsonResource
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
            'name' => $this->product->name,
            'sku' => $this->sku,
            'preview' => $this->preview,
            'manufacturer_id' => $this->manufacturer_id,
            'manufacturer' => $this->manufacturer,
            'description' => $this->description,
            'warning_quantity' => $this->warning_quantity,
            'selling_price' => $this->selling_price,
            'available_quantity' => $this->latestStock->available_quantity ?? 0,
            'requested_quantity' => $this->nonDispatchedRequests->sum('quantity'),
            'dispatched_quantity' => $this->dispatchedRequests->sum('quantity'),
            'quantity_type' => $this->product->quantity_type,
            'has_serials' => $this->product->has_serials,
            'has_variations' => $this->product->has_variations,
            // 'product_immutability' => $this->product_immutability,
            'product_category_id' => $this->product->product_category_id,
            'category' => $this->product->category,
            'merchant_id' => $this->merchant_id,
            'merchant' => $this->merchant,

            'serials' => $this->when($this->product->has_serials && ! $this->product->has_variations, $this->serials()->where('has_requisitions', false)->where('has_dispatched', false)->whereHas('productStock.stock', function ($query) {
                    $query->where('has_approval', 1);
                })->get()->pluck('serial_no')),

            'dispatched_serials' => $this->when($this->product->has_serials && ! $this->product->has_variations, $this->serials()->where('has_requisitions', true)->where('has_dispatched', true)->whereHas('productStock.stock', function ($query) {
                    $query->where('has_approval', 1);
                })->get()->pluck('serial_no')),

            // 'variation_type' => $this->when($this->has_variations, $this->variations->count() ? $this->variations()->first()->variation->variationType->loadMissing('variations') : 'NA'),
            
            'variations' => $this->when($this->product->has_variations, MyProductVariationResource::collection($this->variations->loadMissing('productVariation.variation'))),
        ];
    }
}
