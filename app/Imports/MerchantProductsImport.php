<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\Merchant;
use Illuminate\Validation\Rule;
use App\Models\ProductVariation;
use Illuminate\Support\Collection;
use App\Models\ProductManufacturer;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class MerchantProductsImport implements ToCollection, WithValidation, WithHeadingRow
{
    private $merchant;

    public function  __construct(Merchant $merchant)
    {
        $this->merchant = $merchant;
    }

    public function rules(): array
    {
        return [
        	'name' => 'required|string|exists:products,name',
            // 'sku' => 'nullable|string|max:255',
            // 'preview' => 'nullable|string|max:255',
            'manufacturer_name' => 'nullable|string|exists:product_manufacturers,name',
            'description' => 'nullable|string|max:65500',
            'warning_quantity' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric|between:0,100',
            // 'selling_price' => 'required', // numeric / string with ',' (for merchant-variations)
            // 'variations' => [
            // 	Rule::requiredIf(Product::where('name', strtolower($request->name))->first()->has_variations)
            // ],
        ];
    }

    public function withValidator($validator){
        
        if (! $validator->fails()) {
            
            $validator->after(function ($validator) {
                
                foreach($validator->getData() as $key=>$data){

                    $product = Product::where('name', strtolower($data['name']))->first();

                    if ($product->has_variations) {
                        
                        if (empty(strtolower($data['variations'])) || count(explode(',', $data['variations'])) < 2) {

                            $validator->errors()->add($key, "Incomplete variations");
                        
                        }
                        else if (! empty(strtolower($data['variations'])) && ! empty($data['selling_price']) && count(explode(',', $data['selling_price'])) != count(explode(',', $data['variations']))) {

                            $validator->errors()->add($key, "Selling prices are less than variations");
                        
                        }
                        else {  // if product-variations are valid to add with merchant-products 

                            foreach (explode(',', $data['variations']) as $variationKey => $variation) {
                                
                                $productVariationExists = ProductVariation::where('product_id', $product->id)
                                ->whereHas('variation', function ($query) use ($variation) {
                                    $query->where('name', strtolower($variation));
                                })
                                ->exists();

                                if (! $productVariationExists) {

                                    $validator->errors()->add($key, ucfirst($variation)." is invalid variation");
                                
                                }

                            }

                        }

                    }
                    else { // product has no variation

                        if ($data['selling_price'] < 0) {
                            
                            $validator->errors()->add($key, "Invalid selling price");

                        }

                    }
        
                }
                
            });

        }

    }

    /**
     * @return array
     */
    public function customValidationMessages()
    {
        return [
            'name.required' => 'Product name is required.',
            'name.*' => 'Product name is invalid.',
            // 'sku.required' => 'SKU is required.',
            // 'sku.*' => 'SKU code is invalid.',
            'manufacturer_name.*' => 'Manufacturer name is invalid.',
            'warning_quantity.*' => 'Warning quantity is invalid.',
            'discount.*' => 'Discount rate is invalid.',
            // 'selling_price.*' => 'Selling price is invalid.',
            // 'variations.required' => 'Variations are required.',
            // 'variations.*' => 'Variations are invalid.',
        ];
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {

            $product = Product::where('name', strtolower($row['name']))->first();
            $manufacturer = ProductManufacturer::where('name', strtolower($row['manufacturer_name'] ?? ''))->first();

            if (! $product->has_variations || ($product->has_variations && count(explode(',', $row['variations'])) > 1)) {
            	
            	// skipping same record
                if (! $this->merchant->products()->where('product_id', $product->id)->where('manufacturer_id', ($manufacturer ? $manufacturer->id : NULL))->exists()) {
                    
                    $merchantProduct = $this->merchant->products()->create([
                        'sku' => $this->generateProductSKU($product->product_category_id, $product->id, $this->merchant->id, ($manufacturer ? $manufacturer->id : NULL)),

            			// 'preview' => $row['preview'],
            			'description' => isset($row['description']) ? strtolower($row['description']) : '',
            			'warning_quantity' => $row['warning_quantity'] ?? 0,
            			'available_quantity' => 0,
            			'discount' => $row['discount'] ?? 0,
            			'selling_price' => $product->has_variations && ! empty($row['selling_price']) && count(explode(',', $row['selling_price'])) ? min(explode(',', $row['selling_price'])) : (! empty($row['selling_price']) ? $row['selling_price'] : NULL),
                        'product_id' => $product->id, 
                        'manufacturer_id' => ($manufacturer ? $manufacturer->id : NULL)
                    ]);

                    if ($product->has_variations) {

    	                foreach (explode(',', $row['variations']) as $variationKey => $variation) {

    	                    // product-variation
                            $productVariation = ProductVariation::where('product_id', $product->id)
                            ->whereHas('variation', function ($query) use ($variation) {
    						    $query->where('name', strtolower($variation));
    						})
                            ->first();

    	                    // skipping existing merchant-product-variations
                            if ($productVariation && ! $merchantProduct->variations()->where('product_variation_id', $productVariation->id)->exists()) {
    	                        
    	                        $merchantProduct->variations()->create([
	                                'sku' => ($merchantProduct->sku.'V'.$productVariation->variation_id),
			            			// 'preview' => NULL,
			            			'available_quantity' => 0,
			            			'selling_price' => (! empty($row['selling_price']) && count(explode(',', $row['selling_price'])) > $variationKey) ? explode(',', $row['selling_price'])[$variationKey] : 0, 
                                    'product_variation_id' => $productVariation->id
                                ]);

    	                    }

    	                }

    	            }

                }


            }

        }
    }

    protected function generateProductSKU($productCategory, $product, $merchant, $manufacturer = NULL)
    {
        if ($productCategory) {
            
            // return ('C'.$productCategory.'P'.$product.'M'.$merchant.'M'.$manufacturer ? $manufacturer : $merchant);
            // return ('P'.$product.'M'.$merchant.'MF'.($manufacturer ? $manufacturer : $merchant));
            return ('P'.$product.'M'.$merchant.($manufacturer ? $manufacturer : $merchant));

        }

        // return ('BP'.$product.'M'.$merchant.'MF'.($manufacturer ? $manufacturer : $merchant));
        return ('BP'.$product.'M'.$merchant.($manufacturer ? $manufacturer : $merchant));
    }

}
