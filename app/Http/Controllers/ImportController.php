<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Imports\ProductsImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Resources\Web\ProductCollection;

class ImportController extends Controller
{
    public function __construct()
    {
    	// Product
        // $this->middleware("permission:view-product-index")->only(['exportProducts']);
        $this->middleware("permission:create-product")->only(['importProducts']);
    }

    public function importProducts(Request $request, $perPage) 
    {
        $request->validate([
            'excelFileToImport' => 'required|file|mimes:xls,xlsx'
        ]);

        Excel::import(new ProductsImport, $request->file('excelFileToImport'));

        return response()->json([

            'retail' => new ProductCollection(Product::withTrashed()->where('product_category_id', '!=', 0)->withCount('merchants')->paginate($perPage)),
            'bulk' => new ProductCollection(Product::withTrashed()->whereNull('product_category_id')->orWhere('product_category_id', 0)->withCount('merchants')->paginate($perPage)),

        ], 200);
    }
}
