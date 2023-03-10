<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Manager;
use App\Models\Product;
use App\Models\Merchant;
use App\Models\Warehouse;
use App\Models\Requisition;
use App\Models\MerchantRent;
use App\Models\ProductStock;
use Illuminate\Http\Request;
use App\Models\ProductSerial;
use App\Models\MerchantPayment;
use App\Models\MerchantProduct;
use Illuminate\Validation\Rule;
use App\Models\RequisitionAgent;
use App\Models\MerchantSpaceDeal;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File; 
use App\Jobs\BroadcastNewRequisition;
use App\Models\ProductVariationSerial;
use App\Models\WarehouseContainerStatus;
use App\Models\WarehouseContainerShelfStatus;
use App\Http\Resources\Web\MyProductResource;
use App\Http\Resources\Web\MerchantCollection;
use App\Http\Resources\Web\MerchantRentCollection;
use App\Models\WarehouseContainerShelfUnitStatus;
use App\Jobs\BroadcastProductReceiveConfirmation;
use App\Http\Resources\Web\ProductStockCollection;
use App\Http\Resources\Web\MyRequisitionCollection;
use App\Http\Resources\Web\MerchantProductResource;
use App\Http\Resources\Web\MerchantProductCollection;
use App\Http\Resources\Web\MerchantSpaceDealCollection;

class MerchantController extends Controller
{
    public function __construct()
    {
        // Merchants
        $this->middleware("permission:view-merchant-index")->only(['showAllMerchants', 'searchAllMerchants']);
        $this->middleware("permission:create-merchant")->only('storeNewMerchant');
        $this->middleware("permission:update-merchant")->only('updateMerchant');
        $this->middleware("permission:delete-merchant")->only(['deleteMerchant', 'restoreMerchant']);

        // Merchant-Products
        $this->middleware("permission:view-merchant-product-index")->only(['showMerchantAllProducts', 'showMerchantAvailableProducts', 'searchMerchantAllProducts']);
        $this->middleware("permission:create-merchant-product")->only('storeMerchantNewProduct');
        $this->middleware("permission:update-merchant-product")->only('updateMerchantProduct');
        $this->middleware("permission:delete-merchant-product")->only('deleteMerchantProduct'); 

        // Merchant-Agents
        $this->middleware("permission:view-merchant-index")->only(['showMerchantAllAgents']); 
    }

    public function showAllMerchants($perPage = false)
    {
    	if ($perPage) {
            
            return response()->json([

        		'approved' => new MerchantCollection(Merchant::withCount('spaceDeals')->withCount('products')->where('active', 1)->latest()->paginate($perPage)),

                'pending' => new MerchantCollection(Merchant::withCount('spaceDeals')->withCount('products')->where('active', 0)->latest()->paginate($perPage)),

        		'trashed' => new MerchantCollection(Merchant::withCount('spaceDeals')->withCount('products')->onlyTrashed()->latest()->paginate($perPage)),

        	], 200);

        }

        return Merchant::latest()->get();
    }

    public function storeNewMerchant(Request $request, $perPage)
    {
    	$request->validate([
            'first_name' => 'nullable|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'user_name' => 'required|string|max:100|unique:merchants,user_name',
            'email' => 'required|string|max:100|unique:merchants,email',
            'mobile' => 'required|string|max:50|unique:merchants,mobile',
            'password' => 'required|string|max:255|confirmed',
            'active' => 'required|boolean',
            'support_deal.e_commerce_fulfillment_support' => 'nullable|boolean',
            'support_deal.e_commerce_fulfillment_charge' => 'nullable|numeric',
            'support_deal.pos_support' => 'nullable|boolean',
            'support_deal.pos_support_charge' => 'nullable|numeric',
            'support_deal.purchase_support' => 'nullable|boolean',
            'support_deal.purchase_support_charge' => 'nullable|numeric',
            'support_deal.sale_percentage' => 'nullable|numeric|min:0|max:100',
            'support_deal.rent_period_id' => 'required|numeric|exists:rent_periods,id',
            'support_deal.number_outlets' => 'required_if:pos_support,1|nullable|numeric'
        ]);

        $newUser = new Merchant();

        $newUser->first_name = strtolower($request->first_name);
        $newUser->last_name = strtolower($request->last_name);
        $newUser->user_name = strtolower($request->user_name);
        $newUser->email = strtolower($request->email);
        $newUser->mobile = $request->mobile;
        $newUser->password = Hash::make($request->password);
        $newUser->active = $request->active;

        $newUser->save();

        // support deal
        $newUser->supportDeal()->create([
            'sale_percentage' => $request->support_deal['sale_percentage'] ?? 0,
            'e_commerce_fulfillment_support' => $request->support_deal['e_commerce_fulfillment_support'] ?? 0,
            'e_commerce_fulfillment_charge' => $request->support_deal['e_commerce_fulfillment_charge'] ?? 0,
            'purchase_support' => $request->support_deal['purchase_support'] ?? 0,
            'purchase_support_charge' => $request->support_deal['purchase_support_charge'] ?? 0,
            'pos_support' => $request->support_deal['pos_support'] ?? 0,
            'pos_support_charge' => $request->support_deal['pos_support_charge'] ?? 0,
            'number_outlets' => $request->support_deal['number_outlets'] ?? 0,
            'rent_period_id' => $request->support_deal['rent_period_id']
        ]);

        // profile preview
        if (array_key_exists('preview', $request->profile_preview)) {
            $newUser->profile_preview = $request->profile_preview['preview'];
            $newUser->save();
        }

        // $newUser->user_permissions = json_decode(json_encode($request->permissions));
        // $newUser->user_roles = json_decode(json_encode($request->roles));

        return $this->showAllMerchants($perPage);
    }

