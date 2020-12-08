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
}
