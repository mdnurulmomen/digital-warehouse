<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WarehouseContainerShelfUnitStatus extends Model
{
    public $timestamps = false;
    protected $guarded = ['id'];

    protected $casts = [
        'engaged' => 'float',
        'occupied' => 'float',
    ];

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

    public function parentShelf()
    {
    	return $this->belongsTo(WarehouseContainerShelfStatus::class, 'warehouse_container_shelf_status_id', 'id');
    }

    public function warehouseContainer()
    {
        return $this->belongsTo(WarehouseContainer::class, 'warehouse_container_id', 'id');
    }
}
