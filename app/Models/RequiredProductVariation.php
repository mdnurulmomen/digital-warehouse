<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequiredProductVariation extends Model
{
    public $timestamps = false;
    protected $guarded = ['id'];

    public function requisition()
    {
        return $this->belongsTo(Requisition::class, 'requisition_id', 'id');
    }

    public function productVariation()
    {
    	return $this->belongsTo(ProductVariation::class, 'product_variation_id', 'id');
    }

    public function requiredProduct()
    {
    	return $this->belongsTo(RequiredProduct::class, 'required_product_id', 'id');
    }
}
