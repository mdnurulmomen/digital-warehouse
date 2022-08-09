<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MerchantInstalmentRent extends Model
{
    public $timestamps = false;
    protected $guarded = [ 'id' ];

    public function instalment()
    {
        return $this->belongsTo(MerchantDealInstalment::class, 'merchant_deal_instalment_id', 'id');
    }

    public function space()
    {
        return $this->belongsTo(DealtSpace::class, 'dealt_space_id', 'id');
    }
}
