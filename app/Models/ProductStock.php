<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductStock extends Model
{
    protected $guarded = ['id'];

    public function stock()
    {
    	return $this->belongsTo(Stock::class, 'stock_id', 'id');
    }

    public function merchantProduct()
    {
        return $this->belongsTo(MerchantProduct::class, 'merchant_product_id', 'id');
    }

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

    public function deleteStockVariations()
    {
        if ($this->variations()->count()) {
            
            foreach ($this->variations as $variation) {

                $this->decreaseSuccessorStockVariations($variation, $variation->stock_quantity);
                $this->deleteStockVariationSerials($variation);
                $variation->delete();

            }

        }
    }

    public function setStockVariationsAttribute($variations = [])
    {
        if (count($variations)) {
            
            // update / old stock
            if ($this->variations()->count()) {
                
                foreach ($variations as $stockVariation) {
                    
                    $variationExistingStock = $this->variations()->where('id', $stockVariation->id)->firstOrFail();

                    if ($variationExistingStock->stock_quantity > $stockVariation->stock_quantity) {
                        
                        // decrease quantity
                        $difference = $variationExistingStock->stock_quantity - $stockVariation->stock_quantity;

                        $variationExistingStock->update([
                            'stock_quantity' => $stockVariation->stock_quantity,
                            'available_quantity' => ($variationExistingStock->available_quantity - $difference),
                        ]);

                        $this->decreaseSuccessorStockVariations($variationExistingStock, $difference);

                    }
                    else if ($variationExistingStock->stock_quantity < $stockVariation->stock_quantity) {
                        
                        // increase quantity
                        $difference = $stockVariation->stock_quantity - $variationExistingStock->stock_quantity;
                        
                        $variationExistingStock->update([
                            'stock_quantity' => $stockVariation->stock_quantity,
                            'available_quantity' => ($variationExistingStock->available_quantity + $difference),
                        ]);

                        $this->increaseSuccessorStockVariations($variationExistingStock, $difference);

                    }

                    if ($variationExistingStock->has_serials) {
                        
                        $this->setProductVariationSerialNumbers($stockVariation->serials, $variationExistingStock);

                    }

                }

            }
            else {  // as usual / new stock
                
                foreach ($variations as $stockVariation) {
               
                    if (!empty($stockVariation->stock_quantity) && $stockVariation->stock_quantity > 0) {
                        
                        $variationLastAvailableValue = ProductVariation::findOrFail($stockVariation->id)->latestStock->available_quantity ?? 0;

                        $variationNewStock = $this->variations()->create([
                            'stock_quantity' => $stockVariation->stock_quantity,
                            'available_quantity' => ($variationLastAvailableValue + $stockVariation->stock_quantity),
                            'has_serials' => empty($stockVariation->serials) ? false : true,
                            'product_variation_id' => $stockVariation->id,
                            'product_stock_id' => $this->id,
                            'created_at' => now(),
                        ]);

                        if ($variationNewStock->has_serials) {
                        
                            $this->setProductVariationSerialNumbers($stockVariation->serials, $variationNewStock);
                            
                        }

                    }

                }

            }

        }

    }

    public function setStockAddresses($spaces = [], $product)
    {
        if (count($spaces)) {
            
            // $spaces = json_decode(json_encode($spaces));
            
            $this->deleteOldAddresses();

            foreach ($spaces as $space) {
                
                if ($space->type == "containers" && !empty($space->containers)) {
                
                    $this->setProductContainers($space->containers, $product);

                }
                else if ($space->type == "shelves" && !empty($space->container->shelves)) {
                    
                    $this->setProductShelves($space->container, $product);
                    
                }
                else if ($space->type == "units" && !empty($space->container->shelf->units)) {
                    
                    $this->setProductUnits($space->container, $product);
                    
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
                    [ 'serial_no' => $serial->serial_no, 'product_id' => $this->product_id ],
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
                    'engaged' => 0
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
        if ($this->serials()->count() && ! $this->serials()->where(function($q) { $q->where('has_requisitions', true)->orWhere('has_dispatched', true); })->exists()) {
            
            $this->serials()->forceDelete();

        }
    }

    protected function setProductContainers($containers = array(), $product)
    {
        if (count($containers)) {

            foreach ($containers as $container) {
                
                $warehouseExpectedContainer = WarehouseContainerStatus::find($container->id);

                if (!empty($warehouseExpectedContainer) && $warehouseExpectedContainer->engaged==0.0) {
                    
                    $warehouseExpectedContainer->product()->create([
                        'product_stock_id' => $this->id,
                        'product_id' => $product,
                    ]);

                    $warehouseExpectedContainer->update([
                        'engaged' => 1
                    ]);

                    $this->updateChildShelves($warehouseExpectedContainer, 1);

                }

            }
        }
    }

    protected function setProductShelves($container = NULL, $product)
    {
        if ($container) {

            if (count($container->shelves) === WarehouseContainerStatus::find($container->id)->containerShelfStatuses()->count()) {
                
                $this->setProductContainers([$container], $product);

            }
            else {

                foreach ($container->shelves as $containerShelf) {
                    
                    $warehouseExpectedShelf = WarehouseContainerShelfStatus::find($containerShelf->id);

                    if (!empty($warehouseExpectedShelf) && $warehouseExpectedShelf->engaged==0.0) {
                        
                        $warehouseExpectedShelf->product()->create([
                            'product_stock_id' => $this->id,
                            'product_id' => $product,
                        ]);

                        $warehouseExpectedShelf->update([
                            'engaged' => 1
                        ]);
                        
                        $this->updateChildUnits($warehouseExpectedShelf, 1);

                    }

                }

                $this->updateParentContainer($warehouseExpectedShelf->parentContainer);

            }

        }
    }

    protected function setProductUnits($container = NULL, $product)
    {
        if ($container) {

            if (count($container->shelf->units) === WarehouseContainerShelfStatus::find($container->shelf->id)->containerShelfUnitStatuses()->count()) {
                
                $container->{"shelves"} = [ $container->shelf, ];
                
                $this->setProductShelves($container, $product);

            }
            else {

                foreach ($container->shelf->units as $containerShelfUnit) {
                    
                    $warehouseExpectedShelfUnit = WarehouseContainerShelfUnitStatus::find($containerShelfUnit->id);

                    if (!empty($warehouseExpectedShelfUnit) && $warehouseExpectedShelfUnit->engaged==0.0) {
                        
                        $warehouseExpectedShelfUnit->product()->create([
                            'product_stock_id' => $this->id,
                            'product_id' => $product,
                        ]);

                        $warehouseExpectedShelfUnit->update([
                            'engaged' => 1
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

        // all shelves are engaged
        if ($warehouseExpectedContainer->containerShelfStatuses->count()===$warehouseExpectedContainer->containerShelfStatuses()->where('engaged', 1.0)->count()) {
            
            $warehouseExpectedContainer->update([
                'engaged' => 1
            ]); 

        }
        // no shelf is engaged
        else if ($warehouseExpectedContainer->containerShelfStatuses->count()===$warehouseExpectedContainer->containerShelfStatuses()->where('engaged', 0.0)->count()) {
            
            $warehouseExpectedContainer->update([
                'engaged' => 0
            ]); 

        }
        else {

            // partially engaged
            $warehouseExpectedContainer->update([
                'engaged' => 0.5
            ]);

        }

    }

    protected function updateParentShelf(WarehouseContainerShelfStatus $warehouseExpectedShelf)
    {
        // Related Shelf
        // $warehouseExpectedShelf = WarehouseContainerShelfStatus::find($container->shelf->id);

        // all units are engaged
        if ($warehouseExpectedShelf->containerShelfUnitStatuses->count()===$warehouseExpectedShelf->containerShelfUnitStatuses()->where('engaged', 1.0)->count()) {
            
            $warehouseExpectedShelf->update([
                'engaged' => 1
            ]);

        }
        // no unit is engaged
        else if ($warehouseExpectedShelf->containerShelfUnitStatuses->count()===$warehouseExpectedShelf->containerShelfUnitStatuses()->where('engaged', 0.0)->count()) {
            
            $warehouseExpectedShelf->update([
                'engaged' => 0
            ]);

        }
        else {

            // partially engaged
            $warehouseExpectedShelf->update([
                'engaged' => 0.5
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
                    'engaged' => $newValue
                ]);

                $this->updateChildUnits($containerShelf, $newValue);

            }

        }
    }

    protected function updateChildUnits(WarehouseContainerShelfStatus $shelf, $newValue)
    {
        if ($shelf->containerShelfUnitStatuses->count()) {
            
            $shelf->containerShelfUnitStatuses()->update([
                'engaged' => $newValue
            ]);

        }
    }

    protected function increaseSuccessorStockVariations(ProductVariationStock $variationToUpdate, $amount)
    {
        ProductVariationStock::where('product_variation_id', $variationToUpdate->product_variation_id)->where('id', '>', $variationToUpdate->id)->increment('available_quantity', $amount);
    }

    protected function decreaseSuccessorStockVariations(ProductVariationStock $variationToUpdate, $amount)
    {
        ProductVariationStock::where('product_variation_id', $variationToUpdate->product_variation_id)->where('id', '>', $variationToUpdate->id)->decrement('available_quantity', $amount);
    }

    protected function setProductVariationSerialNumbers($serials, ProductVariationStock $productVariationStock) 
    {
        if (count($serials)) {
            
            // deleting initially
            $productVariationStock->serials()->delete();

            foreach ($serials as $serial) {
                
                $productVariationStock->serials()->withTrashed()->updateOrCreate(
                    [ 'serial_no' => $serial->serial_no, 'product_variation_id' => $productVariationStock->product_variation_id ],
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
        if ($productVariationStock->serials()->count() && ! $productVariationStock->serials()->where(function($q) { $q->where('has_requisitions', true)->orWhere('has_dispatched', true); })->exists()) {
            
            $productVariationStock->serials()->forceDelete();

        }
    }

}
