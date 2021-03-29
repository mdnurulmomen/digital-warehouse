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

    public function productVariation()
    {
    	return $this->belongsTo(ProductVariation::class, 'product_variation_id', 'id');
    }

    public function productStock()
    {
    	return $this->belongsTo(ProductStock::class, 'product_stock_id', 'id');
    }
}