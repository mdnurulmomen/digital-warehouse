<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\Admin;
use App\Models\Manager;
use App\Models\Product;
use App\Models\ProductStock;
use Illuminate\Http\Request;
use App\Models\ProductSerial;
use App\Models\MerchantProduct;
use App\Models\ProductCategory;
use Illuminate\Validation\Rule;
use App\Models\ProductManufacturer;
use Illuminate\Support\Facades\File; 
use App\Models\ProductVariationSerial;
use App\Http\Requests\Web\StockRequest;
use App\Http\Resources\Web\StockCollection;
// use App\Models\WarehouseContainerStatus;
// use Illuminate\Support\Facades\Validator;
use App\Http\Resources\Web\ProductResource;
// use App\Models\WarehouseContainerShelfStatus;
use App\Http\Resources\Web\ProductCollection;
// use App\Models\WarehouseContainerShelfUnitStatus;
use App\Http\Resources\Web\ProductStockCollection;
// use App\Http\Resources\Web\ManagerProductCollection;
use App\Http\Resources\Web\ProductCategoryResource;
use App\Http\Resources\Web\MerchantProductCollection;
use App\Http\Resources\Web\ProductCategoryCollection;

class ProductController extends Controller
{
    public function __construct()
    {
        // Manufacturers
        $this->middleware("permission:view-product-asset-index")->only(['showAllManufacturers', 'searchAllManufacturers']);
        $this->middleware("permission:create-product-asset")->only('storeNewManufacturer');
        $this->middleware("permission:update-product-asset")->only('updateManufacturer');
        $this->middleware("permission:delete-product-asset")->only(['deleteManufacturer', 'restoreManufacturer']);

        // Product-Category
        $this->middleware("permission:view-product-asset-index")->only(['showProductAllCategories', 'searchProductAllCategories']);
        $this->middleware("permission:create-product-asset")->only('storeProductNewCategory');
        $this->middleware("permission:update-product-asset")->only('updateProductCategory');
        $this->middleware("permission:delete-product-asset")->only(['deleteProductCategory', 'restoreProductCategory']);

        // Product
        $this->middleware("permission:view-product-index")->only(['showAllProducts', 'searchAllProducts', 'showCategoryAllProducts', 'searchCategoryAllProducts']);
        $this->middleware("permission:create-product")->only(['storeNewProduct', 'storeCategoryNewProduct']);
        $this->middleware("permission:update-product")->only(['updateProduct', 'updateCategoryProduct']);
        
        // Product-Stock
        $this->middleware("permission:view-product-stock-index")->only(['showProductAllStocks', 'searchProductAllStocks']);
        $this->middleware("permission:create-product-stock")->only('storeProductStock');
        $this->middleware("permission:update-product-stock")->only('updateProductStock');
        $this->middleware("permission:delete-product-stock")->only('deleteProductStock');

        // Stock
        $this->middleware("permission:view-product-stock-index")->only(['showAllStocks', 'searchAllStocks']);
        $this->middleware("permission:create-product-stock")->only('storeStock');
        $this->middleware("permission:update-product-stock")->only('updateStock');
        $this->middleware("permission:delete-product-stock")->only('deleteStock');

        // Product-Merchant
        $this->middleware("permission:view-merchant-product-index")->only(['showProductAllMerchants', 'searchProductAllMerchants']);
        $this->middleware("permission:create-merchant-product")->only('storeProductNewMerchant');
        $this->middleware("permission:update-merchant-product")->only('updateProductMerchant');
        $this->middleware("permission:delete-merchant-product")->only('deleteProductMerchant');        
    }

    // Manufacturers
    public function showAllManufacturers($perPage=false)
    {
        if ($perPage) {
            
            return response()->json([

                'current' => ProductManufacturer::paginate($perPage),
                'trashed' => ProductManufacturer::onlyTrashed()->paginate($perPage),

            ], 200);

        }

        return ProductManufacturer::all();
    }

