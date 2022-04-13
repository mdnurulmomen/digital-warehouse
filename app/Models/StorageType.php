<?php

namespace App\Models;

use App\Models\WarhouseStoragePreview;
use App\Models\WarhouseStorageFeature;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StorageType extends Model
{
    use SoftDeletes;

    public $timestamps = false;
    protected $guarded = ['id'];

    public function warehouses()
    {
    	return $this->belongsToMany(Warehouse::class, 'warehouse_storage_types', 'storage_type_id', 'warehouse_id');
    }

    public function containers()
    {
        return $this->hasMany(Container::class, 'storage_type_id', 'id');
    }

}
