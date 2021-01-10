<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WarehouseContainer extends Model
{
    use SoftDeletes;

    public $timestamps = false;
    protected $guarded = ['id'];

    public function shelf()
    {
    	return $this->hasOne(WarehouseContainerShelf::class, 'warehouse_container_id', 'id');
    }

    public function unit()
    {
    	return $this->hasOneThrough(WarehouseContainerShelfUnit::class, WarehouseContainerShelf::class, 'warehouse_container_id', 'warehouse_container_shelf_id');
    }

    public function container()
    {
        return $this->belongsTo(Container::class, 'container_id', 'id');
    }

    public function rents()
    {
        return $this->morphMany(Rent::class, 'warehouse_storer');
    }

    public function containerStatuses()
    {
        return $this->hasMany(WarehouseContainerStatus::class, 'warehouse_container_id');
    }

    public function containerShelfStatuses()
    {
        return $this->hasMany(WarehouseContainerShelfStatus::class, 'warehouse_container_id');
    }

    public function containerShelfUnitStatuses()
    {
        return $this->hasMany(WarehouseContainerShelfUnitStatus::class, 'warehouse_container_id');
    }
}
