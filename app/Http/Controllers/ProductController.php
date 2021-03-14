<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductStock;
use Illuminate\Http\Request;
use App\Models\ProductCategory;
use App\Http\Resources\Web\ProductCollection;
use App\Http\Resources\Web\ProductStockCollection;

class ProductController extends Controller
{
    // Variations
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

        $newAsset->name = $request->name;
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

        $assetToUpdate->name = $request->name;
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
            // 'initial_quantity' => 'required|numeric|min:1',
            // 'available_quantity' => 'required|numeric|min:0',
            'quantity_type' => 'nullable|string|max:100',
            'has_variations' => 'nullable|boolean',
            // 'variation_type_id' => 'required_if:has_variations,1',
            'variations' => 'required_if:has_variations,1|array',
            'variations.*.variation' => 'required_if:has_variations,1',
            // 'variations.*.initial_quantity' => 'required_if:has_variations,1',
            'variations.*.price' => 'required_if:has_variations,1',

            // 'space_type' => 'required|in:containers,shelves,units',
            // 'containers' => 'required_if:space_type,containers',
            // 'container' => 'required_if:space_type,shelves|required_if:space_type,units',
            // 'container.shelves' => 'required_if:space_type,shelves|array',
            // 'container.shelf' => 'required_if:space_type,units',
            // 'container.shelf.units' => 'required_if:space_type,units|array',
        ]);

        $newProduct = new Product();

        $newProduct->name = $request->name;
        $newProduct->description = $request->description;
        $newProduct->sku = $request->sku ?? $this->generateProductSKU($request);
        $newProduct->price = $request->price ?? 0;
        // $newProduct->initial_quantity = $request->initial_quantity;
        // $newProduct->available_quantity = $request->initial_quantity;
        $newProduct->quantity_type = strtolower($request->quantity_type) ?? ApplicationSetting::first()->default_measure_unit ?? 'kg';
        $newProduct->has_variations = $request->has_variations ?? false;
        $newProduct->product_category_id = $request->product_category_id;
        $newProduct->merchant_id = $request->merchant_id;

        $newProduct->save();

        if ($newProduct->has_variations && !empty($request->variations)) {

            $newProduct->product_variations = $request->variations;

        }

        // $newProduct->product_address = json_decode(json_encode($request->addresses));

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
            // 'initial_quantity' => 'required|numeric|min:1',
            // 'available_quantity' => 'required|numeric|min:0',
            'quantity_type' => 'nullable|string|max:100',
            'has_variations' => 'nullable|boolean',
            // 'variation_type_id' => 'required_if:has_variations,1',
            'variations' => 'required_if:has_variations,1|array',
            'variations.*.variation' => 'required_if:has_variations,1',
            // 'variations.*.initial_quantity' => 'required_if:has_variations,1',
            'variations.*.price' => 'required_if:has_variations,1',

            // 'space_type' => 'required|in:containers,shelves,units',
            // 'containers' => 'required_if:space_type,containers',
            // 'container' => 'required_if:space_type,shelves|required_if:space_type,units',
            // 'container.shelves' => 'required_if:space_type,shelves|array',
            // 'container.shelf' => 'required_if:space_type,units',
            // 'container.shelf.units' => 'required_if:space_type,units|array',
        ]);

        $productToUpdate->name = $request->name;
        $productToUpdate->description = $request->description;
        $productToUpdate->sku = $request->sku ?? $this->generateProductSKU($request);
        $productToUpdate->price = $request->price ?? 0;
        // $productToUpdate->initial_quantity = $request->initial_quantity;
        // $productToUpdate->available_quantity = $request->initial_quantity;
        $productToUpdate->quantity_type = strtolower($request->quantity_type) ?? ApplicationSetting::first()->default_measure_unit ?? 'kg';
        
        if (!$productToUpdate->product_immutability) {
            
            $productToUpdate->has_variations = $request->has_variations ?? false;
            
        }
        
        $productToUpdate->product_category_id = $request->product_category_id ?? NULL;
        $productToUpdate->merchant_id = $request->merchant_id;

        $productToUpdate->save();

        if ($productToUpdate->has_variations && !empty($request->variations)) {

            $productToUpdate->product_variations = json_decode(json_encode($request->variations));

        }

        // $productToUpdate->product_address = json_decode(json_encode($request->addresses));

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
                        // ->orWhere('initial_quantity', 'like', "%$search%")
                        // ->orWhere('available_quantity', 'like', "%$search%")
                        ->orWhere('quantity_type', 'like', "%$search%")
                        ->orWhereHas('category', function ($q) use ($search) {
                            $q->where('name', 'like', "%$search%");
                        });

        return response()->json([
            'all' => new ProductCollection($query->paginate($perPage)),  
        ], 200);
    }

    public function showProductAllStocks($product, $perPage)
    {
        return new ProductStockCollection(ProductStock::with(['addresses', 'variations'])->where('product_id', $product)->paginate($perPage));
    }

    public function storeProductStock(Request $request, $perPage)
    {
        $request->validate([
            'stock_quantity' => 'required|numeric|min:1',
            'product_id' => 'required|numeric|exists:products,id',
        ]);

        $product = Product::find($request->product_id);
        $lastAvailableQuantity = $product->latestStock->available_quantity ?? 0;

        $currentUser = \Auth::guard('admin')->user() ?? Auth::guard('manager')->user();

        $newStock = $product->stocks()->create([
            'stock_quantity' => $request->stock_quantity,
            'available_quantity' => $lastAvailableQuantity + $request->stock_quantity,
            'has_variations' => $product->has_variations,
            'user_type' => class_basename($currentUser),
            'user_id' => $currentUser->id,
        ]);

        if ($newStock->has_variations && !empty($request->variations)) {
            
            $newStock->stock_variations = json_decode(json_encode($request->variations));

        }

        $newStock->setStockAddresses(json_decode(json_encode($request->addresses)), $product->id);

        return $this->showProductAllStocks($request->product_id, $perPage);
    }

    public function updateProductStock(Request $request, $stock, $perPage)
    {
        $request->validate([
            'stock_quantity' => 'required|numeric|min:1',
            'product_id' => 'required|numeric|exists:products,id',
        ]);

        // $product = Product::find($request->product_id);
        $currentUser = \Auth::guard('admin')->user() ?? \Auth::guard('manager')->user();

        $stockToUpdate = ProductStock::findOrFail($stock);

        if (!empty($currentUser) && !empty($stockToUpdate)) {
            
            if ($request->stock_quantity > $stockToUpdate->stock_quantity) {

                $difference = $request->stock_quantity - $stockToUpdate->stock_quantity;

                $stockToUpdate->update([
                    'stock_quantity' => $request->stock_quantity,
                    'available_quantity' => ($stockToUpdate->available_quantity + $difference),
                    'user_type' => class_basename($currentUser),
                    'user_id' => $currentUser->id,
                ]);

                $this->increaseStockAvailableQuantity($stockToUpdate, $difference);

            }
            else if ($request->stock_quantity < $stockToUpdate->stock_quantity) {

                $difference = $stockToUpdate->stock_quantity - $request->stock_quantity;

                $stockToUpdate->update([
                    'stock_quantity' => $request->stock_quantity,
                    'available_quantity' => ($stockToUpdate->available_quantity - $difference),
                    'user_type' => class_basename($currentUser),
                    'user_id' => $currentUser->id,
                ]);

                $this->decreaseStockAvailableQuantity($stockToUpdate, $difference);

            }

            if ($stockToUpdate->has_variations && !empty($request->variations)) {
                
                $stockToUpdate->stock_variations = json_decode(json_encode($request->variations));

            }

            $stockToUpdate->setStockAddresses(json_decode(json_encode($request->addresses)), $request->product_id);

        }

        return $this->showProductAllStocks($request->product_id, $perPage);
    }

    public function deleteProductStock($stock, $perPage)
    {
        $stockToDelete = ProductStock::findOrFail($stock);

        // $currentUser = \Auth::guard('admin')->user() ?? \Auth::guard('manager')->user();

        $productId = $stockToDelete->product_id;

        $this->decreaseStockAvailableQuantity($stockToDelete, $stockToDelete->stock_quantity);
            
        $stockToDelete->deleteStockVariations();

        $stockToDelete->deleteOldAddresses();

        $stockToDelete->delete();

        return $this->showProductAllStocks($productId, $perPage);
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
}
