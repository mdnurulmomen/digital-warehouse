<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Rent extends Model
{
    use SoftDeletes;

    public $timestamps = false;
    protected $guarded = ['id'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'active' => 'boolean',
    ];

    public function rentPeriod()
    {
        return $this->belongsTo(RentPeriod::class, 'rent_period_id', 'id')->withTrashed();
    }

    /*
    public function rent()
    {
        return $this->hasMany(DealtSpace::class, 'rent_id', 'id');
    }
    */
}
