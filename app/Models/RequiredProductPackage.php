<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequiredProductPackage extends Model
{
    public $timestamps = false;
    protected $guarded = ['id'];

    public function package()
    {
    	return $this->belongsTo(PackagingPackage::class, 'packaging_package_id', 'id');
    }
}
