<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public $timestamps = false;
    protected $guarded = ['id'];
    
    // protected $with = ['category', 'merchant', 'variations.variation', 'addresses.space.warehouseContainer.container'];

    protected $casts = [
        'has_serials' => 'boolean',
        'has_variations' => 'boolean',
    ];

    public function category()
    {
    	return $this->belongsTo(ProductCategory::class, 'product_category_id', 'id')->withTrashed();
    }

    public function merchant()
    {
    	return $this->belongsTo(Merchant::class, 'merchant_id', 'id');
    }

    public function variations()
    {
        return $this->hasMany(ProductVariation::class, 'product_id', 'id');
    }

    public function addresses()
    {
        return $this->hasMany(WarehouseProduct::class, 'product_id', 'id');
    }

    public function requests()
    {
        return $this->hasMany(RequiredProduct::class, 'product_id', 'id');
    }

    public function stocks()
    {
        return $this->hasMany(ProductStock::class, 'product_id', 'id')->latest();
    }

    public function latestStock()
    {
        return $this->hasOne(ProductStock::class, 'product_id', 'id')->latest();
    }

    public function nonDispatchedRequests()
    {
        return $this->hasMany(RequiredProduct::class, 'product_id', 'id')->whereHas('requisition', function ($query) {
            $query->where('status', 0);
        });
    }

    // immutable product
    public function getProductImmutabilityAttribute()
    {
        if ($this->requests()->count()) {
            return true;   
        }
        else if ($this->stocks()->count()) {
             return true;
        }

        return false;
    }

    /*
    public function dispatches()
    {
        return $this->hasMany(ProductDispatch::class, 'product_id', 'id');
    }
    */

    public function setProductVariationsAttribute($productNewVariations = array())
    {
        if (count($productNewVariations)) {

            if ($this->getProductImmutabilityAttribute()) {
                
                foreach ($this->variations() as $productVariation) {
                    
                    if (!$productVariation->variation_immutability) {

                        $productVariation->delete();

                    }

                }

            }
            else {

                $this->variations()->delete();

            }
            
            // $variations = json_decode(json_encode($variations));

            foreach ($productNewVariations as $productNewVariation) {

                // $existingVariation = $this->variations()->where('variation_id', $variation->variation->id)->first();

            /*
                if ($existingVariation) {
                    
                    $existingVariation->update([
                       'initial_quantity' => ($existingVariation->initial_quantity + $variation->initial_quantity), 
                       'available_quantity' => ($existingVariation->available_quantity + $variation->initial_quantity),
                       'price' => $variation->price, 
                    ]);

                }
            */

                // else {

                    if (empty($productNewVariation->variation_immutability) || is_null($productNewVariation->variation_immutability)) {
                        
                        $this->variations()->create([
                            'sku' => $productNewVariation->sku ?? $this->generateProductVariationSKU($this->sku, $productNewVariation->variation->id),
                            // 'initial_quantity' => $variation->initial_quantity,
                            // 'available_quantity' => $variation->initial_quantity,
                            'price' => $productNewVariation->price,
                            'variation_id' => $productNewVariation->variation->id,
                        ]);

                    }
                    else {

                        $this->variations()->where('variation_id', $productNewVariation->variation->id)->update([
                            'sku' => $productNewVariation->sku ?? $this->generateProductVariationSKU($this->sku, $productNewVariation->variation->id),
                            // 'initial_quantity' => $variation->initial_quantity,
                            // 'available_quantity' => $variation->initial_quantity,
                            'price' => $productNewVariation->price,
                        ]);

                    }

                // }

            }
        }
    }

    public function deleteOldAddresses()
    {
        if (count($this->addresses)) {
            
            foreach ($this->addresses as $productAddress) {
                
                $productAddress->space->update([
                    'engaged' => 0
                ]);

                if ($productAddress->space instanceof WarehouseContainerStatus) {

                    $this->updateChildShelves($productAddress->space, 0);

                }
                else if ($productAddress->space instanceof WarehouseContainerShelfStatus) {

                    $this->updateChildUnits($productAddress->space, 0);
                    $this->updateParentContainer($productAddress->space->parentContainer);

                }
                else if ($productAddress->space instanceof WarehouseContainerShelfUnitStatus) {

                    $this->updateParentShelf($productAddress->space->parentShelf);

                }

                $productAddress->delete();

            }

        }

        // $this->addresses()->delete();
    }

    public function setProductAddressAttribute($addresses = [])
    {
        if (count($addresses)) {
            
            // $spaces = json_decode(json_encode($spaces));
            
            foreach ($addresses as $dispatchedProductAddress) {
                
                if ($dispatchedProductAddress->type == "containers" && !empty($dispatchedProductAddress->released_containers)) {

                    $this->removeProductContainers($dispatchedProductAddress->released_containers);

                }
                else if ($dispatchedProductAddress->type == "shelves" && !empty($dispatchedProductAddress->container->released_shelves)) {
                    
                    $this->removeProductShelves($dispatchedProductAddress->container);
                    
                }
                else if ($dispatchedProductAddress->type == "units" && !empty($dispatchedProductAddress->container->shelf->released_units)) {
                    
                    $this->removeProductUnits($dispatchedProductAddress->container);
                    
                }

            }

        /*
            $this->deleteOldAddresses();

            foreach ($spaces as $space) {
                
                if ($space->type == "containers" && !empty($space->containers)) {
                
                    $this->setProductContainers($space->containers);

                }
                else if ($space->type == "shelves" && !empty($space->container->shelves)) {
                    
                    $this->setProductShelves($space->container);
                    
                }
                else if ($space->type == "units" && !empty($space->container->shelf->units)) {
                    
                    $this->setProductUnits($space->container);
                    
                }

            }
        */

        }
    }

    protected function removeProductContainers($containers = array())
    {
        if (count($containers)) {

            foreach ($containers as $container) {
                
                $warehouseExpectedContainer = WarehouseContainerStatus::find($container->id);

                $warehouseExpectedContainer->product()->delete();

                $warehouseExpectedContainer->update([
                    'engaged' => 0
                ]);

                $this->updateChildShelves($warehouseExpectedContainer, 0);

            }
        }
    }

    protected function removeProductShelves($container = NULL)
    {
        if ($container) {

            foreach ($container->released_shelves as $containerShelf) {
                
                $warehouseExpectedShelf = WarehouseContainerShelfStatus::find($containerShelf->id);

                $warehouseExpectedShelf->product()->delete();

                $warehouseExpectedShelf->update([
                    'engaged' => 0
                ]);

                $this->updateChildUnits($warehouseExpectedShelf, 0);

            }

            $this->updateParentContainer($warehouseExpectedShelf->parentContainer);

        }
    }

    protected function removeProductUnits($container = NULL)
    {
        if ($container) {

            foreach ($container->shelf->released_units as $containerShelfUnit) {
                
                $warehouseExpectedShelfUnit = WarehouseContainerShelfUnitStatus::find($containerShelfUnit->id);

                $warehouseExpectedShelfUnit->product()->delete();

                $warehouseExpectedShelfUnit->update([
                    'engaged' => 0
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

    /*

    protected function setProductContainers($containers = array())
    {
        if (count($containers)) {

            foreach ($containers as $container) {
                
                $warehouseExpectedContainer = WarehouseContainerStatus::find($container->id);

                $warehouseExpectedContainer->product()->create([
                    'product_id' => $this->id,
                ]);

                $warehouseExpectedContainer->update([
                    'engaged' => 1
                ]);

                $this->updateChildShelves($warehouseExpectedContainer, 1);

            }
        }
    }

    protected function setProductShelves($container = NULL)
    {
        if ($container) {

            foreach ($container->shelves as $containerShelf) {
                
                $warehouseExpectedShelf = WarehouseContainerShelfStatus::find($containerShelf->id);

                $warehouseExpectedShelf->product()->create([
                    'product_id' => $this->id,
                ]);

                $warehouseExpectedShelf->update([
                    'engaged' => 1
                ]);

                $this->updateChildUnits($warehouseExpectedShelf, 1);

            }

            $this->updateParentContainer($warehouseExpectedShelf->parentContainer);

        }
    }

    protected function setProductUnits($container = NULL)
    {
        if ($container) {

            foreach ($container->shelf->units as $containerShelfUnit) {
                
                $warehouseExpectedShelfUnit = WarehouseContainerShelfUnitStatus::find($containerShelfUnit->id);

                $warehouseExpectedShelfUnit->product()->create([
                    'product_id' => $this->id,
                ]);

                $warehouseExpectedShelfUnit->update([
                    'engaged' => 1
                ]);

            }

            // Parent Shelf
            $this->updateParentShelf($warehouseExpectedShelfUnit->parentShelf);

        }
    }
    */

    protected function generateProductVariationSKU($productSKU, $variationId)
    {
        return $productSKU.'VR'.$variationId;
    }
}
