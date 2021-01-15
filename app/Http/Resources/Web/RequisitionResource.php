<?php

namespace App\Http\Resources\Web;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Web\RequiredProductResource;

class RequisitionResource extends JsonResource
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
            'subject' => $this->subject,
            'description' => $this->description,
            'status' => $this->status,
            'merchant_id' => $this->merchant_id,
            'created_at' => $this->created_at,
            'products' => RequiredProductResource::collection($this->products),
            'delivery' =>  $this->when($this->delivery, $this->delivery),
            'agent' => $this->when($this->agent, $this->agent),
        ];
    }
}
