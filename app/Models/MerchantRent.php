<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MerchantRent extends Model
{
    public $timestamps = false;
    protected $guarded = ['id'];
    protected $dates = ['date_from', 'date_to', 'created_at'];
    
    public function spaceRents() 
    {
    	return $this->hasMany(MerchantSpaceRent::class, 'merchant_rent_id', 'id');
    }

    public function payments() 
    {
        return $this->hasMany(MerchantPayment::class, 'merchant_rent_id', 'id');
    }

    public function dealable()
    {
        return $this->morphTo();
    }
}
