<?php

namespace App\Models;

use App\Models\WarhouseStorageType;
use Illuminate\Database\Eloquent\Model;

class WarhouseStoragePreview extends Model
{
    public $timestamps = false;
    protected $guarded = ['id'];

    public function warhouseStorageType()
    {
    	return $this->belongsTo(WarhouseStorageType::class, 'warhouse_storage_type_id', 'id')->withTrashed();
    }
}
