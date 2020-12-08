<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\WarhouseContainerShelfStatus;
use App\Models\WarhouseContainerShelfUnitStatus;

class WarhouseContainerStatus extends Model
{
    public $timestamps = false;
    protected $guarded = ['id'];

    public function containerShelfStatuses()
    {
        return $this->hasMany(WarhouseContainerShelfStatus::class, 'warhouse_container_status_id', 'id');
    }

    public function containerShelfUnitStatuses()
    {
        return $this->hasManyThrough(WarhouseContainerShelfUnitStatus::class,  WarhouseContainerShelfStatus::class, 'warhouse_container_status_id', 'warhouse_container_shelf_status_id');
    }
}