    public function updateMerchant(Request $request, $owner, $perPage)
    {
    	$userToUpdate = Merchant::findOrFail($owner);

        $request->validate([
            'first_name' => 'nullable|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'user_name' => 'required|string|max:100|unique:merchants,user_name,'.$userToUpdate->id,
            'email' => 'required|string|max:100|unique:merchants,email,'.$userToUpdate->id,
            'mobile' => 'required|string|max:50|unique:merchants,mobile,'.$userToUpdate->id,
            'password' => 'nullable|string|max:255|confirmed',
            'active' => 'required|boolean',
            'support_deal.e_commerce_fulfillment_support' => 'nullable|boolean',
            'support_deal.e_commerce_fulfillment_charge' => 'nullable|numeric',
            'support_deal.pos_support' => 'nullable|boolean',
            'support_deal.pos_support_charge' => 'nullable|numeric',
            'support_deal.purchase_support' => 'nullable|boolean',
            'support_deal.purchase_support_charge' => 'nullable|numeric',
            'support_deal.sale_percentage' => 'nullable|numeric|min:0|max:100',
            'support_deal.rent_period_id' => 'required|numeric|exists:rent_periods,id',
            'support_deal.number_outlets' => 'required_if:pos_support,1|nullable|numeric'
        ]);

        $userToUpdate->first_name = strtolower($request->first_name);
        $userToUpdate->last_name = strtolower($request->last_name);
        $userToUpdate->user_name = strtolower($request->user_name);
        $userToUpdate->email = strtolower($request->email);
        $userToUpdate->mobile = $request->mobile;
        $userToUpdate->profile_preview = $request->profile_preview['preview'] ?? NULL;
        $userToUpdate->active = $request->active;

        if ($request->password) {
            $userToUpdate->password = Hash::make($request->password);
        }

        $userToUpdate->save();

        // support deal
        $merchantSupportDeal = $userToUpdate->supportDeal;
        $supportDealLastRent = $merchantSupportDeal->rents()->latest()->first();
        $currentDate = date('Y-m-d');

        if ($supportDealLastRent && $supportDealLastRent->date_to >= $currentDate) {
            
            $merchantSupportDeal->update([
                'sale_percentage' => $request->support_deal['sale_percentage'] ?? 0,
                'e_commerce_fulfillment_charge' => $merchantSupportDeal->e_commerce_fulfillment_support ? $request->support_deal['e_commerce_fulfillment_charge'] : 0,
                'purchase_support_charge' => $merchantSupportDeal->purchase_support ? $request->support_deal['purchase_support_charge'] : 0,
                'pos_support_charge' => $merchantSupportDeal->pos_support ? $request->support_deal['pos_support_charge'] : 0,
                'number_outlets' => $merchantSupportDeal->pos_support ? $request->support_deal['number_outlets'] : 0,
                'rent_period_id' => $request->support_deal['rent_period_id']
            ]);

        }
        else {

            $merchantSupportDeal->update([
                'sale_percentage' => $request->support_deal['sale_percentage'] ?? 0,
                'e_commerce_fulfillment_support' => $request->support_deal['e_commerce_fulfillment_support'],
                'e_commerce_fulfillment_charge' => $request->support_deal['e_commerce_fulfillment_support'] ? $request->support_deal['e_commerce_fulfillment_charge'] : 0,
                'purchase_support' => $request->support_deal['purchase_support'],
                'purchase_support_charge' => $request->support_deal['purchase_support'] ? $request->support_deal['purchase_support_charge'] : 0,
                'pos_support' => $request->support_deal['pos_support'],
                'pos_support_charge' => $request->support_deal['pos_support'] ? $request->support_deal['pos_support_charge'] : 0,
                'number_outlets' => $request->support_deal['pos_support'] ? $request->support_deal['number_outlets'] : 0,
                'rent_period_id' => $request->support_deal['rent_period_id']
            ]);

        }

        return $this->showAllMerchants($perPage);
    }

    public function deleteMerchant($merchant, $perPage)
    {
    	$userToDelete = Merchant::findOrFail($merchant);
        
        if ($userToDelete->spaceDeals()->where('active', 1)->count() || $userToDelete->products()->where('available_quantity', '>', 0)->count()) {
           
            return response()->json(['errors'=>["hasDealOrProducts" => "Merchant has active deals or products"]], 422);

        }

        // $userToDelete->permissions()->detach();
        // $userToDelete->roles()->detach();
        
        $userToDelete->delete();

        return $this->showAllMerchants($perPage);
    }

    public function restoreMerchant($owner, $perPage)
    {
    	$userToRestore = Merchant::withTrashed()->findOrFail($owner);
        $userToRestore->restore();

        return $this->showAllMerchants($perPage);
    }

    public function searchAllMerchants($search, $perPage)
    {
        $columnsToSearch = ['first_name', 'last_name', 'user_name', 'mobile', 'email'];

        $query = Merchant::withTrashed();

        foreach($columnsToSearch as $column){
            $query->orWhere($column, 'like', "%$search%");
        }

        return response()->json([
            'all' => $query->paginate($perPage),    
        ], 200);
    }

    // Merchant-Agents (admin.php)
    public function showMerchantAllAgents($merchant, $perPage = false)
    {
        if ($perPage) {
            
        }
        
        return RequisitionAgent::whereHas('requisition', function ($query) use ($merchant) {
            $query->where('merchant_id', $merchant);
        })->latest('id')->get()->unique('name');
    }

    /*
    // Current User
    public function currentMerchant()
    {
        if (\Auth::check()) {
            // The merchant is logged in...
            return response()->json([

                'user' => \Auth::user(),

            ], 200);
        }
    }
    */

    // Merchant-Products (web.php) 
    public function showMyAllProducts($perPage=false)
    {
        $currentMerchant = \Auth::user();
        
        if ($perPage) {
            
            return [

                'retail' => new MerchantProductCollection(MerchantProduct::where('merchant_id', $currentMerchant->id)
                                                        ->whereHas('product', function ($query) {
                                                            $query->where('product_category_id', '>', 0);
                                                        })
                                                        ->with(['serials', 'nonDispatchedRequests', 'dispatchedRequests', 'variations.serials'])
                                                        ->with(['stocks' => function ($query1) {
                                                            $query1->where('available_quantity', '>', 0);
                                                         
                                                        }])
                                                        ->with(['variations.stocks' => function ($query2) {
                                                            $query2->where('available_quantity', '>', 0);
                                                         
                                                        }])
                                                        ->paginate($perPage)),
                
                'bulk' => new MerchantProductCollection(MerchantProduct::where('merchant_id', $currentMerchant->id)
                                                        ->whereHas('product', function ($query) {
                                                            $query->whereNull('product_category_id')
                                                                ->orWhere('product_category_id', 0);
                                                        })
                                                        ->with(['stocks', 'nonDispatchedRequests', 'dispatchedRequests'])
                                                       ->paginate($perPage)),
            ];

        }

        
        // return MyProductResource::collection(
        //     Product::where('merchant_id', $currentMerchant->id)
        //         ->where(
        //             ProductStock::select('available_quantity')
        //                 ->whereColumn('product_stocks.product_id', 'products.id')
        //                 ->latest()
        //                 ->take(1), '>', 0
        //         )
        //         ->get()
        // );
        
       
        /*
        return MyProductResource::collection(
            MerchantProduct::where('merchant_id', $currentMerchant->id)->whereHas('latestStock', function ($query) {
                $query->where('available_quantity', '>', 0);
            })->latest()->get()
        );
        */
       
        return MyProductResource::collection(
            MerchantProduct::where('merchant_id', $currentMerchant->id)->where('available_quantity', '>', 0)->latest()->get()
        );

    }

    public function searchMyAllProducts(Request $request, $perPage)
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

        $currentMerchant = \Auth::user();

        $query = MerchantProduct::where('merchant_id', $currentMerchant->id)
                ->with(['serials', 'stocks', 'nonDispatchedRequests', 'dispatchedRequests', 'variations.stocks', 'variations.serials']);

