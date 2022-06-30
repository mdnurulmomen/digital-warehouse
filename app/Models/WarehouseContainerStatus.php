<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WarehouseContainerStatus extends Model
{
    public $timestamps = false;
    protected $guarded = ['id'];

    protected $casts = [
        'engaged' => 'float',
        'occupied' => 'float',
    ];

    public function containerShelfStatuses()
    {
        return $this->hasMany(WarehouseContainerShelfStatus::class, 'warehouse_container_status_id', 'id');
    }

    public function containerShelfUnitStatuses()
    {
        return $this->hasManyThrough(WarehouseContainerShelfUnitStatus::class,  WarehouseContainerShelfStatus::class, 'warehouse_container_status_id', 'warehouse_container_shelf_status_id');
    }

    /*
    public function product()
    {
        return $this->morphOne(WarehouseProduct::class, 'space');
    }
    */
   
    public function products()
    {
        return $this->morphMany(WarehouseProduct::class, 'space');
    }

    public function deals()
    {
        return $this->morphMany(DealtSpace::class, 'space');
    }

    public function warehouseContainer()
    {
        return $this->belongsTo(WarehouseContainer::class, 'warehouse_container_id', 'id');
    }

    public function updateContainerStatus($newValue)
    {
        $this->updateChildShelves($newValue);
    }

    public function updateChildShelves($newValue)
    {
        if ($this->containerShelfStatuses->count()) {
            
            foreach ($this->containerShelfStatuses as $containerShelf) {
               
                $containerShelf->update([
                    'engaged' => $newValue
                ]);

                $this->updateChildUnits($containerShelf, $newValue);

            }

        }
    }

    public function updateChildUnits(WarehouseContainerShelfStatus $shelf, $newValue)
    {
        if ($shelf->containerShelfUnitStatuses->count()) {
            
            $shelf->containerShelfUnitStatuses()->update([
                'engaged' => $newValue
            ]);

        }
    }

    public function updateParentContainer(WarehouseContainerStatus $warehouseExpectedContainer)
    {
        // $warehouseExpectedContainer = WarehouseContainerStatus::find($containerId);

        // all shelves are engaged
        if ($warehouseExpectedContainer->containerShelfStatuses->count()===$warehouseExpectedContainer->containerShelfStatuses()->where('engaged', 1.0)->count()) {
            
            $warehouseExpectedContainer->update([
                'engaged' => 1
            ]); 

        }
        // no shelf is engaged
        else if ($warehouseExpectedContainer->containerShelfStatuses->count()===$warehouseExpectedContainer->containerShelfStatuses()->where('engaged', 0.0)->count()) {
            
            $warehouseExpectedContainer->update([
                'engaged' => 0
            ]); 

        }
        else {

            // partially engaged
            $warehouseExpectedContainer->update([
                'engaged' => 0.5
            ]);

        }

    }

    public function updateParentShelf(WarehouseContainerShelfStatus $warehouseExpectedShelf)
    {
        // Related Shelf
        // $warehouseExpectedShelf = WarehouseContainerShelfStatus::find($container->shelf->id);

        // all units are engaged
        if ($warehouseExpectedShelf->containerShelfUnitStatuses->count()===$warehouseExpectedShelf->containerShelfUnitStatuses()->where('engaged', 1.0)->count()) {
            
            $warehouseExpectedShelf->update([
                'engaged' => 1
            ]);

        }
        // no unit is engaged
        else if ($warehouseExpectedShelf->containerShelfUnitStatuses->count()===$warehouseExpectedShelf->containerShelfUnitStatuses()->where('engaged', 0.0)->count()) {
            
            $warehouseExpectedShelf->update([
                'engaged' => 0
            ]);

        }
        else {

            // partially engaged
            $warehouseExpectedShelf->update([
                'engaged' => 0.5
            ]);

        }

        // parent Container
        $this->updateParentContainer($warehouseExpectedShelf->parentContainer);

    }

}
