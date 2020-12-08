<?php

namespace App\Models;

use App\Models\WarhouseContainer;
use Illuminate\Database\Eloquent\Model;
use App\Models\WarhouseContainerShelfUnit;

class WarhouseContainerShelf extends Model
{
    public $timestamps = false;
    protected $guarded = ['id'];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'engaged' => 'boolean',
        'has_units' => 'boolean',
    ];

    public function warhouseContainer()
    {
    	return $this->belongsTo(WarhouseContainer::class, 'warhouse_container_id');
    }

    /*
    public function getQuantityShelvesAttribute()
    {
    	return $this->warhouseContainer->shelves->count();
    }
    */

    public function unit()
    {
        return $this->hasOne(WarhouseContainerShelfUnit::class, 'warhouse_container_shelf_id');
    }

    public function rents()
    {
        return $this->morphMany(Rent::class, 'warhouse_storer');
    }
}
