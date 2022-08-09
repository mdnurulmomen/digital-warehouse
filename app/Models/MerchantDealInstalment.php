<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MerchantDealInstalment extends Model
{
    public $timestamps = false;
    protected $guarded = ['id'];
    protected $dates = ['date_from', 'date_to', 'created_at'];
    
    public function rents() 
    {
    	return $this->hasMany(MerchantInstalmentRent::class, 'merchant_deal_instalment_id', 'id');
    }

    public function payments() 
    {
        return $this->hasMany(MerchantPayment::class, 'merchant_deal_instalment_id', 'id');
    }

    public function deal()
    {
        return $this->belongsTo(MerchantDeal::class, 'merchant_deal_id', 'id');
    }
}
