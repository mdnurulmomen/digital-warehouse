<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductDispatch extends Model
{
    public $timestamps = false;
    protected $guarded = ['id'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'has_variations' => 'boolean',
    ];

    public function variations()
    {
    	return $this->hasMany(ProductVariationDispatch::class, 'product_dispatch_id', 'id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
}
