<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductStock extends Model
{
    use SoftDeletes;

    public $timestamps = false;

    protected $guarded = ['id'];

    public function variations()
    {
        return $this->hasMany(ProductVariationStock::class, 'product_stock_id', 'id');
    }

    public function addresses()
    {
        return $this->hasMany(WarehouseProduct::class, 'product_stock_id', 'id');
    }    

    public function serials()
    {
        return $this->hasMany(ProductSerial::class, 'product_stock_id', 'id');
    }

    public function stock()
    {
        return $this->belongsTo(Stock::class, 'stock_id', 'id');
    }

    public function merchantProduct()
    {
        return $this->belongsTo(MerchantProduct::class, 'merchant_product_id', 'id');
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id', 'id');
    }

    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id', 'id');
    }

    public function deleteStockVariations()
    {
        if ($this->variations->count()) {
            
            foreach ($this->variations as $variation) {

                MerchantProductVariation::findOrFail($variation->merchant_product_variation_id)->decrement('available_quantity', $variation->stock_quantity);

                // $this->decreaseSuccessorStockVariations($variation, $variation->stock_quantity);
                $this->deleteStockVariationSerials($variation);
                $variation->forceDelete();

            }

        }
    }

    public function setStockVariationsAttribute($variations = [])
    {
        if (count($variations)) {
            
            $stockHasApproval = $this->stock->has_approval;

            // update / old stock
            if ($this->variations->count()) {

                $this->variations()->delete();  // primarily deleting variations
                
                foreach ($variations as $stockVariation) {
                    
                    // $merchantProductExpectedVariation = MerchantProductVariation::findOrFail($stockVariation->merchant_product_variation_id);

                    $variationExistingStock = $this->variations()->withTrashed()->where('merchant_product_variation_id', $stockVariation->merchant_product_variation_id)->first();

                    $merchantProductExpectedVariation = $variationExistingStock->merchantProductVariation;

                    /*
                    // decreasing quantity
                    if (! empty($variationExistingStock) && $variationExistingStock->stock_quantity > $stockVariation->stock_quantity) {
                        
                        $difference = $variationExistingStock->stock_quantity - $stockVariation->stock_quantity;

                        $variationExistingStock->update([
                            'stock_quantity' => $stockVariation->stock_quantity,
                            'available_quantity' => ($variationExistingStock->available_quantity - $difference),
                            'unit_buying_price' => $stockVariation->unit_buying_price ?? $variationExistingStock->merchantProductVariation->selling_price ?? 0,
                            'merchant_product_variation_id' => $stockVariation->merchant_product_variation_id,
                        ]);

                        $this->decreaseSuccessorStockVariations($variationExistingStock, $difference);

                        $merchantProductExpectedVariation->decrement('available_quantity', $difference);

                    }
                    // increasing quantity
                    else if (! empty($variationExistingStock) && $variationExistingStock->stock_quantity < $stockVariation->stock_quantity) {
                        
                        // increase quantity
                        $difference = $stockVariation->stock_quantity - $variationExistingStock->stock_quantity;
                        
                        $variationExistingStock->update([
                            'stock_quantity' => $stockVariation->stock_quantity,
                            'available_quantity' => ($variationExistingStock->available_quantity + $difference), 
                            'unit_buying_price' => $stockVariation->unit_buying_price ?? $variationExistingStock->merchantProductVariation->selling_price ?? 0,
                            'merchant_product_variation_id' => $stockVariation->merchant_product_variation_id,
                        ]);

                        $this->increaseSuccessorStockVariations($variationExistingStock, $difference);

                        $merchantProductExpectedVariation->increment('available_quantity', $difference);

                    }
                    // same quantity
                    else if (! empty($variationExistingStock) && $variationExistingStock->stock_quantity == $stockVariation->stock_quantity) {
                        
                        $variationExistingStock->update([
                            'unit_buying_price' => $stockVariation->unit_buying_price ?? $variationExistingStock->merchantProductVariation->selling_price ?? 0,
                        ]);

                    }
                    // adding existing variation new stock
                    else if(empty($variationExistingStock)) {

                        $productVariationStock = $this->variations()->create([
                            'stock_quantity' => $stockVariation->stock_quantity,
                            'available_quantity' => $stockVariation->stock_quantity, 
                            'unit_buying_price' => $stockVariation->unit_buying_price ?? $variationExistingStock->merchantProductVariation->selling_price ?? 0,
                            'merchant_product_variation_id' => $stockVariation->merchant_product_variation_id,
                        ]);

                        $merchantProductExpectedVariation->increment('available_quantity', $stockVariation->stock_quantity);

                    }
                    */
                   
                    // decreasing quantity if available is more than or equal to expected quantity
                    if (! empty($variationExistingStock) && $variationExistingStock->stock_quantity > $stockVariation->stock_quantity && $variationExistingStock->available_quantity >= $stockVariation->stock_quantity) {
                        
                        $difference = $variationExistingStock->available_quantity - $stockVariation->stock_quantity;

                        $variationExistingStock->update([
                            'stock_quantity' => $stockVariation->stock_quantity,
                            'available_quantity' => ($variationExistingStock->available_quantity - $difference),
                            'unit_buying_price' => $stockVariation->unit_buying_price ?? $variationExistingStock->merchantProductVariation->selling_price ?? 0,
                            'manufactured_at' => $stockVariation->manufactured_at ?? NULL,
                            'expired_at' => $stockVariation->expired_at ?? NULL,
                            'merchant_product_variation_id' => $stockVariation->merchant_product_variation_id,
                            'deleted_at' => NULL
                        ]);

                        // $this->decreaseSuccessorStockVariations($variationExistingStock, $difference);

                        if ($stockHasApproval) {
                            
                            $merchantProductExpectedVariation->decrement('available_quantity', $difference);
                        
                        }
                        else {

                            $merchantProductExpectedVariation->increment('available_quantity', $stockVariation->stock_quantity);

                        }

                    }

                    // increasing quantity
                    else if (! empty($variationExistingStock) && $variationExistingStock->stock_quantity < $stockVariation->stock_quantity) {
                        
                        // increase quantity
                        $difference = $stockVariation->stock_quantity - $variationExistingStock->stock_quantity;
                        
                        $variationExistingStock->update([
                            'stock_quantity' => $stockVariation->stock_quantity,
                            'available_quantity' => ($variationExistingStock->available_quantity + $difference), 
                            'unit_buying_price' => $stockVariation->unit_buying_price ?? $variationExistingStock->merchantProductVariation->selling_price ?? 0,
                            'manufactured_at' => $stockVariation->manufactured_at ?? NULL,
                            'expired_at' => $stockVariation->expired_at ?? NULL,
                            'merchant_product_variation_id' => $stockVariation->merchant_product_variation_id, 
                            'deleted_at' => NULL
                        ]);

                        // $this->increaseSuccessorStockVariations($variationExistingStock, $difference);

                        if ($stockHasApproval) {    // already approved stock / not
                            
                            $merchantProductExpectedVariation->increment('available_quantity', $difference);

                        }
                        else {

                            $merchantProductExpectedVariation->increment('available_quantity', $stockVariation->stock_quantity);

                        }

                    }

                    // same quantity, may be only approving
                    else if (! empty($variationExistingStock) && $variationExistingStock->stock_quantity == $stockVariation->stock_quantity) {
                        
                        $variationExistingStock->update([
                            'unit_buying_price' => $stockVariation->unit_buying_price ?? $variationExistingStock->merchantProductVariation->selling_price ?? 0,
                            'manufactured_at' => $stockVariation->manufactured_at ?? NULL,
                            'expired_at' => $stockVariation->expired_at ?? NULL,
                            'deleted_at' => NULL
                        ]);

                        if (! $stockHasApproval) {
                            
                            $merchantProductExpectedVariation->increment('available_quantity', $difference);

                        }

                    }

                    // adding existing variation new stock
                    else if(empty($variationExistingStock)) {

                        $productVariationStock = $this->variations()->create([
                            'stock_code' => $stockVariation->stock_code ?? ($this->stock_code.'V'.$stockVariation->merchant_product_variation_id),
                            'stock_quantity' => $stockVariation->stock_quantity,
                            'available_quantity' => $stockVariation->stock_quantity, 
                            'unit_buying_price' => $stockVariation->unit_buying_price ?? $variationExistingStock->merchantProductVariation->selling_price ?? 0,
                            'manufactured_at' => $stockVariation->manufactured_at ?? NULL,
                            'expired_at' => $stockVariation->expired_at ?? NULL,
                            'merchant_product_variation_id' => $stockVariation->merchant_product_variation_id,
                        ]);

                        if ($stockHasApproval) {
                            
                            $merchantProductExpectedVariation->increment('available_quantity', $stockVariation->stock_quantity);

                        }

                    }
    
                    if ($this->merchantProduct->product->has_serials) {
                        
                        $this->setProductVariationSerialNumbers($stockVariation->serials, $variationExistingStock ?? $productVariationStock);                   

                    }

                }

                // Finally deleting still deleted variation-stocks
                foreach ($this->variations()->onlyTrashed()->get() as $trashedVariationStock) {
                    
                    if ($trashedVariationStock->stock_quantity == $trashedVariationStock->available_quantity && ! $trashedVariationStock->serials()->where(function ($query) {$query->where('has_requisitions', 1)->orWhere('has_dispatched', 1);})->exists()) {
                            
                        $trashedVariationStock->serials()->forceDelete();
                        $trashedVariationStock->forceDelete();   

                    }

                }

            }
            else {  // as usual / new stock
                
                foreach ($variations as $stockVariation) {
               
                    if (!empty($stockVariation->stock_quantity) && $stockVariation->stock_quantity > 0) {
                        
                        $merchantProductExpectedVariation = MerchantProductVariation::findOrFail($stockVariation->id);
                        
                        // $variationLastAvailableValue = /*MerchantProductVariation::findOrFail($stockVariation->id)->latestStock->available_quantity*/ $merchantProductExpectedVariation->available_quantity ?? 0;

                        $variationNewStock = $this->variations()->create([
                            'stock_code' => $stockVariation->stock_code ?? ($this->stock_code.'V'.$stockVariation->id),
                            'stock_quantity' => $stockVariation->stock_quantity,
                            'available_quantity' => $stockVariation->stock_quantity, 
                            'unit_buying_price' => $stockVariation->unit_buying_price ?? $merchantProductExpectedVariation->selling_price ?? 0,
                            // 'has_serials' => empty($stockVariation->serials) ? false : true,
                            'manufactured_at' => $stockVariation->manufactured_at ?? NULL,
                            'expired_at' => $stockVariation->expired_at ?? NULL,
                            'merchant_product_variation_id' => $stockVariation->id,
                            // 'product_stock_id' => $this->id,
                            // 'created_at' => now(),
                        ]);

                        if ($stockHasApproval) {
                            
                            $merchantProductExpectedVariation->increment('available_quantity', $stockVariation->stock_quantity);

                        }

                        if ($this->merchantProduct->product->has_serials) {
                            
                            $this->setProductVariationSerialNumbers($stockVariation->serials, $variationNewStock);

                        }

                    }

                }

            }

        }

    }

    public function setStockAddresses($spaces = [], $merchantProduct)
    {
        if (count($spaces)) {
            
            // $spaces = json_decode(json_encode($spaces));
            
            $this->deleteOldAddresses();

            foreach ($spaces as $space) {
                
                if ($space->type == "containers" && !empty($space->containers)) {
                
                    $this->setProductContainers($space->containers, $merchantProduct);

                }
                else if ($space->type == "shelves" && !empty($space->container->shelves)) {
                    
                    $this->setProductShelves($space->container, $merchantProduct);
                    
                }
                else if ($space->type == "units" && !empty($space->container->shelf->units)) {
                    
                    $this->setProductUnits($space->container, $merchantProduct);
                    
                }

            }

        }
    }

    public function setStockSerialsAttribute($serials) 
    {
        if (count($serials)) {
            
            // deleting initially
            $this->serials()->delete();

            foreach ($serials as $serial) {
                
                $this->serials()->withTrashed()->updateOrCreate(
                    [ 'serial_no' => $serial->serial_no, 'merchant_product_id' => $this->merchant_product_id ],
                    [ 'deleted_at' => NULL ]
                );

            }

            // deleting still deleted serials
            foreach ($this->serials()->onlyTrashed()->get() as $productTrashedSerial) {
                
                if (! $productTrashedSerial->has_requisitions && ! $productTrashedSerial->has_dispatched) {
                    
                    $productTrashedSerial->forceDelete();

                }

            }

        }
    }

    public function deleteOldAddresses()
    {
        if (count($this->addresses)) {
            
            foreach ($this->addresses as $stockAddress) {
                
                $stockAddress->space->update([
                    'occupied' => 0
                ]);

                if ($stockAddress->space instanceof WarehouseContainerStatus) {

                    $this->updateChildShelves($stockAddress->space, 0);

                }
                else if ($stockAddress->space instanceof WarehouseContainerShelfStatus) {

                    $this->updateChildUnits($stockAddress->space, 0);
                    $this->updateParentContainer($stockAddress->space->parentContainer);

                }
                else if ($stockAddress->space instanceof WarehouseContainerShelfUnitStatus) {

                    $this->updateParentShelf($stockAddress->space->parentShelf);

                }

                $stockAddress->delete();

            }

        }

        // $this->addresses()->delete();
    }

    public function deleteStockSerials()
    {
        if ($this->serials->count() && ! $this->serials()->where(function($q) { $q->where('has_requisitions', 1)->orWhere('has_dispatched', 1); })->exists()) {
            
            $this->serials()->forceDelete();

        }
    }

    protected function setProductContainers($containers = array(), $merchantProduct)
    {
        if (count($containers)) {

            foreach ($containers as $container) {
                
                $warehouseExpectedContainer = WarehouseContainerStatus::find($container->id);

                if (! empty($warehouseExpectedContainer) /*&& $warehouseExpectedContainer->occupied==0.0*/) {
                    
                    $warehouseExpectedContainer->products()->create([
                        'product_stock_id' => $this->id,
                        'merchant_product_id' => $merchantProduct,
                    ]);

                    $warehouseExpectedContainer->update([
                        'occupied' => 1
                    ]);

                    $this->updateChildShelves($warehouseExpectedContainer, 1);

                }

            }
        }
    }

    protected function setProductShelves($container = NULL, $merchantProduct)
    {
        if ($container) {

            if (count($container->shelves) === WarehouseContainerStatus::find($container->id)->containerShelfStatuses->count()) {
                
                $this->setProductContainers([ $container ], $merchantProduct);

            }
            else {

                foreach ($container->shelves as $containerShelf) {
                    
                    $warehouseExpectedShelf = WarehouseContainerShelfStatus::find($containerShelf->id);

                    if (! empty($warehouseExpectedShelf) /*&& $warehouseExpectedShelf->occupied==0.0*/) {
                        
                        $warehouseExpectedShelf->products()->create([
                            'product_stock_id' => $this->id,
                            'merchant_product_id' => $merchantProduct,
                        ]);

                        $warehouseExpectedShelf->update([
                            'occupied' => 1
                        ]);
                        
                        $this->updateChildUnits($warehouseExpectedShelf, 1);

                    }

                }

                $this->updateParentContainer($warehouseExpectedShelf->parentContainer);

            }

        }
    }

    protected function setProductUnits($container = NULL, $merchantProduct)
    {
        if ($container) {

            if (count($container->shelf->units) === WarehouseContainerShelfStatus::find($container->shelf->id)->containerShelfUnitStatuses->count()) {
                
                $container->{"shelves"} = [ $container->shelf, ];
                
                $this->setProductShelves($container, $merchantProduct);

            }
            else {

                foreach ($container->shelf->units as $containerShelfUnit) {
                    
                    $warehouseExpectedShelfUnit = WarehouseContainerShelfUnitStatus::find($containerShelfUnit->id);

                    if (! empty($warehouseExpectedShelfUnit) /*&& $warehouseExpectedShelfUnit->occupied==0.0*/) {
                        
                        $warehouseExpectedShelfUnit->products()->create([
                            'product_stock_id' => $this->id,
                            'merchant_product_id' => $merchantProduct,
                        ]);

                        $warehouseExpectedShelfUnit->update([
                            'occupied' => 1
                        ]);

                    }

                }

                // Parent Shelf
                $this->updateParentShelf($warehouseExpectedShelfUnit->parentShelf);

            }

        }
    }

    protected function updateParentContainer(WarehouseContainerStatus $warehouseExpectedContainer)
    {
        // $warehouseExpectedContainer = WarehouseContainerStatus::find($containerId);

        // all shelves are occupied
        if ($warehouseExpectedContainer->containerShelfStatuses->count()===$warehouseExpectedContainer->containerShelfStatuses()->where('occupied', 1.0)->count()) {
            
            $warehouseExpectedContainer->update([
                'occupied' => 1
            ]); 

        }
        // no shelf is occupied
        else if ($warehouseExpectedContainer->containerShelfStatuses->count()===$warehouseExpectedContainer->containerShelfStatuses()->where('occupied', 0.0)->count()) {
            
            $warehouseExpectedContainer->update([
                'occupied' => 0
            ]); 

        }
        else {

            // partially occupied
            $warehouseExpectedContainer->update([
                'occupied' => 0.5
            ]);

        }

    }

    protected function updateParentShelf(WarehouseContainerShelfStatus $warehouseExpectedShelf)
    {
        // Related Shelf
        // $warehouseExpectedShelf = WarehouseContainerShelfStatus::find($container->shelf->id);

        // all units are occupied
        if ($warehouseExpectedShelf->containerShelfUnitStatuses->count()===$warehouseExpectedShelf->containerShelfUnitStatuses()->where('occupied', 1.0)->count()) {
            
            $warehouseExpectedShelf->update([
                'occupied' => 1
            ]);

        }
        // no unit is occupied
        else if ($warehouseExpectedShelf->containerShelfUnitStatuses->count()===$warehouseExpectedShelf->containerShelfUnitStatuses()->where('occupied', 0.0)->count()) {
            
            $warehouseExpectedShelf->update([
                'occupied' => 0
            ]);

        }
        else {

            // partially occupied
            $warehouseExpectedShelf->update([
                'occupied' => 0.5
            ]);

        }

        // parent Container
        $this->updateParentContainer($warehouseExpectedShelf->parentContainer);

    }

    protected function updateChildShelves(WarehouseContainerStatus $container, $newValue)
    {
        if ($container->containerShelfStatuses->count()) {
            
            foreach ($container->containerShelfStatuses as $containerShelf) {
               
                $containerShelf->update([
                    'occupied' => $newValue
                ]);

                $this->updateChildUnits($containerShelf, $newValue);

            }

        }
    }

    protected function updateChildUnits(WarehouseContainerShelfStatus $shelf, $newValue)
    {
        if ($shelf->containerShelfUnitStatuses->count()) {
            
            $shelf->containerShelfUnitStatuses()->update([
                'occupied' => $newValue
            ]);

        }
    }

    protected function increaseSuccessorStockVariations(ProductVariationStock $variationToUpdate, $amount)
    {
        ProductVariationStock::where('merchant_product_variation_id', $variationToUpdate->merchant_product_variation_id)->where('id', '>', $variationToUpdate->id)->increment('available_quantity', $amount);
    }

    protected function decreaseSuccessorStockVariations(ProductVariationStock $variationToUpdate, $amount)
    {
        ProductVariationStock::where('merchant_product_variation_id', $variationToUpdate->merchant_product_variation_id)->where('id', '>', $variationToUpdate->id)->decrement('available_quantity', $amount);
    }

    protected function setProductVariationSerialNumbers($serials, ProductVariationStock $productVariationStock) 
    {
        if (count($serials) && $this->merchantProduct->product->has_serials) {
            
            // deleting initially
            $productVariationStock->serials()->delete();

            foreach ($serials as $serial) {
                
                $productVariationStock->serials()->withTrashed()->updateOrCreate(
                    [ 'serial_no' => $serial->serial_no, 'merchant_product_variation_id' => $productVariationStock->merchant_product_variation_id ],
                    [ 'deleted_at' => NULL ]
                );

            }

            // deleting still deleted serials
            foreach ($productVariationStock->serials()->onlyTrashed()->get() as $variationTrashedSerial) {
                
                if (! $variationTrashedSerial->has_requisitions && ! $variationTrashedSerial->has_dispatched) {
                    
                    $variationTrashedSerial->forceDelete();

                }

            }

        }
    }

    protected function deleteStockVariationSerials(ProductVariationStock $productVariationStock)
    {
        if ($productVariationStock->serials->count() && ! $productVariationStock->serials()->where(function($q) { $q->where('has_requisitions', 1)->orWhere('has_dispatched', 1); })->exists()) {
            
            $productVariationStock->serials()->forceDelete();

        }
    }

}
