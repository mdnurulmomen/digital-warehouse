<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Merchant;
use Illuminate\Http\Request;
use App\Imports\VendorsImport;
use App\Imports\ProductsImport;
use App\Imports\LocationsImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\MerchantProductsImport;
use App\Imports\ProductCategoriesImport;
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

        // Merchant-Product
        // $this->middleware("permission:view-product-index")->only(['exportProducts']);
        $this->middleware("permission:create-product-asset")->only(['importProductCategories']);

        // Vendor
        // $this->middleware("permission:view-product-index")->only(['exportProducts']);
        $this->middleware("permission:create-product-asset")->only(['importVendors']);

        // Vendor
        // $this->middleware("permission:view-product-index")->only(['exportProducts']);
        $this->middleware("permission:create-product-asset")->only(['importLocations']);
    }

    public function importProductCategories(Request $request) 
    {
        $request->validate([
            'perPage' => 'required|integer',
            'excelFileToImport' => 'required|file|mimes:xls,xlsx'
        ]);

        Excel::import(new ProductCategoriesImport, $request->file('excelFileToImport'));

        return redirect()->route('admin.product-categories.index', [$request->perPage]);
    }

    public function importProducts(Request $request) 
    {
        $request->validate([
            'perPage' => 'required|integer',
            'excelFileToImport' => 'required|file|mimes:xls,xlsx'
        ]);

        Excel::import(new ProductsImport, $request->file('excelFileToImport'));

        return redirect()->route('admin.products.index', [$request->perPage]);
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

        return redirect()->route('admin.merchant-products.index', [$request->merchant_id, $request->perPage]);
    }

    public function importVendors(Request $request) 
    {
        $request->validate([
            'perPage' => 'required|integer',
            'excelFileToImport' => 'required|file|mimes:xls,xlsx'
        ]);

        Excel::import(new VendorsImport, $request->file('excelFileToImport'));

        return redirect()->route('admin.vendors.index', [$request->perPage]);
    }

    public function importLocations(Request $request) 
    {
        $request->validate([
            'perPage' => 'required|integer',
            'excelFileToImport' => 'required|file|mimes:xls,xlsx'
        ]);

        Excel::import(new LocationsImport, $request->file('excelFileToImport'));

        return redirect()->route('admin.locations.index', [$request->perPage]);
    }
}
