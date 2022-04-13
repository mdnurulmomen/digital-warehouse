<?php

namespace App\Models;

use App\Models\ContainerShelf;
use App\Models\ContainerShelfUnit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Container extends Model
{
    use SoftDeletes;

    public $timestamps = false;
	protected $guarded = ['id'];

	/**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'has_shelve' => 'boolean',
    ];

	public function shelf()
	{
		return $this->hasOne(ContainerShelf::class, 'container_id', 'id');
	}

	public function unit()
	{
		return $this->hasOneThrough(ContainerShelfUnit::class, ContainerShelf::class, 'container_id', 'container_shelf_id');
	}

	public function warehouses()
    {
        return $this->hasMany(WarehouseContainer::class, 'container_id', 'id');
    }

    public function storageType()
    {
        return $this->belongsTo(StorageType::class, 'storage_type_id', 'id')->withTrashed();
    }
    
}
