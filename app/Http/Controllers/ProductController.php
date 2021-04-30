<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductStock;
use Illuminate\Http\Request;
use App\Models\ProductSerial;
use App\Models\ProductCategory;
use Illuminate\Validation\Rule;
use App\Models\ProductVariationSerial;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\Web\ProductCollection;
use App\Http\Resources\Web\ProductStockCollection;

class ProductController extends Controller
{
    public function __construct()
    {
        // Product-Category
        $this->middleware("permission:view-product-category-index")->only(['showProductAllCategories', 'searchProductAllCategories']);
        $this->middleware("permission:create-product-category")->only('storeProductNewCategory');
        $this->middleware("permission:update-product-category")->only('updateProductCategory');
        $this->middleware("permission:delete-product-category")->only(['deleteProductCategory', 'restoreProductCategory']);

        // Product
        $this->middleware("permission:view-product-index")->only(['showAllProducts', 'searchAllProducts']);
        $this->middleware("permission:create-product")->only('storeNewProduct');
        $this->middleware("permission:update-product")->only('updateProduct');
        
        // Product-Stock
        $this->middleware("permission:view-product-stock-index")->only(['showProductAllStocks', 'searchProductAllStocks']);
        $this->middleware("permission:create-product-stock")->only('storeProductStock');
        $this->middleware("permission:update-product-stock")->only('updateProductStock');
        $this->middleware("permission:delete-product-stock")->only('deleteProductStock');
    }

    // Product-Categories
    public function showProductAllCategories($perPage=false)
    {
        if ($perPage) {
            
            return response()->json([

                'current' => ProductCategory::paginate($perPage),
                'trashed' => ProductCategory::onlyTrashed()->paginate($perPage),

            ], 200);

        }

        return ProductCategory::all();
    }

