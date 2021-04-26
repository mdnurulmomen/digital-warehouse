<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequiredProductSerial extends Model
{
    public $timestamps = false;
    
    protected $guarded = ['id'];

    public function serial()
    {
    	return $this->belongsTo(ProductSerial::class, 'product_serial_id', 'id');
    }
}
