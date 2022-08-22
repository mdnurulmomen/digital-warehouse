<?php

namespace App\Http\Resources\Web;

use Illuminate\Http\Resources\Json\JsonResource;

class MerchantPaymentResource extends JsonResource
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
            'invoice_no' => $this->invoice_no,
            'paid_amount' => $this->paid_amount,
            'current_due' => $this->current_due,
            'paid_at' => $this->paid_at->format('Y-m-d H:i:s'),
            'merchant_rent_id' => $this->merchant_rent_id,
            'rent' => $this->rent,
            'issuer' => $this->issuer,
            'updater' => $this->updater,
        ];
    }
}
