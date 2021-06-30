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

    public function merchantPayment()
    {
    	return $this->belongsTo(MerchantPayment::class, 'merchant_payment_id', 'id');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id', 'id');
    }

    public function storerRent()
    {
        return $this->belongsTo(Rent::class, 'rent_id', 'id');
    }
}
