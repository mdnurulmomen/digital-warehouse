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
            'number_installment' => $this->number_installment,
            'date_from' => $this->date_from->format('Y-m-d H:i:s'),
            'date_to' => $this->date_to->format('Y-m-d H:i:s'),
            'total_rent' => $this->total_rent,
            'discount' => $this->discount,
            'previous_due' => $this->previous_due,
            'net_payable' => $this->net_payable,
            'paid_amount' => $this->paid_amount,
            'current_due' => $this->current_due,
            'rents' => DealPaymentDetailResource::collection($this->rents),
            'merchant_deal_id' => $this->merchant_deal_id,
            'paid_at' => $this->paid_at->format('Y-m-d H:i:s'),
        ];
    }
}
