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
    ];

    public function requisition()
    {
    	return $this->belongsTo(Requisition::class, 'requisition_id', 'id');
    }

    public function product()
    {
    	return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function variations()
    {
        return $this->hasMany(RequiredProductVariation::class, 'required_product_id', 'id');
    }

    public function serials()
    {
        return $this->hasMany(RequiredProductSerial::class, 'required_product_id', 'id');
    }
}
