<?php

namespace App\Models;

use Illuminate\Support\Facades\File;
use Illuminate\Database\Eloquent\Model;
use Intervention\Image\ImageManagerStatic as Image;

class Dispatch extends Model
{
    public $timestamps = false;
    protected $guarded = ['id'];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        // 'released_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function requisition()
    {
    	return $this->belongsTo(Requisition::class, 'requisition_id', 'id');
    }

    public function products()
    {
    	return $this->hasMany(ProductDispatch::class, 'dispatch_id', 'id');
    }

    public function delivery()
    {
        return $this->hasOne(ProductDelivery::class, 'dispatch_id', 'id');
    }

    public function return()
    {
        return $this->hasOne(ProductReturn::class, 'dispatch_id', 'id');
    }

    /**
     * Get the model who dispatched the requisition.
     */
    public function updater()
    {
        return $this->morphTo(__FUNCTION__, 'updater_type', 'updater_id');
    }

    /**
     * Set the user's first name.
     *
     * @param  string  $value
     * @return void
     */
    public function setDispatchProductsAttribute($dispatchingProducts = [])
    {
        if (count($dispatchingProducts)) {

            foreach ($dispatchingProducts as $dispatchingProduct) {
                
                $merchantExpectedProduct = MerchantProduct::find($dispatchingProduct->merchant_product_id);

                // $productAvailableQuantity = $merchantExpectedProduct->latestStock->available_quantity ?? 0;
                
                $productAvailableQuantity = $merchantExpectedProduct->available_quantity ?? 0;

                if ($productAvailableQuantity >= $dispatchingProduct->quantity) {
                    
                    /*
                    $dispatchedProduct = $merchantExpectedProduct->dispatches()->create([
                        'quantity' => $dispatchingProduct->quantity,
                        'has_variations' => $dispatchingProduct->has_variations ?? false,
                        'dispatch_id' => $this->id,
                    ]);
                    */

                    /*
                    $merchantExpectedProduct->latestStock->update([
                        'available_quantity' => $productAvailableQuantity - $dispatchingProduct->quantity 
                    ]);
                    */
                   
                    /*
                    $merchantExpectedProduct->update([
                        'available_quantity' => $productAvailableQuantity - $dispatchingProduct->quantity 
                    ]);
                    */
                   
                    $merchantExpectedProduct->decrement('available_quantity', $dispatchingProduct->quantity);
                   
                    if (! $merchantExpectedProduct->product->has_variations) {
                        
                        $this->deductProductStockQuantity($merchantExpectedProduct, $dispatchingProduct->quantity);

                    }

                    // dispatch variations
                    if ($merchantExpectedProduct->product->has_variations && $dispatchingProduct->has_variations) {
                        
                        foreach ($dispatchingProduct->variations as $variationToDispatch) {
                    
                            $merchantProductExpectedVariation = $merchantExpectedProduct->variations()->where('id', $variationToDispatch->merchant_product_variation_id)->first();

                            // $variationAvailableQuantity = $merchantProductExpectedVariation->latestStock->available_quantity ?? 0;
                            
                            $variationAvailableQuantity = $merchantProductExpectedVariation->available_quantity ?? 0;

                            if ($variationToDispatch->quantity <= $variationAvailableQuantity) {

                                /*
                                    $dispatchedProductVariation = $dispatchedProduct->variations()->create([
                                        'quantity' => $variationToDispatch->quantity,
                                        'product_variation_id' => $variationToDispatch->product_variation_id,
                                    ]);
                                */

                                /*
                                $merchantProductExpectedVariation->latestStock->update([
                                    'available_quantity' => $variationAvailableQuantity - $variationToDispatch->quantity 
                                ]);
                                */
                               
                                /*
                                $merchantProductExpectedVariation->update([
                                    'available_quantity' => $variationAvailableQuantity - $variationToDispatch->quantity 
                                ]);
                                */
                               
                                $merchantProductExpectedVariation->decrement('available_quantity', $variationToDispatch->quantity);
                               
                                $this->deductVariationStockQuantity($merchantProductExpectedVariation, $variationToDispatch->quantity);

                                // dispatching variation-serials
                                if ($variationToDispatch->has_serials && count($variationToDispatch->serials)==$variationToDispatch->quantity) {
                                     
                                    foreach ($variationToDispatch->serials as $variationSerialToDispatch) {

                                        $productVariationSerial = RequiredProductVariationSerial::where('id', $variationSerialToDispatch->id)->whereHas('serial', function($q){
                                            $q->where('has_dispatched', false);
                                        })->firstOrFail();

                                        if ($productVariationSerial) {
                                            
                                            $productVariationSerial->serial()->update([
                                                'has_dispatched' => true,
                                            ]);

                                        }

                                    }

                                } 
                                
                            }

                        }

                    }

                    // dispatch product-serials
                    if ($merchantExpectedProduct->product->has_serials && ! $merchantExpectedProduct->product->has_variations && count($dispatchingProduct->serials)==$dispatchingProduct->quantity) {
                                     
                        foreach ($dispatchingProduct->serials as $productSerialToDispatch) {
                            
                            $productSerial = RequiredProductSerial::where('id', $productSerialToDispatch->id)
                            ->whereHas('serial', function($q){
                                $q->where('has_dispatched', false);
                            })->firstOrFail();

                            if ($productSerial) {
                                
                                $productSerial->serial()->update([
                                    'has_dispatched' => true,
                                ]);

                            }

                        }

                    }

                    // Updating Product Addresses
                    if ($productAvailableQuantity==0 || $merchantExpectedProduct->available_quantity==0) {  // No more products available
                        
                        $merchantExpectedProduct->deleteOldAddresses();
                        // $merchantExpectedProduct->variations()->delete();

                    }
                    else if ($productAvailableQuantity > 0 && $merchantExpectedProduct->available_quantity > 0) {
                        
                        $merchantExpectedProduct->product_address = json_decode(json_encode($dispatchingProduct->addresses));

                    }

                }

                $expectedRequiredProduct = RequiredProduct::find($dispatchingProduct->id);

                if ($expectedRequiredProduct->packaging_service) {
                   
                    $expectedRequiredProduct->dispatchedPackage()->create([

                        'quantity' => $dispatchingProduct->dispatched_package_quantity,
                        'packaging_package_id' => $dispatchingProduct->dispatched_package->id,
                        
                    ]);

                }

            }

        }
    }