        if ($request->search) {
            
            $query->where(function ($query1) use ($request) {
                $query1->where('sku', 'like', "%$request->search%")
                ->orWhere('upc', 'like', "%$request->search%")
                ->orWhere('description', 'like', "%$request->search%")
                ->orWhere('warning_quantity', 'like', "%$request->search%")
                ->orWhere('discount', 'like', "%$request->search%")
                ->orWhere('selling_price', 'like', "%$request->search%")
                ->orWhereHas('manufacturer', function ($query2) use ($request) {
                    $query2->where('name', 'like', "%$request->search%");
                })
                ->orWhereHas('product', function ($query3) use ($request) {
                    $query3->where('name', 'like', "%$request->search%")
                        ->orWhere('quantity_type', 'like', "%$request->search%")
                        ->orWhereHas('category', function ($q) use ($request) {
                            $q->where('name', 'like', "%$request->search%");
                        });
                });
            });

        }

        if ($request->dateFrom && $request->dateTo) {
            
            $query->where(function ($query1) use ($request) {
                $query1->whereDate('created_at', '>=', $request->dateFrom)
                ->whereDate('created_at', '<=', $request->dateTo)
                ->orWhereHas('stocks', function ($query2) use ($request) {
                    $query2->whereHas('stock', function ($query3) use ($request) {
                        $query3->whereDate('created_at', '>=', $request->dateFrom)
                        ->whereDate('created_at', '<=', $request->dateTo);
                    });
                });
            })
            ->with(['serials' => function ($query1) use ($request) {
                $query1->whereHas('productStock', function ($query2) use ($request) {
                    $query2->whereHas('stock', function ($query3) use ($request) {
                        $query3->whereDate('created_at', '>=', $request->dateFrom)
                        ->whereDate('created_at', '<=', $request->dateTo);
                    });
                });
            }])
            ->with(['stocks' => function ($query1) use ($request) {
                $query1->whereHas('stock', function ($query2) use ($request) {
                    $query2->whereDate('created_at', '>=', $request->dateFrom)
                    ->whereDate('created_at', '<=', $request->dateTo);
                });
            }])
            ->with(['nonDispatchedRequests' => function ($query1) use ($request) {
                $query1->whereHas('requisition', function ($query2) use ($request) {
                    $query2->whereDate('created_at', '>=', $request->dateFrom)
                    ->whereDate('created_at', '<=', $request->dateTo);
                });
            }])
            ->with(['dispatchedRequests' => function ($query1) use ($request) {
                $query1->whereHas('dispatch', function ($query2) use ($request) {
                    $query2->whereDate('updated_at', '>=', $request->dateFrom)
                    ->whereDate('updated_at', '<=', $request->dateTo);
                });
            }])
            ->with(['variations.serials' => function ($query1) use ($request) {
                $query1->whereHas('variationStock', function ($query2) use ($request) {
                    $query2->whereHas('productStock', function ($query3) use ($request) {
                        $query3->whereHas('stock', function ($query4) use ($request) {
                            $query4->whereDate('created_at', '>=', $request->dateFrom)
                            ->whereDate('created_at', '<=', $request->dateTo);
                        });
                    });
                });
            }])
            ->with(['variations.stocks' => function ($query1) use ($request) {
                $query1->whereHas('productStock', function ($query2) use ($request) {
                    $query2->whereHas('stock', function ($query3) use ($request) {
                        $query3->whereDate('created_at', '>=', $request->dateFrom)
                        ->whereDate('created_at', '<=', $request->dateTo);
                    });
                });
            }]);

        }

        else if ($request->dateFrom && empty($request->dateTo)) {
            
            $query->where(function ($query1) use ($request) {
                $query1->whereDate('created_at', '>=', $request->dateFrom)
                ->orWhereHas('stocks', function ($query2) use ($request) {
                    $query2->whereHas('stock', function ($query3) use ($request) {
                        $query3->whereDate('created_at', '>=', $request->dateFrom);
                    });
                });
            })
            ->with(['serials' => function ($query1) use ($request) {
                $query1->whereHas('productStock', function ($query2) use ($request) {
                    $query2->whereHas('stock', function ($query3) use ($request) {
                        $query3->whereDate('created_at', '>=', $request->dateFrom);
                    });
                });
            }])
            ->with(['stocks' => function ($query1) use ($request) {
                $query1->whereHas('stock', function ($query2) use ($request) {
                    $query2->whereDate('created_at', '>=', $request->dateFrom);
                });
            }])
            ->with(['nonDispatchedRequests' => function ($query1) use ($request) {
                $query1->whereHas('requisition', function ($query2) use ($request) {
                    $query2->whereDate('created_at', '>=', $request->dateFrom);
                });
            }])
            ->with(['dispatchedRequests' => function ($query1) use ($request) {
                $query1->whereHas('dispatch', function ($query2) use ($request) {
                    $query2->whereDate('updated_at', '>=', $request->dateFrom);
                });
            }])
            ->with(['variations.serials' => function ($query1) use ($request) {
                $query1->whereHas('variationStock', function ($query2) use ($request) {
                    $query2->whereHas('productStock', function ($query3) use ($request) {
                        $query3->whereHas('stock', function ($query4) use ($request) {
                            $query4->whereDate('created_at', '>=', $request->dateFrom);
                        });
                    });
                });
            }])
            ->with(['variations.stocks' => function ($query1) use ($request) {
                $query1->whereHas('productStock', function ($query2) use ($request) {
                    $query2->whereHas('stock', function ($query3) use ($request) {
                        $query3->whereDate('created_at', '>=', $request->dateFrom);
                    });
                });
            }]);

        }

        else if (empty($request->dateFrom) && $request->dateTo) {
            
            $query->where(function ($query1) use ($request) {
                $query1->whereDate('created_at', '<=', $request->dateTo)
                ->orWhereHas('stocks', function ($query2) use ($request) {
                    $query2->whereHas('stock', function ($query3) use ($request) {
                        $query3->whereDate('created_at', '<=', $request->dateTo);
                    });
                });
            })
            ->with(['serials' => function ($query1) use ($request) {
                $query1->whereHas('productStock', function ($query2) use ($request) {
                    $query2->whereHas('stock', function ($query3) use ($request) {
                        $query3->whereDate('created_at', '<=', $request->dateTo);
                    });
                });
            }])
            ->with(['stocks' => function ($query1) use ($request) {
                $query1->whereHas('stock', function ($query2) use ($request) {
                    $query2->whereDate('created_at', '<=', $request->dateTo);
                });
            }])
            ->with(['nonDispatchedRequests' => function ($query1) use ($request) {
                $query1->whereHas('requisition', function ($query2) use ($request) {
                    $query2->whereDate('created_at', '<=', $request->dateTo);
                });
            }])
            ->with(['dispatchedRequests' => function ($query1) use ($request) {
                $query1->whereHas('dispatch', function ($query2) use ($request) {
                    $query2->whereDate('updated_at', '<=', $request->dateTo);
                });
            }])
            ->with(['variations.serials' => function ($query1) use ($request) {
                $query1->whereHas('variationStock', function ($query2) use ($request) {
                    $query2->whereHas('productStock', function ($query3) use ($request) {
                        $query3->whereHas('stock', function ($query4) use ($request) {
                            $query4->whereDate('created_at', '<=', $request->dateTo);
                        });
                    });
                });
            }])
            ->with(['variations.stocks' => function ($query1) use ($request) {
                $query1->whereHas('productStock', function ($query2) use ($request) {
                    $query2->whereHas('stock', function ($query3) use ($request) {
                        $query3->whereDate('created_at', '<=', $request->dateTo);
                    });
                });
            }]);

        }

