<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WarhouseContainerShelfUnitStatus extends Model
{
    public $timestamps = false;
    protected $guarded = ['id'];

    public function product()
    {
        return $this->morphOne(WarhouseProduct::class, 'space');
    }

    public function parentShelf()
    {
    	return $this->belongsTo(WarhouseContainerShelfStatus::class, 'warhouse_container_shelf_status_id', 'id');
    }

    public function warhouseContainer()
    {
        return $this->belongsTo(WarhouseContainer::class, 'warhouse_container_id', 'id');
    }
}
