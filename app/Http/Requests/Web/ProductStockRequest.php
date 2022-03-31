<?php

namespace App\Http\Requests\Web;

use App\Models\ProductStock;
use App\Models\ProductSerial;
use App\Models\MerchantProduct;
use Illuminate\Validation\Rule;
use App\Models\ProductVariationSerial;
use Illuminate\Foundation\Http\FormRequest;

class ProductStockRequest extends FormRequest
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
        $rules = [
            // 'merchant_id' => 'required|exists:merchants,id',
            // 'merchant.id' => 'required|exists:merchants,id',
            'warehouse.id' => 'required|exists:warehouses,id',
            'merchant_product_id' => [
                'required', 'integer', 'exists:merchant_products,id',
                /*
                Rule::exists('merchant_products', 'id')->where(function ($query) {
                    $query->where('merchant_id', $this->input('merchant.id'));
                })
                */
            ],
            'stock_quantity' => 'required|integer|min:1',
            'unit_buying_price' => 'nullable|numeric',
            // 'product_id' => 'required|numeric|exists:products,id',
        ];
            
        $merchantProduct = MerchantProduct::findOrFail($this->input('merchant_product_id'));
        $product = $merchantProduct->product;

        if ($product->has_serials && ! $product->has_variations) {
            $rules['serials'] = 'required|array|min:'.$this->input('stock_quantity');
        }
        if ($product->has_serials && ! $product->has_variations) {
            
            foreach (json_decode(json_encode($this->input('serials'))) as $productSerialkey => $productSerial) {
                
                if (isset($productSerial->id)) {
                    // $rules['serials.'.$productSerialkey.'.serial_no'] = 'required|string|distinct|min:4|unique:product_serials,serial_no,'.$productSerial->id;
                    
                    $rules['serials.'.$productSerialkey.'.serial_no'] = [
                        'required', 'string', 'distinct', 'min:4', 

                        Rule::unique('product_serials', 'serial_no')
                        ->ignore(1, 'has_dispatched')
                        ->where(function ($query) use ($productSerial) {
                            return $query->where('id', '!=', $productSerial->id);
                        })
                    ];
                }
                else {
                    // $rules['serials.'.$productSerialkey.'.serial_no'] = 'required|string|distinct|min:4|unique:product_serials,serial_no';
                    
                    $rules['serials.'.$productSerialkey.'.serial_no'] = [
                        'required', 'string', 'distinct', 'min:4', 

                        Rule::unique('product_serials', 'serial_no')->ignore(1, 'has_dispatched')
                    ];
                }

            }

        }
        if ($product->has_variations) {
            $rules['variations'] = 'required|array';
        }
        if ($product->has_variations) {
            // $rules['variations.*.id'] = 'required_without:variations.*.merchant_product_variation_id|integer|exists:merchant_product_variations,id';

            // $rules['variations.*.merchant_product_variation_id'] = 'required_without:variations.*.id|integer|exists:merchant_product_variations,id';

            foreach (json_decode(json_encode($this->input('variations'))) as $stockingProductVariationKey => $stockingProductVariation) {
                
                if (empty($stockingProductVariation->merchant_product_variation_id)) {
                    
                    $rules['variations.'.$stockingProductVariationKey.'.id'] = 'required|integer|exists:merchant_product_variations,id';

                }
                else {

                    $rules['variations.'.$stockingProductVariationKey.'.merchant_product_variation_id'] = 'required|integer|exists:merchant_product_variations,id';

                }

            }

        }
        if ($product->has_variations && array_sum(array_column($this->input('variations'), 'stock_quantity')) != $this->input('stock_quantity')) {
            $rules['variations.*.stock_quantity'] = 'required|integer|min:1';
            $rules['variations.*.unit_buying_price'] = 'nullable|numeric';
        }
        if ($product->has_variations && $product->has_serials) {

            foreach (json_decode(json_encode($this->input('variations'))) as $stockingProductVariationKey => $stockingProductVariation) {
                
                if (! empty($stockingProductVariation->stock_quantity) && $stockingProductVariation->stock_quantity > 0) {
                    
                    $rules['variations.'.$stockingProductVariationKey.'.serials'] = 'required|array|exclude_if:variations.*.stock_quantity,';

                }

            }

        }
        if ($product->has_variations && $product->has_serials) {

            foreach (json_decode(json_encode($this->input('variations'))) as $stockingProductVariationKey => $stockingProductVariation) {
                
                foreach ($stockingProductVariation->serials as $variationSerialkey => $variationSerial) {
                    
                    if (isset($variationSerial->id)) {
                        // $rules['variations.'.$stockingProductVariationKey.'.serials.'.$variationSerialkey.'.serial_no'] = 'required|string|distinct|min:4|unique:product_variation_serials,serial_no,'.$variationSerial->id;
                        
                        $rules['variations.'.$stockingProductVariationKey.'.serials.'.$variationSerialkey.'.serial_no'] = [
                            'required','string','distinct','min:4',
                            
                            Rule::unique('product_variation_serials', 'serial_no')
                            ->ignore(1, 'has_dispatched')
                            ->where(function ($query) use ($variationSerial) {
                                return $query->where('id', '!=', $variationSerial->id);
                            })
                        ];
                    }
                    else {
                        // $rules['variations.'.$stockingProductVariationKey.'.serials.'.$variationSerialkey.'.serial_no'] = 'required|string|distinct|min:4|unique:product_variation_serials,serial_no';
                        
                        $rules['variations.'.$stockingProductVariationKey.'.serials.'.$variationSerialkey.'.serial_no'] = [
                            'required','string','distinct','min:4',
                            
                            Rule::unique('product_variation_serials', 'serial_no')->ignore(1, 'has_dispatched')
                            
                        ];
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
            'warehouse.id.exists' => 'Warehouse is invalid !',
            'warehouse.id.*' => 'Warehouse is required !',
            'merchant_product_id.required' => 'Merchant-Product id is required !',
            'merchant_product_id.*' => 'Merchant-Product id is invalid !',
            'stock_quantity.*' => 'Stock quantity is required !',
            'unit_buying_price.*' => 'Buying price should be numeric !',
            'serials.required' => 'Product serial is required !',
            
            'serials.*.serial_no' => [
                'distinct' => 'Duplicate product-serial is invalid',
                'unique' => 'Product serial exists',
                '*' => 'Product serial is required',
            ],
            
            // 'product_id.*' => 'Product id is missing !',
            // 'serials.*.unique' => ':attribute serial must be unique !',
            
            'variations.*.id.required' => 'Variation id is required !',
            'variations.*.id.*' => 'Variation id is invalid !',
            'variations.*.stock_quantity' => [
                'required' => 'Variation quantity should be equal to product quantity',
                '*' => 'Variation quantity is required !',
            ],
            'variations.*.unit_buying_price.*' => 'Buying price should be numeric !',

            'variations.*.serials.*' => 'Variation serial is required',

            'variations.*.serials.*.serial_no' => [
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

            if ($this->route('stock')) {
                
                $stockToUpdate = ProductStock::findOrFail($this->route('stock'));
                $merchantExpectedProduct = $stockToUpdate->merchantProduct;
                $product = $merchantExpectedProduct->product;

                // decreasing quantity
                if (! $product->has_variations && $stockToUpdate->stock_quantity > $this->stock_quantity) {
                    
                    $difference = $stockToUpdate->stock_quantity - $this->stock_quantity;

                    if ($difference > $stockToUpdate->available_quantity || $difference > /*$stockToUpdate->merchantProduct->latestStock->available_quantity*/ $stockToUpdate->merchantProduct->available_quantity) {

                        return response()->json(['errors'=>["invalidValue" => "Stock quantity is less than minimum"]], 422);
                        
                    }

                }
                else if($product->has_variations && $this->findVariationInvalidQuantity(json_decode(json_encode($this->variations)), $stockToUpdate)) {

                    return response()->json(['errors'=>["invalidVariationData" => "Trying to update immutable variation or less than minimum"]], 422);

                }

                if ($product->has_serials && ! $product->has_variations && ($this->checkProductSerialInValidity($stockToUpdate, json_decode(json_encode($this->serials))) || $this->checkProductSerialDuplicacy($this))) {

                    if ($this->checkProductSerialInValidity($stockToUpdate, json_decode(json_encode($this->serials)))) {
                        
                        $serialNotFound = $this->checkProductSerialInValidity($stockToUpdate, json_decode(json_encode($this->serials)));
                        return response()->json(['errors'=>["$serialNotFound" => "$serialNotFound serial not found"]], 422);

                    }
                    else {

                        $duplicateValue = $this->checkProductSerialDuplicacy($this);
                        return response()->json(['errors'=>["$duplicateValue" => "$duplicateValue serial is taken"]], 422);

                    }

                }
                else if ($product->has_serials && $product->has_variations && ($this->checkVariationSerialInvalidity($stockToUpdate, json_decode(json_encode($this->variations))) || $this->checkVariationSerialDuplicacy($this))) {
                    
                    if ($this->checkVariationSerialInvalidity($stockToUpdate, json_decode(json_encode($this->variations)))) {
                        
                        $serialNotFound = $this->checkVariationSerialInvalidity($stockToUpdate, json_decode(json_encode($this->variations)));
                        return response()->json(['errors'=>["$serialNotFound" => "$serialNotFound serial not found"]], 422);

                    }
                    else {

                        $duplicateValue = $this->checkVariationSerialDuplicacy($this);
                        return response()->json(['errors'=>["$duplicateValue" => "$duplicateValue serial is taken"]], 422);

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

    protected function checkProductSerialDuplicacy(ProductStockRequest $request)
    {
        foreach($request->serials as $productSerial)
        {
            if ((empty($productSerial['id']) && ProductSerial::where('serial_no', $productSerial['serial_no'])->count()) || (! empty($productSerial['id']) && ProductSerial::where('serial_no', $productSerial['serial_no'])->where('id', '!=', $productSerial['id'])->count())) {
                
                return $productSerial['serial_no'];

            }
        }

        return false;
    }

    protected function checkVariationSerialInvalidity(ProductStock $productStock, $stockVariations)
    {
        if ($productStock->variations()->whereHas('serials', function ($query) {
            $query->where('has_requisitions', 1)->orWhere('has_dispatched', 1);
        })->exists()) {

            foreach ($productStock->variations()->whereHas('serials', function ($query) {
                $query->where('has_requisitions', 1)->orWhere('has_dispatched', 1);
            })->get() as $productStockVariationKey => $productStockVariation) {

                $productVariationToCheck = $this->getVariationExpectedKey($stockVariations, $productStockVariation->id);

                if ($productVariationToCheck!==false && ! collect($stockVariations[$productVariationToCheck]->serials)->isEmpty()) {
                    
                    $productVariationSerialCollection = collect($stockVariations[$productVariationToCheck]->serials);
                    
                }
                else {

                    return 'Required variation'; // true

                }

                foreach ($productStockVariation->serials()->where(
                    function($query) {
                        $query->where('has_requisitions', 1)->orWhere('has_dispatched', 1);
                    })->get() as $productStockVariationSerialKey => $productStockVariationSerial) {
                    
                    $productVariationSerialExists = $productVariationSerialCollection->first(function ($object, $key) use ($productStockVariationSerial) {
                        return $object->serial_no == $productStockVariationSerial->serial_no;
                    });

                    // if variation expected serial not found
                    if (empty($productVariationSerialExists)) {
                        
                        // return true;
                        return $productStockVariationSerial->serial_no;

                    }

                }

            }

            return false;

        }

        return false;
    }

    protected function checkVariationSerialDuplicacy(ProductStockRequest $request)
    {
        foreach($request->variations as $stockVariationKey => $stockVariation)
        {
            foreach ($stockVariation['serials'] as $stockVariationSerialKey => $stockVariationSerial) {
                
                if ((empty($stockVariationSerial['id']) && ProductVariationSerial::where('serial_no', $stockVariationSerial['serial_no'])->count()) || (! empty($stockVariationSerial['id']) && ProductVariationSerial::where('serial_no', $stockVariationSerial['serial_no'])->where('id', '!=', $stockVariationSerial['id'])->count())) {
                    
                    // return true;
                    return $stockVariationSerial['serial_no'];

                }
                
            }
        }

        return false;
    }

    protected function getVariationExpectedKey($stockVariations, $stockVariationId)
    {
        foreach ($stockVariations as $stockVariationKey => $stockVariation) {
           if ($stockVariation->id == $stockVariationId) {
               return $stockVariationKey;
           }
        }

        return false;
    }

}
