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
        'packaging_service' => 'boolean',
    ];

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

    public function cancellation()
    {
        return $this->hasOne(RequisitionCancelationReason::class, 'requisition_id', 'id');
    }

    /**
     * Get the model who dispatched the requisition.
     */
    public function creator()
    {
        return $this->morphTo(__FUNCTION__, 'creator_type', 'creator_id');
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
                    
                    if ($requiredProduct->merchantProduct->product->has_serials && ! $requiredProduct->merchantProduct->product->has_variations) {
                        
                        foreach ($requiredProduct->serials as $requiredProductSerial) {
                    
                            $requiredProductSerial->serial()->update([
                                'has_requisitions' => false,
                            ]);

                        }

                        $requiredProduct->serials()->delete();

                    }
                    else if ($requiredProduct->merchantProduct->product->has_serials && $requiredProduct->merchantProduct->product->has_variations) {
                        
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

            // new / usual
            foreach ($requiredProducts as $requiredProduct) {
    			
    			$requisitionedProduct = $this->products()->create([
    				'merchant_product_id' => $requiredProduct->merchant_product->id,
                    'quantity' => $requiredProduct->total_quantity,
    				// 'has_variations' => $requiredProduct->merchant_product->has_variations ?? false,
                    // 'has_serials' => $requiredProduct->merchant_product->has_serials ?? false,
                    'packaging_service' => $requiredProduct->packaging_service ?? false,
    			]);

                if ($requiredProduct->merchant_product->has_serials && ! $requiredProduct->merchant_product->has_variations && count($requiredProduct->serials)) {

                    if (! empty($requiredProduct->serials[0]->serial_no)) {
                        
                        foreach ($requiredProduct->serials as $requiredProductSerial) {
                            
                            $requisitionedProduct->serials()->create([
                                'product_serial_id' => $requiredProductSerial->id,
                            ]);

                            ProductSerial::findOrFail($requiredProductSerial->id)->update([
                                'has_requisitions' => true,
                            ]);

                        }

                    }
                    else {

                        foreach (ProductSerial::whereIn('serial_no', $requiredProduct->serials)->get() as $productSerial) {
                            
                            $requisitionedProduct->serials()->create([
                                'product_serial_id' => $productSerial->id,
                            ]);

                            $productSerial->update([
                                'has_requisitions' => true,
                            ]);

                        }

                    }

                }

                if ($requiredProduct->merchant_product->has_variations) {
                    
                    foreach ($requiredProduct->merchant_product->variations as $requiredProductVariation) {
                
                        if (! empty($requiredProductVariation->required_quantity) && $requiredProductVariation->required_quantity > 0) {
                           
                            $requisitionedProductVariation = $requisitionedProduct->variations()->create([
                                'merchant_product_variation_id' => $requiredProductVariation->id,
                                'quantity' => $requiredProductVariation->required_quantity,
                                // 'has_serials' => $requiredProduct->merchant_product->has_serials,
                            ]);

                            if ($requiredProduct->merchant_product->has_serials && count($requiredProductVariation->required_serials)) {

                                if (! empty($requiredProductVariation->required_serials[0]->serial_no)) {
                        
                                    foreach ($requiredProductVariation->required_serials as $requiredProductVariationSerial) {
                                        
                                        $requisitionedProductVariation->serials()->create([
                                            'product_variation_serial_id' => $requiredProductVariationSerial->id,
                                        ]);

                                        ProductVariationSerial::findOrFail($requiredProductVariationSerial->id)->update([
                                            'has_requisitions' => true,
                                        ]);

                                    }

                                }
                                else {

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

                // when packaging package is chosen by merchant
                if ($requisitionedProduct->packaging_service && ! empty($requiredProduct->preferred_package)) {
                    
                    $requisitionedProduct->preferredPackage()->create([
                        'packaging_package_id' => $requiredProduct->preferred_package->id
                    ]);

                }

    		}

    	}
    }

    public function cancelProductRequisitions()
    {
        foreach ($this->products as $requiredProduct) {
                        
            if ($requiredProduct->merchantProduct->product->has_serials && ! $requiredProduct->merchantProduct->product->has_variations) {
                
                foreach ($requiredProduct->serials as $requiredProductSerial) {
                    
                    $requiredProductSerial->serial()->update([
                        'has_requisitions' => false,
                    ]);

                }

            }
            else if ($requiredProduct->merchantProduct->product->has_serials && $requiredProduct->merchantProduct->product->has_variations) {
                
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
