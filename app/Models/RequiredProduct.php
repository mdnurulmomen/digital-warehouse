<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequiredProduct extends Model
{
    public $timestamps = false;
    protected $guarded = ['id'];

    public function requisition()
    {
    	return $this->belongsTo(Requisition::class, 'requisition_id', 'id');
    }

    public function product()
    {
    	return $this->belongsTo(Product::class, 'product_id', 'id');
    }
}
