<?php

namespace App\Http\Resources\Web;

use App\Models\WarehouseContainerStatus;
use App\Models\WarehouseContainerShelfStatus;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\WarehouseContainerShelfUnitStatus;

class ManagerProductResource extends JsonResource
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
            'dispatched_quantity' => $this->dispatchedRequests->sum('quantity'),
            'quantity_type' => $this->quantity_type,
            'has_serials' => $this->has_serials,
            'has_variations' => $this->has_variations,
            'product_immutability' => $this->product_immutability,
            'product_category_id' => $this->product_category_id,
            'merchant_id' => $this->merchant_id,
            'category' => $this->category,
            'merchant' => $this->merchant,
            'serials' => $this->when($this->has_serials && ! $this->has_variations, ProductSerialResource::collection($this->serials)),
            'variation_type' => $this->when($this->has_variations, $this->variations->count() ? $this->variations()->first()->variation->variationType->loadMissing('variations') : 'NA'),
            'variations' => $this->when($this->has_variations, ProductVariationResource::collection($this->variations->loadMissing('variation'))),
            'addresses' => new ProductAddressCollection($this->addresses()->whereHasMorph(
                            'space',
                            [WarehouseContainerStatus::class, WarehouseContainerShelfStatus::class, WarehouseContainerShelfUnitStatus::class],
                            function ($query1) {
                                $query1->whereHas('warehouseContainer', function ($query2) {
                                    $query2->whereHas('warehouse', function ($query3) {
                                        $query3->whereHas('managers', function ($query4) {
                                            $query4->where('manager_id', \Auth::guard('manager')->user()->id);
                                        });
                                    });
                                });
                            }
                        )->get()),
            // 'addresses' => $this->when(Auth::user()->isAdmin(), new ProductAddressCollection($this->addresses)),
        ];
    }
}