    /**
     * Set the user's first name.
     *
     * @param  string  $value
     * @return void
     */
    public function setDeliverRequisitionAttribute($delivery = false)
    {
        if ($delivery) {

            $imagePath = 'uploads/dispatch/';

            if(!File::isDirectory($imagePath)){
                File::makeDirectory($imagePath, 0777, true, true);
            }

            $img = Image::make($delivery->delivery_receipt)->resize(300, 300);
            $img->save($imagePath.'dispatch-'.$this->id.'.jpg');

            $this->delivery()->updateOrCreate(
                [ 'dispatch_id' => $this->id ],
                [ 
                    'delivery_price' => $delivery->delivery_price,
                    'receipt_preview' => $imagePath.'dispatch-'.$this->id.'.jpg', 
                ]
            );

        }
    }

    /*
        public function setCancelProductRequisitionAttribute($requiredProducts = [])
        {
            if (count($requiredProducts)) {
                
                foreach ($requiredProducts as $requiredProduct) {
                        
                    if ($requiredProduct->has_serials && ! $requiredProduct->has_variations) {
                        
                        foreach ($requiredProduct->serials as $requiredProductSerial) {

                            RequiredProductSerial::where('product_serial_id', $requiredProductSerial->product_serial_id)
                            ->where('required_product_id', $requiredProductSerial->required_product_id)
                            ->firstOrFail()
                            ->serial()
                            ->update([
                                'has_requisitions' => false,
                            ]);

                        }

                    }
                    else if ($requiredProduct->has_serials && $requiredProduct->has_variations) {
                        
                        foreach ($requiredProduct->variations as $requiredProductVariation) {
                            
                            foreach ($requiredProductVariation->serials as $requiredProductVariationSerial) {

                                RequiredProductVariationSerial::where('product_variation_serial_id', $requiredProductVariationSerial->product_variation_serial_id)
                                ->where('required_product_variation_id', $requiredProductVariationSerial->required_product_variation_id)
                                ->firstOrFail()
                                ->serial()
                                ->update([
                                    'has_requisitions' => false,
                                ]);

                            }

                        }

                    }

                }
                
            }
        }
    */

    /**
     * Set the user's first name.
     *
     * @param  string  $value
     * @return void
     */
    /*
    public function setReturnRequisitionAttribute($return = false)
    {
        if ($return) {

            $imagePath = 'uploads/dispatch/';

            if(!File::isDirectory($imagePath)){
                File::makeDirectory($imagePath, 0777, true, true);
            }

            $img = Image::make($return->agent_receipt)->resize(300, 300);
            $img->save($imagePath.$this->id.'.jpg');

            $this->return()->create([
                'receipt_preview' => $imagePath.$this->id.'.jpg',
            ]);

        }
    }
    */
   
