<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VariationType extends Model
{
    use SoftDeletes;
    public $timestamps = false;
    protected $guarded = ['id'];

    public function variations()
    {
    	return $this->hasMany(Variation::class, 'variation_type_id', 'id');
    }
}
