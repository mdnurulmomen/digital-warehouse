<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rent extends Model
{
    public $timestamps = false;
    protected $guarded = ['id'];

    public function rentPeriod()
    {
        return $this->belongsTo(RentPeriod::class, 'rent_period_id', 'id');
    }
}
