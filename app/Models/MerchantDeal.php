<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MerchantDeal extends Model
{
    public $timestamps = false;
    protected $guarded = ['id'];
    protected $dates = ['created_at'];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'active' => 'boolean',
        'auto_renewal' => 'boolean',
        'e_commerce_fulfillment' => 'boolean'
    ];
    
    public function spaces() 
    {
    	return $this->hasMany(DealtSpace::class, 'merchant_deal_id', 'id');
    }

    public function containers() 
    {
        return $this->spaces()->where('space_type', 'App\Models\WarehouseContainerStatus');
    }

    public function shelves() 
    {
        return $this->spaces()->where('space_type', 'App\Models\WarehouseContainerShelfStatus');
    }

    public function units() 
    {
        return $this->spaces()->where('space_type', 'App\Models\WarehouseContainerShelfUnitStatus');
    }
    
    public function payments() 
    {
    	return $this->hasMany(MerchantPayment::class, 'merchant_deal_id', 'id');
    }

    public function merchant() 
    {
        return $this->belongsTo(Merchant::class, 'merchant_id', 'id');
    }
}
