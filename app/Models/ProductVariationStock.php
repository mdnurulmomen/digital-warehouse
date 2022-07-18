<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductVariationStock extends Model
{   
    use SoftDeletes;
    
    public $timestamps = false;

    protected $guarded = ['id'];

    public function merchantProductVariation()
    {
    	return $this->belongsTo(MerchantProductVariation::class, 'merchant_product_variation_id', 'id');
    }

    public function productStock()
    {
    	return $this->belongsTo(ProductStock::class, 'product_stock_id', 'id');
    }

    public function serials()
    {
        return $this->hasMany(ProductVariationSerial::class, 'product_variation_stock_id', 'id');
    }
}
