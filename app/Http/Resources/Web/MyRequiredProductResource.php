<?php

namespace App\Http\Resources\Web;

use Illuminate\Http\Resources\Json\JsonResource;

class MyRequiredProductResource extends JsonResource
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
            'product_id' => $merchantProduct->product_id,
            'product_name' => $product->name,
            'quantity' => $this->quantity,
            'available_quantity' => /*$merchantProduct->latestStock->available_quantity*/ $merchantProduct->available_quantity ?? 0,
            'has_variations' => $this->has_variations,
            'has_serials' => $this->has_serials,
            'packaging_service' => $this->packaging_service,
            'preferred_package' => $this->when($this->packaging_service, $this->preferredPackage ? new PackagingPackageResource($this->preferredPackage->loadMissing('package')) : NULL),
            'dispatched_package' => $this->when($this->packaging_service, $this->dispatchedPackage ? new PackagingPackageResource($this->dispatchedPackage->loadMissing('package')) : NULL),
            'serials' => $this->when($this->has_serials && ! $this->has_variations, $this->serials->loadMissing('serial')->pluck('serial')->pluck('serial_no')),
            'variations' => $this->when($this->has_variations, MyRequiredProductVariationResource::collection($this->variations)),
            'requisition_id' => $this->requisition_id,
            // 'addresses' => new ProductAddressCollection($this->merchantProduct->addresses),
        ];
    }
}
