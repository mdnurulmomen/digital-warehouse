<?php

namespace App\Http\Resources\Web;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Web\WarehouseRentResource;

class WarehouseContainerResource extends JsonResource
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
            'quantity' => $this->quantity,
            'container_id' => $this->container_id,
            'engaged_quantity' => $this->containerStatuses()->where('engaged', 1)->count(),
            'partially_engaged' => $this->containerStatuses()->where('engaged', 0.5)->count(),
            'container' => $this->container->loadMissing('shelf.unit'),
            'rents' => new WarehouseRentResource($this),
        ];
    }
}
