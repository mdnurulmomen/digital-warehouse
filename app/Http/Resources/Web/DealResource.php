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
            'name' => $this->name,
            'active' => $this->active,
            'e_commerce_fulfillment' => $this->e_commerce_fulfillment,
            'auto_renewal' => $this->auto_renewal,
            'sale_percentage' => $this->sale_percentage,
            'rent_period_id' => $this->rent_period_id,
            'merchant_id' => $this->merchant_id,
            'created_at' => $this->created_at->format('Y-M-d H:i:s'),
            'rent_period' => $this->rentPeriod,
            'warehouses' => DealtWarehouseResource::collection(
                Warehouse::whereHas('containers', function ($query) {
                    $query->whereHas('deals', function ($query1) {
                        $query1->where('merchant_deal_id', $this->id);
                    });
                })
                ->with(['containers.deals' => function ($query) {
                    $query->where('merchant_deal_id', $this->id);
                }])
                ->get()
            ),
            'instalments' => DealInstalmentResource::collection($this->instalments)
        ];
    }
}
