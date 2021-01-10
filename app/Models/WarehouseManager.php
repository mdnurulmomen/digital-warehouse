<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WarehouseManager extends Model
{
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id', 'id');
    }
}