    public function storeNewManufacturer(Request $request, $perPage)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:product_manufacturers,name',
        ]);

        $newAsset = new ProductManufacturer();

        $newAsset->name = strtolower($request->name);

        $newAsset->save();

        return $this->showAllManufacturers($perPage);
    }

    public function updateManufacturer(Request $request, $asset, $perPage)
    {
        $assetToUpdate = ProductManufacturer::findOrFail($asset);

        $request->validate([
            'name' => 'required|string|max:100|unique:product_manufacturers,name,'.$assetToUpdate->id,
        ]);

        $assetToUpdate->name = strtolower($request->name);

        $assetToUpdate->save();

        return $this->showAllManufacturers($perPage);
    }

    public function deleteManufacturer($asset, $perPage)
    {
        $assetToDelete = ProductManufacturer::findOrFail($asset);

        $assetToDelete->delete();

        return $this->showAllManufacturers($perPage);
    }

    public function restoreManufacturer($asset, $perPage)
    {
        $assetToRestore = ProductManufacturer::withTrashed()->findOrFail($asset);
        
        $assetToRestore->restore();

        return $this->showAllManufacturers($perPage);
    }

    public function searchAllManufacturers($search, $perPage)
    {
        $query = ProductManufacturer::withTrashed()->where('name', 'like', "%$search%");

        return response()->json([
            'all' => $query->paginate($perPage),    
        ], 200);
    }

    // Product-Categories
    public function showProductAllCategories($perPage=false)
    {
        if ($perPage) {
            
            return response()->json([

                'current' => new ProductCategoryCollection(ProductCategory::withCount('products')->with('parent')->latest('id')->paginate($perPage)),

                'trashed' => new ProductCategoryCollection(ProductCategory::withCount('products')->onlyTrashed()->with('parent')->latest('id')->paginate($perPage)),

            ], 200);

        }

        return ProductCategoryResource::collection(
            ProductCategory::whereNull('parent_category_id')->with('childs')->latest('id')->get()
        );
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
            // 'parent_category_id' => 'nullable|exists:product_categories,id',
            'parent_category_id' => [
                'nullable', 'exists:product_categories,id', 
                Rule::notIn([ $asset ]),
            ],
        ]);

        if ($request->parent_category_id && $assetToUpdate->id < $request->parent_category_id && ProductCategory::findOrFail($request->parent_category_id)->parent()->exists()) {
            return response()->json(['errors'=>["invalidParent" => "Invalid parent category"]], 422);
        }

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

                'retail' => new ProductCollection(Product::where('product_category_id', '!=', 0)->withCount('merchants')->paginate($perPage)),
                'bulk' => new ProductCollection(Product::whereNull('product_category_id')->orWhere('product_category_id', 0)->withCount('merchants')->paginate($perPage)),

            ], 200);

        }

        return ProductResource::collection(Product::all());
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
            'product_category_id' => 'nullable|numeric|exists:product_categories,id',
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
            'product_category_id' => 'nullable|numeric|exists:product_categories,id',
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
                        // ->orWhere('sku', 'like', "%$search%")
                        // ->orWhere('price', 'like', "%$search%")
                        ->orWhere('quantity_type', 'like', "%$search%")
                        ->orWhereHas('merchants', function ($q) use ($search) {
                            $q->where('merchants.first_name', 'like', "%$search%")
                            ->orWhere('merchants.last_name', 'like', "%$search%")
                            ->orWhere('merchants.user_name', 'like', "%$search%");
                        })
                        ->orWhereHas('category', function ($q) use ($search) {
                            $q->where('name', 'like', "%$search%");
                        });

        return response()->json([
            'all' => new ProductCollection($query->paginate($perPage)),  
        ], 200);
    }

    // Category-Products
    public function showCategoryAllProducts($category, $perPage=false)
    {
        if ($perPage) {

            return new ProductCollection(Product::where('product_category_id', $category)->paginate($perPage));

        }
    }

    public function storeCategoryNewProduct(Request $request, $perPage)
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

        return $this->showCategoryAllProducts($newProduct->product_category_id, $perPage);
    }

    public function updateCategoryProduct(Request $request, $product, $perPage)
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

        return $this->showCategoryAllProducts($productToUpdate->product_category_id, $perPage);
    }

    public function searchCategoryAllProducts($category, $search, $perPage)
    {
        $query = Product::where('product_category_id', $category)
        ->where(function($query2) use ($search) {
            $query2->where('name', 'like', "%$search%")
            ->orWhere('quantity_type', 'like', "%$search%");
        });

        return response()->json([
            'all' => new ProductCollection($query->paginate($perPage)),  
        ], 200);
    }

    // Product-Stock
    public function showProductAllStocks($productMerchant, $perPage)
    {
        return new ProductStockCollection(ProductStock::with(['addresses', 'variations', 'serials'])->where('merchant_product_id', $productMerchant)->paginate($perPage));
    }

    public function storeProductStock(Request $request, $perPage)
    {
        $merchantProduct = MerchantProduct::findOrFail($request->merchant_product_id);
        $product = $merchantProduct->product;

        $request->validate([
            // 'merchant_id' => 'required|exists:merchants,id',
            'stock_quantity' => 'required|numeric|min:1',
            'warehouse.id' => 'required|exists:warehouses,id',
            // 'product_id' => 'required|numeric|exists:products,id',
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
            'variations' => [
                'array', 
                Rule::requiredIf(function () use ($product) {
                    return $product->has_variations;
                })
            ],
            'variations.*.id' => [
                'numeric', 'exists:merchant_product_variations,id', 
                Rule::requiredIf(function () use ($product) {
                    return $product->has_variations;
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
            'warehouse.id.exists' => 'Warehouse is invalid !',
            'warehouse.id.*' => 'Warehouse is required !',
            'serials.required' => 'Product serial is required !',
            
            'serials.*.serial_no' => [
                'distinct' => 'Duplicate product-serial is invalid',
                'unique' => 'Product serial exists',
                '*' => 'Product serial is required',
            ],
            
            // 'product_id.*' => 'Product id is missing !',
            // 'serials.*.unique' => ':attribute serial must be unique !',
            
            'variations.*.id.*' => 'Variation id is required !',
            'variations.*.stock_quantity' => [
                'required' => 'Variation quantity should be equal to product quantity',
                '*' => 'Variation quantity is required !',
            ],

            'variations.*.serials.*' => 'Variation serial is required',

            'variations.*.serials.*.serial_no' => [
                'distinct' => 'Variation duplicate serial is invalid',
                'unique' => 'Variation serial exists',
                '*' => 'Variation serial is required',
            ]
        ]);

        $lastAvailableQuantity = $merchantProduct->latestStock->available_quantity ?? 0;

        $currentUser = \Auth::guard('admin')->user() ?? \Auth::guard('manager')->user() ?? \Auth::guard('warehouse')->user() ?? \Auth::guard('owner')->user() ?? \Auth::user();

        if (empty($currentUser)) {

            return response()->json(['errors'=>["noUser" => "Current user missing, please reload the page"]], 422);
            
        }

        $userHasUpdatingPermission = $currentUser->hasPermissionTo('update-product-stock');

        $newStock = Stock::create([
            'invoice_no' => 'GudamInvoice'.now()->year.now()->month.'_#'.$request->merchant_id,
            'keeper_type' => get_class($currentUser), // who stores the stock
            'keeper_id' => $currentUser->id,
            'has_approval' => $userHasUpdatingPermission ? true : false, 
            'approver_type' => $userHasUpdatingPermission ? get_class($currentUser) : NULL,
            'approver_id' => $userHasUpdatingPermission ? $currentUser->id : NULL,
            'warehouse_id' => $request['warehouse']['id'],
            'merchant_id' => $request->merchant_id,
        ]);

        $productNewStock = $newStock->stocks()->create([
            'stock_quantity' => $request->stock_quantity,
            'available_quantity' => $userHasUpdatingPermission ? $lastAvailableQuantity + $request->stock_quantity : $lastAvailableQuantity,
            'merchant_product_id' => $merchantProduct->id
        ]);       

        if ($product->has_variations && ! empty($request->variations)) {
            
            $productNewStock->stock_variations = json_decode(json_encode($request->variations));

        }

        if ($product->has_serials && ! $product->has_variations && ! empty($request->serials)) {
            
            $productNewStock->stock_serials = json_decode(json_encode($request->serials));

        }

        $productNewStock->setStockAddresses(json_decode(json_encode($request->addresses)), $merchantProduct->id);

        return $this->showProductAllStocks($request->merchant_product_id, $perPage);
    }

    public function updateProductStock(Request $request, $stock, $perPage)
    {
        $stockToUpdate = ProductStock::findOrFail($stock);
        $product = $stockToUpdate->merchantProduct->product;

        $request->validate([
            'stock_quantity' => 'required|numeric|min:1',
            'warehouse.id' => 'required|exists:warehouses,id',
            // 'product_id' => 'required|numeric|exists:products,id',
            'serials' => [
                'array', 
                Rule::requiredIf(function () use ($product) {
                    return $product->has_serials && ! $product->has_variations;
                })
            ],
            'serials.*.serial_no' => [
                'string', 'distinct', 'min:4', 
                // 'unique:product_serials,serial_no,'.$request->input('serials.*').',serial_no',
                // Rule::unique('product_serials', 'serial_no')->ignore(ProductSerial::where('serial_no', $request->input('serials.*'))->first()->id),
                Rule::requiredIf(function () use ($product) {
                    return $product->has_serials && ! $product->has_variations;
                })
            ],
            'variations' => [
                'array', 
                Rule::requiredIf(function () use ($product) {
                    return $product->has_variations;
                })
            ],
            'variations.*.merchant_product_variation_id' => [
                'numeric', 'exists:merchant_product_variations,id', 
                Rule::requiredIf(function () use ($product) {
                    return $product->has_variations;
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
                'string', 'distinct', 'min:4', 
                // 'unique:product_variation_serials,serial_no,'.$request->input('variations.*.serials.*').',serial_no', 
                // Rule::unique('product_variation_serials', 'serial_no')->ignore(ProductVariationSerial::where('serial_no', $request->input('variations.*.serials.*'))->first()->id),
                Rule::requiredIf(function () use ($product) {
                    return $product->has_variations && $product->has_serials;
                })
            ]
        ],
        [
            'stock_quantity.*' => 'Stock quantity is required !',
            'warehouse.id.exists' => 'Warehouse is invalid !',
            'warehouse.id.*' => 'Warehouse is required !',
            'serials.required' => 'Product serial is required !',
            
            'serials.*.serial_no' => [
                'distinct' => 'Duplicate product-serial is invalid',
                'unique' => 'Product serial exists',
                '*' => 'Product serial is required',
            ],
            
            // 'product_id.*' => 'Product id is missing !',
            // 'serials.*.unique' => ':attribute serial must be unique !',
            
            'variations.*.merchant_product_variation_id.*' => 'Merchant product variation id is required !',
            'variations.*.stock_quantity' => [
                'required' => 'Variation quantity should be equal to product quantity',
                '*' => 'Variation quantity is required !',
            ],

            'variations.*.serials.*' => 'Variation serial is required',

            'variations.*.serials.*.serial_no' => [
                'distinct' => 'Variation duplicate serial is invalid',
                'unique' => 'Variation serial exists',
                '*' => 'Variation serial is required',
            ]
        ]);

        if (! $product->has_variations && $stockToUpdate->stock_quantity > $request->stock_quantity) {
            
            $difference = $stockToUpdate->stock_quantity - $request->stock_quantity;

            if ($difference > $stockToUpdate->merchantProduct->latestStock->available_quantity) {

                return response()->json(['errors'=>["invalidValue" => "Stock quantity is less than minimum"]], 422);
                
            }

        }
        else if($product->has_variations && $this->findVariationInvalidQuantity(json_decode(json_encode($request->variations)), $stockToUpdate)) {

            return response()->json(['errors'=>["invalidVariationData" => "Trying to update immutable variation or less than minimum"]], 422);

        }

        if ($product->has_serials && ! $product->has_variations && ($this->checkProductSerialInValidity($stockToUpdate, json_decode(json_encode($request->serials))) || $this->checkProductSerialDuplicacy($request))) {

            if ($this->checkProductSerialInValidity($stockToUpdate, json_decode(json_encode($request->serials)))) {
                
                $serialNotFound = $this->checkProductSerialInValidity($stockToUpdate, json_decode(json_encode($request->serials)));
                return response()->json(['errors'=>["$serialNotFound" => "$serialNotFound serial not found"]], 422);

            }
            else {

                $duplicateValue = $this->checkProductSerialDuplicacy($request);
                return response()->json(['errors'=>["$duplicateValue" => "$duplicateValue serial is taken"]], 422);

            }

        }
        else if ($product->has_serials && $product->has_variations && ($this->checkVariationSerialInvalidity($stockToUpdate, json_decode(json_encode($request->serials))) || $this->checkVariationSerialDuplicacy($request))) {
            
            if ($this->checkVariationSerialInvalidity($stockToUpdate, json_decode(json_encode($request->serials)))) {
                
                $serialNotFound = $this->checkVariationSerialInvalidity($stockToUpdate, json_decode(json_encode($request->serials)));
                return response()->json(['errors'=>["$serialNotFound" => "$serialNotFound serial not found"]], 422);

            }
            else {

                $duplicateValue = $this->checkVariationSerialDuplicacy($request);
                return response()->json(['errors'=>["$duplicateValue" => "$duplicateValue serial is taken"]], 422);

            }
            
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
            ]);

            $stockToUpdate->stock()->update([
                'has_approval' => true,
                'approver_type' => get_class($currentUser),
                'approver_id' => $currentUser->id,
                'warehouse_id' => $request['warehouse']['id'],
            ]);

            $this->increaseStockAvailableQuantity($stockToUpdate, $difference);

        }
        else if ($request->stock_quantity < $stockToUpdate->stock_quantity) {

            $difference = $stockToUpdate->stock_quantity - $request->stock_quantity;

            $stockToUpdate->update([
                'stock_quantity' => $request->stock_quantity,
                'available_quantity' => ($stockToUpdate->available_quantity - $difference), 
            ]);

            $stockToUpdate->stock()->update([
                'has_approval' => true,
                'approver_type' => get_class($currentUser),
                'approver_id' => $currentUser->id,
                'warehouse_id' => $request['warehouse']['id'],
            ]);

            $this->decreaseStockAvailableQuantity($stockToUpdate, $difference);

        }
        else if($request->stock_quantity == $stockToUpdate->stock_quantity && ! $stockToUpdate->stock->has_approval) {

            $stockToUpdate->stock()->update([
                'has_approval' => true,
                'approver_type' => get_class($currentUser),
                'approver_id' => $currentUser->id,
                'warehouse_id' => $request['warehouse']['id'],
            ]);

            $this->increaseStockAvailableQuantity($stockToUpdate, $stockToUpdate->stock_quantity);

        }
        else if($request->stock_quantity == $stockToUpdate->stock_quantity && $stockToUpdate->stock->warehouse_id != $request['warehouse']['id']) {

            $stockToUpdate->stock()->update([
                'has_approval' => true,
                'approver_type' => get_class($currentUser),
                'approver_id' => $currentUser->id,
                'warehouse_id' => $request['warehouse']['id'],
            ]);

        }

        if ($product->has_variations && ! empty($request->variations)) {

            $stockToUpdate->stock_variations = json_decode(json_encode($request->variations));

        }

        if ($stockToUpdate->has_serials && ! $stockToUpdate->has_variations && ! empty($request->serials)) {
            
            $stockToUpdate->stock_serials = json_decode(json_encode($request->serials));

        }

        $stockToUpdate->setStockAddresses(json_decode(json_encode($request->addresses)), $stockToUpdate->merchant_product_id);

        return $this->showProductAllStocks($stockToUpdate->merchant_product_id, $perPage);
    }

    public function deleteProductStock($stock, $perPage)
    {
        $productStockToDelete = ProductStock::findOrFail($stock);
        $merchantProduct = $productStockToDelete->merchantProduct;
        $product = $merchantProduct->product;

        if ($product->has_serials && ! $product->has_variations && $productStockToDelete->serials()->where(function($q) { $q->where('has_requisitions', 1)->orWhere('has_dispatched', 1); })->exists()) {
            
           return response()->json(['errors'=>["undeletableSerial" => "Stock serial has requisition"]], 422);
            
        }

        else if ($product->has_serials && $product->has_variations && $productStockToDelete->variations()->whereHas('serials', function ($query) {
            $query->where('has_requisitions', 1)->orWhere('has_dispatched', 1);
        })->exists()) {
            
            return response()->json(['errors'=>["undeletableVariationSerial" => "Stock variation serial has requisition"]], 422);

        }

        else if ($productStockToDelete->stock_quantity > $productStockToDelete->available_quantity || $productStockToDelete->stock_quantity > $merchantProduct->latestStock->available_quantity) {
            
            return response()->json(['errors'=>["undeletableStock" => "Stock available-quantity is less than stock-quantity"]], 422);

        }

        else if ($product->has_variations && $productStockToDelete->variations()->whereRaw('stock_quantity > available_quantity')->exists()) {
            
            return response()->json(['errors'=>["undeletableStockVariation" => "Stock variation available-quantity is less than stock-quantity"]], 422);

        }

        $this->decreaseStockAvailableQuantity($productStockToDelete, $productStockToDelete->stock_quantity);
            
        $productStockToDelete->deleteStockVariations();

        $productStockToDelete->deleteStockSerials();

        $productStockToDelete->deleteOldAddresses();

        if ($productStockToDelete->stock->stocks->count() < 2) {
            
            $productStockToDelete->stock->delete();

        }

        $productStockToDelete->delete();

        return $this->showProductAllStocks($productStockToDelete->merchant_product_id, $perPage);
    }

    public function searchProductAllStocks(Request $request, $merchantProduct, $perPage)
    {
        $request->validate([
            'search' => 'nullable|required_without_all:dateTo,dateFrom|string', 
            'dateTo' => 'nullable|required_without_all:search,dateFrom|date',
            'dateFrom' => 'nullable|required_without_all:search,dateTo|date',
            // 'showPendingRequisitions' => 'nullable|boolean',
            // 'showCancelledRequisitions' => 'nullable|boolean',
            // 'showDispatchedRequisitions' => 'nullable|boolean',
            // 'showProduct' => 'nullable|string', 
        ]);

        $query = ProductStock::with(['addresses', 'variations', 'serials'])
                        ->where('merchant_product_id', $merchantProduct);

        if ($request->search) {
            
            $query->where(function($q) use ($request) {
                $q->where('stock_quantity', 'like', "%$request->search%")
                ->orWhere('available_quantity', 'like', "%$request->search%");
            });

        }

        if ($request->dateFrom) {
            
            $query->whereHas('stock', function ($q) use ($request) {
                $q->where('created_at', '>=', $request->dateFrom);
            });

        }

        if ($request->dateTo) {
            
            $query->whereHas('stock', function ($q) use ($request) {
                $q->where('created_at', '<=', $request->dateTo);
            });

        }

        return response()->json([
            'all' => new ProductStockCollection($query->paginate($perPage)),  
        ], 200);
    }

    // Stock
    public function showAllStocks($perPage)
    {
        return new StockCollection(Stock::with(['stocks.variations', 'stocks.serials', 'warehouse', 'keeper', 'approver'])->paginate($perPage));
    }

    public function storeStock(StockRequest $request, $perPage)
    {
        $currentUser = \Auth::guard('admin')->user() ?? \Auth::guard('manager')->user() ?? \Auth::guard('warehouse')->user() ?? \Auth::guard('owner')->user() ?? \Auth::user();

        if (empty($currentUser)) {

            return response()->json(['errors'=>["noUser" => "Current user missing, please reload the page"]], 422);
            
        }

        $userHasUpdatingPermission = $currentUser->hasPermissionTo('update-product-stock');

        $numberStock = Stock::where('merchant_id', $request->merchant['id'])->count();

        $newStock = Stock::create([
            'invoice_no' => 'GudamInvoice'.now()->year.now()->format('M').$request->merchant['id'].'_#'.($numberStock+1),
            'keeper_type' => get_class($currentUser), // who stores the stock
            'keeper_id' => $currentUser->id,
            'has_approval' => $userHasUpdatingPermission ? true : false, 
            'approver_type' => $userHasUpdatingPermission ? get_class($currentUser) : NULL,
            'approver_id' => $userHasUpdatingPermission ? $currentUser->id : NULL,
            'warehouse_id' => $request->warehouse['id'],
            'merchant_id' => $request->merchant['id'],
        ]);

        $request->products = json_decode(json_encode($request->products));

        foreach($request->products as $storingProductKey => $storingProduct) {

            $merchantProduct = MerchantProduct::findOrFail($storingProduct->merchant_product_id);

            $lastAvailableQuantity = $merchantProduct->latestStock->available_quantity ?? 0;

            $productNewStock = $newStock->stocks()->create([
                'stock_quantity' => $storingProduct->stock_quantity,
                'available_quantity' => $userHasUpdatingPermission ? $lastAvailableQuantity + $storingProduct->stock_quantity : $lastAvailableQuantity,
                'merchant_product_id' => $merchantProduct->id
            ]);       

            if ($storingProduct->has_variations && ! empty($storingProduct->variations)) {
                
                $productNewStock->stock_variations = json_decode(json_encode($storingProduct->variations));

            }

            if ($storingProduct->has_serials && ! $storingProduct->has_variations && ! empty($storingProduct->serials)) {
                
                $productNewStock->stock_serials = json_decode(json_encode($storingProduct->serials));

            }

            $productNewStock->setStockAddresses(json_decode(json_encode($storingProduct->addresses)), $merchantProduct->id);

        }

        return $this->showAllStocks($request->merchant_product_id, $perPage);
    }

    public function updateStock(StockRequest $request, $stock, $perPage)
    {
        $request['products'] = json_decode(json_encode($request->products));

        // validation
        foreach ($request->products as $productIndex => $stockingProduct) {
            
            $stockToUpdate = ProductStock::findOrFail($stockingProduct->id);
            $product = $stockToUpdate->merchantProduct->product;

            if (! $product->has_variations && $stockToUpdate->stock_quantity > $stockingProduct->stock_quantity) {
                
                $difference = $stockToUpdate->stock_quantity - $stockingProduct->stock_quantity;

                if ($difference > $stockToUpdate->merchantProduct->latestStock->available_quantity) {

                    return response()->json(['errors'=>["invalidValue" => "Stock quantity is less than minimum"]], 422);
                    
                }

            }
            else if($product->has_variations && $this->findVariationInvalidQuantity(json_decode(json_encode($stockingProduct->variations)), $stockToUpdate)) {

                return response()->json(['errors'=>["invalidVariationData" => "Trying to update immutable variation or less than minimum"]], 422);

            }

            if ($product->has_serials && ! $product->has_variations && ($this->checkProductSerialInValidity($stockToUpdate, json_decode(json_encode($stockingProduct->serials))) || $this->checkProductDuplicateSerial($stockingProduct))) {

                if ($this->checkProductSerialInValidity($stockToUpdate, json_decode(json_encode($stockingProduct->serials)))) {
                    
                    $serialNotFound = $this->checkProductSerialInValidity($stockToUpdate, json_decode(json_encode($stockingProduct->serials)));
                    return response()->json(['errors'=>["$serialNotFound" => "$serialNotFound serial not found"]], 422);

                }
                else {

                    $duplicateValue = $this->checkProductDuplicateSerial($stockingProduct);
                    return response()->json(['errors'=>["$duplicateValue" => "$duplicateValue serial is taken"]], 422);

                }

            }
            else if ($product->has_serials && $product->has_variations && ($this->checkVariationInvalidSerial($stockToUpdate, json_decode(json_encode($stockingProduct->variations))) || $this->checkVariationDuplicateSerial($stockingProduct))) {
                
                if ($this->checkVariationInvalidSerial($stockToUpdate, json_decode(json_encode($stockingProduct->variations)))) {
                    
                    $serialNotFound = $this->checkVariationInvalidSerial($stockToUpdate, json_decode(json_encode($stockingProduct->variations)));
                    return response()->json(['errors'=>["$serialNotFound" => "$serialNotFound serial not found"]], 422);

                }
                else {

                    $duplicateValue = $this->checkVariationDuplicateSerial($stockingProduct);
                    return response()->json(['errors'=>["$duplicateValue" => "$duplicateValue serial is taken"]], 422);

                }
                
            }

        }

        $currentUser = \Auth::guard('admin')->user() ?? \Auth::guard('manager')->user() ?? \Auth::guard('warehouse')->user() ?? \Auth::guard('owner')->user() ?? Auth::user();

        if (empty($currentUser)) {

            return response()->json(['errors'=>["noUser" => "Current user missing, please reload the page"]], 422);
            
        }

        foreach ($request->products as $productIndex => $stockingProduct) {
            
            $stockToUpdate = ProductStock::findOrFail($stockingProduct->id);
            $product = $stockToUpdate->merchantProduct->product;

            if ($stockingProduct->stock_quantity > $stockToUpdate->stock_quantity) {

                $difference = $stockingProduct->stock_quantity - $stockToUpdate->stock_quantity;

                $stockToUpdate->update([
                    'stock_quantity' => $stockingProduct->stock_quantity,
                    'available_quantity' => ($stockToUpdate->available_quantity + $difference),
                ]);

                $stockToUpdate->stock()->update([
                    'has_approval' => true,
                    'approver_type' => get_class($currentUser),
                    'approver_id' => $currentUser->id,
                    'warehouse_id' => $request['warehouse']['id'],
                ]);

                $this->increaseStockAvailableQuantity($stockToUpdate, $difference);

            }
            else if ($stockingProduct->stock_quantity < $stockToUpdate->stock_quantity) {

                $difference = $stockToUpdate->stock_quantity - $stockingProduct->stock_quantity;

                $stockToUpdate->update([
                    'stock_quantity' => $stockingProduct->stock_quantity,
                    'available_quantity' => ($stockToUpdate->available_quantity - $difference), 
                ]);

                $stockToUpdate->stock()->update([
                    'has_approval' => true,
                    'approver_type' => get_class($currentUser),
                    'approver_id' => $currentUser->id,
                    'warehouse_id' => $request['warehouse']['id'],
                ]);

                $this->decreaseStockAvailableQuantity($stockToUpdate, $difference);

            }
            else if($stockingProduct->stock_quantity == $stockToUpdate->stock_quantity && ! $stockToUpdate->stock->has_approval) {

                $stockToUpdate->stock()->update([
                    'has_approval' => true,
                    'approver_type' => get_class($currentUser),
                    'approver_id' => $currentUser->id,
                    'warehouse_id' => $request['warehouse']['id'],
                ]);

                $this->increaseStockAvailableQuantity($stockToUpdate, $stockToUpdate->stock_quantity);

            }
            else if($stockingProduct->stock_quantity == $stockToUpdate->stock_quantity && $stockToUpdate->stock->warehouse_id != $request['warehouse']['id']) {

                $stockToUpdate->stock()->update([
                    'has_approval' => true,
                    'approver_type' => get_class($currentUser),
                    'approver_id' => $currentUser->id,
                    'warehouse_id' => $request['warehouse']['id'],
                ]);

            }

            if ($product->has_variations && ! empty($stockingProduct->variations)) {

                $stockToUpdate->stock_variations = json_decode(json_encode($stockingProduct->variations));

            }

            if ($stockToUpdate->has_serials && ! $stockToUpdate->has_variations && ! empty($stockingProduct->serials)) {
                
                $stockToUpdate->stock_serials = json_decode(json_encode($stockingProduct->serials));

            }

            $stockToUpdate->setStockAddresses(json_decode(json_encode($stockingProduct->addresses)), $stockToUpdate->merchant_product_id);

        }            

        return $this->showAllStocks($stockToUpdate->merchant_product_id, $perPage);
    }

    public function deleteStock($stock, $perPage)
    {
        $stockToDelete = Stock::findOrFail($stock);

        foreach ($stockToDelete->stocks as $productStockToDeleteKey => $productStockToDelete) {
            
            $merchantProduct = $productStockToDelete->merchantProduct;
            $product = $merchantProduct->product;

            if ($product->has_serials && ! $product->has_variations && $productStockToDelete->serials()->where(function($q) { $q->where('has_requisitions', 1)->orWhere('has_dispatched', 1); })->exists()) {
                
               return response()->json(['errors'=>["undeletableSerial" => "Stock serial has requisition"]], 422);
                
            }

            else if ($product->has_serials && $product->has_variations && $productStockToDelete->variations()->whereHas('serials', function ($query) {
                $query->where('has_requisitions', 1)->orWhere('has_dispatched', 1);
            })->exists()) {
                
                return response()->json(['errors'=>["undeletableVariationSerial" => "Stock variation serial has requisition"]], 422);

            }

            else if ($productStockToDelete->stock_quantity > $productStockToDelete->available_quantity || $productStockToDelete->stock_quantity > $merchantProduct->latestStock->available_quantity) {
                
                return response()->json(['errors'=>["undeletableStock" => "Stock available-quantity is less than stock-quantity"]], 422);

            }

            else if ($product->has_variations && $productStockToDelete->variations()->whereRaw('stock_quantity > available_quantity')->exists()) {
                
                return response()->json(['errors'=>["undeletableStockVariation" => "Stock variation available-quantity is less than stock-quantity"]], 422);

            }

            $this->decreaseStockAvailableQuantity($productStockToDelete, $productStockToDelete->stock_quantity);
                
            $productStockToDelete->deleteStockVariations();

            $productStockToDelete->deleteStockSerials();

            $productStockToDelete->deleteOldAddresses();

            if ($productStockToDelete->stock->stocks->count() < 2) {
                
                $productStockToDelete->stock->delete();

            }

            $productStockToDelete->delete();    

        }


        return $this->showAllStocks($productStockToDelete->merchant_product_id, $perPage);
    }

    public function searchAllStocks(Request $request, $perPage)
    {
        $request->validate([
            'search' => 'nullable|required_without_all:dateTo,dateFrom|string', 
            'dateTo' => 'nullable|required_without_all:search,dateFrom|date',
            'dateFrom' => 'nullable|required_without_all:search,dateTo|date',
            // 'showPendingRequisitions' => 'nullable|boolean',
            // 'showCancelledRequisitions' => 'nullable|boolean',
            // 'showDispatchedRequisitions' => 'nullable|boolean',
            // 'showProduct' => 'nullable|string', 
        ]);

        $query = Stock::with(['stocks.variations', 'stocks.serials', 'warehouse', 'keeper', 'approver']);

        if ($request->search) {
            
            $query->where(function ($q1) use ($request) {
                $q1->where('invoice_no', 'like', "%$request->search%")
                ->orWhereHas('stocks', function ($q2) use ($request) {
                    $q2->where('stock_quantity', 'like', "%$request->search%")
                    ->orWhere('available_quantity', 'like', "%$request->search%");
                })
                ->orWhereHas('stocks.serials', function ($q3) use ($request) {
                    $q3->where('serial_no', 'like', "%$request->search%");
                })
                ->orWhereHas('stocks.variations', function ($q4) use ($request) {
                    $q4->where('stock_quantity', 'like', "%$request->search%")
                    ->orWhere('available_quantity', 'like', "%$request->search%");
                })
                ->orWhereHas('stocks.variations.serials', function ($q5) use ($request) {
                    $q5->where('serial_no', 'like', "%$request->search%");
                })
                ->orWhereHas('warehouse', function ($q6) use ($request) {
                    $q6->where('name', 'like', "%$request->search%")
                    ->orWhere('user_name', 'like', "%$request->search%")
                    ->orWhere('email', 'like', "%$request->search%")
                    ->orWhere('mobile', 'like', "%$request->search%");
                })
                ->orWhereHasMorph(
                    'keeper',
                    [Admin::class, Manager::class],
                    function ($q7) use ($request) {
                        $q7->where('first_name', 'like', "%$request->search%")
                        ->orWhere('last_name', 'like', "%$request->search%")
                        ->orWhere('user_name', 'like', "%$request->search%")
                        ->orWhere('email', 'like', "%$request->search%")
                        ->orWhere('mobile', 'like', "%$request->search%");
                    }
                )
                ->orWhereHasMorph(
                    'approver',
                    [Admin::class, Manager::class],
                    function ($q8) use ($request) {
                        $q8->where('first_name', 'like', "%$request->search%")
                        ->orWhere('last_name', 'like', "%$request->search%")
                        ->orWhere('user_name', 'like', "%$request->search%")
                        ->orWhere('email', 'like', "%$request->search%")
                        ->orWhere('mobile', 'like', "%$request->search%");
                    }
                );
            });

        }

        if ($request->dateFrom) {
            
            $query->where('created_at', '>=', $request->dateFrom);

        }

        if ($request->dateTo) {
            
            $query->where('created_at', '<=', $request->dateTo);

        }

        return response()->json([
            'all' => new StockCollection($query->paginate($perPage)),  
        ], 200);
    }

    // Manager Products
    /*
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

            return response()->json([
                'all' => new ProductCollection($query->paginate($perPage)),  
            ], 200);
        }
    */

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
            'product_id' => [
                'required', 'numeric', 'exists:products,id', 
                Rule::unique('merchant_products', 'product_id')->where(function ($query) use($request) {
                    return $query->where('merchant_id', $request->merchant_id)->where('manufacturer_id', $request->manufacturer_id);
                }),
            ],
            'merchant_id' => [
                'required', 'numeric', 'exists:merchants,id', 
                Rule::unique('merchant_products', 'merchant_id')->where(function ($query) use($request) {
                    return $query->where('product_id', $request->product_id)->where('manufacturer_id', $request->manufacturer_id);
                }),
            ],
            'sku' => [
                'required', 'string', 
                Rule::unique('merchant_products', 'sku')->where(function ($query) use($request) {
                    return $query->where('product_id', $request->product_id)->where('merchant_id', $request->merchant_id)->where('manufacturer_id', $request->manufacturer_id);
                }),
            ],
            'manufacturer_id' => 'nullable|numeric|exists:product_manufacturers,id',
            // 'selling_price' => 'required|numeric',
            'selling_price' => [ 'nullable', 'numeric', 
                Rule::requiredIf(function () use ($product) {
                    return $product->product_category_id != NULL;
                }),
            ],
            'discount' => 'nullable|numeric|between:0,100',
            'description' => 'nullable|string|max:255',
            'warning_quantity' => 'nullable|numeric',
            'variations' => [
                'array', 
                Rule::requiredIf(function () use ($product) {
                    return $product->has_variations;
                })
            ],
            'variations.*.variation' => 'required_with:variations',
            'variations.*.variation.id' => [
                'required_with:variations', 
                Rule::exists('product_variations', 'id')->where('product_id', $product->id), 
            ],
            'variations.*.selling_price' => 'required_with:variations|numeric',
            'variations.*.sku' => 'string',
        ],
        [
            'product_id.required' => 'Product is required',
            'product_id.unique' => 'Product already exists',
            'product_id.*' => 'Product is invalid',

            'merchant_id.required' => 'Merchant is required',
            'merchant_id.unique' => 'Merchant already exists',
            'merchant_id.*' => 'Merchant is invalid', 

            'sku.required' => 'SKU is required',
            'sku.unique' => 'SKU already exists',
            'sku.*' => 'SKU is invalid', 

            'manufacturer_id' => 'Manufacturer is invalid', 
            'selling_price' => 'Product selling price is required',
            'warning_quantity' => 'Warning quantity should be numeric',
            'variations.*.variation' => 'Variation name is required',
            'variations.*.variation.id.*' => 'Invalid variations, please reload',
            'variations.*.selling_price' => 'Variation selling price is required',
            'variations.*.sku' => 'Invalid variation SKU',
        ]);

        $currentUser = \Auth::guard('admin')->user() ?? \Auth::guard('manager')->user() ?? \Auth::guard('warehouse')->user() ?? \Auth::guard('owner')->user() ?? \Auth::user();

        if (empty($currentUser)) {

            return response()->json(['errors'=>["noUser" => "Current user missing, please reload the page"]], 422);
            
        }

        $productNewMerchant = MerchantProduct::create([

            'sku' => strtoupper($request->sku) ?? $this->generateProductSKU($request->merchant_id, $product->product_category_id, $product->id, $request->manufacturer_id), 
            // 'merchant_product_preview' => $request->preview, 
            'description' => strtolower($request->description), 
            'manufacturer_id' => $request->manufacturer_id, 
            'warning_quantity' => $request->warning_quantity ?? 0,
            'selling_price' => $product->product_category_id ? $request->selling_price : NULL,
            'discount' => $product->product_category_id ? $request->discount : 0,
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
            'product_id' => [
                'required', 'numeric', 'exists:products,id', 
                Rule::unique('merchant_products', 'product_id')->where(function ($query) use($request) {
                    return $query->where('merchant_id', $request->merchant_id)->where('manufacturer_id', $request->manufacturer_id);
                })->ignore($productMerchant),
            ],
            'merchant_id' => [
                'required', 'numeric', 'exists:merchants,id', 
                Rule::unique('merchant_products', 'merchant_id')->where(function ($query) use($request) {
                    return $query->where('product_id', $request->product_id)->where('manufacturer_id', $request->manufacturer_id);
                })->ignore($productMerchant),
            ],
            'sku' => [
                'required', 'string', 
                Rule::unique('merchant_products', 'sku')->where(function ($query) use($request) {
                    return $query->where('product_id', $request->product_id)->where('merchant_id', $request->merchant_id)->where('manufacturer_id', $request->manufacturer_id);
                })->ignore($productMerchant),
            ], 
            'manufacturer_id' => 'nullable|numeric|exists:product_manufacturers,id',
            // 'selling_price' => 'required|numeric',
            'selling_price' => [ 'nullable', 'numeric', 
                Rule::requiredIf(function () use ($product) {
                    return $product->product_category_id != NULL;
                }),
            ],
            'discount' => 'nullable|numeric|between:0,100',
            'description' => 'nullable|string|max:255',
            'warning_quantity' => 'nullable|numeric',
            'variations' => [
                'array', 
                Rule::requiredIf(function () use ($product) {
                    return $product->has_variations;
                })
            ],
            'variations.*.variation.id' => 'required_without:variations.*.product_variation_id',
            'variations.*.product_variation_id' => [
                'required_without:variations.*.variation.id',
                Rule::exists('product_variations', 'id')->where('product_id', $product->id), 
            ],
            'variations.*.selling_price' => 'required_with:variations|numeric',
            'variations.*.sku' => 'string',
        ],
        [
            'product_id.required' => 'Product is required',
            'product_id.unique' => 'Product already exists',
            'product_id.*' => 'Product is invalid',

            'merchant_id.required' => 'Merchant is required',
            'merchant_id.unique' => 'Merchant already exists',
            'merchant_id.*' => 'Merchant is invalid', 

            'sku.required' => 'SKU is required',
            'sku.unique' => 'SKU already exists',
            'sku.*' => 'SKU is invalid', 

            'manufacturer_id' => 'Manufacturer is invalid', 
            'selling_price' => 'Product selling price is required',
            'warning_quantity' => 'Warning quantity should be numeric',
            'variations.*.variation' => 'Variation name is required',
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

            'sku' => strtoupper($request->sku) ?? $this->generateProductSKU($request->merchant_id, $product->product_category_id, $product->id, $request->manufacturer_id), 
            'manufacturer_id' => $request->manufacturer_id, 
            // 'merchant_product_preview' => $request->preview, 
            'description' => strtolower($request->description), 
            'warning_quantity' => $request->warning_quantity ?? 0,
            'selling_price' => $product->product_category_id ? $request->selling_price : NULL,
            'discount' => $product->product_category_id ? $request->discount : 0,
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

        // if any related stock / requisition exists
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
                    ->orWhereHas('manufacturer', function ($q) use ($search) {
                        $q->where('name', 'like', "%$search%");
                    })
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

    protected function findVariationInvalidQuantity($stockVariaitons = [], ProductStock $productStock)
    {
        if (count($stockVariaitons) && $productStock->variations->count()) {
            
            $stockVariationCollection = collect($stockVariaitons);

            foreach ($productStock->variations as $productVariationExistingStockKey => $productVariationExistingStock) {
                
                $merchantProductVariation = $productVariationExistingStock->merchantProductVariation;
                $expectedVariationeLatestStock = $merchantProductVariation->latestStock;

                $expectedVariationInputedStock = $stockVariationCollection->first(function ($object, $key) use ($productVariationExistingStock) {
                    return $object->merchant_product_variation_id == $productVariationExistingStock->merchant_product_variation_id;
                });

                // replaced variation-stock
                if (empty($expectedVariationInputedStock) && $merchantProductVariation->dispatchedRequests->count() && $expectedVariationeLatestStock->available_quantity < $productVariationExistingStock->stock_quantity) {
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

                    // only stock but deducted quantity is less than dispatched
                    if ($merchantProductVariation->stocks->count() < 2 && $merchantProductVariation->dispatchedRequests->count() > ($productVariationExistingStock->stock_quantity - $expectedVariationInputedStock->stock_quantity)) {
                        
                        return true;

                    }
                    // has multiple stocks, but deducted quantity is gonna affect next stocks
                    else if (($expectedVariationeLatestStock->available_quantity < ($productVariationExistingStock->stock_quantity - $expectedVariationInputedStock->stock_quantity)) || $merchantProductVariation->stocks()->where('id', '>', $expectedVariationInputedStock->id)->where('available_quantity', '<', $expectedVariationInputedStock->stock_quantity)->exists()) {
                        
                        return true;

                    }

                    return false;

                }

            }

            return false;

        }

        return true;
    }

    protected function increaseStockAvailableQuantity(ProductStock $stockToUpdate, $amount)
    {
        ProductStock::where('merchant_product_id', $stockToUpdate->merchant_product_id)->where('id', '>', $stockToUpdate->id)->increment('available_quantity', $amount);
    }

    protected function decreaseStockAvailableQuantity(ProductStock $stockToUpdate, $amount)
    {
        ProductStock::where('merchant_product_id', $stockToUpdate->merchant_product_id)->where('id', '>', $stockToUpdate->id)->decrement('available_quantity', $amount);
    }

    protected function generateProductSKU($merchant, $productCategory, $product, $menufacturer)
    {
        if ($productCategory) {
            return 'MR'.$merchant.'CT'.$productCategory.'PR'.$product.'MF'.$menufacturer;
        }
        return 'MR'.$merchant.'BX'.'PR'.$product.'MF'.$menufacturer;
    }

    protected function checkVariationSerialDuplicacy(Request $request)
    {
        foreach($request->variations as $productVariationKey => $productVariation)
        {
            foreach ($productVariation['serials'] as $variationSerialKey => $productVariationSerial) {
                
                if (ProductVariationSerial::where('serial_no', $productVariationSerial['serial_no'])->count() > 1) {
                    
                    // return true;
                    return $productVariationSerial['serial_no'];

                }
                
            }
        }

        return false;
    }

    protected function checkVariationDuplicateSerial($stockingProduct)
    {
        foreach($stockingProduct->variations as $stockingProductVariationKey => $stockingProductVariation)
        {
            foreach ($stockingProductVariation->serials as $variationSerialKey => $stockingVariationSerial) {
                
                if (ProductVariationSerial::where('serial_no', $stockingVariationSerial->serial_no)->count() > 1) {
                    
                    // return true;
                    return $stockingVariationSerial->serial_no;

                }
                
            }
        }

        return false;
    }

    protected function checkVariationSerialInvalidity(ProductStock $productStock, $productVariationSerials)
    {
        if ($productStock->variations()->whereHas('serials', function ($query) {
            $query->where('has_requisitions', 1)->orWhere('has_dispatched', 1);
        })->exists()) {

            $productVariationSerialCollection = collect($productVariationSerials);
            
            foreach ($productStock->variations()->whereHas('serials', function ($query) {
                $query->where('has_requisitions', 1)->orWhere('has_dispatched', 1);
            })->exists() as $productStockVariationKey => $productStockVariation) {
                
                foreach ($productStockVariation->serials()->where('has_requisitions', 1)->orWhere('has_dispatched', 1)->get() as $productStockVariationSerialKey => $productStockVariationSerial) {
                    
                    $productVariationSerialExists = $productVariationSerialCollection->first(function ($object, $key) use ($productStockVariationSerial) {
                        return $object->serial_no == $productStockVariationSerial->serial_no;
                    });

                    if (empty($productVariationSerialExists)) {
                        
                        // return true;
                        return $productStockVariationSerial->serial_no;

                    }

                }

            }

        }

        return false;
    }

    protected function checkVariationInvalidSerial(ProductStock $productStock, $stockingProductVariations)
    {
        if ($productStock->variations()->whereHas('serials', function ($query) {
            $query->where('has_requisitions', 1)->orWhere('has_dispatched', 1);
        })->exists()) {

            $stockingProductVariationCollection = collect($stockingProductVariations);
            
            foreach ($productStock->variations()->whereHas('serials', function ($query) {
                $query->where('has_requisitions', 1)->orWhere('has_dispatched', 1);
            })->exists() as $productStockVariationKey => $productStockVariation) {
                
                foreach ($productStockVariation->serials()->where('has_requisitions', 1)->orWhere('has_dispatched', 1)->get() as $productStockVariationSerialKey => $productStockVariationSerial) {
                    
                    $productVariationSerialExists = $stockingProductVariationCollection->first(function ($stockingProductVariation, $stockingProductVariationKey) use ($productStockVariationSerial) {

                        $stockingProductVariation->serials->first(function ($variationSerial, $variationSerialKey) use ($productStockVariationSerial) {
                            return $variationSerial->serial_no == $productStockVariationSerial->serial_no;
                        });
                    });

                    if (empty($productVariationSerialExists)) {
                        
                        // return true;
                        return $productStockVariationSerial->serial_no;

                    }

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
                
                return $productSerial['serial_no'];

            }
        }

        return false;
    }

    protected function checkProductDuplicateSerial($stockingProduct)
    {
        foreach($stockingProduct->serials as $productSerial)
        {
            if (ProductSerial::where('serial_no', $productSerial->serial_no)->count() > 1) {
                
                return $productSerial->serial_no;

            }
        }

        return false;
    }

    protected function checkProductSerialInValidity(ProductStock $productStock, $productSerials)
    {
        if ($productStock->serials()->where('has_requisitions', 1)->orWhere('has_dispatched', 1)->exists()) {
            
            $productSerialCollection = collect($productSerials);

            foreach ($productStock->serials()->where('has_requisitions', 1)->orWhere('has_dispatched', 1)->get() as $productSerialKey => $productSerial) {
                
                $productSerialExists = $productSerialCollection->first(function ($object, $key) use ($productSerial) {
                    return $object->serial_no == $productSerial->serial_no;
                });

                if (empty($productSerialExists)) {
                    
                    // return true;
                    return $productSerial->serial_no;

                }

            }

        }

        return false;
    }

}
