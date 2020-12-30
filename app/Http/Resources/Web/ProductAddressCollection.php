<?php

namespace App\Http\Resources\Web;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ProductAddressCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $spaces = [];

        $containers = [
            'type' => 'containers',
            'containers' => []
        ];

        $shelves = [
            'type' => 'shelves',
            'container' => [
                'shelves' => []
            ]
        ];

        $units = [
            'type' => 'units',
            'container' => [
                'shelf' => [
                    'units' => []
                ]
            ]
        ];       

        foreach ($this->collection as $productAddress) {
           
            if (strpos($productAddress->space_type, 'ContainerStatus')) {
                array_push($containers['containers'], $productAddress->space->loadMissing('warhouseContainer.container'));
            }

            else if (strpos($productAddress->space_type, 'ShelfStatus')) {
                array_push($shelves['container']['shelves'], $productAddress->space);
                
                if (count($shelves['container']) < 2) {

                    $shelves['container'] = array_merge($shelves['container'], $productAddress->space->parentContainer->loadMissing('warhouseContainer.container')->toArray());

                }
            }

            else if (strpos($productAddress->space_type, 'UnitStatus')) {
                array_push($units['container']['shelf']['units'], $productAddress->space);

                if (count($units['container']['shelf']) < 2) {
                    $units['container']['shelf'] = array_merge($units['container']['shelf'], $productAddress->space->parentShelf->toArray());
                }

                if (count($units['container']) < 2) {
                    $units['container'] = array_merge($units['container'], $productAddress->space->parentShelf->parentContainer->loadMissing('warhouseContainer.container')->toArray());
                }
            }

        }

        if (count($containers['containers'])) {
            array_push($spaces, $containers);
        }

        if (count($shelves['container']['shelves'])) {
            array_push($spaces, $shelves);
        }

        if (count($units['container']['shelf']['units'])) {
            array_push($spaces, $units);
        }

        return $spaces;
    }
}
