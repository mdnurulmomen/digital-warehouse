<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductCategory extends Model
{
    use SoftDeletes;

    public $timestamps = false;
    protected $guarded = ['id'];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_perishable' => 'boolean',
    ];

    // protected $with = ['parent'];

    /*
        public function category()
        {
        	return $this->belongsTo(ProductCategory::class, 'parent_category_id', 'id')->withTrashed();
        }
    */

    public function parent()
    {
        return $this->belongsTo(ProductCategory::class, 'parent_category_id', 'id')->withTrashed();
    }

    public function childs()
    {
        return $this->hasMany(ProductCategory::class, 'parent_category_id', 'id');
    }

    /*public function nestedChilds()
    {
        return $this->childs()->with('childs');
    }*/

    public function products()
    {
    	return $this->hasMany(Product::class, 'product_category_id', 'id');
    }
}
