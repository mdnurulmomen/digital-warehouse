<?php

namespace App\Models;

use App\Models\Warhouse;
use Illuminate\Database\Eloquent\Model;

class WarhouseManager extends Model
{
    public function warhouse()
    {
        return $this->belongsTo(Warhouse::class, 'warhouse_id', 'id');
    }
}
