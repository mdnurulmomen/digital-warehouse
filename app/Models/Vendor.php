<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vendor extends Model
{
    use SoftDeletes;

    public $timestamps = false;
    protected $guarded = ['id'];

    public function stocks()
    {
        return $this->hasMany(ProductStock::class, 'vendor_id', 'id');
    }
}
