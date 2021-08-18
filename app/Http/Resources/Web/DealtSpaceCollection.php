<?php

namespace App\Http\Resources\Web;

use Illuminate\Http\Resources\Json\ResourceCollection;

class DealtSpaceCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {   
        $dealtSpaces = [];

        $containers = [
            'type' => 'containers',
            'containers' => []
        ];

        $shelves = [
            /*
                'type' => 'shelves',
                'container' => [
                    'shelves' => [],
                    'rents' => [],
                    'selected_rent' => []
                ]
            */
        ];

        $units = [
            /*
                'type' => 'units',
                'container' => [
                    'shelf' => [
                        'units' => []
                    ],
                    'rents' => [],
                    'selected_rent' => []
                ]
            */
        ];       

        foreach ($this->collection as $key => $dealtSpace) {
           
            if (strpos($dealtSpace->space_type, 'ContainerStatus')) {
                
                $warehouseExpectedContainer = $dealtSpace->space;

                $newContainer = [
                    
                    // 'type' => 'containers',
                    
                    'id' => $warehouseExpectedContainer->id,

                    'name' => $warehouseExpectedContainer->name,

                    'warehouse_container_id' => $warehouseExpectedContainer->warehouse_container_id,

                    'engaged' => $dealtSpace->engaged,

                    'warehouse_container' => $warehouseExpectedContainer->warehouseContainer->loadMissing(['container']),

                    'rents' => RentResource::collection($warehouseExpectedContainer->warehouseContainer->rents),

                    'selected_rent' => new RentResource($dealtSpace->rent),

                ];

                // array_push($containers['containers'], $dealtSpace->space->loadMissing(['warehouseContainer.container']));

                array_push($containers['containers'], $newContainer);

            
            }

            else if (strpos($dealtSpace->space_type, 'ShelfStatus')) {
                
                $warehouseExpectedShelf = $dealtSpace->space;
                $warehouseExpectedShelfContainer = $warehouseExpectedShelf->parentContainer;

                $newShelf = [
                    
                    'type' => 'shelves',

                    'container' => [

                        'id' => $warehouseExpectedShelfContainer->id,
                        'name' => $warehouseExpectedShelfContainer->name,
                        'warehouse_container_id' => $warehouseExpectedShelfContainer->warehouse_container_id,
                        'warehouse_container' => $warehouseExpectedShelfContainer->warehouseContainer->loadMissing(['container']),

                        'shelves' => [
                            
                            // $dealtSpace->space,
                            
                            [
                                'id' => $warehouseExpectedShelf->id,
                                'name' => $warehouseExpectedShelf->name,
                                'engaged' => $dealtSpace->engaged,
                                'warehouse_container_status_id' => $warehouseExpectedShelf->warehouse_container_status_id,
                            ]
                            
                        ],

                        'rents' => RentResource::collection($warehouseExpectedShelf->warehouseContainer->shelf->rents),

                        'selected_rent' => new RentResource($dealtSpace->rent),

                    ],

                ];

                // $newShelf['container'] = array_merge($newShelf['container'], $dealtSpace->space->parentContainer->loadMissing(['warehouseContainer.container'])->toArray());

                array_push($shelves, $newShelf);

            }

            else if (strpos($dealtSpace->space_type, 'UnitStatus')) {

                $warehouseExpectedUnit = $dealtSpace->space;
                $warehouseExpectedUnitShelf = $warehouseExpectedUnit->parentShelf;
                $warehouseExpectedUnitContainer = $warehouseExpectedUnitShelf->parentContainer;

                $newUnit = [

                    'type' => 'units',
                    
                    'container' => [

                        'id' => $warehouseExpectedUnitContainer->id,
                        'name' => $warehouseExpectedUnitContainer->name,
                        'warehouse_container_id' => $warehouseExpectedUnitContainer->warehouse_container_id,
                        'warehouse_container' => $warehouseExpectedUnitContainer->warehouseContainer->loadMissing(['container']),

                        'shelf' => [

                            'id' => $warehouseExpectedUnitShelf->id,
                            'name' => $warehouseExpectedUnitShelf->name,
                            // 'engaged' => $warehouseExpectedUnitShelf->engaged,
                            'warehouse_container_status_id' => $warehouseExpectedUnitShelf->warehouse_container_status_id,

                            'units' => [

                                // $dealtSpace->space,
                                
                                [
                                    'id' => $warehouseExpectedUnit->id,
                                    'name' => $warehouseExpectedUnit->name,
                                    'engaged' => $dealtSpace->engaged,
                                    // 'warehouse_container_shelf_status_id' => $warehouseExpectedUnit->warehouse_container_shelf_status_id,
                                ]
                            
                            ],
                        
                        ],

                        'rents' => RentResource::collection($dealtSpace->space->warehouseContainer->shelf->unit->rents),

                        'selected_rent' => new RentResource($dealtSpace->rent)

                    ],

                ];

                // $newUnit['container']['shelf'] = array_merge($newUnit['container']['shelf'], $dealtSpace->space->parentShelf->toArray());
            
                // $newUnit['container'] = array_merge($newUnit['container'], $dealtSpace->space->parentShelf->parentContainer->loadMissing(['warehouseContainer.container'])->toArray());                

                array_push($units, $newUnit);

            }



        }

        foreach ($shelves as $shelfKey => &$shelf) {

            foreach ($shelves as $currentShelfKey => $currentShelf) {

                if ($shelf['container']['id']==$currentShelf['container']['id'] && $shelf['container']['name']==$currentShelf['container']['name'] && $shelf['container']['warehouse_container_id']==$currentShelf['container']['warehouse_container_id'] && $shelf['container']['selected_rent']['rent_period_id']==$currentShelf['container']['selected_rent']['rent_period_id'] && $currentShelfKey != $shelfKey) {

                    $shelf['container']['shelves'] = array_merge($shelf['container']['shelves'], $currentShelf['container']['shelves']);
                    
                    unset($shelves[$currentShelfKey]);

                }

            }

        }

        unset($shelf);

        foreach ($units as $unitfKey => &$unit) {

            foreach ($units as $currentUnitfKey => $currentUnit) {

                if ($unit['container']['id']==$currentUnit['container']['id'] && $unit['container']['name']==$currentUnit['container']['name'] && $unit['container']['warehouse_container_id']==$currentUnit['container']['warehouse_container_id'] && $unit['container']['shelf']['id']==$currentUnit['container']['shelf']['id'] && $unit['container']['shelf']['name']==$currentUnit['container']['shelf']['name'] && $unit['container']['shelf']['warehouse_container_status_id']==$currentUnit['container']['shelf']['warehouse_container_status_id'] && $unit['container']['selected_rent']['rent_period_id']==$currentUnit['container']['selected_rent']['rent_period_id'] && $currentUnitfKey != $unitfKey) {
                    
                    $unit['container']['shelf']['units'] = array_merge($unit['container']['shelf']['units'], $currentUnit['container']['shelf']['units']);

                    unset($units[$currentUnitfKey]);

                }

            }

        }

        unset($unit);

        if (count($containers['containers'])) {

            array_push($dealtSpaces, $containers);

        }

        if (count($shelves)) {

            foreach ($shelves as $shelf) {
            
                array_push($dealtSpaces, $shelf);
            
            }

        }

        if (count($units)) {

            foreach ($units as $unit) {
            
                array_push($dealtSpaces, $unit);
            
            }
            
        }

        return $dealtSpaces;
    }
}
