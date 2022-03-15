<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Merchant;
use Illuminate\Http\Request;
use App\Imports\ProductsImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\MerchantProductsImport;
use App\Http\Resources\Web\ProductCollection;

class ImportController extends Controller
{
    public function __construct()
    {
    	// Product
        // $this->middleware("permission:view-product-index")->only(['exportProducts']);
        $this->middleware("permission:create-product")->only(['importProducts']);

        // Merchant-Product
        // $this->middleware("permission:view-product-index")->only(['exportProducts']);
        $this->middleware("permission:create-merchant-product")->only(['importMerchantProducts']);
    }

    public function importProducts(Request $request) 
    {
        $request->validate([
            'perPage' => 'required|integer',
            'excelFileToImport' => 'required|file|mimes:xls,xlsx'
        ]);

        Excel::import(new ProductsImport, $request->file('excelFileToImport'));

        return redirect()->route('admin.products.show', [$request->perPage]);
    }

    public function importMerchantProducts(Request $request) 
    {
        $request->validate([
            'merchant_id' => 'required|integer|exists:merchants,id',
            'perPage' => 'required|integer',
            'excelFileToImport' => 'required|file|mimes:xls,xlsx'
        ]);

        $merchant = Merchant::findOrFail($request->merchant_id);

        Excel::import(new MerchantProductsImport($merchant), $request->file('excelFileToImport'));

        return redirect()->route('admin.merchant-products.show', [$request->merchant_id, $request->perPage]);
    }
}
