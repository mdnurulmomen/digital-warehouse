<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\Merchant;
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
            // 'sku' => 'required|string|max:255|unique:merchant_products,sku',
            'preview' => 'nullable|string|max:255',
            'manufacturer_name' => 'nullable|string|exists:product_manufacturers,name',
            'description' => 'nullable|string|max:255',
            'warning_quantity' => 'numeric',
            'discount' => 'numeric|between:0,100',
            'selling_price' => 'required|numeric',
            // 'variations' => [
            // 	Rule::requiredIf(Product::where('name', strtolower($request->name))->first()->has_variations)
            // ],
        ];
    }

    public function withValidator($validator){
        
        // if (! $validator->fails()) {
            
            $validator->after(function ($validator) {
                
                foreach($validator->getData() as $key=>$data){

                    $product = Product::where('name', strtolower($data['name']))->first();

                    if ($product->has_variations) {
                        
                        if (empty(strtolower($data['variations'])) || count(explode(',', $data['variations'])) < 2) {

                            $validator->errors()->add($key, "Variations are required");
                        
                        }
                        else {

                            foreach (explode(',', $data['variations']) as $variationKey => $variation) {
                                
                                $productVariationExists = ProductVariation::where('product_id', $product->id)
                                ->whereHas('variation', function ($query) use ($variation) {
                                    $query->where('name', trim(strtolower($variation), " "));
                                })
                                ->exists();

                                if (! $productVariationExists) {

                                    $validator->errors()->add($key, ucfirst($variation)." is invalid variation");
                                
                                }

                            }

                        }

                    }
        
                }
                
            });

        // }

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
            'selling_price.required' => 'Selling price is required.',
            'selling_price.*' => 'Selling price is invalid.',
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
            	
            	$merchantProduct = $this->merchant->products()->firstOrCreate(
            		[ 'product_id' => $product->id, 'manufacturer_id' => ($manufacturer ? $manufacturer->id : NULL), ],
            		[
            			'sku' => ('MR'.$this->merchant->id.'CT'.$product->category->id.'PR'.$product->id.'MFR'.($manufacturer ? $manufacturer->id : $this->merchant->id)),
            			'preview' => $row['preview'],
            			'description' => isset($row['description']) ? strtolower($row['description']) : '',
            			'warning_quantity' => $row['warning_quantity'] ?? 100,
            			'available_quantity' => 0,
            			'discount' => $row['discount'] ?? 0,
            			'selling_price' => $row['selling_price'],
            		]
            	);

                if ($product->has_variations) {

	                foreach (explode(',', $row['variations']) as $variationKey => $variation) {

	                    $productVariation = ProductVariation::where('product_id', $product->id)
                        ->whereHas('variation', function ($query) use ($variation) {
						    $query->where('name', strtolower($variation));
						})
                        ->first();

	                    if ($productVariation) {
	                        
	                        $merchantProduct->variations()->firstOrCreate(
	                            [ 'product_variation_id' => $productVariation->id ],
	                            [
	                                'sku' => ($merchantProduct->sku.'VR'.$productVariation->variation_id),
			            			'preview' => NULL,
			            			'selling_price' => $merchantProduct->selling_price,
			            			'available_quantity' => 0
	                            ]
	                        );

	                    }

	                }

	            }

            }

        }
    }
}
