<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequisitionAgent extends Model
{
    public $timestamps = false;
    protected $guarded = ['id'];

    public function requisition()
    {
        return $this->belongsTo(Requisition::class, 'requisition_id', 'id');
    }

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
