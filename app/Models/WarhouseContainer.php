<?php

namespace App\Models;

// use App\Models\WarhouseContainerShelf;
// use App\Models\WarhouseContainerStatus;
use Illuminate\Database\Eloquent\Model;
// use App\Models\WarhouseContainerShelfUnit;
use Illuminate\Database\Eloquent\SoftDeletes;

class WarhouseContainer extends Model
{
    use SoftDeletes;

    public $timestamps = false;
    protected $guarded = ['id'];

    public function shelf()
    {
    	return $this->hasOne(WarhouseContainerShelf::class, 'warhouse_container_id', 'id');
    }

    public function unit()
    {
    	return $this->hasOneThrough(WarhouseContainerShelfUnit::class, WarhouseContainerShelf::class, 'warhouse_container_id', 'warhouse_container_shelf_id');
    }

    public function container()
    {
        return $this->belongsTo(Container::class, 'container_id', 'id');
    }

    public function rents()
    {
        return $this->morphMany(Rent::class, 'warhouse_storer');
    }

    public function containerStatuses()
    {
        return $this->hasMany(WarhouseContainerStatus::class, 'warhouse_container_id');
    }

    public function containerShelfStatuses()
    {
        return $this->hasMany(WarhouseContainerShelfStatus::class, 'warhouse_container_id');
    }

    public function containerShelfUnitStatuses()
    {
        return $this->hasMany(WarhouseContainerShelfUnitStatus::class, 'warhouse_container_id');
    }

}
