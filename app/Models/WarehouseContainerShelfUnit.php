<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WarehouseContainerShelfUnit extends Model
{
    public $timestamps = false;
    protected $guarded = ['id'];

    /*
    public function warehouseContainer()
    {
        return $this->belongsTo(WarehouseContainer::class, 'warehouse_container_id', 'id');
    }
*/

    public function warehouseContainerShelf()
    {
    	return $this->belongsTo(WarehouseContainerShelf::class, 'warehouse_container_shelf_id');
    }

/*
    public function getQuantityUnitsAttribute()
    {
    	return $this->warehouseContainerShelf->units->count();
    }
*/

    public function rents()
    {
        return $this->morphMany(Rent::class, 'warehouse_storer')->withTrashed();
    }

}
