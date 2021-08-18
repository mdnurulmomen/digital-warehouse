<?php

namespace App\Http\Resources\Web;

use App\Models\Warehouse;
use Illuminate\Http\Resources\Json\JsonResource;

class DealResource extends JsonResource
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
            'active' => $this->active,
            'e_commerce_fulfillment' => $this->e_commerce_fulfillment,
            'auto_renewal' => $this->auto_renewal,
            'sale_percentage' => $this->sale_percentage,
            'merchant_id' => $this->merchant_id,
            'created_at' => $this->created_at,
            'warehouses' => DealtWarehouseResource::collection(
                Warehouse::whereHas('deals', function ($query) {
                    $query->where('merchant_deal_id', $this->id);
                })
                ->with(['deals' => function ($query) {
                    $query->where('merchant_deal_id', $this->id);
                }])
                ->get()
            ),
            'payments' => DealPaymentResource::collection($this->payments)
        ];
    }
}
