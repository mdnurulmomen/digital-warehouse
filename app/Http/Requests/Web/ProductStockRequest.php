<?php

namespace App\Http\Requests\Web;

use App\Models\ProductStock;
use App\Models\ProductSerial;
use App\Models\MerchantProduct;
use Illuminate\Validation\Rule;
use App\Models\ProductVariationSerial;
// use App\Rules\ProductVariationSerialRule;
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
            // 'product_id' => 'required|numeric|exists:products,id',
            // 'addresses' => 'required|array|min:1'
        ];

        if (! $this->route('stock') || ($this->route('stock') && $this->has('available_quantity') && $this->input('available_quantity') > 0) || ($this->route('stock') && $this->input('stock_quantity') > ProductStock::findOrFail($this->route('stock'))->stock_quantity)) {        // when creating or product is available or stock_quantity is increased when updating
            
            $rules['addresses'] = 'required|array|min:1';

        }

        if (! empty($this->input('id')) && $this->route('stock')) {
            
            $rules['stock_code'] = 'nullable|string|max:12|unique:product_stocks,stock_code,'.$this->input('id');

        }
        else {

            $rules['stock_code'] = 'nullable|string|max:12|unique:product_stocks,stock_code';

        }
            
        $merchantProduct = MerchantProduct::findOrFail($this->input('merchant_product_id'));
        $product = $merchantProduct->product;
        $merchant = $merchantProduct->merchant;

        if ($merchant->supportDeal->purchase_support && $merchant->supportDeal->rents()->whereDate('date_to', '>=', now())->count()) {
            
            $rules['vendor_id'] = 'required|exists:vendors,id';
            $rules['location_id'] = 'required|exists:locations,id';

            if (! $product->has_variations) {
                
                $rules['unit_buying_price'] = 'required|numeric|min:0';

            }


        }

        // address
        foreach (json_decode(json_encode($this->input('addresses'))) as $addressKey => $address) {
            
            if ($address->type=='containers') {
                $rules['addresses.'.$addressKey.'.containers'] = 'required|array|min:1';

                foreach ($address->containers as $containerKey => $container) {
                    $rules['addresses.'.$addressKey.'.containers.'.$containerKey.'.id'] = [
                        'required', 
                        Rule::exists('dealt_spaces', 'space_id')
                        ->where('space_type', 'App\Models\WarehouseContainerStatus')
                    ];
                }

            }
            else if ($address->type=='shelves') {
                $rules['addresses.'.$addressKey.'.container'] = 'required';
                $rules['addresses.'.$addressKey.'.container.shelves'] = 'required|array|min:1';
            }
            else if ($address->type=='units') {
                $rules['addresses.'.$addressKey.'.container'] = 'required';
                $rules['addresses.'.$addressKey.'.container.shelf'] = 'required';
                $rules['addresses.'.$addressKey.'.container.shelf.units'] = 'required|array|min:1';
            }

        }

        // serials
        if ($product->has_serials && ! $product->has_variations) {
            $rules['serials'] = 'required|array|min:'.$this->input('stock_quantity');
            
            foreach (json_decode(json_encode($this->input('serials'))) as $productSerialkey => $productSerial) {
                
                if (isset($productSerial->id)) {
                    // $rules['serials.'.$productSerialkey.'.serial_no'] = 'required|string|distinct|min:4|unique:product_serials,serial_no,'.$productSerial->id;
                    
                    $rules['serials.'.$productSerialkey.'.serial_no'] = [
                        'required', 'string', 'distinct', 'min:4', 

                        Rule::unique('product_serials', 'serial_no')
                        ->where(function ($query) use ($productSerial) {
                            return $query->where('id', '!=', $productSerial->id);
                        })
                        ->ignore(1, 'has_dispatched')
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

        // variations
        if ($product->has_variations) {

            $rules['variations'] = 'required|array|min:1';
            
            // $rules['variations.*.id'] = 'required_without:variations.*.merchant_product_variation_id|integer|exists:merchant_product_variations,id';

            // $rules['variations.*.merchant_product_variation_id'] = 'required_without:variations.*.id|integer|exists:merchant_product_variations,id';

            $rules['variations.*.stock_quantity'] = 'sometimes|integer|min:0';

            if (array_sum(array_column($this->input('variations'), 'stock_quantity')) != $this->input('stock_quantity')) {

                $rules['total_stock_quantity'] = 'required|integer|min:'.array_sum(array_column($this->input('variations'), 'stock_quantity'));

            }

            foreach (json_decode(json_encode($this->input('variations'))) as $stockingProductVariationKey => $stockingProductVariation) {
                
                if (empty($stockingProductVariation->merchant_product_variation_id)) {  // updating
                    
                    $rules['variations.'.$stockingProductVariationKey.'.id'] = 'required|integer|exists:merchant_product_variations,id';

                }
                else {  // creating

                    $rules['variations.'.$stockingProductVariationKey.'.merchant_product_variation_id'] = 'required|integer|exists:merchant_product_variations,id';

                }

                if (! empty($stockingProductVariation->id) && $this->route('stock')) {
            
                    $rules['variations.'.$stockingProductVariationKey.'.stock_code'] = 'nullable|string|max:12|unique:product_variation_stocks,stock_code,'.$stockingProductVariation->id;

                }
                else {

                    $rules['variations.'.$stockingProductVariationKey.'.stock_code'] = 'nullable|string|max:12|unique:product_variation_stocks,stock_code';

                }

                if ($merchant->supportDeal->purchase_support && $merchant->supportDeal->rents()->whereDate('date_to', '>=', now())->count() && ! empty($stockingProductVariation->stock_quantity) && $stockingProductVariation->stock_quantity > 0) {
                
                    $rules['variations.'.$stockingProductVariationKey.'.unit_buying_price'] = 'required|numeric|min:0';
                    
                }

            }

        }
        
        /*
        // Variation Serial Duplicacy
        if ($product->has_variations && $product->has_serials) {

            foreach (json_decode(json_encode($this->input('variations'))) as $stockingProductVariationKey => $stockingProductVariation) {
                
                if (! empty($stockingProductVariation->stock_quantity) && $stockingProductVariation->stock_quantity > 0) {
                    
                    // $rules['variations.'.$stockingProductVariationKey.'.serials'] = 'required|array|exclude_if:variations.*.stock_quantity,';
                    
                    $rules['variations.'.$stockingProductVariationKey.'.serials'] = ['required', 'array'];

                }
                
                foreach ($stockingProductVariation->serials as $variationSerialkey => $variationSerial) {
                    
                    if (isset($variationSerial->id)) {
                        // $rules['variations.'.$stockingProductVariationKey.'.serials.'.$variationSerialkey.'.serial_no'] = 'required|string|distinct|min:4|unique:product_variation_serials,serial_no,'.$variationSerial->id;
                        
                        $rules['variations.'.$stockingProductVariationKey.'.serials.'.$variationSerialkey.'.serial_no'] = [
                            'required','string','distinct','min:4',
                            
                            Rule::unique('product_variation_serials', 'serial_no')
                            ->where(function ($query) use ($variationSerial) {
                                return $query->where('id', '!=', $variationSerial->id);
                            })
                            ->ignore(1, 'has_dispatched')
                        ];
                    }
                    else {
                        // $rules['variations.'.$stockingProductVariationKey.'.serials.'.$variationSerialkey.'.serial_no'] = 'required|string|distinct|min:4|unique:product_variation_serials,serial_no';
                        
                        $rules['variations.'.$stockingProductVariationKey.'.serials.'.$variationSerialkey.'.serial_no'] = [
                            'required', 'string', 'distinct', 'min:4',
                            
                            Rule::unique('product_variation_serials', 'serial_no')->ignore(1, 'has_dispatched')
                        ];
                    }

                }

            }

        }
        */

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
            'total_stock_quantity.*' => 'Variation total Stock qty is more or less than product qty !',
            'unit_buying_price.required' => 'Buying price is required !',
            'unit_buying_price.*' => 'Buying price should be numeric !',
            
            // 'addresses.*.container.shelf.units'] => 'Unit address is invalid',
            // 'addresses.*.container.shelf.units'] => 'Unit address is invalid',
            // 'addresses.*.container.shelf.units'] => 'Unit address is invalid',

            'serials.required' => 'Product serial is required !',
            
            'serials.*.serial_no' => [
                'distinct' => 'Duplicate product-serial is invalid',
                'unique' => 'Product serial exists',
                '*' => 'Product serial is required',
            ],

            'vendor_id.required' => 'Vendor is required',
            'vendor_id.*' => 'Vendor is invalid',

            'location_id.required' => 'Location is required',
            'location_id.*' => 'Location is invalid',
            
            // 'product_id.*' => 'Product id is missing !',
            // 'serials.*.unique' => ':attribute serial must be unique !',
            
            'variations.*.id.required' => 'Variation id is required !',
            'variations.*.id.*' => 'Variation id is invalid !',
            'variations.*.stock_quantity' => [
                '*' => 'Variation quantity is invalid !',
            ],

            'variations.*.unit_buying_price.required' => 'Buying price is required !',
            'variations.*.unit_buying_price.*' => 'Buying price should be numeric !',

            // 'variations.*.serials.*' => 'Variation serial is required',

            // 'variations.*.serials.*.serial_no' => [
            //     'distinct' => 'Variation duplicate serial is invalid',
            //     'unique' => 'Variation serial exists',
            //     '*' => 'Variation serial is required',
            // ]
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

            // always
            if (property_exists($this, 'variations') && count($this->input('variations')) && $this->checkVariationSerialDuplicacy($this)) {
                
                $duplicateValue = $this->checkVariationSerialDuplicacy($this);
                // return response()->json(['errors'=>["$duplicateValue" => "$duplicateValue serial is taken"]], 422);
                $validator->errors()->add("$duplicateValue", ucfirst($duplicateValue)." serial is taken");

            }

            // only for update operation
            if ($this->route('stock')) {
                
                $stockToUpdate = ProductStock::findOrFail($this->route('stock'));
                $merchantExpectedProduct = $stockToUpdate->merchantProduct;
                $product = $merchantExpectedProduct->product;

                // decreasing quantity
                if (! $product->has_variations && $stockToUpdate->stock_quantity > $this->stock_quantity && ($stockToUpdate->stock_quantity - $this->stock_quantity) > $stockToUpdate->available_quantity) {
                    
                    // $difference = $stockToUpdate->stock_quantity - $this->stock_quantity;

                    // if ($difference > $stockToUpdate->available_quantity || $difference > /*$stockToUpdate->merchantProduct->latestStock->available_quantity*/ $stockToUpdate->merchantProduct->available_quantity) {

                        // return response()->json(['errors'=>["invalidValue" => "Stock quantity is less than minimum"]], 422);
                        
                        $validator->errors()->add("invalidValue", "Stock quantity is less than minimum");
                        
                    // }

                }
                else if($product->has_variations && $this->findVariationInvalidQuantity(json_decode(json_encode($this->variations)), $stockToUpdate)) {

                    // return response()->json(['errors'=>["invalidVariationData" => "Trying to update immutable variation or less than minimum"]], 422);
                    $validator->errors()->add("invalidVariationData", "Trying to update immutable variation or less than minimum");

                }

                if ($product->has_serials && ! $product->has_variations && ($this->checkProductRequiredSerialMissing($stockToUpdate, json_decode(json_encode($this->serials))) /*|| $this->checkProductSerialDuplicacy($this)*/)) {

                    // if ($this->checkProductRequiredSerialMissing($stockToUpdate, json_decode(json_encode($this->serials)))) {
                        
                        $serialNotFound = $this->checkProductRequiredSerialMissing($stockToUpdate, json_decode(json_encode($this->serials)));
                        // return response()->json(['errors'=>["$serialNotFound" => "$serialNotFound serial not found"]], 422);
                        $validator->errors()->add("$serialNotFound", "$serialNotFound serial not found");

                    // }
                    /*
                    else {

                        $duplicateValue = $this->checkProductSerialDuplicacy($this);
                        return response()->json(['errors'=>["$duplicateValue" => "$duplicateValue serial is taken"]], 422);

                    }
                    */

                }
                else if ($product->has_serials && $product->has_variations && $this->checkVariationRequiredSerialMissing($stockToUpdate, json_decode(json_encode($this->variations))) /*|| $this->checkVariationSerialDuplicacy($this)*/) {
                    
                    // if ($this->checkVariationRequiredSerialMissing($stockToUpdate, json_decode(json_encode($this->variations)))) {
                        
                        $serialNotFound = $this->checkVariationRequiredSerialMissing($stockToUpdate, json_decode(json_encode($this->variations)));
                        $validator->errors()->add("$serialNotFound", "$serialNotFound serial not found");
                        // return response()->json(['errors'=>["$serialNotFound" => "$serialNotFound serial not found"]], 422);

                    // }
                    /*
                    else {

                        $duplicateValue = $this->checkVariationSerialDuplicacy($this);
                        return response()->json(['errors'=>["$duplicateValue" => "$duplicateValue serial is taken"]], 422);

                    }
                    */
                    
                }   

            }

        });
    }

    protected function findVariationInvalidQuantity($stockVariaitons = [], ProductStock $productStock)
    {
        if (count($stockVariaitons) && $productStock->variations->count() && $productStock->variations()->where(function ($query) {$query->whereRaw('stock_quantity > available_quantity');})->exists()) {
            
            $stockVariationCollection = collect($stockVariaitons);

            foreach ($productStock->variations()->where(function ($query) {$query->whereRaw('stock_quantity > available_quantity');})->get() as $productVariationExistingStockKey => $productVariationExistingStock) {
                
                // $merchantProductVariation = $productVariationExistingStock->merchantProductVariation;
                // $expectedVariationeLatestStock = $merchantProductVariation->latestStock;

                $expectedVariationInputedStock = $stockVariationCollection->first(function ($object, $key) use ($productVariationExistingStock) {
                    return $object->merchant_product_variation_id == $productVariationExistingStock->merchant_product_variation_id;
                });

                if (empty($expectedVariationInputedStock)) {
                    
                    return "Missing required variation"; // true

                }
                // decreasing quantity is less than available
                else if (! empty($expectedVariationInputedStock) && $expectedVariationInputedStock->stock_quantity < $productVariationExistingStock->available_quantity) {
                    
                    return "Quantity is less than required";

                }

                // replaced variation-stock but replacing-variation available quantity is less than existing quantity
                // if (empty($expectedVariationInputedStock) && $merchantProductVariation->dispatchedRequests->count() && $merchantProductVariation->available_quantity < $productVariationExistingStock->stock_quantity) {
                    
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
                   
                    // return true;

                // }
                // decreased
                // else if (! empty($expectedVariationInputedStock) && $merchantProductVariation->dispatchedRequests->count() && $productVariationExistingStock->stock_quantity > $expectedVariationInputedStock->stock_quantity) {

                    // new quantity is less than stock available quantity
                    // if ($productVariationExistingStock->stock_quantity > $productVariationExistingStock->available_quantity && $expectedVariationInputedStock->stock_quantity < $productVariationExistingStock->available_quantity) {
                        
                        // return true;

                    // }
                    // has multiple stocks, but deducted quantity is more than ultimate available quantity
                    // else if (($merchantProductVariation->available_quantity < ($productVariationExistingStock->stock_quantity - $expectedVariationInputedStock->stock_quantity)) /*|| $merchantProductVariation->stocks()->where('id', '>', $expectedVariationInputedStock->id)->where('available_quantity', '<', $expectedVariationInputedStock->stock_quantity)->exists()*/) {
                        
                        // return true;

                    // }
                    // only stock but deducted quantity is less than dispatched
                    /*
                    else if ($merchantProductVariation->stocks->count() < 2 && $merchantProductVariation->dispatchedRequests->sum('quantity') > ($productVariationExistingStock->stock_quantity - $expectedVariationInputedStock->stock_quantity)) {
                        
                        return true;

                    }
                    */

                    // return false;

                // }

            }

            return false;

        }

        return false;
    }

    protected function checkProductRequiredSerialMissing(ProductStock $productStock, $productSerials)
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

            return false;

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

    protected function checkVariationRequiredSerialMissing(ProductStock $productStock, $stockVariations)
    {
        if ($productStock->variations()->whereHas('serials', function ($query) {
            $query->where('has_requisitions', 1)->orWhere('has_dispatched', 1);
        })->exists()) {

            foreach ($productStock->variations()->whereHas('serials', function ($query) {
                $query->where('has_requisitions', 1)->orWhere('has_dispatched', 1);
            })->get() as $productStockVariationKey => $productStockVariation) {

                $productVariationToCheck = $this->getVariationExpectedKey($stockVariations, $productStockVariation->id);

                if ($productVariationToCheck !== false && ! collect($stockVariations[$productVariationToCheck]->serials)->isEmpty()) {
                    
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
            if (! empty($stockVariation['stock_quantity']) && $stockVariation['stock_quantity'] > 0) {
                
                if (empty($stockVariation['serials']) || ! is_array($stockVariation['serials']) || count($stockVariation['serials']) != $stockVariation['stock_quantity']) {
                    
                    return response()->json(['errors'=>["requiredSerial" => ucfirst($stockVariation['variation']['name']).' serials are missing or invalid']], 422);

                }
                else {

                    foreach ($stockVariation['serials'] as $stockVariationSerialKey => $stockVariationSerial) {
                        
                        if (empty($stockVariationSerial['serial_no']) || ! is_string($stockVariationSerial['serial_no']) || strlen($stockVariationSerial['serial_no']) < 4) {
                                
                            return response()->json(['errors'=>["invalidSerial" => 'Variatons '.($stockVariationKey + 1).'serial '.($stockVariationSerialKey + 1)."is invalid"]], 422);
                            
                        }
                        else if ((empty($stockVariationSerial['id']) && ProductVariationSerial::where('serial_no', $stockVariationSerial['serial_no'])->where('has_dispatched', 0)->count()) || (! empty($stockVariationSerial['id']) && ProductVariationSerial::where('serial_no', $stockVariationSerial['serial_no'])->where('id', '!=', $stockVariationSerial['id'])->where('has_dispatched', 0)->count())) {
                            
                            // return true;
                            return $stockVariationSerial['serial_no'];

                        }

                    }

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
