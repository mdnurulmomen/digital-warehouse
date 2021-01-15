<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequiredProductVariation extends Model
{
    public $timestamps = false;
    protected $guarded = ['id'];

    public function productVariation()
    {
    	return $this->belongsTo(ProductVariation::class, 'product_variation_id', 'id');
    }
}
