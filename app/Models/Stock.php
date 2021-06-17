<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    protected $casts = [
        'has_approval' => 'boolean',
    ];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id', 'id');
    }

    /**
     * Get the model who kept the stock.
     */
    public function keeper()
    {
        return $this->morphTo(__FUNCTION__, 'keeper_type', 'keeper_id');
    }

    /**
     * Get the model who approved the stock.
     */
    public function approver()
    {
        return $this->morphTo(__FUNCTION__, 'approver_type', 'approver_id');
    }
}
