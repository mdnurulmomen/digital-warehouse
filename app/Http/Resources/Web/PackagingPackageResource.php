<?php

namespace App\Http\Resources\Web;

use Illuminate\Http\Resources\Json\JsonResource;

class PackagingPackageResource extends JsonResource
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
            'id' => $this->package->id,
            'name' => $this->package->name,
            'description' => $this->package->description,
            'preview' => $this->package->preview,
            'quantity' => $this->when($this->quantity, $this->quantity)
        ];
    }
}
