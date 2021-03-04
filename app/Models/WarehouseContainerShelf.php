<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WarehouseContainerShelf extends Model
{
    public $timestamps = false;
    protected $guarded = ['id'];

    public function warehouseContainer()
    {
    	return $this->belongsTo(WarehouseContainer::class, 'warehouse_container_id');
    }

    /*
    public function getQuantityShelvesAttribute()
    {
    	return $this->warehouseContainer->shelves->count();
    }
    */

    public function unit()
    {
        return $this->hasOne(WarehouseContainerShelfUnit::class, 'warehouse_container_shelf_id');
    }

    public function rents()
    {
        return $this->morphMany(Rent::class, 'warehouse_storer');
    }
}
