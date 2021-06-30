<?php

namespace App\Http\Resources\Web;

use Illuminate\Http\Resources\Json\JsonResource;

class DealtSpaceResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [

            DealtContainerResource::collection($this->containerStatuses()->has('deals')->get()),
            DealtShelfContainerResource::collection($this->containerShelfStatuses()->has('deals')->get()),
            DealtUnitContainerResource::collection($this->containerShelfUnitStatuses()->has('deals')->get()),
        
        ];
        
        $dealtSpaces = [];

        // $warehouses = [];

        $containers = [
            'type' => 'containers',
            'containers' => []
        ];

        $shelves = [
            'type' => 'shelves',
            'container' => [
                'shelves' => [],
            ]
        ];

        $units = [
            'type' => 'units',
            'container' => [
                'shelf' => [
                    'units' => []
                ],
            ]
        ];       

        foreach ($this->collection as $key => $productAddress) {
           
            if (strpos($productAddress->space_type, 'ContainerStatus')) {
                
                array_push($containers['containers'], $productAddress->space->loadMissing(['warehouseContainer.container']));
            
            }

            else if (strpos($productAddress->space_type, 'ShelfStatus')) {
                
                $newShelf = [
                    
                    'type' => 'shelves',

                    'container' => [

                        'shelves' => [

                            $productAddress->space,
                            
                        ]

                    ],

                ];

                $newShelf['container'] = array_merge($newShelf['container'], $productAddress->space->parentContainer->loadMissing(['warehouseContainer.container'])->toArray());

                array_push($shelves, $newShelf);

            }

            else if (strpos($productAddress->space_type, 'UnitStatus')) {

                $newUnit = [

                    'type' => 'units',
                    
                    'container' => [

                        'shelf' => [

                            'units' => [

                                $productAddress->space,
                            
                            ],
                        
                        ],

                    ],

                ];

                $newUnit['container']['shelf'] = array_merge($newUnit['container']['shelf'], $productAddress->space->parentShelf->toArray());
            
                $newUnit['container'] = array_merge($newUnit['container'], $productAddress->space->parentShelf->parentContainer->loadMissing(['warehouseContainer.container'])->toArray());                

                array_push($units, $newUnit);

            }



        }

        foreach ($shelves as $shelfKey => &$shelf) {

            foreach ($shelves as $currentShelfKey => $currentShelf) {

                if ($shelf['container']['id']==$currentShelf['container']['id'] && $shelf['container']['name']==$currentShelf['container']['name'] && $shelf['container']['warehouse_container_id']==$currentShelf['container']['warehouse_container_id'] && $currentShelfKey != $shelfKey) {

                    $shelf['container']['shelves'] = array_merge($shelf['container']['shelves'], $currentShelf['container']['shelves']);
                    
                    unset($shelves[$currentShelfKey]);

                }

            }

        }

        unset($shelf);

        foreach ($units as $unitfKey => &$unit) {

            foreach ($units as $currentUnitfKey => $currentUnit) {

                if ($unit['container']['id']==$currentUnit['container']['id'] && $unit['container']['name']==$currentUnit['container']['name'] && $unit['container']['warehouse_container_id']==$currentUnit['container']['warehouse_container_id'] && $unit['container']['shelf']['id']==$currentUnit['container']['shelf']['id'] && $unit['container']['shelf']['name']==$currentUnit['container']['shelf']['name'] && $unit['container']['shelf']['warehouse_container_id']==$currentUnit['container']['shelf']['warehouse_container_id'] && $currentUnitfKey != $unitfKey) {
                    
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
