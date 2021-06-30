<?php

namespace App\Http\Resources\Web;

use Illuminate\Http\Resources\Json\JsonResource;

class WarehouseEmptyContainerResource extends JsonResource
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
            'name' => $this->name,
            'engaged' => $this->engaged,
            'warehouse_container_id' => $this->warehouse_container_id,
            'rents' => RentResource::collection($this->warehouseContainer->rents->loadMissing('rentPeriod'))
        ];
    }
}
