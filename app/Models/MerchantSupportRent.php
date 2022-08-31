<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MerchantSupportRent extends Model
{
    public $timestamps = false;
    protected $guarded = [ 'id' ];

    public function rent()
    {
        return $this->belongsTo(MerchantRent::class, 'merchant_rent_id', 'id');
    }
}
