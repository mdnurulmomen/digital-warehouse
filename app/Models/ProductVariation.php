<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariation extends Model
{
    public $timestamps = false;
    protected $guarded = ['id'];

    public function variation()
    {
    	return $this->belongsTo(Variation::class, 'variation_id', 'id');
    }

    public function requests()
    {
        return $this->hasMany(RequiredProductVariation::class, 'product_variation_id', 'id');
    }

    public function nonDispatchedRequests()
    {
        return $this->hasMany(RequiredProductVariation::class, 'product_variation_id', 'id')->whereHas('requisition', function ($query) {
            $query->where('status', 0);
        });
    }
}