    public function storeProductNewCategory(Request $request, $perPage)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:product_categories,name',
            'parent_category_id' => 'nullable|exists:product_categories,id',
        ]);

        $newAsset = new ProductCategory();

        $newAsset->name = strtolower($request->name);
        $newAsset->parent_category_id = $request->parent_category_id;

        $newAsset->save();

        return $this->showProductAllCategories($perPage);
    }

    public function updateProductCategory(Request $request, $asset, $perPage)
    {
        $assetToUpdate = ProductCategory::findOrFail($asset);

        $request->validate([
            'name' => 'required|string|max:100|unique:product_categories,name,'.$assetToUpdate->id,
            'parent_category_id' => 'nullable|exists:product_categories,id',
        ]);

        $assetToUpdate->name = strtolower($request->name);
        $assetToUpdate->parent_category_id = $request->parent_category_id;

        $assetToUpdate->save();

        return $this->showProductAllCategories($perPage);
    }

    public function deleteProductCategory($asset, $perPage)
    {
        $assetToDelete = ProductCategory::findOrFail($asset);
        // $userToDelete->warehouses()->delete();
        $assetToDelete->delete();

        return $this->showProductAllCategories($perPage);
    }

    public function restoreProductCategory($asset, $perPage)
    {
        $assetToRestore = ProductCategory::withTrashed()->findOrFail($asset);
        // $userToRestore->warehouses()->restore();
        $assetToRestore->restore();

        return $this->showProductAllCategories($perPage);
    }

    public function searchProductAllCategories($search, $perPage)
    {
        $columnsToSearch = ['name'];

        $query = ProductCategory::withTrashed();

        foreach($columnsToSearch as $column){
            $query->orWhere($column, 'like', "%$search%");
        }

        return response()->json([
            'all' => $query->paginate($perPage),    
        ], 200);
    }


    // Products
    public function showAllProducts($perPage=false)
    {
        if ($perPage) {

            return response()->json([

                'retail' => new ProductCollection(Product::where('product_category_id', '!=', 0)->paginate($perPage)),
                'bulk' => new ProductCollection(Product::whereNull('product_category_id')->orWhere('product_category_id', 0)->paginate($perPage)),

            ], 200);

        }

        return Product::all();
    }

    public function storeNewProduct(Request $request, $perPage)
    {
        $request->validate([
            'product_category_id' => 'nullable|numeric|exists:product_categories,id',
            'merchant_id' => 'required|numeric|exists:merchants,id',
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:65535',
            'sku' => 'nullable|string|max:100|unique:products,sku',
            'price' => 'nullable|numeric|min:0', // min 0 due to bulk products
            'quantity_type' => 'nullable|string|max:100',
            'has_serials' => 'nullable|boolean',
            'has_variations' => 'nullable|boolean',
            'variations' => 'required_if:has_variations,1|array',
            'variations.*.variation' => 'required_if:has_variations,1',
            'variations.*.price' => 'required_if:has_variations,1',
        ]);

        $newProduct = new Product();

        $newProduct->name = strtolower($request->name);
        $newProduct->description = strtolower($request->description);
        $newProduct->sku = $request->sku ?? $this->generateProductSKU($request);
        $newProduct->price = $request->price ?? 0;
        $newProduct->quantity_type = strtolower($request->quantity_type) ?? ApplicationSetting::first()->default_measure_unit ?? 'kg';
        $newProduct->has_serials = $request->has_serials ?? false;
        $newProduct->has_variations = $request->has_variations ?? false;
        $newProduct->product_category_id = $request->product_category_id;
        $newProduct->merchant_id = $request->merchant_id;

        $newProduct->save();

        if ($newProduct->has_variations && !empty($request->variations)) {

            $newProduct->product_variations = json_decode(json_encode($request->variations));

        }

        return $this->showAllProducts($perPage);
    }

    public function updateProduct(Request $request, $product, $perPage)
    {
        $productToUpdate = Product::findOrFail($product);

        $request->validate([
            'product_category_id' => 'nullable|numeric|exists:product_categories,id',
            'merchant_id' => 'required|numeric|exists:merchants,id',
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:65535',
            'sku' => 'nullable|string|max:100|unique:products,sku,'.$productToUpdate->id,
            'price' => 'required|numeric|min:0', // min 0 due to bulk products
            'quantity_type' => 'nullable|string|max:100',
            'has_serials' => 'nullable|boolean',
            'has_variations' => 'nullable|boolean',
            'variations' => 'required_if:has_variations,1|array',
            'variations.*.variation' => 'required_if:has_variations,1',
            'variations.*.price' => 'required_if:has_variations,1',
        ]);

        $productToUpdate->name = strtolower($request->name);
        $productToUpdate->description = strtolower($request->description);
        $productToUpdate->sku = $request->sku ?? $this->generateProductSKU($request);
        $productToUpdate->price = $request->price ?? 0;
        $productToUpdate->quantity_type = strtolower($request->quantity_type) ?? ApplicationSetting::first()->default_measure_unit ?? 'kg';
        
        if (! $productToUpdate->product_immutability) {
            
            $productToUpdate->has_serials = $request->has_serials ?? false;
            $productToUpdate->has_variations = $request->has_variations ?? false;
            
        }
        
        $productToUpdate->product_category_id = $request->product_category_id ?? NULL;
        $productToUpdate->merchant_id = $request->merchant_id;

        $productToUpdate->save();

        if ($productToUpdate->has_variations && !empty($request->variations)) {

            $productToUpdate->product_variations = json_decode(json_encode($request->variations));

        }

        return $this->showAllProducts($perPage);
    }

    /*
        public function deleteProduct($asset, $perPage)
        {
            $assetToDelete = ProductCategory::findOrFail($asset);
            // $userToDelete->warehouses()->delete();
            $assetToDelete->delete();

            return $this->showProductAllCategories($perPage);
        }

        public function restoreProduct($asset, $perPage)
        {
            $assetToRestore = ProductCategory::withTrashed()->findOrFail($asset);
            // $userToRestore->warehouses()->restore();
            $assetToRestore->restore();

            return $this->showProductAllCategories($perPage);
        }
    */

    public function searchAllProducts($search, $perPage)
    {
        $query = Product::where('name', 'like', "%$search%")
                        ->orWhere('sku', 'like', "%$search%")
                        ->orWhere('price', 'like', "%$search%")
                        ->orWhere('quantity_type', 'like', "%$search%")
                        ->orWhereHas('category', function ($q) use ($search) {
                            $q->where('name', 'like', "%$search%");
                        });

        return response()->json([
            'all' => new ProductCollection($query->paginate($perPage)),  
        ], 200);
    }

    // Product-Stock
    public function showProductAllStocks($product, $perPage)
    {
        return new ProductStockCollection(ProductStock::with(['addresses', 'variations', 'serials'])->where('product_id', $product)->paginate($perPage));
    }

    public function storeProductStock(Request $request, $perPage)
    {
        $product = Product::findOrFail($request->product_id);

        $request->validate([
            'stock_quantity' => 'required|numeric|min:1',
            'product_id' => 'required|numeric|exists:products,id',
            'variations' => [
                'array', 
                Rule::requiredIf(function () use ($product) {
                    return $product->has_variations;
                })
            ],
            'serials' => [
                'array', 
                Rule::requiredIf(function () use ($product) {
                    return $product->has_serials && ! $product->has_variations;
                })
            ],
            'serials.*.serial_no' => [
                'string', 'distinct', 'min:4', 'unique:product_serials,serial_no', 
                Rule::requiredIf(function () use ($product) {
                    return $product->has_serials && ! $product->has_variations;
                })
            ],
            'variations.*.stock_quantity' => ['required_with:variations', 'numeric', 'min:1'],
            'variations.*.serials' => [
                'array', 
                Rule::requiredIf(function () use ($product) {
                    return $product->has_variations && $product->has_serials;
                })
            ],
            'variations.*.serials.*.serial_no' => [
                'string', 'distinct', 'min:4', 'unique:product_variation_serials,serial_no', 
                Rule::requiredIf(function () use ($product) {
                    return $product->has_variations && $product->has_serials;
                })
            ]
        ],
        [
            'stock_quantity.*' => 'Stock quantity is required !',
            'product_id.*' => 'Product id is missing !',
            'serials.required' => 'Product serial is required !',
            // 'serials.*.unique' => ':attribute serial must be unique !',
            'variations.*.stock_quantity.required' => 'Variation quantity is required !'
        ]);

        $lastAvailableQuantity = $product->latestStock->available_quantity ?? 0;

        $currentUser = \Auth::guard('admin')->user() ?? \Auth::guard('manager')->user() ?? \Auth::guard('warehouse')->user() ?? \Auth::guard('owner')->user() ?? Auth::user();

        if (empty($currentUser)) {

            return response()->json(['errors'=>["noUser" => "Current user missing, please reload the page"]], 422);
            
        }

        $newStock = $product->stocks()->create([
            'stock_quantity' => $request->stock_quantity,
            'available_quantity' => $currentUser->hasPermissionTo('update-product-stock') ? $lastAvailableQuantity + $request->stock_quantity : $lastAvailableQuantity,
            'has_variations' => $product->has_variations,
            'has_serials' => $product->has_serials,
            'keeper_type' => class_basename($currentUser), // who stores the stock
            'keeper_id' => $currentUser->id,
            'has_approved' => $currentUser->hasPermissionTo('update-product-stock') ? true : false, 
            'approver_type' => $currentUser->hasPermissionTo('update-product-stock') ? class_basename($currentUser) : NULL,
            'approver_id' => $currentUser->hasPermissionTo('update-product-stock') ? $currentUser->id : NULL,
        ]);

        if ($newStock->has_variations && ! empty($request->variations)) {
            
            $newStock->stock_variations = json_decode(json_encode($request->variations));

        }

        if ($newStock->has_serials && ! $newStock->has_variations && ! empty($request->serials)) {
            
            $newStock->stock_serials = json_decode(json_encode($request->serials));

        }

        $newStock->setStockAddresses(json_decode(json_encode($request->addresses)), $product->id);

        return $this->showProductAllStocks($request->product_id, $perPage);
    }

    public function updateProductStock(Request $request, $stock, $perPage)
    {
        $stockToUpdate = ProductStock::findOrFail($stock);

        $request->validate([
            'stock_quantity' => 'required|numeric|min:1',
            // 'product_id' => 'required|numeric|exists:products,id',
            'variations' => [
                'array', 
                Rule::requiredIf(function () use ($stockToUpdate) {
                    return $stockToUpdate->has_variations;
                })
            ],
            'serials' => [
                'array', 
                Rule::requiredIf(function () use ($stockToUpdate) {
                    return $stockToUpdate->has_serials && ! $stockToUpdate->has_variations;
                })
            ],
            'serials.*.serial_no' => [
                'string', 'distinct', 'min:4', 
                // 'unique:product_serials,serial_no,'.$request->input('serials.*').',serial_no',
                // Rule::unique('product_serials', 'serial_no')->ignore(ProductSerial::where('serial_no', $request->input('serials.*'))->first()->id),
                Rule::requiredIf(function () use ($stockToUpdate) {
                    return $stockToUpdate->has_serials && ! $stockToUpdate->has_variations;
                })
            ],
            'variations.*.stock_quantity' => ['required_with:variations', 'numeric', 'min:1'],
            'variations.*.serials' => [
                'array', 
                Rule::requiredIf(function () use ($stockToUpdate) {
                    return $stockToUpdate->has_variations && $stockToUpdate->has_serials;
                })
            ],
            'variations.*.serials.*.serial_no' => [
                'string', 'distinct', 'min:4', 
                // 'unique:product_variation_serials,serial_no,'.$request->input('variations.*.serials.*').',serial_no', 
                // Rule::unique('product_variation_serials', 'serial_no')->ignore(ProductVariationSerial::where('serial_no', $request->input('variations.*.serials.*'))->first()->id),
                Rule::requiredIf(function () use ($stockToUpdate) {
                    return $stockToUpdate->has_variations && $stockToUpdate->has_serials;
                })
            ]
        ],
        [
            'stock_quantity.*' => 'Stock quantity is required !',
            // 'product_id.*' => 'Product id is missing !',
            'serials.required' => 'Product serial is required !',
            // 'serials.*.unique' => ':attribute serial must be unique !',
            'variations.*.stock_quantity.required' => 'Variation quantity is required !'
        ]);


        if ($stockToUpdate->has_serials && ! $stockToUpdate->has_variations && $this->checkProductSerialDuplicacy($request)) {

            $duplicateValue = $this->checkProductSerialDuplicacy($request);
            return response()->json(['errors'=>["$duplicateValue" => "$duplicateValue serial is taken"]], 422);

        }

        else if ($stockToUpdate->has_serials && $stockToUpdate->has_variations && $this->checkVariationSerialDuplicacy($request)) {
            
            $duplicateValue = $this->checkVariationSerialDuplicacy($request);
            return response()->json(['errors'=>["$duplicateValue" => "$duplicateValue serial is taken"]], 422);
            
        }

        $currentUser = \Auth::guard('admin')->user() ?? \Auth::guard('manager')->user() ?? \Auth::guard('warehouse')->user() ?? \Auth::guard('owner')->user() ?? Auth::user();

        if (empty($currentUser)) {

            return response()->json(['errors'=>["noUser" => "Current user missing, please reload the page"]], 422);
            
        }
            
        if ($request->stock_quantity > $stockToUpdate->stock_quantity) {

            $difference = $request->stock_quantity - $stockToUpdate->stock_quantity;

            $stockToUpdate->update([
                'stock_quantity' => $request->stock_quantity,
                'available_quantity' => ($stockToUpdate->available_quantity + $difference),
                'has_approved' => true,
                'approver_type' => class_basename($currentUser),
                'approver_id' => $currentUser->id,
            ]);

            $this->increaseStockAvailableQuantity($stockToUpdate, $difference);

        }
        else if ($request->stock_quantity < $stockToUpdate->stock_quantity) {

            $difference = $stockToUpdate->stock_quantity - $request->stock_quantity;

            $stockToUpdate->update([
                'stock_quantity' => $request->stock_quantity,
                'available_quantity' => ($stockToUpdate->available_quantity - $difference), 
                'has_approved' => true,
                'approver_type' => class_basename($currentUser),
                'approver_id' => $currentUser->id,
            ]);

            $this->decreaseStockAvailableQuantity($stockToUpdate, $difference);

        }
        else if(! $stockToUpdate->has_approved) {

            $stockToUpdate->update([
                'has_approved' => true,
                'approver_type' => class_basename($currentUser),
                'approver_id' => $currentUser->id,
            ]);

            $this->increaseStockAvailableQuantity($stockToUpdate, $stockToUpdate->stock_quantity);

        }

        if ($stockToUpdate->has_variations && !empty($request->variations)) {
            
            $stockToUpdate->stock_variations = json_decode(json_encode($request->variations));

        }

        if ($stockToUpdate->has_serials && ! $stockToUpdate->has_variations && ! empty($request->serials)) {
            
            $stockToUpdate->stock_serials = json_decode(json_encode($request->serials));

        }

        $stockToUpdate->setStockAddresses(json_decode(json_encode($request->addresses)), $stockToUpdate->product_id);

        return $this->showProductAllStocks($stockToUpdate->product_id, $perPage);
    }

    public function deleteProductStock($stock, $perPage)
    {
        $stockToDelete = ProductStock::findOrFail($stock);

        if ($stockToDelete->serials()->where('has_requisitions', 1)->orWhere('has_dispatched', 1)->count() > 0) {
            
           return response()->json(['errors'=>["undeletableSerial" => "Product serial has requisition"]], 422);
            
        }

        else if ($stockToDelete->variations()->whereHas('serials', function ($query) {
            $query->where('has_requisitions', 1)->orWhere('has_dispatched', 1);
        })->count() > 0) {
            
            return response()->json(['errors'=>["undeletableSerial" => "Product variation serial has requisition"]], 422);

        }

        $this->decreaseStockAvailableQuantity($stockToDelete, $stockToDelete->stock_quantity);
            
        $stockToDelete->deleteStockVariations();

        $stockToDelete->deleteStockSerials();

        $stockToDelete->deleteOldAddresses();

        $stockToDelete->delete();

        return $this->showProductAllStocks($stockToDelete->product_id, $perPage);
    }

    public function searchProductAllStocks($product, $search, $perPage)
    {
        $query = ProductStock::with(['addresses', 'variations'])
                        ->where('product_id', $product)
                        ->where(function($q) use ($search) {
                            $q->where('stock_quantity', 'like', "%$search%")
                            ->orWhere('available_quantity', 'like', "%$search%");
                        });

        return response()->json([
            'all' => new ProductStockCollection($query->paginate($perPage)),  
        ], 200);
    }

    protected function increaseStockAvailableQuantity(ProductStock $stockToUpdate, $amount)
    {
        ProductStock::where('product_id', $stockToUpdate->product_id)->where('created_at', '>', $stockToUpdate->created_at)->increment('available_quantity', $amount);
    }

    protected function decreaseStockAvailableQuantity(ProductStock $stockToUpdate, $amount)
    {
        ProductStock::where('product_id', $stockToUpdate->product_id)->where('created_at', '>', $stockToUpdate->created_at)->decrement('available_quantity', $amount);
    }

    protected function generateProductSKU(Request $request)
    {
        if ($request->product_category_id) {
            return 'MR'.$request->merchant_id.'CT'.$request->product_category_id;
        }
        return 'MR'.$request->merchant_id.'BX'.$request->name;
    }

    protected function checkVariationSerialDuplicacy(Request $request)
    {
        foreach($request->variations as $productVariationKey => $productVariation)
        {
            foreach ($productVariation['serials'] as $variationSerialKey => $productVariationSerial) {
                
                if (ProductVariationSerial::where('serial_no', $productVariationSerial['serial_no'])->count() > 1) {
                    
                    // return true;
                    return $productVariationSerial;

                }
                
            }
        }

        return false;
    }

    protected function checkProductSerialDuplicacy(Request $request)
    {
        foreach($request->serials as $productSerial)
        {
            if (ProductSerial::where('serial_no', $productSerial['serial_no'])->count() > 1) {
                
                return $productSerial;

            }
        }
    }

}
