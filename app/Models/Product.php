<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public $timestamps = false;
    protected $guarded = ['id'];
    
    // protected $with = ['category', 'merchant', 'variations.variation', 'addresses.space.warehouseContainer.container'];

    protected $casts = [
        'has_variations' => 'boolean'
    ];

    public function category()
    {
    	return $this->belongsTo(ProductCategory::class, 'product_category_id', 'id');
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

    public function nonDispatchedRequests()
    {
        return $this->hasMany(RequiredProduct::class, 'product_id', 'id')->whereHas('requisition', function ($query) {
            $query->where('status', 0);
        });
    }

    public function requests()
    {
        return $this->hasMany(RequiredProduct::class, 'product_id', 'id');
    }

    public function dispatches()
    {
        return $this->hasMany(ProductDispatch::class, 'product_id', 'id');
    }

    public function setProductVariationsAttribute($variations = array())
    {
        if (count($variations)) {

            $this->variations()->delete();
            
            $variations = json_decode(json_encode($variations));

            foreach ($variations as $variation) {

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

                    $this->variations()->create([
                        'sku' => $variation->sku ?? $this->generateProductVariationSKU($this->sku, $variation->variation->id),
                        'initial_quantity' => $variation->initial_quantity,
                        'available_quantity' => $variation->initial_quantity,
                        'price' => $variation->price,
                        'variation_id' => $variation->variation->id,
                    ]);

                // }

            }
        }
    }

    public function setProductAddressAttribute($spaces = [])
    {
        if (count($spaces)) {
            
            $spaces = json_decode(json_encode($spaces));
            
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

        }
    }

    protected function deleteOldAddresses()
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

    protected function generateProductVariationSKU($productSKU, $variationId)
    {
        return $productSKU.'VR'.$variationId;
    }
}
