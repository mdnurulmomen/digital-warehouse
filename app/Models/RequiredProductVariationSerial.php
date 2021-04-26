<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequiredProductVariationSerial extends Model
{
    public $timestamps = false;
    
    protected $guarded = ['id'];

    public function serial()
    {
    	return $this->belongsTo(ProductVariationSerial::class, 'product_variation_serial_id', 'id');
    }
}
