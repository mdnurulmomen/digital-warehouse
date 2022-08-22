<?php

namespace App\Http\Resources\Web;

use Illuminate\Http\Resources\Json\JsonResource;

class MerchantRentResource extends JsonResource
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
            'number_installment' => $this->number_installment,
            'date_from' => $this->date_from->format('Y-m-d'),
            'date_to' => $this->date_to->format('Y-m-d'),
            'total_rent' => $this->total_rent,
            'discount' => $this->discount,
            'net_payable' => $this->net_payable,
            'total_paid_amount' => $this->total_paid_amount,
            'payments' => MerchantPaymentResource::collection($this->payments),
            'spaceRents' => $this->when($this->spaceRents->count(), MerchantSpaceRentResource::collection($this->spaceRents)),
            'deal' => $this->dealable,
            'created_at' => $this->created_at->format('Y-m-d H:i:s')
        ];
    }
}
