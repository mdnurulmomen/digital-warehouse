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
    // protected $casts = [
        // 'status' => 'boolean',
    // ];

    /**
     * Set the user's first name.
     *
     * @param  string  $value
     * @return void
     */
    
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

    public function dispatch()
    {
        return $this->hasOne(Dispatch::class, 'requisition_id', 'id');
    }

    /**
     * Get the model who dispatched the requisition.
     */
    public function updater()
    {
        return $this->morphTo(__FUNCTION__, 'updater_type', 'updater_id');
    }

    public function setRequiredProductsAttribute($requiredProducts = [])
    {
    	if (count($requiredProducts)) {

            // when updating requisition
            if ($this->products()->count()) {
                
                foreach ($this->products as $requiredProduct) {
                    
                    if ($requiredProduct->has_serials && ! $requiredProduct->has_variations) {
                        
                        foreach ($requiredProduct->serials as $requiredProductSerial) {
                    
                            $requiredProductSerial->serial()->update([
                                'has_requisitions' => false,
                            ]);

                        }

                        $requiredProduct->serials()->delete();

                    }
                    else if ($requiredProduct->has_serials && $requiredProduct->has_variations) {
                        
                        foreach ($requiredProduct->variations as $requiredProductVariation) {
                            
                            foreach ($requiredProductVariation->serials as $requiredProductVariationSerial) {
                    
                                $requiredProductVariationSerial->serial()->update([
                                    'has_requisitions' => false,
                                ]);

                            }

                            $requiredProductVariation->serials()->delete();
                            
                        }

                        $requiredProduct->variations()->delete();

                    }

                    $requiredProduct->delete();

                }                    
                
            }                		

            foreach ($requiredProducts as $requiredProduct) {
    			
    			$requisitionedProduct = $this->products()->create([
    				'product_id' => $requiredProduct->id,
                    'quantity' => $requiredProduct->total_quantity,
    				'has_variations' => $requiredProduct->product->has_variations ?? false,
                    'has_serials' => $requiredProduct->product->has_serials ?? false,
    			]);

                if ($requiredProduct->product->has_serials && ! $requiredProduct->product->has_variations && count($requiredProduct->serials)) {

                    foreach (ProductSerial::whereIn('serial_no', $requiredProduct->serials)->get() as $productSerial) {
                        
                        $requisitionedProduct->serials()->create([
                            'product_serial_id' => $productSerial->id,
                        ]);

                        $productSerial->update([
                            'has_requisitions' => true,
                        ]);

                    }

                }

                if ($requiredProduct->product->has_variations) {
                    
                    foreach ($requiredProduct->product->variations as $requiredProductVariation) {
                
                        if (! empty($requiredProductVariation->required_quantity) && $requiredProductVariation->required_quantity > 0) {
                           
                            $requisitionedProductVariation = $requisitionedProduct->variations()->create([
                                'product_variation_id' => $requiredProductVariation->id,
                                'quantity' => $requiredProductVariation->required_quantity,
                                'has_serials' => $requiredProduct->product->has_serials,
                            ]);

                            if ($requiredProduct->product->has_serials && count($requiredProductVariation->required_serials)) {

                                foreach (ProductVariationSerial::whereIn('serial_no', $requiredProductVariation->required_serials)->get() as $productVariationSerial) {
                        
                                    $requisitionedProductVariation->serials()->create([
                                        'product_variation_serial_id' => $productVariationSerial->id,
                                    ]);

                                    $productVariationSerial->update([
                                        'has_requisitions' => true,
                                    ]);

                                }

                            }

                        }

                    }

                }

    		}

    	}
    }

    public function cancelProductRequisitions()
    {
        foreach ($this->products as $requiredProduct) {
                        
            if ($requiredProduct->has_serials && ! $requiredProduct->has_variations) {
                
                foreach ($requiredProduct->serials as $requiredProductSerial) {
                    
                    $requiredProductSerial->serial()->update([
                        'has_requisitions' => false,
                    ]);

                }

            }
            else if ($requiredProduct->has_serials && $requiredProduct->has_variations) {
                
                foreach ($requiredProduct->variations as $requiredProductVariation) {
                    
                    foreach ($requiredProductVariation->serials as $requiredProductVariationSerial) {
                    
                        $requiredProductVariationSerial->serial()->update([
                            'has_requisitions' => false,
                        ]);

                    }

                }

            }

        }
    }

}
