<?php

namespace App\Http\Controllers;

use App\Models\Manager;
use App\Models\Product;
use App\Models\Merchant;
use App\Models\Dispatch;
use App\Models\Warehouse;
use App\Models\Requisition;
use App\Models\ProductStock;
use Illuminate\Http\Request;
use App\Models\ProductReturn;
use App\Models\WarehouseOwner;
use App\Models\ProductDelivery;

class AnalyticsController extends Controller
{
    public function __construct()
    {
        $this->middleware("permission:view-general-dashboard-one")->only(['getGeneralDashboardOneData']);
        $this->middleware("permission:view-general-dashboard-two")->only(['getGeneralDashboardTwoData']);
    }

    public function getGeneralDashboardOneData()
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

        $numberUnreceivedDispatches = ProductDelivery::where('receiving_confirmation', false)->count() + ProductReturn::where('receiving_confirmation', false)->count();

        $numberCancelledRequisitions = Requisition::where('status', -1)->count() + Dispatch::where('has_approval', -1)->count();

    	return response()->json([

    		'numberPendingOwner' => $numberPendingOwner, 
    		'numberPendingManagers' => $numberPendingManagers,
    		'numberPendingMerchants' => $numberPendingMerchants,
    		'numberPendingWarehouses' => $numberPendingWarehouses,
    		'numberPendingDispatches' => $numberPendingDispatches,
    		'numberPendingRequistiions' => $numberPendingRequistiions,
            'numberPendingProductStocks' => $numberPendingProductStocks,
            'limitedStockProducts' => $limitedStockProducts,
            'numberUnreceivedDispatches' => $numberUnreceivedDispatches,
    		'numberCancelledRequisitions' => $numberCancelledRequisitions,
            
    	], 200);
    }

    public function getGeneralDashboardTwoData()
    {
        // stock in
        $stockIns = [
        
            'labels' => [
                // 'ProductName',
            ],

            'datasets' => [

                0 => [

                    "data" => [ 
                        // 300, 
                    ],

                    "backgroundColor" => [
                      // 'red',
                    ],
                    
                    "hoverOffset" => 100

                ],

            ]
        ];

        $stockedProducts = Product::whereHas('stocks', function ($query) {
            $query->where('created_at', '>=', now()->subDays(7));
        })->get();

        foreach ($stockedProducts as $product) {
            
            $stockIns['labels'][] = $product->name;
            $stockIns['datasets'][0]['data'][] = $product->stocks->sum('stock_quantity');
            $stockIns['datasets'][0]['backgroundColor'][] = sprintf('#%06X', mt_rand(0, 0xFFFFFF));

        }

        $stockIns['datasets'][0]['hoverOffset'] = 10;


        // stock out
        $stockOuts = [
        
            'labels' => [
                // 'ProductName',
            ],

            'datasets' => [

                0 => [

                    "data" => [ 
                        // 300, 
                    ],

                    "backgroundColor" => [
                      // 'red',
                    ],
                    
                    "hoverOffset" => 100

                ],

            ]
        ];

        $dispatchedRequisitions = Requisition::where('status', 1)->where('created_at', '>=', now()->subDays(7))->get();

        foreach ($dispatchedRequisitions as $dispatchedRequisition) {
        
            
            foreach ($dispatchedRequisition->products as $requiredProduct) {
                
                if (! in_array($requiredProduct->product->name, $stockOuts['labels'])) {
                    
                    $stockOuts['labels'][] = $requiredProduct->product->name;

                    $stockOuts['datasets'][0]['data'][] = Requisition::with(['products' => function ($query) use ($requiredProduct) {
                                                            $query->where('product_id', $requiredProduct->product_id);
                                                        }])
                                                        ->where('status', 1)->where('created_at', '>=', now()->subDays(7))
                                                        ->whereHas('products', function ($query) use ($requiredProduct) {
                                                            $query->where('product_id', $requiredProduct->product_id);
                                                        })
                                                        ->get()->pluck('products')->collapse()
                                                        ->sum('quantity');

                    $stockOuts['datasets'][0]['backgroundColor'][] = sprintf('#%06X', mt_rand(0, 0xFFFFFF));
                
                    $stockOuts['datasets'][0]['hoverOffset'] = 10;

                }

            
            }

        }

        return response()->json([

            'stockIns' => $stockIns,
            'stockOuts' => $stockOuts,
            
        ]);

    }
}
