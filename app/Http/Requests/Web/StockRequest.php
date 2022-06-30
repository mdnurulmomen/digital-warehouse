<?php

namespace App\Http\Requests\Web;

use App\Models\ProductStock;
use App\Models\MerchantProduct;
use Illuminate\Validation\Rule;
use App\Models\ProductVariationSerial;
use Illuminate\Foundation\Http\FormRequest;

class StockRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $storingProducts = json_decode(json_encode($this->products));

        $rules = [
            // 'merchant_id' => 'required|exists:merchants,id',
            'merchant.id' => 'required|exists:merchants,id',
            'warehouse.id' => 'required|exists:warehouses,id',
            'products.*.merchant_product_id' => [
                'required', 'integer', 
                Rule::exists('merchant_products', 'id')->where(function ($query) {
                    $query->where('merchant_id', $this->input('merchant.id'));
                })
            ],
            'products.*.stock_quantity' => 'required|integer|min:1',
            'products.*.unit_buying_price' => 'nullable|numeric',
            // 'product_id' => 'required|numeric|exists:products,id',
        ];

        foreach ($storingProducts as $stockingProductKey => $stockingProduct) {
            
            $merchantProduct = MerchantProduct::findOrFail($stockingProduct->merchant_product_id);
            $product = $merchantProduct->product;

            $rules['products.'.$stockingProductKey.'.addresses'] = 'required|array|min:1';

            foreach (json_decode(json_encode($stockingProduct->addresses)) as $addressKey => $address) {
            
                if ($address->type=='containers') {
                    $rules['products.'.$stockingProductKey.'.addresses.'.$addressKey.'.containers'] = 'required|array|min:1';

                    foreach ($address->containers as $containerKey => $container) {
                        $rules['products.'.$stockingProductKey.'.addresses.'.$addressKey.'.containers.'.$containerKey.'.id'] = [
                            'required', 
                            Rule::exists('dealt_spaces', 'space_id')
                            ->where('space_type', 'App\Models\WarehouseContainerStatus')
                        ];
                    }

                }
                else if ($address->type=='shelves') {
                    $rules['products.'.$stockingProductKey.'.addresses.'.$addressKey.'.container'] = 'required';
                    $rules['products.'.$stockingProductKey.'.addresses.'.$addressKey.'.container.shelves'] = 'required|array|min:1';
                }
                else if ($address->type=='units') {
                    $rules['products.'.$stockingProductKey.'.addresses.'.$addressKey.'.container'] = 'required';
                    $rules['products.'.$stockingProductKey.'.addresses.'.$addressKey.'.container.shelf'] = 'required';
                    $rules['products.'.$stockingProductKey.'.addresses.'.$addressKey.'.container.shelf.units'] = 'required|array|min:1';
                }

            }

            if ($product->has_serials && ! $product->has_variations) {
                $rules['products.'.$stockingProductKey.'.serials'] = 'required|array|min:'.$stockingProduct->stock_quantity;
            }
            if ($product->has_serials && ! $product->has_variations) {
                
                foreach ($stockingProduct->serials as $productSerialkey => $productSerial) {
                    
                    if (isset($productSerial->id)) {
                        // $rules['products.'.$stockingProductKey.'.serials.'.$productSerialkey.'.serial_no'] = 'required|string|distinct|min:4|unique:product_serials,serial_no,'.$productSerial->id;
                        
                        $rules['products.'.$stockingProductKey.'.serials.'.$productSerialkey.'.serial_no'] = [
                            'required', 'string', 'distinct', 'min:4', 

                            Rule::unique('product_serials', 'serial_no')
                            ->where(function ($query) use ($productSerial) {
                                return $query->where('id', '!=', $productSerial->id);
                            })
                            ->ignore(1, 'has_dispatched')
                        ];
                    }
                    else {
                        // $rules['products.'.$stockingProductKey.'.serials.'.$productSerialkey.'.serial_no'] = 'required|string|distinct|min:4|unique:product_serials,serial_no';
                        
                        $rules['products.'.$stockingProductKey.'.serials.'.$productSerialkey.'.serial_no'] = [
                            'required', 'string', 'distinct', 'min:4', 

                            Rule::unique('product_serials', 'serial_no')->ignore(1, 'has_dispatched')
                        ];
                    }

                }

            }
            if ($product->has_variations) {
                $rules['products.'.$stockingProductKey.'.variations'] = 'required|array|min:1';
            }
            if ($product->has_variations) {
                $rules['products.'.$stockingProductKey.'.variations.*.merchant_product_variation_id'] = 'required|integer|exists:merchant_product_variations,id';

                $rules['products.'.$stockingProductKey.'.variations.*.stock_quantity'] = 'sometimes|integer|min:0';
                $rules['products.'.$stockingProductKey.'.variations.*.unit_buying_price'] = 'sometimes|numeric|min:0';
            }
            if ($product->has_variations && array_sum(array_column($stockingProduct->variations, 'stock_quantity')) != $stockingProduct->stock_quantity) {
                $rules['products.'.$stockingProductKey.'.variations.*.total_stock_quantity'] = 'required|integer|min:'.array_sum(array_column($stockingProduct->variations, 'stock_quantity'));
            }
            if ($product->has_variations && $product->has_serials) {

                foreach ($stockingProduct->variations as $stockingProductVariationKey => $stockingProductVariation) {
                    
                    if (! empty($stockingProductVariation->stock_quantity) && $stockingProductVariation->stock_quantity > 0) {
                        
                        // $rules['products.'.$stockingProductKey.'.variations.'.$stockingProductVariationKey.'.serials'] = 'required|array';
                        
                        $rules['products.'.$stockingProductKey.'.variations.'.$stockingProductVariationKey.'.serials'] = ['required', 'array'];

                        foreach ($stockingProductVariation->serials as $variationSerialkey => $variationSerial) {
                        
                            if (isset($variationSerial->id)) {
                                // $rules['products.'.$stockingProductKey.'.variations.'.$stockingProductVariationKey.'.serials.'.$variationSerialkey.'.serial_no'] = 'required|string|distinct|min:4|unique:product_variation_serials,serial_no,'.$variationSerial->id;
                                
                                $rules['products.'.$stockingProductKey.'.variations.'.$stockingProductVariationKey.'.serials.'.$variationSerialkey.'.serial_no'] = [
                                    'required','string','distinct','min:4',
                                    
                                    Rule::unique('product_variation_serials', 'serial_no')
                                    ->where(function ($query) use ($variationSerial) {
                                        return $query->where('id', '!=', $variationSerial->id);
                                    })
                                    ->ignore(1, 'has_dispatched')
                                ];
                            }
                            else {
                                // $rules['products.'.$stockingProductKey.'.variations.'.$stockingProductVariationKey.'.serials.'.$variationSerialkey.'.serial_no'] = 'required|string|distinct|min:4|unique:product_variation_serials,serial_no';
                                
                                $rules['products.'.$stockingProductKey.'.variations.'.$stockingProductVariationKey.'.serials.'.$variationSerialkey.'.serial_no'] = [
                                    'required','string','distinct','min:4',
                                    
                                    Rule::unique('product_variation_serials', 'serial_no')->ignore(1, 'has_dispatched')
                                    
                                ];
                            }

                        }

                    }

                }

            }

        }

        return $rules;
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'products.*.stock_quantity.*' => 'Stock quantity is required !',
            'products.*.unit_buying_price.*' => 'Buying price should be numeric !',
            'warehouse.id' => [
                'exists' => 'Warehouse is invalid !',
                '*' => 'Warehouse is required !',
            ],

            'products.*.serials.required' => 'Product serial is required !',
            
            'products.*.serials.*.serial_no' => [
                'distinct' => 'Duplicate product-serial is invalid',
                'unique' => 'Product serial exists',
                '*' => 'Product serial is required',
            ],
            
            // 'product_id.*' => 'Product id is missing !',
            // 'serials.*.unique' => ':attribute serial must be unique !',
            
            'products.*.variations.*.merchant_product_variation_id.*' => 'Merchant product variation id is required !',
            
            'products.*.variations.*.stock_quantity' => [
                'required' => 'Variation quantity should be equal to product quantity',
                '*' => 'Variation quantity is required !',
            ],

            'products.*.variations.*.unit_buying_price.*' => 'Buying price should be numeric',
            'products.*.variations.*.total_stock_quantity.*' => 'Stock qty does not match',

            'products.*.variations.*.serials' => [
                'required' => 'Variation serial is required',
                'array' => 'Variation serial should be an array',
                '*' => 'Variation invalid serial',
            ],

            'products.*.variations.*.serials.*.serial_no' => [
                'distinct' => 'Variation duplicate serial is invalid',
                'unique' => 'Variation serial exists',
                '*' => 'Variation serial is required',
            ]
        ];
    }

    /**
    * Configure the validator instance.
    *
    * @param  \Illuminate\Validation\Validator  $validator
    * @return void
    */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {

            // when updating
            if ($this->route('stock')) {
                
                // validation
                foreach (json_decode(json_encode($this->input('products'))) as $productIndex => $stockingProduct) {
                    
                    $stockToUpdate = ProductStock::findOrFail($stockingProduct->id);
                    $merchantProduct = MerchantProduct::findOrFail($stockingProduct->merchant_product_id);
                    $product = $merchantProduct->product;
                    
                    // If Replaced Merchant-Product and available-quantity is gonna be negative
                    if ($stockToUpdate->merchant_product_id != $stockingProduct->merchant_product_id && ! $product->has_variations && $stockToUpdate->stock_quantity > $merchantProduct->available_quantity) {
                        
                        $missingProduct = ucfirst($product->name);
                        // return response()->json(['errors'=>["$product->name" => "$missingProduct is not found"]], 422);
                        $validator->errors()->add("$product->name", "$missingProduct is not found");

                    }

                    // already stocked product && reducing stock quantity
                    // decreasing qty is more than stock or product available
                    else if ($stockToUpdate->merchant_product_id == $stockingProduct->merchant_product_id && ! $product->has_variations && $stockToUpdate->stock_quantity > $stockingProduct->stock_quantity && ((($stockToUpdate->stock_quantity - $stockingProduct->stock_quantity) > $stockToUpdate->available_quantity) || (($stockToUpdate->stock_quantity - $stockingProduct->stock_quantity) > $stockToUpdate->merchantProduct->available_quantity))) {

                        // return response()->json(['errors'=>["invalidValue" => "Stock quantity is less than minimum"]], 422);
                        $validator->errors()->add("invalidValue", "Stock quantity is less than minimum");

                    }
                    else if($product->has_variations && $this->findVariationInvalidQuantity(json_decode(json_encode($stockingProduct->variations)), $stockToUpdate)) {

                        // return response()->json(['errors'=>["invalidVariationData" => "Trying to update immutable variation or less than minimum"]], 422);
                        $validator->errors()->add("invalidVariationData", "Trying to update immutable variation or less than minimum");

                    }
                    else if ($product->has_serials && ! $product->has_variations && ($this->checkProductSerialInValidity($stockToUpdate, json_decode(json_encode($stockingProduct->serials))) /* || $this->checkProductDuplicateSerial($stockingProduct)*/ )) {

                        // if ($this->checkProductSerialInValidity($stockToUpdate, json_decode(json_encode($stockingProduct->serials)))) {
                            
                            $serialNotFound = $this->checkProductSerialInValidity($stockToUpdate, json_decode(json_encode($stockingProduct->serials)));
                            // return response()->json(['errors'=>["$serialNotFound" => "$serialNotFound serial not found"]], 422);
                            $validator->errors()->add("$serialNotFound", "$serialNotFound serial not found");

                        // }
                        /*
                        else {

                            $duplicateValue = $this->checkProductDuplicateSerial($stockingProduct);
                            // return response()->json(['errors'=>["$duplicateValue" => "$duplicateValue serial is taken"]], 422);
                            $validator->errors()->add("$duplicateValue", "$duplicateValue serial is taken");

                        }
                        */

                    }
                    else if ($product->has_serials && $product->has_variations && ($this->checkVariationInvalidSerial($stockToUpdate, json_decode(json_encode($stockingProduct->variations))) /* || $this->checkVariationDuplicateSerial($stockingProduct)*/ )) {

                        // if ($this->checkVariationInvalidSerial($stockToUpdate, json_decode(json_encode($stockingProduct->variations)))) {
                            
                            $serialNotFound = $this->checkVariationInvalidSerial($stockToUpdate, json_decode(json_encode($stockingProduct->variations)));
                            // return response()->json(['errors'=>["$serialNotFound" => "$serialNotFound serial not found"]], 422);
                            $validator->errors()->add("$serialNotFound", "$serialNotFound serial not found");

                        // }
                        /*
                        else {

                            $duplicateValue = $this->checkVariationDuplicateSerial($stockingProduct);
                            // return response()->json(['errors'=>["$duplicateValue" => "$duplicateValue serial is taken"]], 422);
                            $validator->errors()->add("$duplicateValue", "$duplicateValue serial is taken");

                        }
                        */
                        
                    }

                }

            }

        });
    }

    protected function findVariationInvalidQuantity($stockVariaitons = [], ProductStock $productStock)
    {
        if (count($stockVariaitons) && $productStock->variations->count()) {
            
            $stockVariationCollection = collect($stockVariaitons);

            foreach ($productStock->variations as $productVariationExistingStockKey => $productVariationExistingStock) {
                
                $merchantProductVariation = $productVariationExistingStock->merchantProductVariation;
                // $expectedVariationeLatestStock = $merchantProductVariation->latestStock;

                $expectedVariationInputedStock = $stockVariationCollection->first(function ($object, $key) use ($productVariationExistingStock) {
                    return $object->merchant_product_variation_id == $productVariationExistingStock->merchant_product_variation_id;
                });

                // replaced variation-stock but replacing-variation available quantity is less than existing quantity
                if (empty($expectedVariationInputedStock) && $merchantProductVariation->dispatchedRequests->count() && $merchantProductVariation->available_quantity < $productVariationExistingStock->stock_quantity) {
                    
                    /*
                    // MerchantProductVariation which has only stock but already dispatched, cant be replaced
                    if ($merchantProductVariation->stocks->count() < 2 && $merchantProductVariation->dispatchedRequests->count()) {
                        
                        return true;

                    }
                    // have multiple stocks, but next available quantities are gonna have negative value 
                    else if ($merchantProductVariation->stocks()->where('id', '>', $productVariationExistingStock->id)->where('available_quantity', '<', $productVariationExistingStock->stock_quantity)->exists()) {
                        
                        return true;

                    }
                    
                    return false;
                    */
                   
                    return true;

                }
                // decreased
                else if (! empty($expectedVariationInputedStock) && $merchantProductVariation->dispatchedRequests->count() && $productVariationExistingStock->stock_quantity > $expectedVariationInputedStock->stock_quantity) {

                    // new quantity is less than stock available quantity
                    if ($productVariationExistingStock->stock_quantity > $productVariationExistingStock->available_quantity && $expectedVariationInputedStock->stock_quantity < $productVariationExistingStock->available_quantity) {
                        
                        return true;

                    }
                    // has multiple stocks, but deducted quantity is more than ultimate available quantity
                    else if (($merchantProductVariation->available_quantity < ($productVariationExistingStock->stock_quantity - $expectedVariationInputedStock->stock_quantity)) /*|| $merchantProductVariation->stocks()->where('id', '>', $expectedVariationInputedStock->id)->where('available_quantity', '<', $expectedVariationInputedStock->stock_quantity)->exists()*/) {
                        
                        return true;

                    }
                    // only stock but deducted quantity is less than dispatched
                    /*
                    else if ($merchantProductVariation->stocks->count() < 2 && $merchantProductVariation->dispatchedRequests->sum('quantity') > ($productVariationExistingStock->stock_quantity - $expectedVariationInputedStock->stock_quantity)) {
                        
                        return true;

                    }
                    */

                    // return false;

                }

            }

            return false;

        }

        return false;
    }

    protected function checkProductSerialInValidity(ProductStock $productStock, $productSerials)
    {
        if ($productStock->serials()->where(function ($query) {$query->where('has_requisitions', 1)->orWhere('has_dispatched', 1);})->exists()) {
            
            $productSerialCollection = collect($productSerials);

            foreach ($productStock->serials()->where(function ($query) {$query->where('has_requisitions', 1)->orWhere('has_dispatched', 1);})->get() as $productSerialKey => $productSerial) {
                
                $productSerialExists = $productSerialCollection->first(function ($object, $key) use ($productSerial) {
                    return $object->serial_no == $productSerial->serial_no;
                });

                // if product expected serial not found
                if (empty($productSerialExists)) {
                    
                    // return true;
                    return $productSerial->serial_no;

                }

            }

        }

        return false;
    }

    /*
    protected function checkProductDuplicateSerial($stockingProduct)
    {
        foreach($stockingProduct->serials as $productSerial)
        {
            if ((empty($productSerial->id) && ProductSerial::where('serial_no', $productSerial->serial_no)->count()) || (! empty($productSerial->id) && ProductSerial::where('serial_no', $productSerial->serial_no)->where('id', '!=', $productSerial->id)->count())) {
                
                return $productSerial->serial_no;

            }
        }

        return false;
    }
    */

    protected function checkVariationInvalidSerial(ProductStock $productStock, $stockingProductVariations)
    {
        if ($productStock->variations()->whereHas('serials', function ($query) {
            $query->where('has_requisitions', 1)->orWhere('has_dispatched', 1);
        })->exists()) {

            // $stockingProductVariationCollection = collect($stockingProductVariations);
            
            foreach ($productStock->variations()->whereHas('serials', function ($query) {
                $query->where('has_requisitions', 1)->orWhere('has_dispatched', 1);
            })->get() as $productStockVariationKey => $productStockVariation) {
                
                foreach ($productStockVariation->serials()->where(
                    function($query) {
                        $query->where('has_requisitions', 1)->orWhere('has_dispatched', 1);
                })->get() as $productStockVariationSerialKey => $productStockVariationSerial) {
                    
                    foreach ($stockingProductVariations as $stockingProductVariationKey => $stockingProductVariation) {
                        
                        $stockingProductVariationSerialCollection = collect($stockingProductVariation->serials);

                        $productVariationSerialExists = $stockingProductVariationSerialCollection->first(function ($variationSerial, $variationSerialKey) use ($productStockVariationSerial) {
                            return $variationSerial->serial_no == $productStockVariationSerial->serial_no;
                        });

                        if (! empty($productVariationSerialExists)) {
                            break;
                        }

                    }

                    /*
                    $productVariationSerialExists = $stockingProductVariationCollection->first(function ($stockingProductVariation, $stockingProductVariationKey) use ($productStockVariationSerial) {

                        $stockingProductVariationSerialCollection = collect($stockingProductVariation->serials);

                        $stockingProductVariationSerialCollection->first(function ($variationSerial, $variationSerialKey) use ($productStockVariationSerial) {
                            return $variationSerial->serial_no == $productStockVariationSerial->serial_no;
                        });
                    });
                    */

                    // if variation serial not found
                    if (empty($productVariationSerialExists)) {
                        
                        // return true;
                        return $productStockVariationSerial->serial_no;

                    }

                }

            }

        }

        return false;
    }

    /*
    protected function checkVariationDuplicateSerial($stockingProduct)
    {
        foreach($stockingProduct->variations as $stockingProductVariationKey => $stockingProductVariation)
        {
            foreach ($stockingProductVariation->serials as $variationSerialKey => $stockingVariationSerial) {
                
                if ((empty($stockingVariationSerial->id) && ProductVariationSerial::where('serial_no', $stockingVariationSerial->serial_no)->count()) || (! empty($stockingVariationSerial->id) && ProductVariationSerial::where('serial_no', $stockingVariationSerial->serial_no)->where('id', '!=', $stockingVariationSerial->id)->count())) {
                    
                    // return true;
                    return $stockingVariationSerial->serial_no;

                }
                
            }
        }

        return false;
    }
    */
}
