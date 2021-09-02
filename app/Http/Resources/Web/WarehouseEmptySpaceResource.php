<?php

namespace App\Http\Resources\Web;

use Illuminate\Http\Resources\Json\JsonResource;

class WarehouseEmptySpaceResource extends JsonResource
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
            
            'emptyContainers' => WarehouseEmptyContainerResource::collection(
                
                $this->containerStatuses()->where('engaged', 0.0)->get()
            
            ),

            'emptyShelfContainers' => WarehouseEmptyShelfContainerResource::collection(
                
                $this->containerStatuses()->whereHas('containerShelfStatuses', function ($query) {
                    $query->where('warehouse_container_shelf_statuses.engaged', 0.0);
                })
                ->with([
                    'containerShelfStatuses' => 
                        function ($query) {
                            $query->where('engaged', 0.0);
                        }
                ])
                ->get()

            ),

            'emptyUnitContainers' => WarehouseEmptyUnitContainerResource::collection(
                $this->containerStatuses()->whereHas('containerShelfUnitStatuses', function ($query) {
                    $query->where('warehouse_container_shelf_unit_statuses.engaged', 0.0);
                })
                ->with([
                    'containerShelfStatuses' => 
                        function ($query) {
                            $query->where('engaged', 0.0)
                                    ->orWhere('engaged', 0.5);
                        },
    
                    'containerShelfStatuses.containerShelfUnitStatuses' => 
                        function ($query) {
                            $query->where('engaged', 0.0);
                        },
                ])
                ->get()
            )

        ];
    }
}
