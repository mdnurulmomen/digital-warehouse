<?php

namespace App\Http\Resources\Web;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Web\ProductAddressCollection;

class ProductResource extends JsonResource
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

        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'sku' => $this->sku,
            'price' => $this->price,
            'initial_quantity' => $this->initial_quantity,
            'available_quantity' => $this->available_quantity,
            'requested_quantity' => $this->nonDispatchedRequests->sum('quantity'),
            // 'total_requested_quantity' => $this->available_quantity,
            'quantity_type' => $this->quantity_type,
            'has_variations' => $this->has_variations,
            'has_requisitions' => $this->product_requisition,
            'product_category_id' => $this->product_category_id,
            'merchant_id' => $this->merchant_id,
            'category' => $this->category,
            'merchant' => $this->merchant,
            'variation_type' => $this->when($this->has_variations, $this->variations->count() ? $this->variations()->first()->variation->variationType->loadMissing('variations') : 'NA'),
            'variations' => $this->when($this->has_variations, ProductVariationResource::collection($this->variations->loadMissing('variation'))),
            'spaces' => new ProductAddressCollection($this->addresses),
            // 'spaces' => $this->when(Auth::user()->isAdmin(), new ProductAddressCollection($this->addresses)),
        ];
    }
}
