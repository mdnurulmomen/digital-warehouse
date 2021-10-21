<?php

namespace App\Http\Resources\Web;

use Illuminate\Http\Resources\Json\JsonResource;

class DealPaymentDetailResource extends JsonResource
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
            // 'issued_from' => $this->issued_from,
            // 'expired_at' => $this->expired_at,
            'rent' => $this->rent,
            // 'number_installment' => $this->number_installment,
            'dealt_space_id' => $this->dealt_space_id,
            'dealt_space' => new DealtSpaceResource($this->space->space)
        ];
    }
}
