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
}
