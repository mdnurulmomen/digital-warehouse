<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductDelivery extends Model
{
    public $timestamps = false;
    protected $guarded = ['id'];

    protected $casts = [
        'receiving_confirmation' => 'boolean',
    ];
}
