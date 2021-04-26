<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductVariationSerial extends Model
{
    use SoftDeletes;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    protected $guarded = ['id'];

    protected $casts = [
        'has_dispatched' => 'boolean',
        'has_requisitions' => 'boolean',
    ];

    public function required()
    {
        return $this->hasOne(RequiredProductVariationSerial::class, 'product_variation_serial_id', 'id');
    }
}
