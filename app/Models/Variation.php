<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Variation extends Model
{
    use SoftDeletes;
    
    public $timestamps = false;
    protected $guarded = ['id'];

    // protected $with = ['variationType'];

    /*
        public function variationType()
        {
        	return $this->belongsTo(VariationType::class, 'variation_type_id', 'id')->withTrashed();
        }
    */
   
    public function type()
    {
        return $this->belongsTo(VariationType::class, 'variation_type_id', 'id')->withTrashed();
    }


    /*
        public function variationParent()
        {
        	return $this->belongsTo(Variation::class, 'variation_parent_id', 'id')->withTrashed();
        }
    */
    
    public function parent()
    {
        return $this->belongsTo(Variation::class, 'variation_parent_id', 'id')->withTrashed();
    }

    /*
        public function variationChilds()
        {
        	return $this->hasMany(Variation::class, 'variation_parent_id', 'id');
        }
    */
   
    public function childs()
    {
        return $this->hasMany(Variation::class, 'variation_parent_id', 'id');
    }

    public function nestedChilds()
    {
        return $this->hasMany(Variation::class, 'variation_parent_id', 'id')->with('childs');
    }


    /*
        public function variationGrandParent()
        {
            $currentAncestor = $this->belongsTo(Variation::class, 'variation_parent_id', 'id')->withTrashed();

            while ($currentAncestor->variation_parent_id !== NULL) {
                
                $currentAncestor = $currentAncestor->variationParent;

            }

            return $currentAncestor;
        }
    */
}
