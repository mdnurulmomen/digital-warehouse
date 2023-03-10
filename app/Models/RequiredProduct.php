<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequiredProduct extends Model
{
    public $timestamps = false;
    protected $guarded = ['id'];

    protected $casts = [
        'has_serials' => 'boolean',
        'has_variations' => 'boolean',
        'packaging_service' => 'boolean',
    ];

    public function requisition()
    {
    	return $this->belongsTo(Requisition::class, 'requisition_id', 'id');
    }

    public function dispatch()
    {
        return $this->belongsTo(Dispatch::class, 'requisition_id', 'requisition_id');
    }

    public function merchantProduct()
    {
    	return $this->belongsTo(MerchantProduct::class, 'merchant_product_id', 'id');
    }

    public function variations()
    {
        return $this->hasMany(RequiredProductVariation::class, 'required_product_id', 'id');
    }

    public function serials()
    {
        return $this->hasMany(RequiredProductSerial::class, 'required_product_id', 'id');
    }

    public function stocks()
    {
        return $this->hasMany(RequiredProductStock::class, 'required_product_id', 'id');
    }

    public function preferredPackage()
    {
        return $this->hasOne(RequiredProductPackage::class, 'required_product_id', 'id');
    }

    public function dispatchedPackage()
    {
        return $this->hasOne(PackedDispatch::class, 'required_product_id', 'id');
    }
}
