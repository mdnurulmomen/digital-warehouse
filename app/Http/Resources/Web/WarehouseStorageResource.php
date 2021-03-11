<?php

namespace App\Http\Resources\Web;

use Illuminate\Http\Resources\Json\JsonResource;

class WarehouseStorageResource extends JsonResource
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
            'feature' => $this->feature,
            'previews' => $this->previews,
            'storage_type' => $this->storageType,
            'deleted_at' => !empty($this->deleted_at) ? $this->deleted_at->diffForHumans() : NULL
        ];
    }
}
