<?php

namespace App\Http\Resources\Web;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductVariationSerialResource extends JsonResource
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
            'serial_no' => $this->serial_no,
            'has_requisitions' => $this->has_requisitions,
            'has_dispatched' => $this->has_dispatched,
            'has_approval' => $this->variationStock->productStock->stock->has_approval,
        ];
    }
}
