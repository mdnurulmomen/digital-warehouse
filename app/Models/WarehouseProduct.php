<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WarehouseProduct extends Model
{
    public $timestamps = false;
    protected $guarded = ['id'];

    /**
     * Get the parent commentable model (post or video).
     */
    public function space()
    {
        return $this->morphTo();
    }

    public function merchantProduct()
    {
    	return $this->belongsTo(MerchantProduct::class, 'merchant_product_id', 'id');
    }
}
