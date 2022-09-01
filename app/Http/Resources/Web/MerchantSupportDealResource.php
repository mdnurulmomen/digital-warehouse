<?php

namespace App\Http\Resources\Web;

use Illuminate\Http\Resources\Json\JsonResource;

class MerchantSupportDealResource extends JsonResource
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
            'sale_percentage' => $this->sale_percentage,
            'e_commerce_fulfillment_support' => $this->rents()->whereDate('date_to', '>=', now())->count() ? $this->e_commerce_fulfillment_support : false,
            'e_commerce_fulfillment_charge' => $this->e_commerce_fulfillment_charge,
            'purchase_support' => $this->rents()->whereDate('date_to', '>=', now())->count() ? $this->purchase_support : false,
            'purchase_support_charge' => $this->purchase_support_charge,
            'pos_support' => $this->rents()->whereDate('date_to', '>=', now())->count() ? $this->pos_support : false,
            'pos_support_charge' => $this->pos_support_charge,
            'number_outlets' => $this->number_outlets,
            'rent_period_id' => $this->rent_period_id,
            'rent_period' => $this->rentPeriod,
            'created_at' => $this->created_at
        ];
    }
}
