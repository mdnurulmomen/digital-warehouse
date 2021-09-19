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
use App\Models\MerchantProduct;
use App\Models\ProductDelivery;

class AnalyticsController extends Controller
{
    public function __construct()
    {
        if (! \Auth::guard('admin')->check()) {
            
            $this->middleware("permission:view-general-dashboard-one")->only(['getGeneralDashboardOneData']);
            $this->middleware("permission:view-general-dashboard-two")->only(['getGeneralDashboardTwoData']);

        }
    }

    public function getGeneralDashboardOneData()
    {
        $owners = $this->getOwnerStatusChartData();
        $managers = $this->getManagerStatusChartData();
        $merchants = $this->getMerchantStatusChartData();
    	$warehouses = $this->getWarehouseStatusChartData();
        
    	$numberPendingDispatches = Dispatch::where('has_approval', 0)->count();
    	$numberPendingRequistiions = Requisition::where('status', 0)->count();
        $numberPendingProductStocks = ProductStock::whereHas('stock', function ($query) {
            $query->where('has_approval', 0);
        })->count();

        $limitedStockProducts = MerchantProduct::with(['merchant', 'latestStock'])
        ->doesntHave('stocks')
        ->orWhere(
            ProductStock::select('available_quantity')
                ->whereColumn('product_stocks.merchant_product_id', 'merchant_products.id')
                ->latest()
                ->take(1), '<=', 'merchant_products.warning_quantity'
        )
        ->get();

        $numberUnreceivedDispatches = ProductDelivery::where('receiving_confirmation', false)->count() + ProductReturn::where('receiving_confirmation', false)->count();

        $numberCancelledRequisitions = Requisition::where('status', -1)->count() + Dispatch::where('has_approval', -1)->count();

        // // stock in
        // $stockIns = $this->getRecentStockInChartData();

        // // stock out
        // $stockOuts = $this->getRecentStockOutChartData();

        // // warehouses
        // $warehouseAnalyticDatas = $this->getWarehouseUnusedContainerChartData();

    	return response()->json([

    		// 'stockIns' => $stockIns,
      //       'stockOuts' => $stockOuts,
            // 'warehouses' => $warehouseAnalyticDatas,
            'owners' => $owners, 
            'managers' => $managers, 
            'merchants' => $merchants, 
            'warehouses' => $warehouses, 
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
        $stockIns = $this->getRecentStockInChartData();

        // stock out
        $stockOuts = $this->getRecentStockOutChartData();

        // warehouses
        $warehouseAnalyticDatas = $this->getWarehouseUnusedContainerChartData();

        return response()->json([

            'stockIns' => $stockIns,
            'stockOuts' => $stockOuts,
            'warehouses' => $warehouseAnalyticDatas,
            
        ]);

    }

    protected function getRecentStockInChartData()
    {
        $stockIns = [
        
            'labels' => [
                // 'ProductName',
            ],

            'datasets' => [

                0 => [

                    "label" => "Last week stock in's",

                    "data" => [ 
                        // 300, 
                    ],

                    "backgroundColor" => [
                      // 'red',
                    ],
                    
                    "hoverOffset" => 10,
                    "borderWidth" => 1,

                ],

            ]
        ];

        $stockedProducts = Product::whereHas('stocks', function ($query) {
            $query->where('created_at', '>=', now()->subDays(7));
        })->get();

        foreach ($stockedProducts as $product) {
            
            $stockIns['labels'][] = ucwords($product->name);
            $stockIns['datasets'][0]['data'][] = $product->stocks->sum('stock_quantity');
            $stockIns['datasets'][0]['backgroundColor'][] = sprintf('#%06X', mt_rand(0, 0xFFFFFF));

        }

        return $stockIns;
    }

    protected function getRecentStockOutChartData()
    {
        $stockOuts = [
        
            'labels' => [
                // 'ProductName',
            ],

            'datasets' => [

                0 => [

                    "label" => "Last week stock out's",

                    "data" => [ 
                        // 300, 
                    ],

                    "backgroundColor" => [
                      // 'red',
                    ],
                    
                    "hoverOffset" => 10,
                    "borderWidth" => 1,

                ],

            ]
        ];

        $dispatchedRequisitions = Requisition::where('status', 1)->where('created_at', '>=', now()->subDays(7))->get();

        foreach ($dispatchedRequisitions as $dispatchedRequisition) {
        
            
            foreach ($dispatchedRequisition->products as $requiredProduct) {
                
                if (! in_array(ucwords($requiredProduct->product->name), $stockOuts['labels'])) {
                    
                    $stockOuts['labels'][] = ucwords($requiredProduct->product->name);

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

                }

            
            }

        }

        return $stockOuts;
    }

    protected function getWarehouseUnusedContainerChartData()
    {
        $warehouses = Warehouse::where('active', true)->whereHas('containerStatuses', function ($query) {
            $query->where('engaged', 0.0);
        })->get();

        $warehouseAnalyticDatas = [];

        foreach ($warehouses as $warehouseIndex => $warehouse) {
            
            $warehouseAnalyticDatas[$warehouseIndex] = [
            
                'id' => $warehouse->id,

                'name' => ucwords($warehouse->name),

                'labels' => [
                    // 'Container Names',
                ],

                'datasets' => [

                    0 => [

                        "label" => "$warehouse->name Unused Spaces (%)",

                        "data" => [ 
                            // Unused Percentage, 
                        ],

                        "backgroundColor" => [
                          // 'red',
                        ],
                        
                        "hoverOffset" => 10

                    ],

                ]
            ];

            foreach ($warehouse->containers as $warehouseContainerIndex => $warehouseContainer) {
                
                // container types
                $warehouseAnalyticDatas[$warehouseIndex]['labels'][] = ucwords($warehouseContainer->container->name);
                $warehouseAnalyticDatas[$warehouseIndex]['datasets'][0]['data'][] = ( $warehouseContainer->containerStatuses()->where('engaged', 0.0)->count() / $warehouseContainer->quantity ) * 100;
                $warehouseAnalyticDatas[$warehouseIndex]['datasets'][0]['backgroundColor'][] = sprintf('#%06X', mt_rand(0, 0xFFFFFF));

            }

        }

        return $warehouseAnalyticDatas;
    }

    protected function getOwnerStatusChartData()
    {
        $owners = [
        
            'labels' => [
                'Active', 
                'Pending', 
                'Trashed', 
            ],

            'datasets' => [

                0 => [

                    "label" => "Warehouse-Owner Status",

                    "data" => [ 
                        WarehouseOwner::where('active', true)->count(), 
                        WarehouseOwner::where('active', false)->count(), 
                        WarehouseOwner::onlyTrashed()->count(), 
                    ],

                    "backgroundColor" => [
                        sprintf('#%06X', mt_rand(0, 0xFFFFFF)), 
                        sprintf('#%06X', mt_rand(0, 0xFFFFFF)), 
                        sprintf('#%06X', mt_rand(0, 0xFFFFFF)), 
                    ],
                    
                    "hoverOffset" => 10

                ],

            ]
        ];

        return $owners;
    }

    protected function getManagerStatusChartData()
    {
        $managers = [
        
            'labels' => [
                'Active', 
                'Pending', 
                'Trashed', 
            ],

            'datasets' => [

                0 => [

                    "label" => "Manager Status",

                    "data" => [ 
                        Manager::where('active', true)->count(), 
                        Manager::where('active', false)->count(), 
                        Manager::onlyTrashed()->count(), 
                    ],

                    "backgroundColor" => [
                        sprintf('#%06X', mt_rand(0, 0xFFFFFF)), 
                        sprintf('#%06X', mt_rand(0, 0xFFFFFF)), 
                        sprintf('#%06X', mt_rand(0, 0xFFFFFF)), 
                    ],
                    
                    "hoverOffset" => 10

                ],

            ]
        ];

        return $managers;
    }

    protected function getMerchantStatusChartData()
    {
        $merchants = [
        
            'labels' => [
                'Active', 
                'Pending', 
                'Trashed', 
            ],

            'datasets' => [

                0 => [

                    "label" => "Merchant Status",

                    "data" => [ 
                        Merchant::where('active', true)->count(), 
                        Merchant::where('active', false)->count(), 
                        Merchant::onlyTrashed()->count(), 
                    ],

                    "backgroundColor" => [
                        sprintf('#%06X', mt_rand(0, 0xFFFFFF)), 
                        sprintf('#%06X', mt_rand(0, 0xFFFFFF)), 
                        sprintf('#%06X', mt_rand(0, 0xFFFFFF)), 
                    ],
                    
                    "hoverOffset" => 10

                ],

            ]
        ];

        return $merchants;
    }

    protected function getWarehouseStatusChartData()
    {
        $warehouses = [
        
            'labels' => [
                'Active', 
                'Pending', 
                'Trashed', 
            ],

            'datasets' => [

                0 => [

                    "label" => "Warehouse Status",

                    "data" => [ 
                        Warehouse::where('active', true)->count(), 
                        Warehouse::where('active', false)->count(), 
                        Warehouse::onlyTrashed()->count(), 
                    ],

                    "backgroundColor" => [
                        sprintf('#%06X', mt_rand(0, 0xFFFFFF)), 
                        sprintf('#%06X', mt_rand(0, 0xFFFFFF)), 
                        sprintf('#%06X', mt_rand(0, 0xFFFFFF)), 
                    ],
                    
                    "hoverOffset" => 10

                ],

            ]
        ];

        return $warehouses;
    }
}
