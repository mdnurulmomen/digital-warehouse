<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContainerShelf extends Model
{
    public $timestamps = false;
	protected $guarded = ['id'];

	/**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'has_units' => 'boolean',
    ];

    public function unit()
    {
        return $this->hasOne(ContainerShelfUnit::class, 'container_shelf_id', 'id');
    }
}
