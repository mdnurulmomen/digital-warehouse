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

    public function stocks()
    {
        return $this->hasMany(ProductVariationStock::class, 'product_variation_id', 'id')->orderBy('id', 'desc');
    }

    public function getVariationImmutabilityAttribute()
    {
        if ($this->requests()->count()) {
            return true;   
        }
        else if ($this->stocks()->count()) {
             return true;
        }

        return false;
    }

    public function latestStock()
    {
        return $this->hasOne(ProductVariationStock::class, 'product_variation_id', 'id')->orderBy('id', 'desc');
    }

    public function nonDispatchedRequests()
    {
        return $this->requests()->whereHas('requiredProduct.requisition', function ($query) {
            $query->where('status', 0);
        });
    }
}
