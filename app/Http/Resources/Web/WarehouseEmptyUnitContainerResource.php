<?php

namespace App\Http\Resources\Web;

use Illuminate\Http\Resources\Json\JsonResource;

class WarehouseEmptyUnitContainerResource extends JsonResource
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
            'container_shelf_statuses' => $this->containerShelfStatuses()->where('engaged', 0.0)->orWhere('engaged', 0.5)->with('containerShelfUnitStatuses')->get(),
            'rents' => RentResource::collection($this->warehouseContainer->unit->rents->load('rentPeriod')),
        ];
    }
}
