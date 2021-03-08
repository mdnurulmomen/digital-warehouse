<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductStock extends Model
{
    protected $guarded = ['id'];

    public function product()
    {
    	return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function variations()
    {
        return $this->hasMany(ProductVariationStock::class, 'product_stock_id', 'id');
    }

    public function addresses()
    {
        return $this->hasMany(WarehouseProduct::class, 'product_stock_id', 'id');
    }

    public function deleteStockVariations()
    {
        if ($this->variations()->count()) {
            
            foreach ($this->variations as $variation) {

                $this->decreaseSuccessorStockVariations($variation, $variation->stock_quantity);
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
                    
                    $existingStockVariation = $this->variations()->where('id', $stockVariation->id)->first();

                    if ($existingStockVariation->stock_quantity > $stockVariation->stock_quantity) {
                        
                        // decrease quantity
                        $difference = $existingStockVariation->stock_quantity - $stockVariation->stock_quantity;

                        $existingStockVariation->update([
                            'stock_quantity' => $stockVariation->stock_quantity,
                            'available_quantity' => ($existingStockVariation->available_quantity - $difference),
                        ]);

                        $this->decreaseSuccessorStockVariations($existingStockVariation, $difference);

                    }
                    else if ($existingStockVariation->stock_quantity < $stockVariation->stock_quantity) {
                        
                        // increase quantity
                        $difference = $stockVariation->stock_quantity - $existingStockVariation->stock_quantity;
                        
                        $existingStockVariation->update([
                            'stock_quantity' => $stockVariation->stock_quantity,
                            'available_quantity' => ($existingStockVariation->available_quantity + $difference),
                        ]);

                        $this->increaseSuccessorStockVariations($existingStockVariation, $difference);

                    }

                }

            }
            else {  // as usual / new stock
                
                foreach ($variations as $stockVariation) {
               
                    if (!empty($stockVariation->stock_quantity) && $stockVariation->stock_quantity > 0) {
                        
                        $variationLastAvailableValue = ProductVariation::find($stockVariation->id)->stocks->first()->available_quantity ?? 0;

                        $this->variations()->create([
                            'stock_quantity' => $stockVariation->stock_quantity,
                            'available_quantity' => ($variationLastAvailableValue + $stockVariation->stock_quantity),
                            'product_variation_id' => $stockVariation->id,
                            'product_stock_id' => $this->id,
                            'created_at' => now(),
                        ]);

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

    protected function setProductUnits($container = NULL, $product)
    {
        if ($container) {

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

    protected function updateParentContainer(WarehouseContainerStatus $warehouseExpectedContainer)
    {
        // $warehouseExpectedContainer = WarehouseContainerStatus::find($containerId);

        // all shelves are engaged
        if ($warehouseExpectedContainer->containerShelfStatuses->count()===$warehouseExpectedContainer->containerShelfStatuses()->where('engaged', 1)->count()) {
            
            $warehouseExpectedContainer->update([
                'engaged' => 1
            ]); 

        }
        // no shelf is engaged
        else if ($warehouseExpectedContainer->containerShelfStatuses->count()===$warehouseExpectedContainer->containerShelfStatuses()->where('engaged', 0)->count()) {
            
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
        if ($warehouseExpectedShelf->containerShelfUnitStatuses->count()===$warehouseExpectedShelf->containerShelfUnitStatuses()->where('engaged', 1)->count()) {
            
            $warehouseExpectedShelf->update([
                'engaged' => 1
            ]);

        }
        // no unit is engaged
        else if ($warehouseExpectedShelf->containerShelfUnitStatuses->count()===$warehouseExpectedShelf->containerShelfUnitStatuses()->where('engaged', 0)->count()) {
            
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
}
