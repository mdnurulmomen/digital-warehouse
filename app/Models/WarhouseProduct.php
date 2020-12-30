<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WarhouseProduct extends Model
{
    public $timestamps = false;
    protected $guarded = ['id'];

    /**
     * Get the parent commentable model (post or video).
     */
    public function space()
    {
        return $this->morphTo();
    }
}
