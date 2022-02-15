<?php

namespace App\Http\Requests\Web;

use App\Models\MerchantProduct;
use Illuminate\Validation\Rule;
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
                'required', 'numeric', 
                Rule::exists('merchant_products', 'id')->where(function ($query) {
                    $query->where('merchant_id', $this->input('merchant.id'));
                })
            ],
            'products.*.stock_quantity' => 'required|numeric|min:1',
            'products.*.unit_buying_price' => 'nullable|numeric',
            // 'product_id' => 'required|numeric|exists:products,id',
        ];

        foreach ($storingProducts as $stockingProductKey => $stockingProduct) {
            
            $merchantProduct = MerchantProduct::findOrFail($stockingProduct->merchant_product_id);
            $product = $merchantProduct->product;

            if ($product->has_serials && ! $product->has_variations) {
                $rules['products.'.$stockingProductKey.'.serials'] = 'required|array|min:'.$stockingProduct->stock_quantity;
            }
            if ($product->has_serials && ! $product->has_variations) {
                if (isset($stockingProduct->id)) {
                    $rules['products.'.$stockingProductKey.'.serials.*.serial_no'] = 'required|string|distinct|min:4|unique:product_serials,serial_no,'.$stockingProduct->id;
                }
                else {
                    $rules['products.'.$stockingProductKey.'.serials.*.serial_no'] = 'required|string|distinct|min:4|unique:product_serials,serial_no';
                }
            }
            if ($product->has_variations) {
                $rules['products.'.$stockingProductKey.'.variations'] = 'required|array';
            }
            if ($product->has_variations) {
                $rules['products.'.$stockingProductKey.'.variations.*.merchant_product_variation_id'] = 'required|numeric|exists:merchant_product_variations,id';
            }
            if ($product->has_variations && array_sum(array_column($stockingProduct->variations, 'stock_quantity')) != $stockingProduct->stock_quantity) {
                $rules['products.'.$stockingProductKey.'.variations.*.stock_quantity'] = 'required|numeric|min:1';
                $rules['products.'.$stockingProductKey.'.variations.*.unit_buying_price'] = 'nullable|numeric';
            }
            if ($product->has_variations && $product->has_serials) {

                foreach ($stockingProduct->variations as $stockingProductVariationKey => $stockingProductVariation) {
                    
                    if (! empty($stockingProductVariation->stock_quantity) && $stockingProductVariation->stock_quantity > 0) {
                        
                        $rules['products.'.$stockingProductKey.'.variations.'.$stockingProductVariationKey.'.serials'] = 'required|array';

                    }

                }

            }
            if ($product->has_variations && $product->has_serials) {
                if (isset($stockingProduct->id)) {
                    $rules['products.'.$stockingProductKey.'.variations.*.serials.*.serial_no'] = 'required|string|distinct|min:4|unique:product_variation_serials,serial_no,'.$stockingProduct->id;
                }
                else{
                    $rules['products.'.$stockingProductKey.'.variations.*.serials.*.serial_no'] = 'required|string|distinct|min:4|unique:product_variation_serials,serial_no';
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
}
