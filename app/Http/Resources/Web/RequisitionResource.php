<?php

namespace App\Http\Resources\Web;

use Illuminate\Http\Resources\Json\JsonResource;

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
            'cancellation_reason' =>  $this->when($this->cancellation, $this->cancellation ? $this->cancellation->reason : NULL),
            'updater' =>  $this->when($this->status, $this->updater),
            'merchant_id' => $this->merchant_id,
            'created_at' => $this->created_at->diffForHumans(),
            'updated_at' => $this->updated_at->diffForHumans(),
            'products' => RequiredProductResource::collection($this->products),
            'delivery' =>  $this->when($this->delivery, $this->delivery),
            'agent' => $this->when($this->agent, $this->agent),
            'dispatch' => $this->when($this->status==1, new RequisitionDispatchResource($this->dispatch)),
        ];
    }
}
