<?php

namespace App\Http\Resources\Web;

use App\Models\WarehouseContainerStatus;
use App\Models\WarehouseContainerShelfStatus;
use Illuminate\Http\Resources\Json\JsonResource;

class DealtSpaceResource extends JsonResource
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
            'type' => get_class($this->resource),
            // 'engaged' => $this->engaged,
            // 'warehouse_container_id' => $this->warehouse_container_id,
        ];
    }
}
