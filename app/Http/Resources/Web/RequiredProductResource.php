<?php

namespace App\Http\Resources\Web;

use Illuminate\Http\Resources\Json\JsonResource;

class RequiredProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $merchantProduct = $this->merchantProduct;
        $product = $this->merchantProduct->product;

        return [
            'id' => $this->id,
            'merchant_product_id' => $this->merchant_product_id,
            'product_name' => $product->name,
            'quantity' => $this->quantity,
            'quantity_type' => $product->quantity_type,
            'available_quantity' => /*$merchantProduct->latestStock->available_quantity*/ $merchantProduct->available_quantity,
            'has_variations' => $product->has_variations,
            'has_serials' => $product->has_serials,
            'available_stocks' => $this->when($merchantProduct->relationLoaded('stocks'), $merchantProduct->stocks),
            'packaging_service' => $this->packaging_service,
            // 'selected_stock_code' => $this->when($this->selected_stock_code, $this->selected_stock_code),
            'selected_stocks' => $this->when($this->stocks->count(), $this->stocks), 
            'preferred_package' => $this->when($this->packaging_service, $this->preferredPackage ? new PackagingPackageResource($this->preferredPackage->loadMissing('package')) : NULL),
            'dispatched_package' => $this->when($this->packaging_service, $this->dispatchedPackage ? new PackagingPackageResource($this->dispatchedPackage->loadMissing('package')) : NULL),
            'serials' => $this->when($product->has_serials && ! $product->has_variations, $this->serials->loadMissing('serial')),
            'variations' => $this->when($product->has_variations, RequiredProductVariationResource::collection($this->variations)),
            'addresses' => new ProductAddressCollection($merchantProduct->addresses), 
            'requisition_id' => $this->requisition_id,
        ];
    }
}
