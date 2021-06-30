<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MerchantPayment extends Model
{
    public $timestamps = false;
    protected $guarded = ['id'];
    
    public function rents() 
    {
    	return $this->hasMany(MerchantPaymentDetail::class, 'merchant_payment_id', 'id');
    }

    public function merchantDeal()
    {
        return $this->belongsTo(MerchantDeal::class, 'merchant_deal_id', 'id');
    }
}
