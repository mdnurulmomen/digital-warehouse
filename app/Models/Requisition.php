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
    			
    			$requisitionedProduct = $this->products()->create([
    				'product_id' => $requiredProduct->id,
                    'quantity' => $requiredProduct->total_quantity,
    				'has_variations' => $requiredProduct->product->has_variations ?? false,
    			]);

                if ($requiredProduct->product->has_variations) {
                    
                    foreach ($requiredProduct->product->variations as $requiredProductVariation) {
                
                        $requisitionedProduct->variations()->create([
                            'product_variation_id' => $requiredProductVariation->id,
                            'quantity' => $requiredProductVariation->required_quantity,
                            'requisition_id' => $this->id,
                        ]);

                    }

                }

    		}

    	}
    }

    public function products()
    {
    	return $this->hasMany(RequiredProduct::class, 'requisition_id', 'id');
    }

    public function delivery()
    {
        return $this->hasOne(RequisitionAddress::class, 'requisition_id', 'id');
    }

    public function agent()
    {
        return $this->hasOne(RequisitionAgent::class, 'requisition_id', 'id');
    }
}
