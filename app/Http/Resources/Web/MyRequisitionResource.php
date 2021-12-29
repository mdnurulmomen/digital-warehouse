<?php

namespace App\Http\Resources\Web;

use Illuminate\Http\Resources\Json\JsonResource;

class MyRequisitionResource extends JsonResource
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
            'creator' => $this->creator,
            'status' => $this->status,
            'updater' =>  $this->when($this->status, $this->updater),
            'merchant_id' => $this->merchant_id,
            'created_at' => $this->created_at->diffForHumans(),
            'products' => MyRequiredProductResource::collection($this->products),
            'delivery' =>  $this->when($this->delivery, $this->delivery),
            'agent' => $this->when($this->agent, $this->agent),
            'dispatch' => $this->when($this->status, new RequisitionDispatchResource($this->dispatch)),
            'cancellation_reason' => $this->when($this->status==-1, $this->cancellation)
        ];
    }
}
