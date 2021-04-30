<?php

namespace App\Http\Resources\Web;

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductVariationResource extends JsonResource
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
            'available_quantity' => $this->latestStock->available_quantity ?? 0,
            'requested_quantity' => $this->nonDispatchedRequests->sum('quantity'),
            'price' => $this->price,
            'variation_immutability' => $this->variation_immutability,
            'variation' => $this->variation,
            // 'serials' => $this->when($this->product->has_serials && $this->product->has_variations, str_ends_with(Route::currentRouteName(), 'products') ? ProductVariationSerialResource::collection($this->serials) : $this->serials()->where('has_requisitions', false)->get()->pluck('serial_no')),
            // 'serials' => $this->when($this->product->has_serials && $this->product->has_variations, 
            //     str_ends_with(Route::currentRouteName(), '.products') ? 
            //     ProductVariationSerialResource::collection(
            //         $this->serials()->where('has_requisitions', false)
            //         ->whereHas('variationStock.productStock', function ($query) {
            //             $query->where('has_approved', 1);
            //         })->get()
            //     ) 
            //     : 
            //     $this->serials()->where('has_requisitions', false)->whereHas('variationStock.productStock', function ($query) {
            //         $query->where('has_approved', 1);
            //     })->get()->pluck('serial_no')),
            'serials' => $this->when($this->product->has_serials && $this->product->has_variations, ProductVariationSerialResource::collection($this->serials))
        ];
    }
}
