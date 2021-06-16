<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MerchantProductVariation extends Model
{
    public function requests()
    {
        return $this->hasMany(RequiredProductVariation::class, 'merchant_product_variation_id', 'id');
    }

    public function stocks()
    {
        return $this->hasMany(ProductVariationStock::class, 'merchant_product_variation_id', 'id')->orderBy('id', 'desc');
    }

    public function serials()
    {
        return $this->hasMany(ProductVariationSerial::class, 'merchant_product_variation_id', 'id');
    }

    public function latestStock()
    {
        return $this->hasOne(ProductVariationStock::class, 'merchant_product_variation_id', 'id')->whereHas('productStock', function ($query) {
            $query->where('has_approval', 1);
        })->orderBy('id', 'desc');
    }

    public function nonDispatchedRequests()
    {
        return $this->requests()->whereHas('requiredProduct.requisition', function ($query) {
            $query->where('status', 0);
        });
    }

    public function dispatchedRequests()
    {
        return $this->requests()->whereHas('requiredProduct.requisition', function ($query) {
            $query->where('status', 1);
        });
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
}
