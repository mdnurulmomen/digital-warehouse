<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MerchantPayment extends Model
{
    public $timestamps = false;
    protected $guarded = ['id'];
    protected $dates = ['paid_at'];

    public function rent()
    {
        return $this->belongsTo(MerchantRent::class, 'merchant_rent_id', 'id');
    }

    public function issuer()
    {
        return $this->morphTo();
    }

    public function updater()
    {
        return $this->morphTo();
    }
}