        return [
            'all' => (new MerchantProductCollection($query->paginate($perPage)))->setFromDate($request->dateFrom),  
        ];

    }
    
    // My-Agents (web.php)
    public function showMyAllAgents()
    {
        $currentMerchant = \Auth::user();

        return RequisitionAgent::whereHas('requisition', function ($query) use ($currentMerchant) {
            $query->where('merchant_id', $currentMerchant->id);
        })->latest('id')->get()->unique('name');
    }

    // My-Requisition (web.php)
    public function showMyAllRequisitions($perPage = false)
    {
        $currentMerchant = \Auth::user();
            
        if ($perPage) {

            return [

                'pending' => new MyRequisitionCollection(Requisition::with(['creator', 'updater', 'delivery', 'agent', 'dispatch.delivery', 'dispatch.return', 'products.merchantProduct.product', 'products.merchantProduct', 'products.variations.merchantProductVariation', 'products.variations.merchantProductVariation.productVariation.variation', 'cancellation'])->where('status', 0)->where('merchant_id', $currentMerchant->id)->paginate($perPage)),  

                'dispatched' => new MyRequisitionCollection(Requisition::with(['creator', 'updater', 'delivery', 'agent', 'dispatch.delivery', 'dispatch.return', 'products.merchantProduct.product', 'products.merchantProduct', 'products.variations.merchantProductVariation', 'products.variations.merchantProductVariation.productVariation.variation', 'cancellation'])->where('status', 1)->where('merchant_id', $currentMerchant->id)->paginate($perPage)),  

                'cancelled' => new MyRequisitionCollection(Requisition::with(['creator', 'updater', 'delivery', 'agent', 'dispatch.delivery', 'dispatch.return', 'products.merchantProduct.product', 'products.merchantProduct', 'products.variations.merchantProductVariation', 'products.variations.merchantProductVariation.productVariation.variation', 'cancellation'])->where('status', -1)->where('merchant_id', $currentMerchant->id)->paginate($perPage)),  
            
            ];

        }

        return Requisition::with('products.merchantProduct')->where('merchant_id', $currentMerchant->id)->latest()->get();

    }

    public function makeNewRequisition(Request $request, $perPage)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            
            'products' => 'required|array|min:1',
            'products.*.id' => 'required|numeric|exists:merchant_products,id',
            'products.*.total_quantity' => 'required|numeric|min:1',
            'products.*.packaging_service' => 'boolean',
            // 'products.*.package' => 'required_if:products.*.packaging_service,true',
            'products.*.product' => 'required',
            'products.*.product.has_serials' => 'required|boolean',
            'products.*.product.has_variations' => 'required|boolean',

            'products.*.serials' => 'exclude_if:products.*.product.has_variations,true|required_if:products.*.product.has_serials,true|array',
            'products.*.serials.*' => [
                    Rule::exists('product_serials', 'serial_no')->where(function ($query) {
                        return $query->where('has_requisitions', false)->where('has_dispatched', false);
                    }),
            ],

            'products.*.product.variations' => 'required_if:products.*.product.has_variations,true|array',
            'products.*.product.variations.*.required_serials' => 'required_with:products.*.product.variations.*.required_quantity|array',
            'products.*.product.variations.*.required_serials.*' => [
                    Rule::exists('product_variation_serials', 'serial_no')->where(function ($query) {
                        return $query->where('has_requisitions', false)->where('has_dispatched', false);
                    }),
            ],

            'delivery_service' => 'boolean',

            'agent' => 'required_if:delivery_service,false,',
            'agent.name' => 'required_if:delivery_service,false,',
            'agent.mobile' => 'required_if:delivery_service,false,',
            'agent.code' => 'required_if:delivery_service,false,|string',
            // 'agent.presence_confirmation' => 'nullable|boolean',
            'delivery' => 'required_if:delivery_service,true',
            'delivery.address' => 'required_if:delivery_service,true',
        ]);

        $serialError = $this->validateProductSerials(json_decode(json_encode($request->products)));

        if (! empty($serialError)) {

            return $serialError;

        }

        $currentMerchant = \Auth::user();

        $newRequisition = new Requisition();

        $newRequisition->subject = strtolower($request->subject);
        $newRequisition->description = strtolower($request->description);
        $newRequisition->creator_type = get_class($currentMerchant);
        $newRequisition->creator_id = $currentMerchant->id;
        $newRequisition->merchant_id = $currentMerchant->id;

        $newRequisition->save();

        $newRequisition->required_products = json_decode(json_encode($request->products));

        if ($request->delivery_service) {
            
            $newRequisition->delivery()->create([
                'address' => $request->delivery['address'],
            ]);
        
        }

        else {

           $newRequisition->agent()->create([
                'name' => $request->agent['name'],
                'mobile' => $request->agent['mobile'],
                'code' => $request->agent['code'],
                // 'presence_confirmation' => $request->agent['presence_confirmation'] ?? false,
            ]); 

        }

        BroadcastNewRequisition::dispatch($newRequisition);

        return $this->showMyAllRequisitions($perPage);
    }

    public function searchMyAllRequisitions(Request $request, $perPage)
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

        $currentMerchant = \Auth::user();

        $query = Requisition::with(['creator', 'updater', 'delivery', 'agent', 'dispatch.delivery', 'dispatch.return', 'products.merchantProduct.product', 'products.merchantProduct', 'products.variations.merchantProductVariation', 'products.variations.merchantProductVariation.productVariation.variation', 'cancellation'])
        ->where(function ($query) use ($currentMerchant) {
            $query->where('merchant_id', $currentMerchant->id);
        });

        if ($request->search) {
            
            $query->where(function ($query1) use ($request) {
                $query1->where('subject', 'like', "%$request->search%")
                ->orWhere('description', 'like', "%$request->search%")
                ->orWhereHas('products.merchantProduct.product', function ($q) use ($request) {
                    $q->where('name', 'like', "%$request->search%");
                })
                ->orWhereHasMorph(
                    'updater',
                    [Admin::class, Manager::class, Warehouse::class],
                    function ($query2) use ($request) {
                        $query2->where('user_name', 'like', "%$request->search%");
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


        return [
            'all' => new MyRequisitionCollection($query->paginate($perPage)),  
        ];
    }

    public function receiveDispatchedProducts(Request $request, $perPage)
    {
        $request->validate([
            'id' => 'required|numeric|exists:requisitions,id|exists:dispatches,requisition_id',
        ]);

        $dispatchedRequisition = Requisition::findOrFail($request->id);

        if ($dispatchedRequisition->dispatch && $dispatchedRequisition->agent) {
            
            $dispatchedRequisition->dispatch->return()->update([
                'receiving_confirmation' => true,
            ]);

        }
        else if ($dispatchedRequisition->dispatch && $dispatchedRequisition->delivery) {
            
            $dispatchedRequisition->dispatch->delivery()->update([
                'receiving_confirmation' => true,
            ]);

        }

        BroadcastProductReceiveConfirmation::dispatch($dispatchedRequisition);

        return $this->showMyAllRequisitions($perPage);
    }

    // Merchant-Products for Admin (stocks) only
    public function showMerchantAllProducts($merchant)
    {
        $expectedMerchant = Merchant::findOrFail($merchant);
       
        return MerchantProductResource::collection(
            MerchantProduct::where('merchant_id', $expectedMerchant->id)
            ->with(['variations'])
            ->latest()
            ->get()
        );
    }

    // Merchant-Products for Admin && Merchant 
    public function showMerchantAvailableProducts($merchant, $perPage=false)
    {
        $expectedMerchant = Merchant::withTrashed()->findOrFail($merchant);
        
        if ($perPage) {
            
            return [

                'retail' => new MerchantProductCollection(MerchantProduct::where('merchant_id', $expectedMerchant->id)
                                                        ->whereHas('product', function ($query) {
                                                            $query->where('product_category_id', '>', 0);
                                                        })
                                                        ->with(['addresses', 'serials', 'nonDispatchedRequests', 'dispatchedRequests', 'variations.serials'])
                                                        ->with(array('merchant' => function($query) {
                                                            $query->withTrashed();
                                                        }))
                                                        ->with(['stocks' => function ($query1) {
                                                            $query1->where('available_quantity', '>', 0);
                                                         
                                                        }])
                                                        ->with(['variations.stocks' => function ($query2) {
                                                            $query2->where('available_quantity', '>', 0);
                                                         
                                                        }])
                                                        ->paginate($perPage)),
                
                'bulk' => new MerchantProductCollection(MerchantProduct::where('merchant_id', $expectedMerchant->id)
                                                        ->whereHas('product', function ($query) {
                                                            $query->whereNull('product_category_id')
                                                                ->orWhere('product_category_id', 0);
                                                        })
                                                        ->with(['stocks', 'addresses', 'nonDispatchedRequests', 'dispatchedRequests'])
                                                        ->with(array('merchant' => function($query) {
                                                            $query->withTrashed();
                                                        }))
                                                       ->paginate($perPage)),
            ];

        }
       
        return MerchantProductResource::collection(
            MerchantProduct::where('merchant_id', $expectedMerchant->id)
            ->where('available_quantity', '>', 0)
            ->with(['nonDispatchedRequests', 'dispatchedRequests', 'variations', 'variations.nonDispatchedRequests'])
            ->with(array('merchant' => function($query) {
                $query->withTrashed();
            }))
            ->with(['serials' => function ($query) {
                $query->where('has_requisitions', 0)->where('has_dispatched', 0);
            }])
            ->with(['variations.serials' => function ($query) {
                $query->where('has_requisitions', 0)->where('has_dispatched', 0);
            }])
            ->latest()
            ->get()
        );
    }

    public function storeMerchantMultipleProducts(Request $request, $perPage)
    {
        $request->validate([
            'merchant_id' => 'required|exists:merchants,id',
            'merchantMultipleProducts' => 'required|array|min:1',
            'merchantMultipleProducts.*' => 'required|numeric|exists:products,id',
        ]);

        foreach ($request->merchantMultipleProducts as $productKey => $productId) {

            $product = Product::findOrFail($productId);
        
            if (! MerchantProduct::where('merchant_id', $request->merchant_id)->where('product_id', $product->id)->exists()) {
                
                $productNewMerchant = MerchantProduct::create([

                    'sku' => $this->generateProductSKU($product->product_category_id, $product->id, $request->merchant_id, $request->manufacturer_id), 
                    'description' => '',
                    'warning_quantity' => 0,
                    'selling_price' => NULL,
                    'discount' => 0,
                    'product_id' => $product->id,
                    'merchant_id' => $request->merchant_id,
                    'created_at' => now()->format('Y-m-d')

                ]);

                if ($product->has_variations) {
                    
                    foreach ($product->variations as $productVariationKey => $productVariation) {
                        
                        $merchantProductVariation = $productNewMerchant->variations()->create([

                            'sku' => ($productNewMerchant->sku.'V'.$productVariation->variation->id), 
                            'available_quantity' => 0,
                            'selling_price' => 0,
                            'product_variation_id' => $productVariation->id

                        ]);

                    }

                }

            }

        }

        return $this->showMerchantAvailableProducts($request->merchant_id, $perPage);
    }

    public function storeMerchantNewProduct(Request $request, $perPage)
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

            'manufacturer_id' => 'nullable|numeric|exists:product_manufacturers,id',
            // 'selling_price' => 'required|numeric',
            'selling_price' => [ 'nullable', 'numeric', 'min:0'
                /*
                Rule::requiredIf(function () use ($product) {
                    return $product->product_category_id != NULL;
                }),
                */
            ],
            'discount' => 'nullable|numeric|between:0,100',
            'description' => 'nullable|string|max:65535',
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
            'upc' => $request->upc ?? NULL,
            // 'merchant_product_preview' => $request->preview, 
            'description' => strtolower($request->description), 
            'manufacturer_id' => $request->manufacturer_id, 
            'warning_quantity' => $request->warning_quantity ?? 0,
            'selling_price' => $product->product_category_id ? $request->selling_price : NULL,
            'discount' => $product->product_category_id ? $request->discount : NULL,
            'product_id' => $request->product_id,
            'merchant_id' => $request->merchant_id,
            'created_at' => now()->format('Y-m-d')

        ]);

        $productNewMerchant->merchant_product_preview = $request->preview;
        $productNewMerchant->save();

        if ($product->has_variations) {
            
            $productNewMerchant->merchant_product_variations = json_decode(json_encode($request->variations));

        }

        return $this->showMerchantAvailableProducts($request->merchant_id, $perPage);
    }

    public function updateMerchantProduct(Request $request, $productMerchant, $perPage)
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

            'manufacturer_id' => 'nullable|numeric|exists:product_manufacturers,id',
            // 'selling_price' => 'required|numeric',
            'selling_price' => [ 'nullable', 'numeric', 'min:0'
                /*
                Rule::requiredIf(function () use ($product) {
                    return $product->product_category_id != NULL;
                }),
                */
            ],
            'discount' => 'nullable|numeric|between:0,100',
            'description' => 'nullable|string|max:65535',
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
            'selling_price' => 'Invalid selling price',
            'warning_quantity' => 'Warning quantity should be numeric',
            'variations.*.variation' => 'Variation name is required',
            'variations.*.variation.id.*' => 'Invalid variations, please reload',
            'variations.*.selling_price' => 'Invalid selling price',
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
            'upc' => $request->upc ?? NULL,
            'manufacturer_id' => $request->manufacturer_id, 
            // 'merchant_product_preview' => $request->preview, 
            'description' => strtolower($request->description), 
            'warning_quantity' => $request->warning_quantity ?? 0,
            'selling_price' => $product->product_category_id ? $request->selling_price : NULL,
            'discount' => $product->product_category_id ? $request->discount : NULL,
            'product_id' => $request->product_id,
            'merchant_id' => $request->merchant_id,
            // 'created_at' => now()->format('Y-m-d')

        ]);

        $productMerchantToUpdate->merchant_product_preview = $request->preview;
        $productMerchantToUpdate->save();

        if ($product->has_variations) {
            
            $productMerchantToUpdate->merchant_product_variations = json_decode(json_encode($request->variations));

        }

        return $this->showMerchantAvailableProducts($request->merchant_id, $perPage);
    }

    public function deleteMerchantProduct($productMerchant, $perPage)
    {
        $merchantProductToDelete = MerchantProduct::findOrFail($productMerchant);
        $merchant = $merchantProductToDelete->merchant_id;

        // if any related stock / requisition exists
        if ($merchantProductToDelete->product_immutability) {
            
           return response()->json(['errors'=>["undeletableMerchant" => "Merchant has stock or requisition"]], 422); 

        }
            
        // $merchantProductToDelete->stocks()->delete();
        // $merchantProductToDelete->requests()->delete();
        // $merchantProductToDelete->deleteOldAddresses();
        
        File::delete($merchantProductToDelete->preview);

        foreach ($merchantProductToDelete->variations as $merchantProductVariationKey => $merchantProductVariationToDelete) {
            
            File::delete($merchantProductVariationToDelete->preview);
            
        }

        $merchantProductToDelete->variations()->delete();
        $merchantProductToDelete->delete();

        return $this->showMerchantAvailableProducts($merchant, $perPage);
    }

    public function searchMerchantAllProducts(Request $request, $perPage)
    {
        $request->validate([
            'search' => 'nullable|required_without_all:dateTo,dateFrom|string', 
            'dateTo' => 'nullable|required_without_all:search,dateFrom|date',
            'dateFrom' => 'nullable|required_without_all:search,dateTo|date',
            'merchant_id' => 'required|numeric|exists:merchants,id'
        ]); 

        $query = MerchantProduct::where('merchant_id', $request->merchant_id)
                ->with(['merchant', 'addresses', 'serials', 'stocks', 'nonDispatchedRequests', 'dispatchedRequests', 'variations.stocks', 'variations.serials']);

        if ($request->search) {
            
            $query->where(function ($query1) use ($request) {
                $query1->where('sku', 'like', "%$request->search%")
                ->orWhere('upc', 'like', "%$request->search%")
                ->orWhere('description', 'like', "%$request->search%")
                ->orWhere('warning_quantity', 'like', "%$request->search%")
                ->orWhere('discount', 'like', "%$request->search%")
                ->orWhere('selling_price', 'like', "%$request->search%")
                ->orWhereHas('manufacturer', function ($query2) use ($request) {
                    $query2->where('name', 'like', "%$request->search%");
                })
                ->orWhereHas('product', function ($query3) use ($request) {
                    $query3->where('name', 'like', "%$request->search%")
                        ->orWhere('quantity_type', 'like', "%$request->search%")
                        ->orWhereHas('category', function ($q) use ($request) {
                            $q->where('name', 'like', "%$request->search%");
                        });
                });
            });

        }

        if ($request->dateFrom && $request->dateTo) {
            
            $query->where(function ($query1) use ($request) {
                $query1->whereDate('created_at', '>=', $request->dateFrom)
                ->whereDate('created_at', '<=', $request->dateTo)
                ->orWhereHas('stocks', function ($query2) use ($request) {
                    $query2->whereHas('stock', function ($query3) use ($request) {
                        $query3->whereDate('created_at', '>=', $request->dateFrom)
                        ->whereDate('created_at', '<=', $request->dateTo);
                    });
                });
            })
            ->with(['serials' => function ($query1) use ($request) {
                $query1->whereHas('productStock', function ($query2) use ($request) {
                    $query2->whereHas('stock', function ($query3) use ($request) {
                        $query3->whereDate('created_at', '>=', $request->dateFrom)
                        ->whereDate('created_at', '<=', $request->dateTo);
                    });
                });
            }])
            ->with(['stocks' => function ($query1) use ($request) {
                $query1->whereHas('stock', function ($query2) use ($request) {
                    $query2->whereDate('created_at', '>=', $request->dateFrom)
                    ->whereDate('created_at', '<=', $request->dateTo);
                });
            }])
            ->with(['nonDispatchedRequests' => function ($query1) use ($request) {
                $query1->whereHas('requisition', function ($query2) use ($request) {
                    $query2->whereDate('created_at', '>=', $request->dateFrom)
                    ->whereDate('created_at', '<=', $request->dateTo);
                });
            }])
            ->with(['dispatchedRequests' => function ($query1) use ($request) {
                $query1->whereHas('dispatch', function ($query2) use ($request) {
                    $query2->whereDate('updated_at', '>=', $request->dateFrom)
                    ->whereDate('updated_at', '<=', $request->dateTo);
                });
            }])
            ->with(['variations.serials' => function ($query1) use ($request) {
                $query1->whereHas('variationStock', function ($query2) use ($request) {
                    $query2->whereHas('productStock', function ($query3) use ($request) {
                        $query3->whereHas('stock', function ($query4) use ($request) {
                            $query4->whereDate('created_at', '>=', $request->dateFrom)
                            ->whereDate('created_at', '<=', $request->dateTo);
                        });
                    });
                });
            }])
            ->with(['variations.stocks' => function ($query1) use ($request) {
                $query1->whereHas('productStock', function ($query2) use ($request) {
                    $query2->whereHas('stock', function ($query3) use ($request) {
                        $query3->whereDate('created_at', '>=', $request->dateFrom)
                        ->whereDate('created_at', '<=', $request->dateTo);
                    });
                });
            }]);

        }

        else if ($request->dateFrom && empty($request->dateTo)) {
            
            $query->where(function ($query1) use ($request) {
                $query1->whereDate('created_at', '>=', $request->dateFrom)
                ->orWhereHas('stocks', function ($query2) use ($request) {
                    $query2->whereHas('stock', function ($query3) use ($request) {
                        $query3->whereDate('created_at', '>=', $request->dateFrom);
                    });
                });
            })
            ->with(['serials' => function ($query1) use ($request) {
                $query1->whereHas('productStock', function ($query2) use ($request) {
                    $query2->whereHas('stock', function ($query3) use ($request) {
                        $query3->whereDate('created_at', '>=', $request->dateFrom);
                    });
                });
            }])
            ->with(['stocks' => function ($query1) use ($request) {
                $query1->whereHas('stock', function ($query2) use ($request) {
                    $query2->whereDate('created_at', '>=', $request->dateFrom);
                });
            }])
            ->with(['nonDispatchedRequests' => function ($query1) use ($request) {
                $query1->whereHas('requisition', function ($query2) use ($request) {
                    $query2->whereDate('created_at', '>=', $request->dateFrom);
                });
            }])
            ->with(['dispatchedRequests' => function ($query1) use ($request) {
                $query1->whereHas('dispatch', function ($query2) use ($request) {
                    $query2->whereDate('updated_at', '>=', $request->dateFrom);
                });
            }])
            ->with(['variations.serials' => function ($query1) use ($request) {
                $query1->whereHas('variationStock', function ($query2) use ($request) {
                    $query2->whereHas('productStock', function ($query3) use ($request) {
                        $query3->whereHas('stock', function ($query4) use ($request) {
                            $query4->whereDate('created_at', '>=', $request->dateFrom);
                        });
                    });
                });
            }])
            ->with(['variations.stocks' => function ($query1) use ($request) {
                $query1->whereHas('productStock', function ($query2) use ($request) {
                    $query2->whereHas('stock', function ($query3) use ($request) {
                        $query3->whereDate('created_at', '>=', $request->dateFrom);
                    });
                });
            }]);

        }

        else if (empty($request->dateFrom) && $request->dateTo) {
            
            $query->where(function ($query1) use ($request) {
                $query1->whereDate('created_at', '<=', $request->dateTo)
                ->orWhereHas('stocks', function ($query2) use ($request) {
                    $query2->whereHas('stock', function ($query3) use ($request) {
                        $query3->whereDate('created_at', '<=', $request->dateTo);
                    });
                });
            })
            ->with(['serials' => function ($query1) use ($request) {
                $query1->whereHas('productStock', function ($query2) use ($request) {
                    $query2->whereHas('stock', function ($query3) use ($request) {
                        $query3->whereDate('created_at', '<=', $request->dateTo);
                    });
                });
            }])
            ->with(['stocks' => function ($query1) use ($request) {
                $query1->whereHas('stock', function ($query2) use ($request) {
                    $query2->whereDate('created_at', '<=', $request->dateTo);
                });
            }])
            ->with(['nonDispatchedRequests' => function ($query1) use ($request) {
                $query1->whereHas('requisition', function ($query2) use ($request) {
                    $query2->whereDate('created_at', '<=', $request->dateTo);
                });
            }])
            ->with(['dispatchedRequests' => function ($query1) use ($request) {
                $query1->whereHas('dispatch', function ($query2) use ($request) {
                    $query2->whereDate('updated_at', '<=', $request->dateTo);
                });
            }])
            ->with(['variations.serials' => function ($query1) use ($request) {
                $query1->whereHas('variationStock', function ($query2) use ($request) {
                    $query2->whereHas('productStock', function ($query3) use ($request) {
                        $query3->whereHas('stock', function ($query4) use ($request) {
                            $query4->whereDate('created_at', '<=', $request->dateTo);
                        });
                    });
                });
            }])
            ->with(['variations.stocks' => function ($query1) use ($request) {
                $query1->whereHas('productStock', function ($query2) use ($request) {
                    $query2->whereHas('stock', function ($query3) use ($request) {
                        $query3->whereDate('created_at', '<=', $request->dateTo);
                    });
                });
            }]);

        }

        return [
            'all' => (new MerchantProductCollection($query->paginate($perPage)))->setFromDate($request->dateFrom),  
        ];
    }

    // Product-Stock
    public function showMyProductAllStocks($productMerchant, $perPage)
    {
        $currentMerchant =  \Auth::user();
        $merchantExpectedProduct = MerchantProduct::findOrFail($productMerchant);

        if ($merchantExpectedProduct->merchant_id == $currentMerchant->id) {
            
            return new ProductStockCollection(ProductStock::with(['variations', 'serials'])->where('merchant_product_id', $productMerchant)->latest('id')->paginate($perPage));

        }

        return response()->json(['errors'=>["invalidProduct" => "Invalid product query"]], 422);

    }

    public function searchMyProductAllStocks(Request $request, $merchantProduct, $perPage)
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

        $currentMerchant =  \Auth::user();
        $merchantExpectedProduct = MerchantProduct::findOrFail($merchantProduct);

        if ($merchantExpectedProduct->merchant_id != $currentMerchant->id) {
            
            return response()->json(['errors'=>["invalidProduct" => "Invalid product query"]], 422);

        }

        $query = ProductStock::with(['variations', 'serials'])
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
                ->orWhereHas('variations', function ($query3) use ($request) {
                    $query3->where('stock_code', 'like', "%$request->search%")
                        ->orWhere('stock_quantity', 'like', "%$request->search%")
                        ->orWhere('available_quantity', 'like', "%$request->search%")
                        ->orWhere('unit_buying_price', 'like', "%$request->search%")
                        ->orWhereHas('serials', function ($query4) use ($request) {
                            $query4->where('serial_no', 'like', "%$request->search%");
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

    // My-Deal
    public function showMyAllSpaceDeals($perPage)
    {
        $currentMerchant = \Auth::user();

        return new MerchantSpaceDealCollection(
            MerchantSpaceDeal::where('merchant_id', $currentMerchant->id)
            ->with(['spaces', 'rents'])
            ->with(['rentPeriod' => function($query) {
                $query->withTrashed();
            }])
            ->latest()->paginate($perPage)
        );
    }

    public function searchMyAllSpaceDeals(Request $request, $perPage)
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

        $currentMerchant = \Auth::user();

        $query = MerchantSpaceDeal::with(['spaces', 'rents'])->where('merchant_id', $currentMerchant->id);

        if ($request->search) {
            
            $query->where(function($query5) use ($request) {
                $query5->whereHas('merchant', function ($query6) use ($request) {
                    $query6->where('first_name', 'like', "%$request->search%")
                    ->orWhere('last_name', 'like', "%$request->search%")
                    ->orWhere('user_name', 'like', "%$request->search%")
                    ->orWhere('email', 'like', "%$request->search%")
                    ->orWhere('mobile', 'like', "%$request->search%");
                })
                ->orWhereHas('spaces', function ($query2) use ($request) {
                    $query2->whereHasMorph(
                        'space',
                        [ WarehouseContainerStatus::class, WarehouseContainerShelfStatus::class, WarehouseContainerShelfUnitStatus::class ],
                        function ($query3) use ($request) {
                            $query3->where('name', 'like', "%$request->search%")
                            ->orWhereHas('warehouseContainer.container', function ($query8) use ($request) {
                                $query8->where('name', 'like', "%$request->search%");
                            });
                        }
                    );
                })
                ->orWhereHas('rents', function ($query4) use ($request) {
                    $query4->where('invoice_no', 'like', "%$request->search%")
                    ->orWhere('number_installment', 'like', "%$request->search%")
                    ->orWhere('date_from', 'like', "%$request->search%")
                    ->orWhere('date_to', 'like', "%$request->search%")
                    ->orWhere('total_rent', 'like', "%$request->search%")
                    ->orWhere('discount', 'like', "%$request->search%")
                    ->orWhere('net_payable', 'like', "%$request->search%")
                    ->orWhere('paid_amount', 'like', "%$request->search%")
                    ->orWhere('current_due', 'like', "%$request->search%");
                });
            });

        }

        if ($request->dateFrom) {
            
            $query->whereDate('created_at', '>=', $request->dateFrom);

        }

        if ($request->dateTo) {
            
            $query->whereDate('created_at', '<=', $request->dateTo);

        }

        return response()->json([
            'all' => new MerchantSpaceDealCollection($query->latest()->paginate($perPage)),  
        ], 200);
    }

    // My-Deal-Rents
    public function showMySpaceDealAllRents($deal, $perPage = false)
    {
        if ($perPage) {
            
            $currentMerchant = \Auth::user();

            $merchantExpectedDeal = MerchantSpaceDeal::findOrFail($deal);

            if ($merchantExpectedDeal->merchant_id == $currentMerchant->id) {
                
                return new MerchantRentCollection(
                    MerchantRent::with(['spaceRents'])
                    ->whereHasMorph('dealable', [MerchantSpaceDeal::class],
                        function ($query) use ($deal) {
                            $query->where('id', $deal);
                        }
                    )
                    ->latest('created_at')->paginate($perPage)
                );

            }

            return response()->json(['errors'=>["invalidDeal" => "Invalid deal query"]], 422);

        }
    }

    public function searchMySpaceDealAllRents(Request $request, $perPage)
    {
        $request->validate([
            'merchant_space_deal_id' => 'required|exists:merchant_deals,id',
            'search' => 'nullable|required_without_all:dateTo,dateFrom|string', 
            'dateTo' => 'nullable|required_without_all:search,dateFrom|date|after_or_equal:1970-01-01 00:00:01|before_or_equal:2038-01-19 03:14:07',
            'dateFrom' => 'nullable|required_without_all:search,dateTo|date|after_or_equal:1970-01-01 00:00:01|before_or_equal:2038-01-19 03:14:07',
            // 'showPendingRequisitions' => 'nullable|boolean',
            // 'showCancelledRequisitions' => 'nullable|boolean',
            // 'showDispatchedRequisitions' => 'nullable|boolean',
            // 'showProduct' => 'nullable|string', 
        ]);

        $currentMerchant = \Auth::user();

        $merchantExpectedDeal = MerchantSpaceDeal::findOrFail($request->merchant_space_deal_id);

        if ($merchantExpectedDeal->merchant_id != $currentMerchant->id) {
            
            return response()->json(['errors'=>["invalidDeal" => "Invalid deal query"]], 422);

        }

        $query = MerchantPayment::with(['rents'])->whereHas('deal', function ($query1) use ($request) {
            $query1->where('merchant_space_deal_id', $request->merchant_space_deal_id);
        });

        if ($request->search) {
            
            $query->where(function($query2) use ($request) {
                $query2->where('invoice_no', 'like', "%$request->search%")
                ->orWhere('number_installment', 'like', "%$request->search%")
                ->orWhere('date_from', 'like', "%$request->search%")
                ->orWhere('date_to', 'like', "%$request->search%")
                ->orWhere('total_rent', 'like', "%$request->search%")
                ->orWhere('discount', 'like', "%$request->search%")
                ->orWhere('previous_due', 'like', "%$request->search%")
                ->orWhere('net_payable', 'like', "%$request->search%")
                ->orWhere('paid_amount', 'like', "%$request->search%")
                ->orWhere('current_due', 'like', "%$request->search%")
                ->orWhereHas('rents', function ($query3) use ($request) {
                    $query3->where('rent', 'like', "%$request->search%");
                });
            });

        }

        if ($request->dateFrom) {
            
            $query->whereDate('paid_at', '>=', $request->dateFrom);

        }

        if ($request->dateTo) {
            
            $query->whereDate('paid_at', '<=', $request->dateTo);

        }

        return response()->json([
            'all' => new MerchantRentCollection($query->latest('paid_at')->paginate($perPage)),  
        ], 200);
    }

    protected function validateProductSerials($products = [])
    {
        foreach ($products as $requiredProduct) {
            
            if ($requiredProduct->product->has_serials && ! $requiredProduct->product->has_variations && (empty($requiredProduct->serials) || $requiredProduct->total_quantity != count($requiredProduct->serials))) {
                
                return response()->json(['errors'=>["productSerial" => "Product serial is required"]], 422);

            }

            else if ($requiredProduct->product->has_serials && ! $requiredProduct->product->has_variations && count($requiredProduct->serials)) {
                        
                foreach ($requiredProduct->serials as $requiredProductSerialIndex => $requiredProductSerial) {
                    
                    if (! ProductSerial::where('serial_no', $requiredProductSerial)->where('has_requisitions', false)->where('has_dispatched', false)->exists()) {
                        
                        return response()->json(['errors'=>["productSerial" => "Product serial has already requisition"]], 422);

                    }

                }

            }

            else if ($requiredProduct->product->has_serials && $requiredProduct->product->has_variations) {
                
                foreach ($requiredProduct->product->variations as $requiredProductVariation) {
                    
                    if (! empty($requiredProductVariation->required_quantity) && $requiredProductVariation->required_quantity > 0 && (empty($requiredProductVariation->required_serials) || $requiredProductVariation->required_quantity != count($requiredProductVariation->required_serials))) {
                
                        return response()->json(['errors'=>["variationSerial" => "Variation serial is required"]], 422);

                    }

                    else if (! empty($requiredProductVariation->required_quantity) && $requiredProductVariation->required_quantity > 0 && count($requiredProductVariation->required_serials)) {
                        
                        foreach ($requiredProductVariation->required_serials as $requiredProductVariationSerialIndex => $requiredProductVariationSerial) {
                            
                            if (! ProductVariationSerial::where('serial_no', $requiredProductVariationSerial)->where('has_requisitions', false)->where('has_dispatched', false)->exists()) {
                                
                                return response()->json(['errors'=>["variationSerial" => "Variation serial has already requisition"]], 422);

                            }

                        }

                    }

                }

            }

        }
    }

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

}
