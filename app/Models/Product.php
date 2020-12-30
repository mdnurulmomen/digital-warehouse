<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public $timestamps = false;
    protected $guarded = ['id'];
    
    // protected $with = ['category', 'merchant', 'variations.variation', 'addresses.space.warhouseContainer.container'];

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
        return $this->hasMany(WarhouseProduct::class, 'product_id', 'id');
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

                if ($productAddress->space instanceof WarhouseContainerStatus) {

                    $this->updateChildShelves($productAddress->space, 0);

                }
                else if ($productAddress->space instanceof WarhouseContainerShelfStatus) {

                    $this->updateChildUnits($productAddress->space, 0);

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
                
                $warhouseExpectedContainer = WarhouseContainerStatus::find($container->id);

                $warhouseExpectedContainer->product()->create([
                    'product_id' => $this->id,
                ]);

                $warhouseExpectedContainer->update([
                    'engaged' => 1
                ]);

                $this->updateChildShelves($warhouseExpectedContainer, 1);

            }
        }
    }

    protected function setProductShelves($container = NULL)
    {
        if ($container) {

            foreach ($container->shelves as $containerShelf) {
                
                $warhouseExpectedShelf = WarhouseContainerShelfStatus::find($containerShelf->id);

                $warhouseExpectedShelf->product()->create([
                    'product_id' => $this->id,
                ]);

                $warhouseExpectedShelf->update([
                    'engaged' => 1
                ]);

                $this->updateChildUnits($warhouseExpectedShelf, 1);

            }

            $this->updateParentContainer($container->id);

        }
    }

    protected function setProductUnits($container = NULL)
    {
        if ($container) {

            foreach ($container->shelf->units as $containerShelfUnit) {
                
                $warhouseExpectedShelfUnit = WarhouseContainerShelfUnitStatus::find($containerShelfUnit->id);

                $warhouseExpectedShelfUnit->product()->create([
                    'product_id' => $this->id,
                ]);

                $warhouseExpectedShelfUnit->update([
                    'engaged' => 1
                ]);

            }

            // Parent Shelf
            $this->updateParentShelf($container);

        }
    }

    protected function updateParentContainer($containerId)
    {
        $warhouseExpectedContainer = WarhouseContainerStatus::find($containerId);

        // partially engaged
        $warhouseExpectedContainer->update([
            'engaged' => 0.5
        ]);

        // all shelves are engaged
        if ($warhouseExpectedContainer->containerShelfStatuses->count()===$warhouseExpectedContainer->containerShelfStatuses()->where('engaged', 1)->count()) {
            
            $warhouseExpectedContainer->update([
                'engaged' => 1
            ]); 

        }
    }

    protected function updateParentShelf($container)
    {
        // Related Shelf
        $warhouseExpectedShelf = WarhouseContainerShelfStatus::find($container->shelf->id);

        // partially engaged
        $warhouseExpectedShelf->update([
            'engaged' => 0.5
        ]);

        // all shelves are engaged
        if ($warhouseExpectedShelf->containerShelfUnitStatuses->count()===$warhouseExpectedShelf->containerShelfUnitStatuses()->where('engaged', 1)->count()) {
            
            $warhouseExpectedShelf->update([
                'engaged' => 1
            ]);

        }

        // parent Container
        $this->updateParentContainer($container->id);
    }

    protected function updateChildShelves(WarhouseContainerStatus $container, $newValue)
    {
        foreach ($container->containerShelfStatuses as $containerShelf) {
           
            $containerShelf->update([
                'engaged' => $newValue
            ]);

            $this->updateChildUnits($containerShelf, $newValue);

        }
    }

    protected function updateChildUnits(WarhouseContainerShelfStatus $shelf, $newValue)
    {
        $shelf->containerShelfUnitStatuses()->update([
            'engaged' => $newValue
        ]);
    }

    protected function generateProductVariationSKU($productSKU, $variationId)
    {
        return $productSKU.'VR'.$variationId;
    }
}
