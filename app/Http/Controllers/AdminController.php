<?php

namespace App\Http\Controllers;

use App\Models\Manager;
use App\Models\Product;
use App\Models\Merchant;
use App\Models\Warehouse;
use App\Models\Dispatch;
use App\Models\Requisition;
use App\Models\ProductStock;
use Illuminate\Http\Request;
use App\Models\WarehouseOwner;

class AdminController extends Controller
{
    public function getDashboardOneData()
    {
    	$numberPendingOwner = WarehouseOwner::where('active', 0)->count();
    	$numberPendingManagers = Manager::where('active', 0)->count();
    	$numberPendingMerchants = Merchant::where('active', 0)->count();
    	$numberPendingWarehouses = Warehouse::where('active', 0)->count();
    	$numberPendingDispatches = Dispatch::where('has_approval', 0)->count();
    	$numberPendingRequistiions = Requisition::where('status', 0)->count();
    	$numberPendingProductStocks = ProductStock::where('has_approval', 0)->count();
        $limitedStockProducts = Product::with(['merchant', 'latestStock'])
        ->doesntHave('stocks')
        ->orWhere(
            ProductStock::select('available_quantity')
                ->whereColumn('product_stocks.product_id', 'products.id')
                ->latest()
                ->take(1), '<', 50
        )
        ->get();

    	return response()->json([

    		'numberPendingOwner' => $numberPendingOwner, 
    		'numberPendingManagers' => $numberPendingManagers,
    		'numberPendingMerchants' => $numberPendingMerchants,
    		'numberPendingWarehouses' => $numberPendingWarehouses,
    		'numberPendingDispatches' => $numberPendingDispatches,
    		'numberPendingRequistiions' => $numberPendingRequistiions,
            'numberPendingProductStocks' => $numberPendingProductStocks,
    		'limitedStockProducts' => $limitedStockProducts,
            
    	], 200);
    }
}
