<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MerchantPayment extends Model
{
    public $timestamps = false;
    protected $guarded = ['id'];
    protected $dates = ['paid_at'];

    public function instalment()
    {
        return $this->belongsTo(MerchantDealInstalment::class, 'merchant_deal_instalment_id', 'id');
    }

    public function issuer()
    {
        return $this->morphTo();
    }

    public function updater()
    {
        return $this->morphTo();
    }
}
