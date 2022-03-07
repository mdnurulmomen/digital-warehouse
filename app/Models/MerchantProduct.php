<?php

namespace App\Models;

use Illuminate\Support\Facades\File;
use Illuminate\Database\Eloquent\Model;
use Intervention\Image\ImageManagerStatic as Image;
use Intervention\Image\Exception\NotReadableException;

class MerchantProduct extends Model
{
    public $timestamps = false;
    
    protected $guarded = ['id'];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function merchant()
    {
        return $this->belongsTo(Merchant::class, 'merchant_id', 'id');
    }

    public function manufacturer()
    {
        return $this->belongsTo(ProductManufacturer::class, 'manufacturer_id', 'id');
    }

    public function variations()
    {
        return $this->hasMany(MerchantProductVariation::class, 'merchant_product_id', 'id');
    }
    
    public function requests()
    {
        return $this->hasMany(RequiredProduct::class, 'merchant_product_id', 'id');
    }

    public function addresses()
    {
        return $this->hasMany(WarehouseProduct::class, 'merchant_product_id', 'id');
    }

    public function stocks()
    {
        return $this->hasMany(ProductStock::class, 'merchant_product_id', 'id')
        ->whereHas('stock', function ($q) {
            $q->where('has_approval', 1);
        })
        ->orderBy('id', 'desc');
    }

    public function serials()
    {
        return $this->hasMany(ProductSerial::class, 'merchant_product_id', 'id');
    }
    
    /*
    public function latestStock()
    {
        return $this->hasOne(ProductStock::class, 'merchant_product_id', 'id')
        ->whereHas('stock', function ($q) {
            $q->where('has_approval', 1);
        })->orderBy('id', 'desc');
    }
    */
   
    public function oldestAvailableStock()
    {
        return $this->hasOne(ProductStock::class, 'merchant_product_id', 'id')
        ->whereHas('stock', function ($q) {
            $q->where('has_approval', 1);
        })
        ->where('available_quantity', '>', 0)
        ->oldest('id');
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
        /*
            else if ($this->serials()->where('has_requisitions', 1)->orWhere('has_dispatched', 1)->count()) {
                 return true;
            }
        */

        return false;
    }

    public function nonDispatchedRequests()
    {
        return $this->hasMany(RequiredProduct::class, 'merchant_product_id', 'id')
        ->whereHas('requisition', function ($query) {
            $query->where('status', 0);
        });
    }

    public function dispatchedRequests()
    {
        return $this->hasMany(RequiredProduct::class, 'merchant_product_id', 'id')
        ->whereHas('requisition', function ($query) {
            $query->where('status', 1);
        })
        ->whereHas('dispatch', function ($query1) {
            $query1->where('has_approval', 1);
        });
    }

    /*
    public function dispatches()
    {
        return $this->hasMany(ProductDispatch::class, 'merchant_product_id', 'id');
    }
    */
    
