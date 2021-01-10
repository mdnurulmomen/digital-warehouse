<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WarehouseContainerStatus extends Model
{
    public $timestamps = false;
    protected $guarded = ['id'];

    public function containerShelfStatuses()
    {
        return $this->hasMany(WarehouseContainerShelfStatus::class, 'warehouse_container_status_id', 'id');
    }

    public function containerShelfUnitStatuses()
    {
        return $this->hasManyThrough(WarehouseContainerShelfUnitStatus::class,  WarehouseContainerShelfStatus::class, 'warehouse_container_status_id', 'warehouse_container_shelf_status_id');
    }

    public function product()
    {
        return $this->morphOne(WarehouseProduct::class, 'space');
    }

    public function warehouseContainer()
    {
        return $this->belongsTo(WarehouseContainer::class, 'warehouse_container_id', 'id');
    }
}
