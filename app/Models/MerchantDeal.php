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

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::created(function ($deal) {
            $deal->name = 'M'.$deal->merchant_id.'D'.$deal->id;
            $deal->save();
        });
    }
    
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

    public function rentPeriod()
    {
        return $this->belongsTo(RentPeriod::class, 'rent_period_id', 'id');
    }
}
