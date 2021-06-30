<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WarehouseContainerShelfStatus extends Model
{
    public $timestamps = false;
    protected $guarded = ['id'];

    public function containerShelfUnitStatuses()
    {
        return $this->hasMany(WarehouseContainerShelfUnitStatus::class, 'warehouse_container_shelf_status_id', 'id');
    }

    public function product()
    {
        return $this->morphOne(WarehouseProduct::class, 'space');
    }

    public function deal()
    {
        return $this->morphOne(DealtSpace::class, 'space');
    }

    public function warehouseContainer()
    {
        return $this->belongsTo(WarehouseContainer::class, 'warehouse_container_id', 'id');
    }

    public function parentContainer()
    {
        return $this->belongsTo(WarehouseContainerStatus::class, 'warehouse_container_status_id', 'id');
    }
}
