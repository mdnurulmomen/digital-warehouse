<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\WarhouseContainerShelfUnitStatus;

class WarhouseContainerShelfStatus extends Model
{
    public $timestamps = false;
    protected $guarded = ['id'];

    public function containerShelfUnitStatuses()
    {
        return $this->hasMany(WarhouseContainerShelfUnitStatus::class, 'warhouse_container_shelf_status_id', 'id');
    }

    public function product()
    {
        return $this->morphOne(WarhouseProduct::class, 'space');
    }

    public function warhouseContainer()
    {
        return $this->belongsTo(WarhouseContainer::class, 'warhouse_container_id', 'id');
    }

    public function parentContainer()
    {
        return $this->belongsTo(WarhouseContainerStatus::class, 'warhouse_container_status_id', 'id');
    }
}
