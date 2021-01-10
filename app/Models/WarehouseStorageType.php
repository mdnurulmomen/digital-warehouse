<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WarehouseStorageType extends Model
{
    use SoftDeletes;

    public $guarded = ['id'];
    public $timestamps = false;

    public function storageType() 
    {
    	return $this->belongsTo(StorageType::class, 'storage_type_id', 'id');
    }

    /**
     * Get the user's image.
     */
    public function previews()
    {
        return $this->hasMany(WarehouseStoragePreview::class, 'warehouse_storage_type_id', 'id');
    }

    public function feature()
    {
        return $this->hasOne(WarehouseStorageFeature::class, 'warehouse_storage_type_id', 'id');
    }
}
