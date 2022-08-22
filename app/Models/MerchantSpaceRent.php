<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MerchantSpaceRent extends Model
{
    public $timestamps = false;
    protected $guarded = [ 'id' ];

    public function rent()
    {
        return $this->belongsTo(MerchantRent::class, 'merchant_rent_id', 'id');
    }

    public function space()
    {
        return $this->belongsTo(DealtSpace::class, 'dealt_space_id', 'id');
    }
}
