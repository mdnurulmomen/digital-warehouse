<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductCategory extends Model
{
    use SoftDeletes;

    public $timestamps = false;
    protected $guarded = ['id'];

    protected $with = ['category'];

    public function category()
    {
    	return $this->belongsTo(ProductCategory::class, 'parent_category_id', 'id')->withTrashed();
    }

    public function products()
    {
    	return $this->hasMany(Product::class, 'product_category_id', 'id');
    }
}
