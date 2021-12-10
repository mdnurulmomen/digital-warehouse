<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MerchantPaymentRent extends Model
{
    public $timestamps = false;
    protected $guarded = [ 'id' ];

    public function payment()
    {
        return $this->belongsTo(MerchantPayment::class, 'merchant_payment_id', 'id');
    }

    public function space()
    {
        return $this->belongsTo(DealtSpace::class, 'dealt_space_id', 'id');
    }
}
