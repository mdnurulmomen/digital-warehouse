<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequisitionAgent extends Model
{
    public $timestamps = false;
    protected $guarded = ['id'];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    /*
    protected $casts = [
        'presence_confirmation' => 'boolean',
    ];
    */
}
