<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DealtSpace extends Model
{
    protected $guarded = ['id'];

    public $timestamps = false;
    
    /**
     * Get the parent commentable model (post or video).
     */
    public function space()
    {
        return $this->morphTo();
    }

    public function warehouseContainer()
    {
        return $this->belongsTo(WarehouseContainer::class, 'warehouse_container_id', 'id');
    }

    /*
    public function rent()
    {
        return $this->belongsTo(Rent::class, 'rent_id', 'id');
    }
    */

    public function deal()
    {
        return $this->belongsTo(MerchantDeal::class, 'merchant_deal_id', 'id');
    }

    public function rents()
    {
        return $this->hasMany(MerchantPaymentDetail::class, 'dealt_space_id', 'id');
    }
}
