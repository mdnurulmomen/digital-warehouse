<?php

namespace App\Http\Resources\Web;

use Illuminate\Http\Resources\Json\JsonResource;

class MerchantSupportRentResource extends JsonResource
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
            'e_commerce_fulfillment_charge' => $this->e_commerce_fulfillment_charge,
            'purchase_support_charge' => $this->purchase_support_charge,
            'pos_support_charge' => $this->pos_support_charge,
            'merchant_rent_id' => $this->merchant_rent_id
        ];
    }
}
