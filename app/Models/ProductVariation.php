<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariation extends Model
{
    public $timestamps = false;
    
    protected $guarded = ['id'];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function variation()
    {
    	return $this->belongsTo(Variation::class, 'variation_id', 'id');
    }

    /**
     * Get all of the merchant-products who has this variation.
     */
    public function merchantProductVariations()
    {
        return $this->hasMany(MerchantProductVariation::class, 'product_variation_id', 'id');
    }

    public function getVariationImmutabilityAttribute()
    {
        if ($this->merchantProductVariations()->count()) {

            return true; 
              
        }

        return false;
    }
}
