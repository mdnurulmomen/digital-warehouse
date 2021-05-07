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
            'name' => $this->name,
            'description' => $this->description,
            'sku' => $this->sku,
            'price' => $this->price,
            'available_quantity' => $this->latestStock->available_quantity ?? 0,
            'requested_quantity' => $this->nonDispatchedRequests->sum('quantity'),
            'quantity_type' => $this->quantity_type,
            'has_serials' => $this->has_serials,
            'has_variations' => $this->has_variations,
            // 'product_immutability' => $this->product_immutability,
            'product_category_id' => $this->product_category_id,
            'merchant_id' => $this->merchant_id,
            'category' => $this->category,
            'merchant' => $this->merchant,
            'serials' => $this->when($this->has_serials && ! $this->has_variations, $this->serials()->where('has_requisitions', false)->where('has_dispatched', false)->whereHas('stock', function ($query) {
                    $query->where('has_approved', 1);
                })->get()->pluck('serial_no')),
            // 'variation_type' => $this->when($this->has_variations, $this->variations->count() ? $this->variations()->first()->variation->variationType->loadMissing('variations') : 'NA'),
            'variations' => $this->when($this->has_variations, MyProductVariationResource::collection($this->variations->loadMissing('variation'))),
        ];
    }
}
