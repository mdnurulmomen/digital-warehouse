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
        return $this->hasMany(ProductVariationStock::class, 'product_stock_id', 'id')->latest();
    }

    public function addresses()
    {
        return $this->hasMany(WarehouseProduct::class, 'product_stock_id', 'id');
    }

    public function setStockVariations($variations = [], $product)
    {
        if (count($variations)) {
           
            $this->variations()->delete();

            foreach ($variations as $stockVariation) {
               
                if (!empty($stockVariation->stock_quantity) && $stockVariation->stock_quantity > 0) {
                    
                    $this->variations()->create([
                        'stock_quantity' => $stockVariation->stock_quantity,
                        'available_quantity' => $this->variations()->where('product_variation_id', $stockVariation->id)->first()->available_quantity ?? 0 + $stockVariation->stock_quantity,
                        'product_variation_id' => $stockVariation->id,
                        'product_stock_id' => $this->id,
                    ]);

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

    protected function setProductShelves($container = NULL, $product)
    {
        if ($container) {

            foreach ($container->shelves as $containerShelf) {
                
                $warehouseExpectedShelf = WarehouseContainerShelfStatus::find($containerShelf->id);

                $warehouseExpectedShelf->product()->create([
                    'product_stock_id' => $this->id,
                    'product_id' => $product,
                ]);

                $warehouseExpectedShelf->update([
                    'engaged' => 1
                ]);

                $this->updateChildUnits($warehouseExpectedShelf, 1);

            }

            $this->updateParentContainer($warehouseExpectedShelf->parentContainer);

        }
    }

    protected function setProductUnits($container = NULL, $product)
    {
        if ($container) {

            foreach ($container->shelf->units as $containerShelfUnit) {
                
                $warehouseExpectedShelfUnit = WarehouseContainerShelfUnitStatus::find($containerShelfUnit->id);

                $warehouseExpectedShelfUnit->product()->create([
                    'product_stock_id' => $this->id,
                    'product_id' => $product,
                ]);

                $warehouseExpectedShelfUnit->update([
                    'engaged' => 1
                ]);

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
}
