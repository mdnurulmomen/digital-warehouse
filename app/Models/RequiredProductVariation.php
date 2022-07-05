<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequiredProductVariation extends Model
{
    public $timestamps = false;
    protected $guarded = ['id'];

    protected $casts = [
        'has_serials' => 'boolean',
    ];

    /*
    public function requisition()
    {
        return $this->belongsTo(Requisition::class, 'requisition_id', 'id');
    }
    */

    public function merchantProductVariation()
    {
    	return $this->belongsTo(MerchantProductVariation::class, 'merchant_product_variation_id', 'id');
    }

    public function requiredProduct()
    {
    	return $this->belongsTo(RequiredProduct::class, 'required_product_id', 'id');
    }

    public function serials()
    {
        return $this->hasMany(RequiredProductVariationSerial::class, 'required_product_variation_id', 'id');
    }

    public function stocks()
    {
        return $this->hasMany(RequiredProductVariationStock::class, 'required_product_variation_id', 'id');
    }
}
