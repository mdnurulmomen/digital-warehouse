<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Variation extends Model
{
    use SoftDeletes;
    
    public $timestamps = false;
    protected $guarded = ['id'];

    protected $with = ['variationType'];

    public function variationType()
    {
    	return $this->belongsTo(VariationType::class, 'variation_type_id', 'id')->withTrashed();
    }
}
