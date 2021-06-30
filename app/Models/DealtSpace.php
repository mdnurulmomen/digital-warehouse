<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DealtSpace extends Model
{
    /**
     * Get the parent commentable model (post or video).
     */
    public function space()
    {
        return $this->morphTo();
    }

    public function merchantDeal()
    {
    	return $this->belongsTo(MerchantDeal::class, 'merchant_deal_id', 'id');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id', 'id');
    }
}
