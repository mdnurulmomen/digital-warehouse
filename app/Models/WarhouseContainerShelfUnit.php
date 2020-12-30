<?php

namespace App\Models;

use App\Models\WarhouseContainerShelf;
use Illuminate\Database\Eloquent\Model;

class WarhouseContainerShelfUnit extends Model
{
    public $timestamps = false;
    protected $guarded = ['id'];

    public function warhouseContainerShelf()
    {
    	return $this->belongsTo(WarhouseContainerShelf::class, 'warhouse_container_shelf_id');
    }

/*
    public function getQuantityUnitsAttribute()
    {
    	return $this->warhouseContainerShelf->units->count();
    }
*/

    public function rents()
    {
        return $this->morphMany(Rent::class, 'warhouse_storer');
    }

    public function warhouseContainer()
    {
        return $this->belongsTo(WarhouseContainer::class, 'warhouse_container_id', 'id');
    }
}
