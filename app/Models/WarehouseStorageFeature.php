<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WarehouseStorageFeature extends Model
{
    public $timestamps = false;
    protected $guarded = ['id'];

    public function warehouseStorageType()
    {
    	return $this->belongsTo(WarehouseStorageType::class, 'warehouse_storage_type_id', 'id')->withTrashed();
    }
}