    public function deleteOldAddresses()
    {
        if (count($this->addresses)) {
            
            foreach ($this->addresses as $productAddress) {
                
                $productAddress->space->update([
                    'occupied' => 0
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

    /**
     * Set the user's first name.
     *
     * @param  string  $value
     * @return void
     */
    public function setMerchantProductPreviewAttribute($encodedImageFile)
    {
        if ($encodedImageFile) {
            
            $imagePath = 'storage/merchant-products/';

            if(!File::isDirectory($imagePath)){
                File::makeDirectory($imagePath, 0777, true, true);
            }

            try 
            {
                $img = Image::make($encodedImageFile);
            }
            catch(NotReadableException $e)
            {
                // If error, stop and return
                return;
            }

            // $img = $img->resize(100, 100);
            
            $img->save($imagePath.'merchant-'.$this->merchant_id.'-product-'.$this->product_id.'.jpg');

            $this->attributes['preview'] = $imagePath.'merchant-'.$this->merchant_id.'-product-'.$this->product_id.'.jpg';

        }
    }

    public function setMerchantProductVariationsAttribute($merchantProductNewVariations = array())
    {
        if (count($merchantProductNewVariations)) {

            // updation
            if ($this->variations()->count()) {
                
                // deducting variations
                if ($this->variations()->count() > count($merchantProductNewVariations)) {
                    
                    for ($i=$this->variations()->count(); $i>count($merchantProductNewVariations); $i--) { 
                        
                        File::delete($this->variations->get($i-1)->preview);

                    }

                }
                
                if ($this->getProductImmutabilityAttribute()) {
                    
                    foreach ($this->variations as $merchantProductVariation) {
                        
                        if (! $merchantProductVariation->variation_immutability) {

                            $merchantProductVariation->delete();

                        }

                    }

                }
                else {

                    $this->variations()->delete();

                }

            }


            foreach ($merchantProductNewVariations as $merchantProductNewVariation) {

                $previewPath = $this->saveProductVariationPreview($merchantProductNewVariation->preview ?? NULL, $this->merchant->user_name, $this->product->name, $merchantProductNewVariation->variation);

                $merchantProductVariation = $this->variations()->updateOrCreate(
                    
                    [ 'product_variation_id' => $merchantProductNewVariation->product_variation_id ?? $merchantProductNewVariation->variation->id ],
                    [ 
                        'sku' => $merchantProductNewVariation->sku ?? $this->generateProductVariationSKU($this->merchant_id, $this->product->category->id, $this->product_id, $merchantProductNewVariation->variation->id), 
                        'preview' => $previewPath,
                        'selling_price' => $merchantProductNewVariation->selling_price, 
                    ]

                );

            }
            
        }
    }

    protected function removeProductContainers($containers = array())
    {
        if (count($containers)) {

            foreach ($containers as $container) {
                
                $warehouseExpectedContainer = WarehouseContainerStatus::find($container->id);

                $warehouseExpectedContainer->product()->delete();

                $warehouseExpectedContainer->update([
                    'occupied' => 0
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
                    'occupied' => 0
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
                    'occupied' => 0
                ]);

            }

            // Parent Shelf
            $this->updateParentShelf($warehouseExpectedShelfUnit->parentShelf);

        }
    }

    protected function updateParentContainer(WarehouseContainerStatus $warehouseExpectedContainer)
    {
        // $warehouseExpectedContainer = WarehouseContainerStatus::find($containerId);

        // all shelves are occupied
        if ($warehouseExpectedContainer->containerShelfStatuses->count()===$warehouseExpectedContainer->containerShelfStatuses()->where('occupied', 1)->count()) {
            
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
        if ($warehouseExpectedShelf->containerShelfUnitStatuses->count()===$warehouseExpectedShelf->containerShelfUnitStatuses()->where('occupied', 1)->count()) {
            
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

    /*

    protected function setProductContainers($containers = array())
    {
        if (count($containers)) {

            foreach ($containers as $container) {
                
                $warehouseExpectedContainer = WarehouseContainerStatus::find($container->id);

                $warehouseExpectedContainer->product()->create([
                    'merchant_product_id' => $this->id,
                ]);

                $warehouseExpectedContainer->update([
                    'occupied' => 1
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
                    'merchant_product_id' => $this->id,
                ]);

                $warehouseExpectedShelf->update([
                    'occupied' => 1
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
                    'merchant_product_id' => $this->id,
                ]);

                $warehouseExpectedShelfUnit->update([
                    'occupied' => 1
                ]);

            }

            // Parent Shelf
            $this->updateParentShelf($warehouseExpectedShelfUnit->parentShelf);

        }
    }
    */
   
    protected function saveProductVariationPreview($encodedImageFile, $merchantName, $productName, $variation)
    {
        if ($encodedImageFile) {
            
            $imagePath = 'storage/merchant-products/';

            if(!File::isDirectory($imagePath)){
                File::makeDirectory($imagePath, 0777, true, true);
            }

            try 
            {
                $img = Image::make($encodedImageFile);
            }
            catch(NotReadableException $e)
            {
                // If error, stop and return
                return;
            }

            // $img = $img->resize(300, 300);
            
            $variationName = $variation->name ?? $variation->variation->name;

            $img->save($imagePath.str_replace(' ', '_', strtolower($merchantName.'_'.$productName.'_'.$variationName)).'.jpg');

            return $imagePath.str_replace(' ', '_', strtolower($merchantName.'_'.$productName.'_'.$variationName)).'.jpg';

        }

        return;
    }

    protected function generateProductVariationSKU($merchant, $productCategory, $product, $variation)
    {
        return 'MR'.$merchant.'CT'.$productCategory.'PR'.$product.'VR'.$variation;
    }

}
