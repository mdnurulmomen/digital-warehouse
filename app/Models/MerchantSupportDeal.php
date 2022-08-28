<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MerchantSupportDeal extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = false;
    protected $guarded = ['id'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'e_commerce_fulfillment_support' => 'boolean',
        'purchase_support' => 'boolean',
        'pos_support' => 'boolean'
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::created(function ($deal) {
            $deal->name = 'M'.$deal->merchant_id.'SD'.$deal->id;
            $deal->save();
        });
    }

    public function rents()
    {
        return $this->morphMany(MerchantRent::class, 'dealable');
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