    protected function deductProductStockQuantity(MerchantProduct $merchantExpectedProduct, $quantityToDeduct)
    {
        while ($quantityToDeduct > 0) {
            
            // $productStockToDeduct = $merchantExpectedProduct->oldestAvailableStock;
            
            /*
            $productStockToDeduct = $merchantExpectedProduct->stocks()
            ->where('available_quantity', '>', 0)
            ->whereHas('stock', function ($q) {
                $q->where('has_approval', 1);
            })
            ->oldest('id')
            ->first();
            */
           
            $productStockToDeduct = ProductStock::where('merchant_product_id', $merchantExpectedProduct->id)
            ->where('available_quantity', '>', 0)
            ->whereHas('stock', function ($q) {
                $q->where('has_approval', 1);
            })
            ->oldest('id')
            ->first();
                     

            // \Log::info('Merchant-Product id: '.$merchantExpectedProduct->id.'. Oldest product-stock id: '.$productStockToDeduct->id.'. Quantity to deduct is: '.$quantityToDeduct.'. But oldest product-stock available qty is :'.$productStockToDeduct->available_quantity);

            if ($productStockToDeduct->available_quantity >= $quantityToDeduct) { 

                $productStockToDeduct->decrement('available_quantity', $quantityToDeduct);
                // $merchantExpectedProduct->decrement('available_quantity', $quantityToDeduct);
                $quantityToDeduct = 0;

            }
            else if ($productStockToDeduct->available_quantity < $quantityToDeduct) {
                
                $currentQuantity = $productStockToDeduct->available_quantity;
                $productStockToDeduct->decrement('available_quantity', $currentQuantity);
                $quantityToDeduct -= $currentQuantity;

            }
            // stop loop
            else {

                $quantityToDeduct = 0;

            }

        }
    }

    protected function deductVariationStockQuantity(MerchantProductVariation $merchantProductExpectedVariation, $quantityToDeduct)
    {
        while ($quantityToDeduct > 0) {
            
            // $variationStockToDeduct = $merchantProductExpectedVariation->oldestAvailableStock;

            // \Log::debug($merchantProductExpectedVariation->oldestAvailableStock->toSql());

            /*
            $variationStockToDeduct = $merchantProductExpectedVariation->stocks()
            ->where('available_quantity', '>', 0)
            ->whereHas('productStock', function ($query) {
                $query->whereHas('stock', function ($q) {
                    $q->where('has_approval', 1);
                });
            })
            ->oldest('id')
            ->first();
            */
           
            $variationStockToDeduct = ProductVariationStock::where('merchant_product_variation_id', $merchantProductExpectedVariation->id)
            ->where('available_quantity', '>', 0)
            ->whereHas('productStock', function ($query) {
                $query->whereHas('stock', function ($q) {
                    $q->where('has_approval', 1);
                });
            })
            ->oldest('id')
            ->first();

            if ($variationStockToDeduct->available_quantity >= $quantityToDeduct) {                

                $variationStockToDeduct->decrement('available_quantity', $quantityToDeduct);
                $variationStockToDeduct->productStock()->decrement('available_quantity', $quantityToDeduct);

                // \Log::info('Product Stock id : '.$variationStockToDeduct->productStock->id.'. Quantity deducted is : '.$quantityToDeduct.'. Now Product Stock Available Quantity : '.$variationStockToDeduct->productStock->available_quantity);
                
                /*
                $variationStockToDeduct->productStock()->update([
                    'available_quantity' => $variationStockToDeduct->productStock->available_quantity - $quantityToDeduct,
                ]);
                */
                
                $quantityToDeduct = 0;

            }
            else if ($variationStockToDeduct->available_quantity < $quantityToDeduct) {
                
                $currentQuantity = $variationStockToDeduct->available_quantity;
                $quantityToDeduct -= $currentQuantity;
                
                $variationStockToDeduct->decrement('available_quantity', $currentQuantity);
                $variationStockToDeduct->productStock()->decrement('available_quantity', $currentQuantity);

                // \Log::info('Product Stock id : '.$variationStockToDeduct->productStock->id.'. Quantity deducted is : '.$currentQuantity.'. Now Product Stock Available Quantity : '.$variationStockToDeduct->productStock->available_quantity);

                /*
                $variationStockToDeduct->productStock()->update([
                    'available_quantity' => $variationStockToDeduct->productStock->available_quantity - $variationStockToDeduct->available_quantity,
                ]);
                */

            }
            // stop loop
            else {

                $quantityToDeduct = 0;

            }

            // \Log::info("End Of while loop");
            // \Log::info("");

        }
    }

}
