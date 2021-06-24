<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductStock;
use Illuminate\Http\Request;
use App\Models\ProductSerial;
use App\Models\MerchantProduct;
use App\Models\ProductCategory;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\File; 
use App\Models\ProductVariationSerial;
use App\Models\WarehouseContainerStatus;
use Illuminate\Support\Facades\Validator;
use App\Models\WarehouseContainerShelfStatus;
use App\Http\Resources\Web\ProductCollection;
use App\Models\WarehouseContainerShelfUnitStatus;
use App\Http\Resources\Web\ProductStockCollection;
use App\Http\Resources\Web\ManagerProductCollection;
use App\Http\Resources\Web\MerchantProductCollection;

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

        // Product-Merchant
        $this->middleware("permission:view-merchant-product-index")->only(['showProductAllMerchants', 'searchProductAllMerchants']);
        $this->middleware("permission:create-merchant-product")->only('storeProductNewMerchant');
        $this->middleware("permission:update-merchant-product")->only('updateProductMerchant');
        $this->middleware("permission:delete-merchant-product")->only('deleteProductMerchant');        
    }

    // Product-Categories
    public function showProductAllCategories($perPage=false)
    {
        if ($perPage) {
            
            return response()->json([

                'current' => ProductCategory::withCount('products')->paginate($perPage),
                'trashed' => ProductCategory::withCount('products')->onlyTrashed()->paginate($perPage),

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
            'name' => 'required|string|max:100',
            // 'description' => 'nullable|string|max:65535',
            // 'sku' => 'nullable|string|max:100|unique:products,sku',
            // 'price' => 'numeric|min:0', // min 0 due to bulk products
            'quantity_type' => 'nullable|string|max:100',
            'has_serials' => 'boolean',
            'has_variations' => 'boolean',
            'product_category_id' => 'numeric|exists:product_categories,id',
            // 'merchant_id' => 'required|numeric|exists:merchants,id',
            'variations' => 'required_if:has_variations,1|array',
            'variations.*.variation' => 'required_if:has_variations,1',
            'variations.*.variation.id' => 'required_if:has_variations,1|exists:variations,id',
            // 'variations.*.price' => 'required_if:has_variations,1',
        ]);

        $newProduct = new Product();

        $newProduct->name = strtolower($request->name);
        // $newProduct->description = strtolower($request->description);
        // $newProduct->sku = $request->sku ?? $this->generateProductSKU($request);
        // $newProduct->price = $request->price ?? 0;
        $newProduct->quantity_type = strtolower($request->quantity_type) ?? ApplicationSetting::first()->default_measure_unit ?? 'kg';
        $newProduct->has_serials = $request->has_serials ?? false;
        $newProduct->has_variations = $request->has_variations ?? false;
        $newProduct->product_category_id = $request->product_category_id;
        // $newProduct->merchant_id = $request->merchant_id;

        $newProduct->save();

        $newProduct->product_preview = $request->preview;
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
            'name' => 'required|string|max:100',
            // 'description' => 'nullable|string|max:65535',
            // 'sku' => 'nullable|string|max:100|unique:products,sku,'.$productToUpdate->id,
            // 'price' => 'required|numeric|min:0', // min 0 due to bulk products
            'quantity_type' => 'nullable|string|max:100',
            'has_serials' => 'boolean',
            'has_variations' => 'boolean',
            'product_category_id' => 'numeric|exists:product_categories,id',
            // 'merchant_id' => 'required|numeric|exists:merchants,id',
            'variations' => 'required_if:has_variations,1|array',
            'variations.*.variation' => 'required_if:has_variations,1',
            'variations.*.variation.id' => 'required_if:has_variations,1|exists:variations,id',
            // 'variations.*.price' => 'required_if:has_variations,1',
        ]);

        $productToUpdate->name = strtolower($request->name);
        $productToUpdate->product_preview = $request->preview;
        // $productToUpdate->description = strtolower($request->description);
        // $productToUpdate->sku = $request->sku ?? $this->generateProductSKU($request);
        // $productToUpdate->price = $request->price ?? 0;
        $productToUpdate->quantity_type = strtolower($request->quantity_type) ?? ApplicationSetting::first()->default_measure_unit ?? 'kg';
        
        if (! $productToUpdate->product_immutability) {
            
            $productToUpdate->has_serials = $request->has_serials ?? false;
            $productToUpdate->has_variations = $request->has_variations ?? false;
            
        }
        
        $productToUpdate->product_category_id = $request->product_category_id ?? NULL;
        // $productToUpdate->merchant_id = $request->merchant_id;

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
                        // ->orWhere('price', 'like', "%$search%")
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
            'warehouse.id' => 'required|exists:warehouses,id',
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
            'variations.*.stock_quantity' => [
                'numeric', 'min:1', 
                Rule::requiredIf(function () use ($product, $request) {
                    return $product->has_variations && array_sum($request->input('variations.*.stock_quantity')) != $request->stock_quantity;
                })
            ],
            'variations.*.serials' => [
                'array', 
                'exclude_if:variations.*.stock_quantity,', 
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

        $currentUser = \Auth::guard('admin')->user() ?? \Auth::guard('manager')->user() ?? \Auth::guard('warehouse')->user() ?? \Auth::guard('owner')->user() ?? \Auth::user();

        if (empty($currentUser)) {

            return response()->json(['errors'=>["noUser" => "Current user missing, please reload the page"]], 422);
            
        }

        $userHasUpdatingPermission = $currentUser->hasPermissionTo('update-product-stock');

        $newStock = $product->stocks()->create([
            'stock_quantity' => $request->stock_quantity,
            'available_quantity' => $userHasUpdatingPermission ? $lastAvailableQuantity + $request->stock_quantity : $lastAvailableQuantity,
            'has_variations' => $product->has_variations,
            'has_serials' => $product->has_serials,
            'keeper_type' => get_class($currentUser), // who stores the stock
            'keeper_id' => $currentUser->id,
            'has_approval' => $userHasUpdatingPermission ? true : false, 
            'approver_type' => $userHasUpdatingPermission ? get_class($currentUser) : NULL,
            'approver_id' => $userHasUpdatingPermission ? $currentUser->id : NULL,
            'warehouse_id' => $request['warehouse']['id'],
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
            'warehouse.id' => 'required|exists:warehouses,id',
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
            'variations.*.stock_quantity' => [
                'numeric', 'min:1', 
                Rule::requiredIf(function () use ($stockToUpdate, $request) {
                    return $stockToUpdate->has_variations && array_sum($request->input('variations.*.stock_quantity')) != $request->stock_quantity;
                })
            ],
            'variations.*.serials' => [
                'array', 
                'exclude_if:variations.*.stock_quantity,', 
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
                'has_approval' => true,
                'approver_type' => get_class($currentUser),
                'approver_id' => $currentUser->id,
                'warehouse_id' => $request['warehouse']['id'],
            ]);

            $this->increaseStockAvailableQuantity($stockToUpdate, $difference);

        }
        else if ($request->stock_quantity < $stockToUpdate->stock_quantity) {

            $difference = $stockToUpdate->stock_quantity - $request->stock_quantity;

            if ($difference > $stockToUpdate->product->latestStock->available_quantity) {

                return response()->json(['errors'=>["invalidValue" => "Stock quantity is less than minimum"]], 422);
                
            }
            else if ($stockToUpdate->variations->count() && $this->findVariationInvalidQuantity(json_decode(json_encode($request->variations)), $stockToUpdate)) {
                
                return response()->json(['errors'=>["invalidValue" => "Variation quantity is less than minimum"]], 422);

            }

            $stockToUpdate->update([
                'stock_quantity' => $request->stock_quantity,
                'available_quantity' => ($stockToUpdate->available_quantity - $difference), 
                'has_approval' => true,
                'approver_type' => get_class($currentUser),
                'approver_id' => $currentUser->id,
                'warehouse_id' => $request['warehouse']['id'],
            ]);

            $this->decreaseStockAvailableQuantity($stockToUpdate, $difference);

        }
        else if(! $stockToUpdate->has_approval) {

            $stockToUpdate->update([
                'has_approval' => true,
                'approver_type' => get_class($currentUser),
                'approver_id' => $currentUser->id,
                'warehouse_id' => $request['warehouse']['id'],
            ]);

            $this->increaseStockAvailableQuantity($stockToUpdate, $stockToUpdate->stock_quantity);

        }
        else {

            $stockToUpdate->update([
                'warehouse_id' => $request['warehouse']['id'],
            ]);

        }

        if ($stockToUpdate->has_variations && !empty($request->variations)) {
                
            if ($this->findVariationInvalidQuantity(json_decode(json_encode($request->variations)), $stockToUpdate)) {
                
                return response()->json(['errors'=>["invalidValue" => "Variation quantity is less than minimum"]], 422);

            }

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

        if ($stockToDelete->has_serials && ! $stockToDelete->has_variations && $stockToDelete->serials()->where(function($q) { $q->where('has_requisitions', true)->orWhere('has_dispatched', true); })->exists()) {
            
           return response()->json(['errors'=>["undeletableSerial" => "Stock serial has requisition"]], 422);
            
        }

        else if ($stockToDelete->has_serials && $stockToDelete->has_variations && $stockToDelete->variations()->whereHas('serials', function ($query) {
            $query->where('has_requisitions', true)->orWhere('has_dispatched', true);
        })->exists()) {
            
            return response()->json(['errors'=>["undeletableVariationSerial" => "Stock variation serial has requisition"]], 422);

        }

        else if ($stockToDelete->stock_quantity > $stockToDelete->available_quantity) {
            
            return response()->json(['errors'=>["undeletableStock" => "Stock available-quantity is less than stock-quantity"]], 422);

        }

        else if ($stockToDelete->has_variations && $stockToDelete->variations()->whereRaw('stock_quantity > available_quantity')->exists()) {
            
            return response()->json(['errors'=>["undeletableStockVariation" => "Stock variation available-quantity is less than stock-quantity"]], 422);

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

    // Manager Products
    public function showManagerAllProducts($perPage=false)
    {
        if ($perPage) {

            $manager = \Auth::guard('manager')->user();

            return response()->json([

                'retail' => new ManagerProductCollection(
                    Product::where('product_category_id', '!=', 0)->whereHas('addresses', function ($query) use ($manager) {
                        $query->whereHasMorph(
                            'space',
                            [WarehouseContainerStatus::class, WarehouseContainerShelfStatus::class, WarehouseContainerShelfUnitStatus::class],
                            function ($query1) use ($manager) {
                                $query1->whereHas('warehouseContainer', function ($query2) use ($manager) {
                                    $query2->whereHas('warehouse', function ($query3) use ($manager) {
                                        $query3->whereHas('managers', function ($query4) use ($manager) {
                                            $query4->where('manager_id', $manager->id);
                                        });
                                    });
                                });
                            }
                        );
                    })->paginate($perPage)
                ),

                'bulk' => new ManagerProductCollection(
                    Product::whereNull('product_category_id')->whereHas('addresses', function ($query) use ($manager) {
                        $query->whereHasMorph(
                            'space',
                            [WarehouseContainerStatus::class, WarehouseContainerShelfStatus::class, WarehouseContainerShelfUnitStatus::class],
                            function ($query1) use ($manager) {
                                $query1->whereHas('warehouseContainer', function ($query2) use ($manager) {
                                    $query2->whereHas('warehouse', function ($query3) use ($manager) {
                                        $query3->whereHas('managers', function ($query4) use ($manager) {
                                            $query4->where('manager_id', $manager->id);
                                        });
                                    });
                                });
                            }
                        );
                    })->paginate($perPage)
                ),

            ], 200);

            /*
            return response()->json([

                'retail' => new ProductCollection(Product::where('product_category_id', '!=', 0)->paginate($perPage)),
                'bulk' => new ProductCollection(Product::whereNull('product_category_id')->orWhere('product_category_id', 0)->paginate($perPage)),

            ], 200);
            */

        }

        return Product::all();
    }
    
    public function searchManagerAllProducts($search, $perPage)
    {
        $query = Product::where('name', 'like', "%$search%")
                        ->orWhere('sku', 'like', "%$search%")
                        ->orWhere('price', 'like', "%$search%")
                        ->orWhere('quantity_type', 'like', "%$search%")
                        ->orWhereHas('category', function ($q) use ($search) {
                            $q->where('name', 'like', "%$search%");
                        });

                        /*
                        ->whereHas('addresses', function ($query) use ($manager) {
                            $query->whereHasMorph(
                                'space',
                                [WarehouseContainerStatus::class, WarehouseContainerShelfStatus::class, WarehouseContainerShelfUnitStatus::class],
                                function ($query1) use ($manager) {
                                    $query1->whereHas('warehouseContainer', function ($query2) use ($manager) {
                                        $query2->whereHas('warehouse', function ($query3) use ($manager) {
                                            $query3->whereHas('managers', function ($query4) use ($manager) {
                                                $query4->where('manager_id', $manager->id);
                                            });
                                        });
                                    });
                                }
                            );
                        })
                         */

        return response()->json([
            'all' => new ProductCollection($query->paginate($perPage)),  
        ], 200);
    }

    // Product-Merchants
    public function showProductAllMerchants($product, $perPage)
    {
        return new MerchantProductCollection(MerchantProduct::where('product_id', $product)->with(['merchant', 'variations', 'requests', 'addresses', 'stocks', 'serials', 'latestStock', 'nonDispatchedRequests', 'dispatchedRequests'])->paginate($perPage));
    }

    public function storeProductNewMerchant(Request $request, $perPage)
    {
        $product = Product::findOrFail($request->product_id);

        $request->validate([
            // 'product' => 'required',
            // 'product.id' => 'required|numeric|exists:products,id',
            // 'merchant' => 'required',
            // 'merchant.id' => 'required|numeric|exists:merchants,id',
            'product_id' => 'required|numeric|exists:products,id',
            'merchant_id' => 'required|numeric|exists:merchants,id',
            'sku' => 'required|string',
            'selling_price' => 'required|numeric',
            'description' => 'nullable|string',
            'warning_quantity' => 'numeric',
            'variations' => [
                'array', 
                Rule::requiredIf(function () use ($product) {
                    return $product->has_variations;
                })
            ],
            'variations.*.variation' => 'required_with:variations',
            'variations.*.variation.id' => [
                'required_with:variations', 'exists:variations,id', 
                Rule::exists('product_variations', 'variation_id')->where(function ($query) use ($product) {
                    return $query->where('product_id', $product->id);
                })
            ],
            'variations.*.selling_price' => 'required_with:variations|numeric',
            'variations.*.sku' => 'string',
        ],
        [
            'product_id' => 'Product is required',
            'merchant_id' => 'Merchant is required',
            'selling_price' => 'Product selling price is required',
            'warning_quantity' => 'Warning quantity should be numeric',
            'variations.*.variation' => 'Invalid variations, please reload',
            'variations.*.variation.id.*' => 'Invalid variations, please reload',
            'variations.*.selling_price' => 'Variation selling price is required',
            'variations.*.sku' => 'Invalid variation SKU',
        ]);

        $currentUser = \Auth::guard('admin')->user() ?? \Auth::guard('manager')->user() ?? \Auth::guard('warehouse')->user() ?? \Auth::guard('owner')->user() ?? \Auth::user();

        if (empty($currentUser)) {

            return response()->json(['errors'=>["noUser" => "Current user missing, please reload the page"]], 422);
            
        }

        $productNewMerchant = MerchantProduct::create([

            'sku' => strtoupper($request->sku) ?? $this->generateProductSKU($request->merchant_id, $product->product_category_id, $product->id), 
            // 'merchant_product_preview' => $request->preview, 
            'description' => strtolower($request->description), 
            'warning_quantity' => $request->warning_quantity ?? 100,
            'selling_price' => $request->selling_price,
            'product_id' => $request->product_id,
            'merchant_id' => $request->merchant_id,
            'created_at' => now()

        ]);

        $productNewMerchant->merchant_product_preview = $request->preview;
        $productNewMerchant->save();

        if ($product->has_variations) {
            
            $productNewMerchant->merchant_product_variations = json_decode(json_encode($request->variations));

        }

        return $this->showProductAllMerchants($request->product_id, $perPage);
    }

    public function updateProductMerchant(Request $request, $productMerchant, $perPage)
    {
        $product = Product::findOrFail($request->product_id);

        $request->validate([
            // 'product' => 'required',
            // 'product.id' => 'required|numeric|exists:products,id',
            // 'merchant' => 'required',
            // 'merchant.id' => 'required|numeric|exists:merchants,id',
            'product_id' => 'required|numeric|exists:products,id',
            'merchant_id' => 'required|numeric|exists:merchants,id',
            'sku' => 'required|string',
            'selling_price' => 'required|numeric',
            'description' => 'nullable|string',
            'warning_quantity' => 'numeric',
            'variations' => [
                'array', 
                Rule::requiredIf(function () use ($product) {
                    return $product->has_variations;
                })
            ],
            'variations.*.variation' => 'required_with:variations',
            'variations.*.variation.id' => [
                'required_with:variations', 'exists:variations,id', 
                Rule::exists('product_variations', 'variation_id')->where(function ($query) use ($product) {
                    return $query->where('product_id', $product->id);
                })
            ],
            'variations.*.selling_price' => 'required_with:variations|numeric',
            'variations.*.sku' => 'string',
        ],
        [
            'product_id' => 'Product is required',
            'merchant_id' => 'Merchant is required',
            'selling_price' => 'Product selling price is required',
            'warning_quantity' => 'Warning quantity should be numeric',
            'variations.*.variation' => 'Invalid variations, please reload',
            'variations.*.variation.id.*' => 'Invalid variations, please reload',
            'variations.*.selling_price' => 'Variation selling price is required',
            'variations.*.sku' => 'Invalid variation SKU',
        ]);

        $currentUser = \Auth::guard('admin')->user() ?? \Auth::guard('manager')->user() ?? \Auth::guard('warehouse')->user() ?? \Auth::guard('owner')->user() ?? \Auth::user();

        if (empty($currentUser)) {

            return response()->json(['errors'=>["noUser" => "Current user missing, please reload the page"]], 422);
            
        }

        $productMerchantToUpdate = MerchantProduct::findOrFail($productMerchant);

        $productMerchantToUpdate->update([

            'sku' => strtoupper($request->sku) ?? $this->generateProductSKU($request->merchant_id, $product->product_category_id, $product->id), 
            // 'merchant_product_preview' => $request->preview, 
            'description' => strtolower($request->description), 
            'warning_quantity' => $request->warning_quantity ?? 100,
            'selling_price' => $request->selling_price,
            'product_id' => $request->product_id,
            'merchant_id' => $request->merchant_id,
            'created_at' => now()

        ]);

        $productMerchantToUpdate->merchant_product_preview = $request->preview;
        $productMerchantToUpdate->save();

        if ($product->has_variations) {
            
            $productMerchantToUpdate->merchant_product_variations = json_decode(json_encode($request->variations));

        }

        return $this->showProductAllMerchants($request->product_id, $perPage);
    }

    public function deleteProductMerchant($productMerchant, $perPage)
    {
        $productMerchantToDelete = MerchantProduct::findOrFail($productMerchant);

        if ($productMerchantToDelete->product_immutability) {
            
           return response()->json(['errors'=>["undeletableMerchant" => "Merchant has stock or requisition"]], 422); 

        }
            
        // $productMerchantToDelete->stocks()->delete();
        // $productMerchantToDelete->requests()->delete();
        // $productMerchantToDelete->deleteOldAddresses();
        
        File::delete($productMerchantToDelete->preview);

        foreach ($productMerchantToDelete->variations as $key => $merchantProductVariationToDelete) {
            
            File::delete($merchantProductVariationToDelete->preview);
            
        }

        $productMerchantToDelete->variations()->delete();
        $productMerchantToDelete->delete();

        return $this->showProductAllMerchants($productMerchantToDelete->product_id, $perPage);
    }

    public function searchProductAllMerchants($product, $search, $perPage)
    {
        $query = MerchantProduct::where(function($query) use ($search) {
                $query->where('sku', 'like', "%$search%")
                    ->orWhere('description', 'like', "%$search%")
                    ->orWhere('warning_quantity', 'like', "%$search%")
                    ->orWhere('selling_price', 'like', "%$search%")
                    ->orWhereHas('merchant', function ($q) use ($search) {
                        $q->where('first_name', 'like', "%$search%")
                        ->orWhere('last_name', 'like', "%$search%")
                        ->orWhere('user_name', 'like', "%$search%")
                        ->orWhere('email', 'like', "%$search%")
                        ->orWhere('mobile', 'like', "%$search%");
                });
        })
        ->where('product_id', $product)
        ->with(['merchant', 'variations', 'requests', 'addresses', 'stocks', 'serials', 'latestStock', 'nonDispatchedRequests', 'dispatchedRequests']);

        return response()->json([
            'all' => new MerchantProductCollection($query->paginate($perPage)),  
        ], 200);
    }

    protected function findVariationInvalidQuantity($stockVariaitonQuantities = [], ProductStock $productStock)
    {
        // update / old stock
        if (count($stockVariaitonQuantities)) {
            
            foreach ($stockVariaitonQuantities as $stockVariation) {

                $productStockVariation = $productStock->variations()->where('id', $stockVariation->id)->firstOrFail();

                if ($productStockVariation->stock_quantity > $stockVariation->stock_quantity) {
                    
                    // decrease quantity
                    $difference = $productStockVariation->stock_quantity - $stockVariation->stock_quantity;

                    if ($difference > $productStockVariation->productVariation->latestStock->available_quantity) {
                        
                        return true;

                    }

                }

            }

            return false;

        }
    }

    protected function increaseStockAvailableQuantity(ProductStock $stockToUpdate, $amount)
    {
        ProductStock::where('product_id', $stockToUpdate->product_id)->where('created_at', '>', $stockToUpdate->created_at)->increment('available_quantity', $amount);
    }

    protected function decreaseStockAvailableQuantity(ProductStock $stockToUpdate, $amount)
    {
        ProductStock::where('product_id', $stockToUpdate->product_id)->where('created_at', '>', $stockToUpdate->created_at)->decrement('available_quantity', $amount);
    }

    protected function generateProductSKU($merchant, $productCategory, $product)
    {
        if ($productCategory) {
            return 'MR'.$merchant.'CT'.$productCategory.'PR'.$product;
        }
        return 'MR'.$merchant.'BX'.'PR'.$product;
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
