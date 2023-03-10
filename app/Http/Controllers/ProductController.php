<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\Admin;
use App\Models\Manager;
use App\Models\Product;
use App\Models\Merchant;
use App\Models\ProductStock;
use Illuminate\Http\Request;
use App\Models\Vendor;
// use App\Models\ProductSerial;
use App\Models\MerchantProduct;
use App\Models\ProductCategory;
use Illuminate\Validation\Rule;
use App\Models\ProductVariation;
use App\Models\ProductManufacturer;
use Illuminate\Support\Facades\File; 
// use App\Models\ProductVariationSerial;
use App\Http\Requests\Web\StockRequest;
use App\Http\Resources\Web\StockCollection;
// use App\Models\WarehouseContainerStatus;
// use Illuminate\Support\Facades\Validator;
use App\Http\Resources\Web\ProductResource;
use App\Http\Requests\Web\ProductStockRequest;
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
        $this->middleware("permission:delete-product")->only(['deleteProduct', 'restoreProduct']);
        
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

        if ($assetToDelete->merchantProducts->count()) {
            
            return response()->json(['errors'=>["engaged" => ucfirst($assetToDelete->name)." is in use at ".$assetToDelete->merchantProducts->count()." products"]], 422);

        }

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

                'trashed' => new ProductCategoryCollection(ProductCategory::onlyTrashed()->withCount('products')->with('parent')->latest('id')->paginate($perPage)),

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
        $newAsset->is_perishable = $request->parent_category_id ? ProductCategory::findOrFail($request->parent_category_id)->is_perishable ? true : $request->is_perishable : $request->is_perishable;
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
        $assetToUpdate->is_perishable = $request->parent_category_id ? ProductCategory::findOrFail($request->parent_category_id)->is_perishable ? true : $request->is_perishable : $request->is_perishable;
        $assetToUpdate->parent_category_id = $request->parent_category_id;

        $assetToUpdate->save();

        if ($assetToUpdate->is_perishable && $assetToUpdate->childs->count()) {
            
            $assetToUpdate->childs()->update([
                
                'is_perishable' => true

            ]);

        }

        return $this->showProductAllCategories($perPage);
    }

    public function deleteProductCategory($asset, $perPage)
    {
        $assetToDelete = ProductCategory::findOrFail($asset);
        
        if ($assetToDelete->childs->count()) {
            
            return response()->json(['errors'=>["engaged" => ucfirst($assetToDelete->name)." is in use at ".$assetToDelete->childs->count()." childs"]], 422);

        }
        else if ($assetToDelete->products->count()) {
            
            return response()->json(['errors'=>["engaged" => ucfirst($assetToDelete->name)." is in use at ".$assetToDelete->products->count()." products"]], 422);

        }

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

        $query = ProductCategory::withTrashed()->withCount('products')->with('parent')->latest('id');

        foreach($columnsToSearch as $column){
            $query->orWhere($column, 'like', "%$search%");
        }

        return response()->json([
            'all' => new ProductCategoryCollection($query->paginate($perPage)),    
        ], 200);
    }

    // Products
    public function showAllProducts($perPage=false)
    {
        if ($perPage) {

            return response()->json([

                'retail' => new ProductCollection(Product::where('product_category_id', '!=', 0)->with(['variations.variation.type.variations', 'category', 'merchantProducts'])->withCount('merchants')->paginate($perPage)),

                'bulk' => new ProductCollection(Product::whereNull('product_category_id')->orWhere('product_category_id', 0)->withCount('merchants')->with(['merchantProducts'])->paginate($perPage)),

                'trashed' => new ProductCollection(Product::onlyTrashed()->with(['variations.variation.type.variations', 'category', 'merchantProducts'])->withCount('merchants')->paginate($perPage)),

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
            'product_category_id' => 'nullable|integer|exists:product_categories,id',
            // 'merchant_id' => 'required|integer|exists:merchants,id',
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
            'product_category_id' => 'nullable|integer|exists:product_categories,id',
            // 'merchant_id' => 'required|integer|exists:merchants,id',
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
    
    public function deleteProduct($asset, $perPage)
    {
        $assetToDelete = Product::findOrFail($asset);
        
        if ($assetToDelete->merchants->count()) {
            
            return response()->json(['errors'=>["engaged" => ucfirst($assetToDelete->name)." is in use at ".$assetToDelete->merchants->count()." merchants"]], 422);

        }

        // $userToDelete->warehouses()->delete();
        $assetToDelete->delete();

        return $this->showAllProducts($perPage);
    }

    public function restoreProduct($asset, $perPage)
    {
        $assetToRestore = Product::withTrashed()->findOrFail($asset);
        // $userToRestore->warehouses()->restore();
        $assetToRestore->restore();

        return $this->showAllProducts($perPage);
    }

    public function searchAllProducts($search, $perPage)
    {
        $query = Product::with(['variations.variation.type.variations', 'category', 'merchantProducts'])->withCount('merchants')
        ->where('name', 'like', "%$search%")
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
            'product_category_id' => 'integer|exists:product_categories,id',
            // 'merchant_id' => 'required|integer|exists:merchants,id',
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
            'product_category_id' => 'integer|exists:product_categories,id',
            // 'merchant_id' => 'required|integer|exists:merchants,id',
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
        return new ProductStockCollection(ProductStock::with(['addresses', 'variations', 'serials'])->where('merchant_product_id', $productMerchant)->latest('id')->paginate($perPage));
    }

    public function storeProductStock(ProductStockRequest $request, $perPage)
    {
        $merchantProduct = MerchantProduct::findOrFail($request->merchant_product_id);
        $product = $merchantProduct->product;

        /*
        $request->validate([
            // 'merchant_id' => 'required|exists:merchants,id',
            'warehouse.id' => 'required|exists:warehouses,id',
            'stock_quantity' => 'required|integer|min:1', 
            'unit_buying_price' => [ 'nullable', 'numeric', 
                
                // Rule::requiredIf(function () use ($product) {
                //     return $product->product_category_id != NULL;
                // }),
                
            ],
            // 'product_id' => 'required|integer|exists:products,id',
            'serials' => [
                'array', 'min:'.$request->stock_quantity,
                Rule::requiredIf(function () use ($product) {
                    return $product->has_serials && ! $product->has_variations;
                })
            ],
            'serials.*.serial_no' => [
                'string', 'distinct', 'min:4', 
                // 'unique:product_serials,serial_no', 
                Rule::unique('product_serials', 'serial_no')->ignore(1, 'has_dispatched'),
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
                'integer', 'exists:merchant_product_variations,id', 
                Rule::requiredIf(function () use ($product) {
                    return $product->has_variations;
                })
            ],
            'variations.*.stock_quantity' => [
                'integer', 'min:1', 
                Rule::requiredIf(function () use ($product, $request) {
                    return $product->has_variations && array_sum($request->input('variations.*.stock_quantity')) != $request->stock_quantity;
                })
            ],
            'variations.*.unit_buying_price' => [ 'nullable', 'numeric', 
                
                // Rule::requiredIf(function () use ($product) {
                //     return $product->product_category_id != NULL;
                // }),
                
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
                // 'unique:product_variation_serials,serial_no', 
                Rule::unique('product_variation_serials', 'serial_no')->ignore(1, 'has_dispatched'),
                Rule::requiredIf(function () use ($product) {
                    return $product->has_variations && $product->has_serials;
                })
            ]
        ],
        [
            'warehouse.id.exists' => 'Warehouse is invalid !',
            'stock_quantity.*' => 'Stock quantity is required !',
            'unit_buying_price.*' => 'Buying price should be numeric !',
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
            'variations.*.unit_buying_price.*' => 'Buying price should be numeric !',

            'variations.*.serials.*' => 'Variation serial is required',

            'variations.*.serials.*.serial_no' => [
                'distinct' => 'Variation duplicate serial is invalid',
                'unique' => 'Variation serial exists',
                '*' => 'Variation serial is required',
            ]
        ]);
        */

        // $lastAvailableQuantity = /*$merchantProduct->latestStock->available_quantity*/ $merchantProduct->available_quantity ?? 0;

        $currentUser = \Auth::guard('admin')->user() ?? \Auth::guard('manager')->user();

        if (empty($currentUser)) {

            return response()->json(['errors'=>["noUser" => "Current user missing, please reload the page"]], 422);
            
        }

        $numberStock = Stock::where('merchant_id', $request->merchant_id)->count();
        $userHasUpdatingPermission = $currentUser->hasPermissionTo('update-product-stock');

        $newStock = Stock::create([
            'invoice_no' => 'GudamStockInvoice'.now()->year.now()->month.'M'.$request->merchant_id.'S'.($numberStock+1),
            'keeper_type' => get_class($currentUser), // who stores the stock
            'keeper_id' => $currentUser->id,
            'has_approval' => $userHasUpdatingPermission ? true : false, 
            'approver_type' => $userHasUpdatingPermission ? get_class($currentUser) : NULL,
            'approver_id' => $userHasUpdatingPermission ? $currentUser->id : NULL,
            'warehouse_id' => $request['warehouse']['id'],
            'merchant_id' => $request->merchant_id,
            'created_at' => $request->created_at ? $request->created_at : now(),
            'updated_at' => ($userHasUpdatingPermission && $request->updated_at) ? $request->updated_at : now(),
        ]);

        $productNewStock = $newStock->stocks()->create([
            'stock_code' => (! empty($request->stock_code) ? strtoupper(trim($request->stock_code)) : ($merchantProduct->sku.'S'.($merchantProduct->stocks->count()+1))),
            'stock_quantity' => $request->stock_quantity,
            // 'available_quantity' => $userHasUpdatingPermission ? $lastAvailableQuantity + $request->stock_quantity : $lastAvailableQuantity,
            'available_quantity' => $request->stock_quantity,
            'unit_buying_price' => ! $product->has_variations ? ($request->unit_buying_price ?? 0.0) : 0.0,  // No Costing Price if variation exists
            'manufactured_at' => $request->manufactured_at ?? NULL,
            'expired_at' => $request->expired_at ?? NULL,
            'vendor_id' => $request->vendor_id,
            'location_id' => $request->location_id,
            'merchant_product_id' => $merchantProduct->id
        ]);

        if ($userHasUpdatingPermission) {
            
            $merchantProduct->increment('available_quantity', $request->stock_quantity);

        }

        if ($product->has_variations && ! empty($request->variations)) {
            
            $productNewStock->stock_variations = json_decode(json_encode($request->variations));

        }

        if ($product->has_serials && ! $product->has_variations && ! empty($request->serials)) {
            
            $productNewStock->stock_serials = json_decode(json_encode($request->serials));

        }

        $productNewStock->setStockAddresses(json_decode(json_encode($request->addresses)), $merchantProduct->id);

        return $this->showProductAllStocks($request->merchant_product_id, $perPage);
    }

    public function updateProductStock(ProductStockRequest $request, $stock, $perPage)
    {
        $productStockToUpdate = ProductStock::findOrFail($stock);
        $merchantExpectedProduct = $productStockToUpdate->merchantProduct;
        $product = $merchantExpectedProduct->product;
        $stockToUpdate = $productStockToUpdate->stock;

        /*
        $request->validate([
            'warehouse.id' => 'required|exists:warehouses,id',
            'stock_quantity' => 'required|integer',
            'unit_buying_price' => [ 'nullable', 'numeric', 
            ],
            // 'product_id' => 'required|integer|exists:products,id',
            'serials' => [
                'array', 'min:'.$request->stock_quantity,
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
                'integer', 'exists:merchant_product_variations,id', 
                Rule::requiredIf(function () use ($product) {
                    return $product->has_variations;
                })
            ],
            'variations.*.stock_quantity' => [
                'integer', 'min:1', 
                Rule::requiredIf(function () use ($product, $request) {
                    return $product->has_variations && array_sum($request->input('variations.*.stock_quantity')) != $request->stock_quantity;
                })
            ],
            'variations.*.unit_buying_price' => [ 'nullable', 'numeric', 
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
            'warehouse.id.exists' => 'Warehouse is invalid !',
            'warehouse.id.*' => 'Warehouse is required !',
            'stock_quantity.*' => 'Stock quantity is missing or invalid !',
            'unit_buying_price.*' => 'Buying price should be numeric !',
            
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

            'variations.*.unit_buying_price.*' => 'Buying price should be numeric',
            'variations.*.serials.*' => 'Variation serial is required',

            'variations.*.serials.*.serial_no' => [
                'distinct' => 'Variation duplicate serial is invalid',
                'unique' => 'Variation serial exists',
                '*' => 'Variation serial is required',
            ]
        ]);

        // decreasing quantity
        if (! $product->has_variations && $productStockToUpdate->stock_quantity > $request->stock_quantity) {
            
            $difference = $productStockToUpdate->stock_quantity - $request->stock_quantity;

            if ($difference > $productStockToUpdate->available_quantity || $difference > $productStockToUpdate->merchantProduct->available_quantity) {

                return response()->json(['errors'=>["invalidValue" => "Stock quantity is less than minimum"]], 422);
                
            }

        }
        else if($product->has_variations && $this->findVariationInvalidQuantity(json_decode(json_encode($request->variations)), $productStockToUpdate)) {

            return response()->json(['errors'=>["invalidVariationData" => "Trying to update immutable variation or less than minimum"]], 422);

        }

        if ($product->has_serials && ! $product->has_variations && ($this->checkProductSerialInValidity($productStockToUpdate, json_decode(json_encode($request->serials))) || $this->checkProductSerialDuplicacy($request))) {

            if ($this->checkProductSerialInValidity($productStockToUpdate, json_decode(json_encode($request->serials)))) {
                
                $serialNotFound = $this->checkProductSerialInValidity($productStockToUpdate, json_decode(json_encode($request->serials)));
                return response()->json(['errors'=>["$serialNotFound" => "$serialNotFound serial not found"]], 422);

            }
            else {

                $duplicateValue = $this->checkProductSerialDuplicacy($request);
                return response()->json(['errors'=>["$duplicateValue" => "$duplicateValue serial is taken"]], 422);

            }

        }
        else if ($product->has_serials && $product->has_variations && ($this->checkVariationSerialInvalidity($productStockToUpdate, json_decode(json_encode($request->variations))) || $this->checkVariationSerialDuplicacy($request))) {
            
            if ($this->checkVariationSerialInvalidity($productStockToUpdate, json_decode(json_encode($request->variations)))) {
                
                $serialNotFound = $this->checkVariationSerialInvalidity($productStockToUpdate, json_decode(json_encode($request->variations)));
                return response()->json(['errors'=>["$serialNotFound" => "$serialNotFound serial not found"]], 422);

            }
            else {

                $duplicateValue = $this->checkVariationSerialDuplicacy($request);
                return response()->json(['errors'=>["$duplicateValue" => "$duplicateValue serial is taken"]], 422);

            }
            
        }
        */

        $currentUser = \Auth::guard('admin')->user() ?? \Auth::guard('manager')->user();

        if (empty($currentUser)) {

            return response()->json(['errors'=>["noUser" => "Current user missing, please reload the page"]], 422);
            
        }
            
        // increasing quantity
        if ($request->stock_quantity > $productStockToUpdate->stock_quantity) {

            $difference = $request->stock_quantity - $productStockToUpdate->stock_quantity;

            $productStockToUpdate->update([
                'stock_quantity' => $request->stock_quantity, 
                'available_quantity' => ($productStockToUpdate->available_quantity + $difference),
                'unit_buying_price' => ! $product->has_variations ? ($request->unit_buying_price ?? 0.0) : 0.0,
                'vendor_id' => $request->vendor_id,
                'location_id' => $request->location_id,
            ]);

            // $this->increaseStockAvailableQuantity($productStockToUpdate, $difference);

            $stockToUpdate->has_approval ? $merchantExpectedProduct->increment('available_quantity', $difference) : $merchantExpectedProduct->increment('available_quantity', $request->stock_quantity); // defferent increments for already approved / not

        }
        // decreasing quantity
        else if ($request->stock_quantity < $productStockToUpdate->stock_quantity) {

            $difference = $productStockToUpdate->stock_quantity - $request->stock_quantity;

            $productStockToUpdate->update([
                'stock_quantity' => $request->stock_quantity, 
                'available_quantity' => ($productStockToUpdate->available_quantity - $difference), 
                'unit_buying_price' => ! $product->has_variations ? ($request->unit_buying_price ?? 0.0) : 0.0,
                'vendor_id' => $request->vendor_id,
                'location_id' => $request->location_id,
            ]);

            // $this->decreaseStockAvailableQuantity($productStockToUpdate, $difference);

            $stockToUpdate->has_approval ? $merchantExpectedProduct->decrement('available_quantity', $difference) : $merchantExpectedProduct->increment('available_quantity', $request->stock_quantity);    // decrement for already approved but increments if about to approve

        }
        /*
        // approving stock
        else if($request->stock_quantity == $productStockToUpdate->stock_quantity && ! $productStockToUpdate->stock->has_approval) {

            $productStockToUpdate->update([
                'unit_buying_price' => ! $product->has_variations ? ($request->unit_buying_price ?? $merchantExpectedProduct->selling_price ?? 0) : 0.0,
            ]);

            // $this->increaseStockAvailableQuantity($productStockToUpdate, $productStockToUpdate->stock_quantity);

            $merchantExpectedProduct->increment('available_quantity', $productStockToUpdate->stock_quantity);

        }
        // changing warehouse
        else if($request->stock_quantity == $productStockToUpdate->stock_quantity && $productStockToUpdate->stock->warehouse_id != $request['warehouse']['id']) {

            $productStockToUpdate->update([
                'unit_buying_price' => $request->unit_buying_price ?? $productStockToUpdate->merchantProduct->selling_price ?? 0,
            ]);

        }
        */
        else {  // approving stock / updating only price

            $productStockToUpdate->update([
                'unit_buying_price' => ! $product->has_variations ? ($request->unit_buying_price ?? 0.0) : 0.0,
                'vendor_id' => $request->vendor_id,
                'location_id' => $request->location_id,
            ]);

            if (! $stockToUpdate->has_approval) { // approving with requested quantity
                
                $merchantExpectedProduct->increment('available_quantity', $productStockToUpdate->stock_quantity);

            }

        }

        $this->updateParentStock($productStockToUpdate, $currentUser, $request);

        if ($product->has_variations && ! empty($request->variations)) {

            $productStockToUpdate->stock_variations = json_decode(json_encode($request->variations));

        }

        if ($productStockToUpdate->has_serials && ! $productStockToUpdate->has_variations && ! empty($request->serials)) {
            
            $productStockToUpdate->stock_serials = json_decode(json_encode($request->serials));

        }

        if ($productStockToUpdate->available_quantity > 0) {
            
            $productStockToUpdate->setStockAddresses(json_decode(json_encode($request->addresses)), $productStockToUpdate->merchant_product_id);

        }

        return $this->showProductAllStocks($productStockToUpdate->merchant_product_id, $perPage);
    }

    public function deleteProductStock($stock, $perPage)
    {
        $productStockToDelete = ProductStock::findOrFail($stock);
        $merchantProduct = $productStockToDelete->merchantProduct;
        $product = $merchantProduct->product;

        /*
        if ($product->has_serials && ! $product->has_variations && $productStockToDelete->serials()->where(function($q) { $q->where('has_requisitions', 1)->orWhere('has_dispatched', 1); })->exists()) {
            
           return response()->json(['errors'=>["undeletableSerial" => "Stock serial has requisition"]], 422);
            
        }

        else if ($product->has_serials && $product->has_variations && $productStockToDelete->variations()->whereHas('serials', function ($query) {
            $query->where('has_requisitions', 1)->orWhere('has_dispatched', 1);
        })->exists()) {
            
            return response()->json(['errors'=>["undeletableVariationSerial" => "Stock variation serial has requisition"]], 422);

        }
        */
        // if a few dispatched or available qty is less than stock quantity
        if ($productStockToDelete->stock_quantity > $productStockToDelete->available_quantity || $productStockToDelete->stock_quantity > /*$merchantProduct->latestStock->available_quantity*/ $merchantProduct->available_quantity) {
            
            return response()->json(['errors'=>["undeletableStock" => "Stock or Product available-quantity is less than stock-quantity"]], 422);

        }
        // if a few dispatched or available qty is less than stock quantity
        else if ($product->has_variations && $productStockToDelete->variations()->whereRaw('stock_quantity > available_quantity')->exists()) {
            
            return response()->json(['errors'=>["undeletableStockVariation" => "Stock variation available-quantity is less than stock-quantity"]], 422);

        }

        // $this->decreaseStockAvailableQuantity($productStockToDelete, $productStockToDelete->stock_quantity);

        $merchantProduct->decrement('available_quantity', $productStockToDelete->stock_quantity);
            
        $productStockToDelete->deleteStockVariations();

        $productStockToDelete->deleteStockSerials();

        $productStockToDelete->deleteOldAddresses();

        if ($productStockToDelete->stock->stocks->count() < 2) {    //  if this is the only product-stock in parent-stock
            
            $productStockToDelete->stock->delete();

        }

        $productStockToDelete->forceDelete();

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
                $q->where('stock_code', 'like', "%$request->search%")
                ->orWhere('stock_quantity', 'like', "%$request->search%")
                ->orWhere('available_quantity', 'like', "%$request->search%")
                ->orWhere('unit_buying_price', 'like', "%$request->search%")
                ->orWhereHas('serials', function ($query2) use ($request) {
                    $query2->where('serial_no', 'like', "%$request->search%");
                })
                ->orWhereHas('vendor', function ($query3) use ($request) {
                    $query3->where('name', 'like', "%$request->search%");
                })
                ->orWhereHas('location', function ($query4) use ($request) {
                    $query4->where('name', 'like', "%$request->search%");
                })
                ->orWhereHas('variations', function ($query5) use ($request) {
                    $query5->where('stock_code', 'like', "%$request->search%")
                        ->orWhere('stock_quantity', 'like', "%$request->search%")
                        ->orWhere('available_quantity', 'like', "%$request->search%")
                        ->orWhere('unit_buying_price', 'like', "%$request->search%")
                        ->orWhereHas('serials', function ($query6) use ($request) {
                            $query6->where('serial_no', 'like', "%$request->search%");
                        });
                });
            });

        }

        if ($request->dateFrom) {
            
            $query->whereHas('stock', function ($q) use ($request) {
                $q->whereDate('created_at', '>=', $request->dateFrom);
            });

        }

        if ($request->dateTo) {
            
            $query->whereHas('stock', function ($q) use ($request) {
                $q->whereDate('created_at', '<=', $request->dateTo);
            });

        }

        return response()->json([
            'all' => new ProductStockCollection($query->paginate($perPage)),  
        ], 200);
    }

    // Stock
    public function showAllStocks($perPage)
    {
        return new StockCollection(Stock::with(['stocks.variations', 'stocks.serials', 'warehouse', 'keeper', 'approver'])->latest()->paginate($perPage));
    }

    public function storeStock(StockRequest $request, $perPage)
    {
        $currentUser = \Auth::guard('admin')->user() ?? \Auth::guard('manager')->user();

        if (empty($currentUser)) {

            return response()->json(['errors'=>["noUser" => "Current user missing, please reload the page"]], 422);
            
        }

        $userHasUpdatingPermission = $currentUser->hasPermissionTo('update-product-stock');

        $numberStock = Stock::where('merchant_id', $request->merchant['id'])->count();

        $newStock = Stock::create([
            'invoice_no' => 'GudamStockInvoice'.now()->year.now()->month.'M'.$request->merchant['id'].'S'.($numberStock+1),
            'keeper_type' => get_class($currentUser), // who stores the stock
            'keeper_id' => $currentUser->id,
            'has_approval' => $userHasUpdatingPermission ? true : false, 
            'approver_type' => $userHasUpdatingPermission ? get_class($currentUser) : NULL,
            'approver_id' => $userHasUpdatingPermission ? $currentUser->id : NULL,
            'warehouse_id' => $request->warehouse['id'],
            'merchant_id' => $request->merchant['id'],
            'created_at' => $request->created_at ? $request->created_at : now(),
            'updated_at' => ($userHasUpdatingPermission && $request->updated_at) ? $request->updated_at : now(),
        ]);

        $request->products = json_decode(json_encode($request->products));

        foreach($request->products as $storingProductKey => $storingProduct) {

            $merchantProduct = MerchantProduct::findOrFail($storingProduct->merchant_product_id);
            $product = $merchantProduct->product;

            // $lastAvailableQuantity = /*$merchantProduct->latestStock->available_quantity*/ $merchantProduct->available_quantity ?? 0;

            $productNewStock = $newStock->stocks()->create([
                'stock_code' => (! empty($storingProduct->stock_code) ? strtoupper(trim($storingProduct->stock_code)) : ($merchantProduct->sku.'S'.($merchantProduct->stocks->count()+1))),
                'stock_quantity' => $storingProduct->stock_quantity,
                // 'available_quantity' => $userHasUpdatingPermission ? $lastAvailableQuantity + $storingProduct->stock_quantity : $lastAvailableQuantity,
                'available_quantity' => $storingProduct->stock_quantity,
                'unit_buying_price' => ! $product->has_variations ? ($storingProduct->unit_buying_price ?? 0.0) : 0.0,
                'manufactured_at' => $storingProduct->manufactured_at ?? NULL,
                'expired_at' => $storingProduct->expired_at ?? NULL,
                'vendor_id' => $storingProduct->vendor_id,
                'location_id' => $storingProduct->location_id,
                'merchant_product_id' => $merchantProduct->id
            ]);

            if ($userHasUpdatingPermission) {
                
                $merchantProduct->increment('available_quantity', $storingProduct->stock_quantity);

            }      

            if ($product->has_variations && ! empty($storingProduct->variations)) {
                
                $productNewStock->stock_variations = json_decode(json_encode($storingProduct->variations));

            }

            if ($product->has_serials && ! $storingProduct->has_variations && ! empty($storingProduct->serials)) {
                
                $productNewStock->stock_serials = json_decode(json_encode($storingProduct->serials));

            }

            $productNewStock->setStockAddresses(json_decode(json_encode($storingProduct->addresses)), $merchantProduct->id);

        }

        return $this->showAllStocks($perPage);
    }

    public function updateStock(StockRequest $request, $stock, $perPage)
    {   
        $currentUser = \Auth::guard('admin')->user() ?? \Auth::guard('manager')->user();

        if (empty($currentUser)) {

            return response()->json(['errors'=>["noUser" => "Current user missing, please reload the page"]], 422);
            
        }

        $stockToUpdate = Stock::findOrFail($stock);

        $stockToUpdate->stocks()->delete(); // primarily deleting products-stocks

        $request['products'] = json_decode(json_encode($request->products));

        foreach ($request->products as $productIndex => $stockingProduct) {

            $merchantExpectedProduct = MerchantProduct::findOrFail($stockingProduct->merchant_product_id);
            $product = $merchantExpectedProduct->product;
            
            if (empty($stockingProduct->id)) {      // added new product-stock
                
                $productStockToUpdate = $stockToUpdate->stocks()->create([
                    'stock_code' => ($merchantExpectedProduct->sku.'S'.($merchantExpectedProduct->stocks->count()+1)),
                    'stock_quantity' => $stockingProduct->stock_quantity,
                    'available_quantity' => $stockingProduct->stock_quantity,
                    'unit_buying_price' => ! $product->has_variations ? ($stockingProduct->unit_buying_price ?? 0.0) : 0.0,  // No Costing Price if variation exists
                    'manufactured_at' => $stockingProduct->manufactured_at ?? NULL,
                    'expired_at' => $stockingProduct->expired_at ?? NULL,
                    'vendor_id' => $stockingProduct->vendor_id,
                    'location_id' => $stockingProduct->location_id,
                    'merchant_product_id' => $merchantExpectedProduct->id
                ]);
                    
                $merchantExpectedProduct->increment('available_quantity', $stockingProduct->stock_quantity);

            }

            else {
                
                $productStockToUpdate = ProductStock::withTrashed()->findOrFail($stockingProduct->id);

                if ($productStockToUpdate->merchant_product_id == $stockingProduct->merchant_product_id) { 
                    
                    if ($stockingProduct->stock_quantity > $productStockToUpdate->stock_quantity) {

                        $difference = $stockingProduct->stock_quantity - $productStockToUpdate->stock_quantity;

                        $productStockToUpdate->update([
                            'stock_quantity' => $stockingProduct->stock_quantity,
                            'available_quantity' => ($productStockToUpdate->available_quantity + $difference),
                            'unit_buying_price' => ! $product->has_variations ? ($stockingProduct->unit_buying_price ?? 0.0) : 0.0,
                            'manufactured_at' => $stockingProduct->manufactured_at ?? NULL,
                            'expired_at' => $stockingProduct->expired_at ?? NULL, 
                            'vendor_id' => $stockingProduct->vendor_id,
                            'location_id' => $stockingProduct->location_id,
                            'deleted_at' => NULL
                        ]);

                        // $this->increaseStockAvailableQuantity($productStockToUpdate, $difference);
                        
                        $stockToUpdate->has_approval ? $merchantExpectedProduct->increment('available_quantity', $difference) : $merchantExpectedProduct->increment('available_quantity', $stockingProduct->stock_quantity);

                    }
                    else if ($stockingProduct->stock_quantity < $productStockToUpdate->stock_quantity) {

                        $difference = $productStockToUpdate->stock_quantity - $stockingProduct->stock_quantity;

                        $productStockToUpdate->update([
                            'stock_quantity' => $stockingProduct->stock_quantity,
                            'available_quantity' => ($productStockToUpdate->available_quantity - $difference), 
                            'unit_buying_price' => ! $product->has_variations ? ($stockingProduct->unit_buying_price ?? 0.0) : 0.0,
                            'manufactured_at' => $stockingProduct->manufactured_at ?? NULL,
                            'expired_at' => $stockingProduct->expired_at ?? NULL, 
                            'vendor_id' => $stockingProduct->vendor_id,
                            'location_id' => $stockingProduct->location_id,
                            'deleted_at' => NULL
                        ]);

                        // $this->decreaseStockAvailableQuantity($productStockToUpdate, $difference);
                        
                        $stockToUpdate->has_approval ? $merchantExpectedProduct->decrement('available_quantity', $difference) : $merchantExpectedProduct->increment('available_quantity', $stockingProduct->stock_quantity);

                    }
                    else {  // approving stock / updating only price

                        $productStockToUpdate->update([
                            'unit_buying_price' => ! $product->has_variations ? ($stockingProduct->unit_buying_price ?? 0.0) : 0.0,
                            'manufactured_at' => $stockingProduct->manufactured_at ?? NULL,
                            'expired_at' => $stockingProduct->expired_at ?? NULL, 
                            'vendor_id' => $stockingProduct->vendor_id,
                            'location_id' => $stockingProduct->location_id,
                            'deleted_at' => NULL
                        ]);

                        if (! $stockToUpdate->has_approval) {
                            
                            $merchantExpectedProduct->increment('available_quantity', $productStockToUpdate->stock_quantity);

                        }

                    }

                }
                else { // replaced / swaped merchant-product
                    
                    $productStockToUpdate->merchantProduct->decrement('available_quantity', $productStockToUpdate->stock_quantity);
            
                    $productStockToUpdate->deleteStockVariations();

                    $productStockToUpdate->deleteStockSerials();

                    $productStockToUpdate->deleteOldAddresses();

                    $productStockToUpdate->delete();

                    $productStockToUpdate = ProductStock::create([
                        'stock_code' => ($merchantExpectedProduct->sku.'S'.($merchantExpectedProduct->stocks->count()+1)),
                        'stock_quantity' => $stockingProduct->stock_quantity,
                        'available_quantity' => $stockingProduct->stock_quantity,
                        'unit_buying_price' => ! $product->has_variations ? ($stockingProduct->unit_buying_price ?? 0.0) : 0.0,
                        'manufactured_at' => $stockingProduct->manufactured_at ?? NULL,
                        'expired_at' => $stockingProduct->expired_at ?? NULL, 
                        'vendor_id' => $stockingProduct->vendor_id,
                        'location_id' => $stockingProduct->location_id,
                        'merchant_product_id' => $merchantExpectedProduct->id,
                        'stock_id' => $stock
                    ]);

                    $merchantExpectedProduct->increment('available_quantity', $productStockToUpdate->stock_quantity);

                }

            }

            if ($product->has_variations && ! empty($stockingProduct->variations)) {

                $productStockToUpdate->stock_variations = json_decode(json_encode($stockingProduct->variations));

            }

            if ($product->has_serials && ! $product->has_variations && ! empty($stockingProduct->serials)) {
                
                $productStockToUpdate->stock_serials = json_decode(json_encode($stockingProduct->serials));

            }

            if ($productStockToUpdate->available_quantity > 0) {
                
                $productStockToUpdate->setStockAddresses(json_decode(json_encode($stockingProduct->addresses)), $productStockToUpdate->merchant_product_id);
            
            }

        }

        $this->updateParentStock($productStockToUpdate, $currentUser, $request);

        // deleting still soft-deleted stocks
        foreach ($stockToUpdate->stocks()->onlyTrashed()->get() as $trashedProductStockKey => $trashedProductStock) {

            $trashedProductStock->merchantProduct->decrement('available_quantity', $trashedProductStock->stock_quantity);
            
            $trashedProductStock->deleteStockVariations();

            $trashedProductStock->deleteStockSerials();

            $trashedProductStock->deleteOldAddresses();

            $trashedProductStock->forceDelete();

        }

        return $this->showAllStocks($perPage);
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

            else if ($productStockToDelete->stock_quantity > $productStockToDelete->available_quantity || $productStockToDelete->stock_quantity > /*$merchantProduct->latestStock->available_quantity*/ $merchantProduct->available_quantity) {
                
                return response()->json(['errors'=>["undeletableStock" => "Stock available-quantity is less than stock-quantity"]], 422);

            }

            else if ($product->has_variations && $productStockToDelete->variations()->whereRaw('stock_quantity > available_quantity')->exists()) {
                
                return response()->json(['errors'=>["undeletableStockVariation" => "Stock variation available-quantity is less than stock-quantity"]], 422);

            }

            // $this->decreaseStockAvailableQuantity($productStockToDelete, $productStockToDelete->stock_quantity);
                
            $merchantProduct->decrement('available_quantity', $productStockToDelete->stock_quantity);

            $productStockToDelete->deleteStockVariations();

            $productStockToDelete->deleteStockSerials();

            $productStockToDelete->deleteOldAddresses();

            if ($productStockToDelete->stock->stocks->count() < 2) {
                
                $productStockToDelete->stock->delete();

            }

            $productStockToDelete->forceDelete();    

        }


        return $this->showAllStocks($perPage);
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
                    $q2->where('stock_code', 'like', "%$request->search%")
                    ->orWhere('stock_quantity', 'like', "%$request->search%")
                    ->orWhere('available_quantity', 'like', "%$request->search%")
                    ->orWhere('unit_buying_price', 'like', "%$request->search%")
                    ->orWhereHas('vendor', function ($q3) use ($request) {  
                        $q3->where('name', 'like', "%$request->search%");
                    })
                    ->orWhereHas('location', function ($q4) use ($request) {
                        $q4->where('name', 'like', "%$request->search%");
                    })
                    ->orWhereHas('merchantProduct', function ($q5) use ($request) {
                        $q5->whereHas('product', function ($q6) use ($request) {
                            $q6->where('name', 'like', "%$request->search%");
                        });
                    });
                })
                ->orWhereHas('stocks.serials', function ($q7) use ($request) {
                    $q7->where('serial_no', 'like', "%$request->search%");
                })
                ->orWhereHas('stocks.variations', function ($q8) use ($request) {
                    $q8->where('stock_code', 'like', "%$request->search%")
                        ->orWhere('stock_quantity', 'like', "%$request->search%")
                        ->orWhere('available_quantity', 'like', "%$request->search%")
                        ->orWhere('unit_buying_price', 'like', "%$request->search%")
                        ->orWhereHas('serials', function ($query9) use ($request) {
                            $query9->where('serial_no', 'like', "%$request->search%");
                        });
                })
                ->orWhereHas('stocks.variations.serials', function ($q9) use ($request) {
                    $q9->where('serial_no', 'like', "%$request->search%");
                })
                ->orWhereHas('warehouse', function ($q10) use ($request) {
                    $q10->where('name', 'like', "%$request->search%")
                    ->orWhere('user_name', 'like', "%$request->search%")
                    ->orWhere('email', 'like', "%$request->search%")
                    ->orWhere('mobile', 'like', "%$request->search%");
                })
                ->orWhereHas('merchant', function ($q11) use ($request) {
                    $q11->where('first_name', 'like', "%$request->search%")
                    ->orWhere('last_name', 'like', "%$request->search%")
                    ->orWhere('user_name', 'like', "%$request->search%")
                    ->orWhere('email', 'like', "%$request->search%")
                    ->orWhere('mobile', 'like', "%$request->search%");
                })
                ->orWhereHasMorph(
                    'keeper',
                    [Admin::class, Manager::class],
                    function ($q12) use ($request) {
                        $q12->where('first_name', 'like', "%$request->search%")
                        ->orWhere('last_name', 'like', "%$request->search%")
                        ->orWhere('user_name', 'like', "%$request->search%")
                        ->orWhere('email', 'like', "%$request->search%")
                        ->orWhere('mobile', 'like', "%$request->search%");
                    }
                )
                ->orWhereHasMorph(
                    'approver',
                    [Admin::class, Manager::class],
                    function ($q13) use ($request) {
                        $q13->where('first_name', 'like', "%$request->search%")
                        ->orWhere('last_name', 'like', "%$request->search%")
                        ->orWhere('user_name', 'like', "%$request->search%")
                        ->orWhere('email', 'like', "%$request->search%")
                        ->orWhere('mobile', 'like', "%$request->search%");
                    }
                );
            });

        }

        if ($request->dateFrom) {
            
            $query->whereDate('created_at', '>=', $request->dateFrom);

        }

        if ($request->dateTo) {
            
            $query->whereDate('created_at', '<=', $request->dateTo);

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
        return new MerchantProductCollection(MerchantProduct::where('product_id', $product)->with(['merchant', 'variations', 'requests', 'addresses', 'stocks', 'serials', 'nonDispatchedRequests', 'dispatchedRequests'])->paginate($perPage));
    }

    public function storeProductNewMerchant(Request $request, $perPage)
    {
        $product = Product::findOrFail($request->product_id);

        $request->validate([
            // 'product' => 'required',
            // 'product.id' => 'required|integer|exists:products,id',
            // 'merchant' => 'required',
            // 'merchant.id' => 'required|integer|exists:merchants,id',
            'product_id' => [
                'required', 'integer', 'exists:products,id', 
                Rule::unique('merchant_products', 'product_id')->where(function ($query) use($request) {
                    return $query->where('merchant_id', $request->merchant_id)->where('manufacturer_id', $request->manufacturer_id);
                }),
            ],
            'merchant_id' => [
                'required', 'integer', 'exists:merchants,id', 
                Rule::unique('merchant_products', 'merchant_id')->where(function ($query) use($request) {
                    return $query->where('product_id', $request->product_id)->where('manufacturer_id', $request->manufacturer_id);
                }),
            ],
            'sku' => [
                'sometimes', 'nullable', 'string', 'max:15', 'unique:merchant_products,sku',
                /*
                Rule::unique('merchant_products', 'sku')->where(function ($query) use($request) {
                    return $query->where('product_id', $request->product_id)->where('merchant_id', $request->merchant_id)->where('manufacturer_id', $request->manufacturer_id);
                }),
                */
            ],

            'upc' => [
                'nullable', 'numeric', 'max:999999999999', 'digits:12',
                
                /*
                Rule::unique('merchant_products', 'upc')->where(function ($query) use($request) {
                    return $query->where('product_id', $request->product_id)->where('merchant_id', $request->merchant_id)->where('manufacturer_id', $request->manufacturer_id);
                }),
                */
            ],

            'manufacturer_id' => 'nullable|integer|exists:product_manufacturers,id',
            // 'selling_price' => 'required|numeric',
            'selling_price' => [ 'nullable', 'numeric', 'min:0'
                /*
                Rule::requiredIf(function () use ($product) {
                    return $product->product_category_id != NULL;
                }),
                */
            ],
            'discount' => 'nullable|numeric|between:0,100',
            'description' => 'nullable|string|max:65500',
            'warning_quantity' => 'nullable|integer',
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
            'variations.*.selling_price' => 'nullable|numeric|min:0',
            'variations.*.sku' => 'sometimes|nullable|string|max:15|unique:merchant_product_variations,sku',
        ],
        [
            'product_id.required' => 'Product is required',
            'product_id.unique' => 'Product already exists',
            'product_id.*' => 'Product is invalid',

            'merchant_id.required' => 'Merchant is required',
            'merchant_id.unique' => 'Merchant already exists',
            'merchant_id.*' => 'Merchant is invalid', 

            // 'sku.required' => 'SKU is required',
            'sku.unique' => 'SKU already exists',
            'sku.*' => 'SKU is invalid', 

            'manufacturer_id' => 'Manufacturer is invalid', 
            'selling_price' => 'Invalid selling price',
            'warning_quantity' => 'Warning quantity should be numeric',
            'variations.*.variation' => 'Variation name is required',
            'variations.*.variation.id.*' => 'Invalid variations, please reload',
            'variations.*.selling_price' => 'Invalid selling price',
            'variations.*.sku' => 'Invalid variation SKU',
        ]);

        if ($request->upc) {        // same upc for other products / manufacturers
        
            if (! empty($request->manufacturer_id)) {
               
                $sameUPCExists = MerchantProduct::where('upc', $request->upc)
                ->where(function ($query) use ($request) {
                    $query->where('product_id', '!=', $request->product_id)
                          ->orWhere('manufacturer_id', '!=', $request->manufacturer_id);
                })
                ->exists();

            }
            else {

                $sameUPCExists = MerchantProduct::where('upc', $request->upc)->where('product_id', '!=', $request->product_id)->exists();

            }

            if ($sameUPCExists) {
                
                return response()->json(['errors'=>["engaged" => "UPC is taken."]], 422);

            }

        }

        if ($product->has_variations) {
            
            // same upc for other product-variations
            foreach (json_decode(json_encode($request->variations)) as $merchantProductVariationKey => $merchantProductVariation) {
                
                if (! empty($merchantProductVariation->upc) && MerchantProductVariation::where('upc', $merchantProductVariation->upc)->where('product_variation_id', '!=', $merchantProductVariation->variation->id)->exists()) {
                    
                    return response()->json(['errors'=>["engaged" => ucfirst($merchantProductVariation->productVariation->variation->name)." upc is taken."]], 422);

                }

            }

        }

        $currentUser = \Auth::guard('admin')->user() ?? \Auth::guard('manager')->user() ?? \Auth::guard('warehouse')->user() ?? \Auth::guard('owner')->user() ?? \Auth::user();

        if (empty($currentUser)) {

            return response()->json(['errors'=>["noUser" => "Current user missing, please reload the page"]], 422);
            
        }

        $productNewMerchant = MerchantProduct::create([

            'sku' => ! empty($request->sku) ? strtoupper($request->sku) : $this->generateProductSKU($product->product_category_id, $product->id, $request->merchant_id, $request->manufacturer_id), 
            'upc' => $request->upc,
            // 'merchant_product_preview' => $request->preview, 
            'description' => strtolower($request->description), 
            'manufacturer_id' => $request->manufacturer_id, 
            'warning_quantity' => $request->warning_quantity ?? 0,
            'selling_price' => $product->product_category_id ? $request->selling_price : NULL,
            'discount' => $product->product_category_id ? $request->discount : 0,
            'product_id' => $request->product_id,
            'merchant_id' => $request->merchant_id,
            'created_at' => now()->format('Y-m-d')

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
            // 'product.id' => 'required|integer|exists:products,id',
            // 'merchant' => 'required',
            // 'merchant.id' => 'required|integer|exists:merchants,id',
            'product_id' => [
                'required', 'integer', 'exists:products,id', 
                Rule::unique('merchant_products', 'product_id')->where(function ($query) use($request) {
                    return $query->where('merchant_id', $request->merchant_id)->where('manufacturer_id', $request->manufacturer_id);
                })->ignore($productMerchant),
            ],
            'merchant_id' => [
                'required', 'integer', 'exists:merchants,id', 
                Rule::unique('merchant_products', 'merchant_id')->where(function ($query) use($request) {
                    return $query->where('product_id', $request->product_id)->where('manufacturer_id', $request->manufacturer_id);
                })->ignore($productMerchant),
            ],
            'sku' => [
                'sometimes', 'nullable', 'string', 'max:15',
                /*
                Rule::unique('merchant_products', 'sku')->where(function ($query) use($request) {
                    return $query->where('product_id', $request->product_id)->where('merchant_id', $request->merchant_id)->where('manufacturer_id', $request->manufacturer_id);
                })->ignore($productMerchant),
                */
                Rule::unique('merchant_products', 'sku')->ignore($productMerchant),
            ], 
            'upc' => [
                'nullable', 'numeric', 'max:999999999999', 'digits:12', 
                
                /*
                Rule::unique('merchant_products', 'upc')->where(function ($query) use($request) {
                    return $query->where('product_id', $request->product_id)->where('merchant_id', $request->merchant_id)->where('manufacturer_id', $request->manufacturer_id);
                })->ignore($productMerchant),
                */ 
            ],
            'manufacturer_id' => 'nullable|integer|exists:product_manufacturers,id',
            // 'selling_price' => 'required|numeric',
            'selling_price' => [ 'nullable', 'numeric', 'min:0'
                /*
                Rule::requiredIf(function () use ($product) {
                    return $product->product_category_id != NULL;
                }),
                */
            ],
            'discount' => 'nullable|numeric|between:0,100',
            'description' => 'nullable|string|max:65500',
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
            'variations.*.selling_price' => 'nullable|numeric|min:0',
            'variations.*.sku' => 'sometimes|nullable|string|max:15',
        ],
        [
            'product_id.required' => 'Product is required',
            'product_id.unique' => 'Product already exists',
            'product_id.*' => 'Product is invalid',

            'merchant_id.required' => 'Merchant is required',
            'merchant_id.unique' => 'Merchant already exists',
            'merchant_id.*' => 'Merchant is invalid', 

            // 'sku.required' => 'SKU is required',
            'sku.unique' => 'SKU already exists',
            'sku.*' => 'SKU is invalid', 

            'manufacturer_id' => 'Manufacturer is invalid', 
            'selling_price' => 'Selling price is invalid',
            'warning_quantity' => 'Warning quantity should be numeric',
            'variations.*.variation' => 'Variation name is required',
            'variations.*.variation.id.*' => 'Invalid variations, please reload',
            'variations.*.selling_price' => 'Selling price is invalid',
            'variations.*.sku' => 'Invalid variation SKU',
        ]);

        if ($request->upc) {        // same upc for other products / manufacturers
        
            if (! empty($request->manufacturer_id)) {
               
                $sameUPCExists = MerchantProduct::where('id', '!=', $productMerchant)
                ->where('upc', $request->upc)
                ->where(function ($query) use ($request) {
                    $query->where('product_id', '!=', $request->product_id)
                          ->orWhere('manufacturer_id', '!=', $request->manufacturer_id);
                })
                ->exists();

            }
            else {

                $sameUPCExists = MerchantProduct::where('id', '!=', $productMerchant)
                ->where('upc', $request->upc)
                ->where('product_id', '!=', $request->product_id)
                ->exists();

            }

            if ($sameUPCExists) {
                
                return response()->json(['errors'=>["engaged" => "UPC is taken."]], 422);

            }

        }

        if ($product->has_variations) {
            
            // same upc for other product-variations
            foreach (json_decode(json_encode($request->variations)) as $merchantProductVariationKey => $merchantProductVariation) {
                
                // new variation
                if (empty($merchantProductVariation->id) && ! empty($merchantProductVariation->upc) && MerchantProductVariation::where('upc', $merchantProductVariation->upc)->where('product_variation_id', '!=', $merchantProductVariation->variation->id)->exists()) {
                    
                    return response()->json(['errors'=>["engaged" => ucfirst($merchantProductVariation->productVariation->variation->name)." upc is taken."]], 422);

                }
                // old variation
                else if (! empty($merchantProductVariation->id) && ! empty($merchantProductVariation->upc) && MerchantProductVariation::where('upc', $merchantProductVariation->upc)->where('product_variation_id', '!=', $merchantProductVariation->product_variation_id)->where('id', '!=', $merchantProductVariation->id)->exists()) {
                    
                    return response()->json(['errors'=>["engaged" => ucfirst($merchantProductVariation->productVariation->variation->name)." upc is taken."]], 422);

                }

            }

        }

        $currentUser = \Auth::guard('admin')->user() ?? \Auth::guard('manager')->user() ?? \Auth::guard('warehouse')->user() ?? \Auth::guard('owner')->user() ?? \Auth::user();

        if (empty($currentUser)) {

            return response()->json(['errors'=>["noUser" => "Current user missing, please reload the page"]], 422);
            
        }

        $productMerchantToUpdate = MerchantProduct::findOrFail($productMerchant);

        $productMerchantToUpdate->update([

            'sku' => ! empty($request->sku) ? strtoupper($request->sku) : $this->generateProductSKU($product->product_category_id, $product->id, $request->merchant_id, $request->manufacturer_id), 
            'upc' => $request->upc,
            'manufacturer_id' => $request->manufacturer_id, 
            // 'merchant_product_preview' => $request->preview, 
            'description' => strtolower($request->description), 
            'warning_quantity' => $request->warning_quantity ?? 0,
            'selling_price' => $product->product_category_id ? $request->selling_price : NULL,
            'discount' => $product->product_category_id ? $request->discount : 0,
            'product_id' => $request->product_id,
            'merchant_id' => $request->merchant_id,
            // 'created_at' => now()->format('Y-m-d')

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
        ->with(['merchant', 'variations', 'requests', 'addresses', 'stocks', 'serials', 'nonDispatchedRequests', 'dispatchedRequests']);

        return response()->json([
            'all' => new MerchantProductCollection($query->paginate($perPage)),  
        ], 200);
    }

    /*
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
                    
                    
                    // MerchantProductVariation which has only stock but already dispatched, cant be replaced
                    if ($merchantProductVariation->stocks->count() < 2 && $merchantProductVariation->dispatchedRequests->count()) {
                        
                        return true;

                    }
                    // have multiple stocks, but next available quantities are gonna have negative value 
                    else if ($merchantProductVariation->stocks()->where('id', '>', $productVariationExistingStock->id)->where('available_quantity', '<', $productVariationExistingStock->stock_quantity)->exists()) {
                        
                        return true;

                    }
                    
                    return false;
                    
                   
                    return true;

                }
                // decreased
                else if (! empty($expectedVariationInputedStock) && $merchantProductVariation->dispatchedRequests->count() && $productVariationExistingStock->stock_quantity > $expectedVariationInputedStock->stock_quantity) {

                    // new quantity is less than stock available quantity
                    if ($productVariationExistingStock->stock_quantity > $productVariationExistingStock->available_quantity && $expectedVariationInputedStock->stock_quantity < $productVariationExistingStock->available_quantity) {
                        
                        return true;

                    }
                    // has multiple stocks, but deducted quantity is more than ultimate available quantity
                    else if (($merchantProductVariation->available_quantity < ($productVariationExistingStock->stock_quantity - $expectedVariationInputedStock->stock_quantity))) {
                        
                        // || $merchantProductVariation->stocks()->where('id', '>', $expectedVariationInputedStock->id)->where('available_quantity', '<', $expectedVariationInputedStock->stock_quantity)->exists()
                        return true;

                    }
                    // only stock but deducted quantity is less than dispatched
                    
                    // else if ($merchantProductVariation->stocks->count() < 2 && $merchantProductVariation->dispatchedRequests->sum('quantity') > ($productVariationExistingStock->stock_quantity - $expectedVariationInputedStock->stock_quantity)) {
                        
                    //     return true;

                    // }
                    

                    // return false;

                }

            }

            return false;

        }

        return false;
    }
    */

    /*
    protected function increaseStockAvailableQuantity(ProductStock $productStockToUpdate, $amount)
    {
        ProductStock::where('merchant_product_id', $productStockToUpdate->merchant_product_id)->where('id', '>', $productStockToUpdate->id)->increment('available_quantity', $amount);
    }

    protected function decreaseStockAvailableQuantity(ProductStock $productStockToUpdate, $amount)
    {
        ProductStock::where('merchant_product_id', $productStockToUpdate->merchant_product_id)->where('id', '>', $productStockToUpdate->id)->decrement('available_quantity', $amount);
    }
    */

    protected function generateProductSKU($productCategory, $product, $merchant, $manufacturer = NULL)
    {
        if ($productCategory) {

            // return 'C'.$productCategory.'P'.$product.'M'.$merchant.'M'.$manufacturer;
            // return ('P'.$product.'M'.$merchant.'MF'.($manufacturer ? $manufacturer : $merchant));
            $generatedSKU = ('P'.$product.'M'.$merchant.($manufacturer ? $manufacturer : $merchant));

        }
        
        else {

            // return ('BP'.$product.'M'.$merchant.'MF'.($manufacturer ? $manufacturer : $merchant));
            $generatedSKU = ('BP'.$product.'M'.$merchant.($manufacturer ? $manufacturer : $merchant));

        }
        
        if (MerchantProduct::where('sku', $generatedSKU)->exists()) {
            
            $this->generateProductSKU($productCategory, rand(1, Product::count()), rand(1, Merchant::count()), $manufacturer);

        }

        return $generatedSKU;
    }

    /*
    protected function checkVariationSerialDuplicacy(Request $request)
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
    */

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

    /*
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
    */

    /*
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

                    
                    // $productVariationSerialExists = $stockingProductVariationCollection->first(function ($stockingProductVariation, $stockingProductVariationKey) use ($productStockVariationSerial) {

                    //     $stockingProductVariationSerialCollection = collect($stockingProductVariation->serials);

                    //     $stockingProductVariationSerialCollection->first(function ($variationSerial, $variationSerialKey) use ($productStockVariationSerial) {
                    //         return $variationSerial->serial_no == $productStockVariationSerial->serial_no;
                    //     });
                    // });
                    

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
    */

    /*
    protected function checkProductSerialDuplicacy(Request $request)
    {
        foreach($request->serials as $productSerial)
        {
            if ((empty($productSerial['id']) && ProductSerial::where('serial_no', $productSerial['serial_no'])->count()) || (! empty($productSerial['id']) && ProductSerial::where('serial_no', $productSerial['serial_no'])->where('id', '!=', $productSerial['id'])->count())) {
                
                return $productSerial['serial_no'];

            }
        }

        return false;
    }
    */

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

    /*
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
    */

    /*
    protected function getVariationExpectedKey($stockVariations, $stockVariationId)
    {
        foreach ($stockVariations as $stockVariationKey => $stockVariation) {
           if ($stockVariation->id == $stockVariationId) {
               return $stockVariationKey;
           }
        }

        return false;
    }
    */

    protected function updateParentStock(ProductStock $productStockToUpdate, $currentUser, Request $request)
    {
        $productStockToUpdate->stock()->update([
            'has_approval' => true,
            'approver_type' => get_class($currentUser),
            'approver_id' => $currentUser->id,
            'warehouse_id' => $request['warehouse']['id'],
            'updated_at' => $request->updated_at ? $request->updated_at : now(),
        ]);
    }

}
