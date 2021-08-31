<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Variation extends Model
{
    use SoftDeletes;
    
    public $timestamps = false;
    protected $guarded = ['id'];

    protected $with = ['variationType', 'variationParent'];

    public function variationType()
    {
    	return $this->belongsTo(VariationType::class, 'variation_type_id', 'id')->withTrashed();
    }

    public function variationParent()
    {
    	return $this->belongsTo(Variation::class, 'variation_parent_id', 'id')->withTrashed();
    }

    public function childs()
    {
    	return $this->hasMany(Variation::class, 'variation_parent_id', 'id');
    }
}
