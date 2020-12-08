<?php

namespace App\Models;

use App\Models\WarhouseStoragePreview;
use App\Models\WarhouseStorageFeature;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StorageType extends Model
{
    use SoftDeletes;

    public $timestamps = false;
    protected $guarded = ['id'];

    public function previews()
    {
    	return $this->hasMany(WarhouseStoragePreview::class, 'storage_type_id', 'id');
    }

    public function features()
    {
    	return $this->hasMany(WarhouseStorageFeature::class, 'storage_type_id', 'id');
    }
}
