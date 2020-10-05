<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfilePreview extends Model
{
    protected $guarded = ['id'];
    public $timestamps = false;

    /**
     * Get the owning user model.
     */
    public function user()
    {
        return $this->morphTo();
    }
}
