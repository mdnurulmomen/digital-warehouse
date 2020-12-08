<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RentPeriod extends Model
{
    use SoftDeletes;
    protected $guarded = ['id'];
    public $timestamps = false;
}
