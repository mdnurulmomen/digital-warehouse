<?php

namespace App\Http\Resources\Web;

use Illuminate\Http\Resources\Json\JsonResource;

class DealPaymentResource extends JsonResource
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
            'previous_due' => $this->previous_due,
            'total_rent' => $this->total_rent,
            'discount' => $this->discount,
            'net_payable' => $this->net_payable,
            'paid_amount' => $this->paid_amount,
            'current_due' => $this->current_due,
            'rents' => DealPaymentDetailResource::collection($this->rents),
            'paid_at' => $this->paid_at
        ];
    }
}
