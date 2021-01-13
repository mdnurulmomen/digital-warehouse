<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Requisition extends Model
{
    protected $guarded = ['id'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'status' => 'boolean',
    ];

    /**
     * Set the user's first name.
     *
     * @param  string  $value
     * @return void
     */
    public function setRequiredProductsAttribute($requiredProducts = [])
    {
    	if (count($requiredProducts)) {
    		
    		$requiredProducts = json_decode(json_encode($requiredProducts));

    		foreach ($requiredProducts as $requiredProduct) {
    			
    			$this->products()->create([
    				'product_id' => $requiredProduct->id,
    				'quantity' => $requiredProduct->quantity,
    			]);

    		}

    	}
    }

    public function products()
    {
    	return $this->hasMany(RequiredProduct::class, 'requisition_id', 'id');
    }
}
