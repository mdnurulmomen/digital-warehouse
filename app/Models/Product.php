<?php

namespace App\Models;

use Illuminate\Support\Facades\File;
use Illuminate\Database\Eloquent\Model;
use Intervention\Image\ImageManagerStatic as Image;
use Intervention\Image\Exception\NotReadableException;

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

    public function variations()
    {
        return $this->hasMany(ProductVariation::class, 'product_id', 'id');
    }

    /**
     * Get all of the merchants who has this product.
     */
    public function merchants()
    {
        return $this->belongsToMany(Merchant::class, 'merchant_products', 'product_id', 'merchant_id');
    }

    // immutable product
    public function getProductImmutabilityAttribute()
    {
        if ($this->merchants()->count()) {

            return true;
             
        }

        return false;
    }

    public function setProductVariationsAttribute($productNewVariations = array())
    {
        if (count($productNewVariations)) {

            if ($this->getProductImmutabilityAttribute()) {
                
                foreach ($this->variations() as $productVariation) {
                    
                    if (! $productVariation->variation_immutability) {

                        $productVariation->delete();

                    }

                }

            }
            else {

                $this->variations()->delete();

            }
            
            // $variations = json_decode(json_encode($variations));

            foreach ($productNewVariations as $productNewVariation) {

                $previewPath = $this->saveProductVariationPreview($productNewVariation->preview, $productNewVariation->variation);

                $productVariation = $this->variations()->updateOrCreate(
                    
                    [ 'variation_id' => $productNewVariation->variation->id ],
                    [ 'preview' => $previewPath ]

                );

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
                    /*
                        if (empty($productNewVariation->variation_immutability) || is_null($productNewVariation->variation_immutability)) {
                            
                            $this->variations()->create([
                                // 'sku' => $productNewVariation->sku ?? $this->generateProductVariationSKU($this->sku, $productNewVariation->variation->id),
                                // 'initial_quantity' => $variation->initial_quantity,
                                // 'available_quantity' => $variation->initial_quantity,
                                // 'price' => $productNewVariation->price,
                                'variation_id' => $productNewVariation->variation->id,
                            ]);

                        }
                    */
                    
                    /*
                        else {

                            $this->variations()->where('variation_id', $productNewVariation->variation->id)->update([
                                // 'sku' => $productNewVariation->sku ?? $this->generateProductVariationSKU($this->sku, $productNewVariation->variation->id),
                                // 'initial_quantity' => $variation->initial_quantity,
                                // 'available_quantity' => $variation->initial_quantity,
                                // 'price' => $productNewVariation->price,
                            ]);

                        }
                    */

                // }

            }
        }
    }

    public function setProductPreviewAttribute($encodedImageFile)
    {
        if ($encodedImageFile) {
            
            $imagePath = 'storage/products/';

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
            $img->save($imagePath.str_replace(' ', '_', strtolower($this->name)).'.jpg');

            $this->attributes['preview'] = $imagePath.str_replace(' ', '_', strtolower($this->name)).'.jpg';

        }
    }

    protected function saveProductVariationPreview($encodedImageFile, $variation)
    {
        if ($encodedImageFile) {
            
            $imagePath = 'storage/products/';

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
            $img->save($imagePath.str_replace(' ', '_', strtolower($this->name.'_'.$variation->name)).'.jpg');

            return $imagePath.str_replace(' ', '_', strtolower($this->name.'_'.$variation->name)).'.jpg';

        }

        return;
    }
    
}
