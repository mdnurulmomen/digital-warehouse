<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariationStock extends Model
{   
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    protected $guarded = ['id'];

    protected $casts = [
        'has_serials' => 'boolean',
    ];

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
