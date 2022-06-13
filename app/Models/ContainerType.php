<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContainerType extends Model
{
    use SoftDeletes;

    public $timestamps = false;
    protected $guarded = ['id'];

    public function containers()
    {
        return $this->hasMany(Container::class, 'container_type_id', 'id');
    }
}
