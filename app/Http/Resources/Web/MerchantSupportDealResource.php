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
            'name' => $this->name,
            'sale_percentage' => $this->sale_percentage,
            'e_commerce_fulfillment_support' => $this->e_commerce_fulfillment_support,
            'e_commerce_fulfillment_charge' => $this->e_commerce_fulfillment_charge,
            'purchase_support' => $this->purchase_support,
            'purchase_support_charge' => $this->purchase_support_charge,
            'pos_support' => $this->pos_support,
            'pos_support_charge' => $this->pos_support_charge,
            'number_outlets' => $this->number_outlets,
            'rent_period_id' => $this->rent_period_id,
            'rent_period' => $this->rentPeriod,
            'created_at' => $this->created_at
        ];
    }
}
