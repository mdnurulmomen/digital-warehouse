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
        'released_at' => 'datetime',
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
     * Set the user's first name.
     *
     * @param  string  $value
     * @return void
     */
    public function setDispatchProductsAttribute($dispatchingProducts = [])
    {
        if (count($dispatchingProducts)) {

            foreach ($dispatchingProducts as $productToDispatch) {
                
                $expectedProduct = Product::find($productToDispatch->product_id);

                $productAvailableQuantity = $expectedProduct->stocks->first()->available_quantity ?? 0;

                if ($productAvailableQuantity >= $productToDispatch->quantity) {
                    
                /*
                    $dispatchedProduct = $expectedProduct->dispatches()->create([
                        'quantity' => $productToDispatch->quantity,
                        'has_variations' => $productToDispatch->has_variations ?? false,
                        'dispatch_id' => $this->id,
                    ]);
                */

                    $expectedProduct->stocks->first()->update([
                        'available_quantity' => $productAvailableQuantity - $productToDispatch->quantity 
                    ]);

                    if ($expectedProduct->has_variations && $productToDispatch->has_variations) {
                        
                        foreach ($productToDispatch->variations as $variationToDispatch) {
                    
                            $productExpectedVariation = $expectedProduct->variations()->where('id', $variationToDispatch->product_variation_id)->first();

                            $variationAvailableQuantity = $productExpectedVariation->stocks->first()->available_quantity ?? 0;


                            if ($variationToDispatch->quantity <= $variationAvailableQuantity) {

                            /*
                                $dispatchedProductVariation = $dispatchedProduct->variations()->create([
                                    'quantity' => $variationToDispatch->quantity,
                                    'product_variation_id' => $variationToDispatch->product_variation_id,
                                ]);
                            */

                                $productExpectedVariation->stocks->first()->update([
                                    'available_quantity' => $variationAvailableQuantity - $variationToDispatch->quantity 
                                ]);   
                                
                            }

                        }

                    }

                    // Updating Product Addresses
                    if ($productAvailableQuantity==0) {  // No more products available
                        
                        $expectedProduct->deleteOldAddresses();
                        // $expectedProduct->variations()->delete();

                    }
                    else if ($productAvailableQuantity > 0) {
                        
                        $expectedProduct->product_address = json_decode(json_encode($productToDispatch->addresses));

                    }

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

            $this->delivery()->create([
                // 'destination_name' => $delivery->address,
                'delivery_price' => $delivery->delivery_price,
                'receipt_preview' => $imagePath.'dispatch-'.$this->id.'.jpg',
            ]);

        }
    }

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
}
