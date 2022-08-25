<?php

namespace App\Http\Controllers;

use App\Models\Requisition;
use Illuminate\Http\Request;
use App\Models\ProductSerial;
use Illuminate\Validation\Rule;
use App\Jobs\BroadcastNewRequisition;
use App\Models\ProductVariationSerial;
use App\Http\Resources\Web\RequisitionResource;
use App\Http\Resources\Web\RequisitionCollection;

class RequisitionController extends Controller
{
    public function __construct()
    {
        // requisitions
        $this->middleware("permission:view-requisition-index")->only(['showAllRequisitions', 'searchAllRequisitions']);
        $this->middleware("permission:create-requisition")->only(['makeNewRequisition']);
        $this->middleware("permission:update-requisition")->only('cancelRequisition');

        // requisitions
        $this->middleware("permission:view-requisition-index")->only(['showMerchantProductAllRequisitions', 'searchMerchantProductAllRequisitions']);
    }

    // Requisition (Admin RequisitionIndex)
    public function showAllRequisitions($perPage = false)
    {       
        if ($perPage) {

            return [

                'pending' => new RequisitionCollection(Requisition::with(['creator', 'updater', 'delivery', 'agent', 'products.merchantProduct.product', 'products.merchantProduct.addresses', 'products.variations.merchantProductVariation.stocks', 'products.variations.merchantProductVariation.productVariation.variation'])->where('status', 0)
                    ->with(['products.merchantProduct.stocks' => function ($query) {
                        $query->where('available_quantity', '>', 0);
                    }])
                    ->with(['products.variations.merchantProductVariation.stocks' => function ($query) {
                        $query->where('available_quantity', '>', 0);
                    }])
                    ->latest()->paginate($perPage)),  

                'dispatched' => new RequisitionCollection(Requisition::with(['creator', 'updater', 'delivery', 'agent', 'products.merchantProduct.product', 'products.merchantProduct.addresses', 'products.variations.merchantProductVariation.stocks', 'products.variations.merchantProductVariation.productVariation.variation', 'dispatch.delivery', 'dispatch.return', 'products.stocks', 'products.variations.stocks'])->where('status', 1)
                    ->with(['products.merchantProduct.stocks' => function ($query) {
                        $query->where('available_quantity', '>', 0);
                    }])
                    ->with(['products.variations.merchantProductVariation.stocks' => function ($query) {
                        $query->where('available_quantity', '>', 0);
                    }])
                    ->latest()->paginate($perPage)),  
                
                'cancelled' => new RequisitionCollection(Requisition::with(['creator', 'updater', 'delivery', 'agent', 'products.merchantProduct.product', 'products.merchantProduct.addresses', 'products.variations.merchantProductVariation.stocks', 'products.variations.merchantProductVariation.productVariation.variation', 'cancellation'])->where('status', -1)
                    ->with(['products.merchantProduct.stocks' => function ($query) {
                        $query->where('available_quantity', '>', 0);
                    }])
                    ->with(['products.variations.merchantProductVariation.stocks' => function ($query) {
                        $query->where('available_quantity', '>', 0);
                    }])
                    ->latest()->paginate($perPage)),  
            
            ];

        }

        return RequisitionResource::collection(Requisition::with(['creator', 'updater', 'delivery', 'agent', 'dispatch.delivery', 'dispatch.return', 'products.merchantProduct.product', 'products.merchantProduct.addresses', 'products.variations.merchantProductVariation.stocks', 'products.variations.merchantProductVariation.productVariation.variation'])
            ->with(['products.merchantProduct.stocks' => function ($query) {
                $query->where('available_quantity', '>', 0);
            }])
            ->with(['products.variations.merchantProductVariation.stocks' => function ($query) {
                $query->where('available_quantity', '>', 0);
            }])
            ->where('status', 0)
            ->latest()->get());

    }

    public function makeNewRequisition(Request $request, $perPage)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'merchant_id' => 'required|exists:merchants,id',
            
            'products' => 'required|array|min:1',
            // 'products.*.id' => 'required|numeric|exists:merchant_products,id',
            'products.*.total_quantity' => 'required|numeric|min:1',
            'products.*.packaging_service' => 'boolean',
            // 'products.*.package' => 'required_if:products.*.packaging_service,true',
            'products.*.merchant_product' => 'required',
            'products.*.merchant_product.id' => 'required|numeric|exists:merchant_products,id',
            'products.*.merchant_product.has_serials' => 'required|boolean',
            'products.*.merchant_product.has_variations' => 'required|boolean',

            'products.*.serials' => 'exclude_if:products.*.merchant_product.has_variations,true|required_if:products.*.merchant_product.has_serials,true|array',
            'products.*.serials.*.serial_no' => [
                    Rule::exists('product_serials', 'serial_no')->where(function ($query) {
                        return $query->where('has_requisitions', false)->where('has_dispatched', false);
                    }),
            ],

            'products.*.merchant_product.variations' => 'required_if:products.*.merchant_product.has_variations,true|array',
            'products.*.merchant_product.variations.*.required_serials' => 'exclude_if:products.*.merchant_product.has_serials,false|required_with:products.*.merchant_product.variations.*.required_quantity|array',
            'products.*.merchant_product.variations.*.required_serials.*' => [
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

        $currentUser = \Auth::guard('admin')->user() ?? \Auth::guard('manager')->user() ?? \Auth::guard('warehouse')->user() ?? \Auth::guard('owner')->user() ?? \Auth::user();

        $newRequisition = new Requisition();

        $newRequisition->subject = strtolower($request->subject);
        $newRequisition->description = strtolower($request->description);
        $newRequisition->creator_type = get_class($currentUser);
        $newRequisition->creator_id = $currentUser->id;
        $newRequisition->merchant_id = $request->merchant_id;
        $newRequisition->created_at = $request->created_at ? $request->created_at : now();

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

        return $this->showAllRequisitions($perPage);
    }

    public function cancelRequisition(Request $request, $requisition, $perPage)
    {
        $request->validate([
            'cancellation_reason' => 'required|string', 
        ]);

        $currentUser = \Auth::guard('admin')->user() ?? \Auth::guard('manager')->user() ?? \Auth::guard('warehouse')->user() ?? \Auth::guard('owner')->user() ?? Auth::user();

        if (empty($currentUser)) {

            return response()->json(['errors'=>["noUser" => "Current user missing, please reload the page"]], 422);
            
        }

        $expectedRequisition = Requisition::findOrFail($requisition);

        if ($expectedRequisition->status == -1) {
            
            return response()->json(['errors'=>["cancelledRequisition" => "Cancelled requisition, please reload the page"]], 422);

        }

        else if ($expectedRequisition->status == 1) {        // dispatched requisition
            
            $dispatchToUpdate = $expectedRequisition->dispatch;

            if ($dispatchToUpdate->has_approval) { // 1 / -1
               
                return response()->json(['errors'=>["approvedOrCancelled" => "Already approved or cancelled dispatch"]], 422);

            }
       
            $dispatchToUpdate->has_approval = -1;
            $dispatchToUpdate->updater_type = get_class($currentUser);
            $dispatchToUpdate->updater_id = $currentUser->id;
            $dispatchToUpdate->updated_at = now();
            $dispatchToUpdate->save();

            $dispatchToUpdate->delivery()->delete();
            $dispatchToUpdate->return()->delete();

        }
        else if ($expectedRequisition->status == 0) {       // cancelling non dispatched requisition
            
            $expectedRequisition->update([
                'status' => -1,
                'updater_type' => get_class($currentUser),
                'updater_id' => $currentUser->id,
            ]);    

        }

        $expectedRequisition->cancellation()->updateOrCreate(
            [ 'requisition_id' => $expectedRequisition->id ],
            [ 'reason' => $request->cancellation_reason ]
        );

        $expectedRequisition->cancelProductRequisitions();

        // BroadcastRequisitionDispatch::dispatch($dispatchToUpdate->requisition);

        return $this->showAllRequisitions($perPage);
    }

    public function searchAllRequisitions(Request $request, $perPage)
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

        $query = Requisition::with(['creator', 'updater', 'delivery', 'agent', 'dispatch.delivery', 'dispatch.return', 'products.merchantProduct.product', 'products.merchantProduct.addresses', 'products.variations.merchantProductVariation.stocks', 'products.variations.merchantProductVariation.productVariation.variation', 'cancellation'])
            ->with(['products.merchantProduct.stocks' => function ($query) {
                $query->where('available_quantity', '>', 0);
            }])
            ->with(['products.variations.merchantProductVariation.stocks' => function ($query) {
                $query->where('available_quantity', '>', 0);
            }]);

        /*
        
            if ($request->showPendingRequisitions) {
                
                $query->where('status', 0);

            }

            if ($request->showCancelledRequisitions) {
                
                $query->where('status', -1);

            }

            if ($request->showDispatchedRequisitions) {
                
                $query->where('status', 1);

            }

         */

        if ($request->search) {
            
            $query->where(function ($query1) use ($request) {
                $query1->where('subject', 'like', "%$request->search%")
                    ->orWhere('description', 'like', "%$request->search%")
                    ->orWhereHas('products.merchantProduct.product', function ($q) use ($request) {
                        $q->where('name', 'like', "%$request->search%");
                });
            });

        }

        if ($request->dateFrom) {
            
            $query->whereDate('created_at', '>=', $request->dateFrom);

        }

        if ($request->dateTo) {
            
            $query->whereDate('created_at', '<=', $request->dateTo);

        }

        /*
        
            if ($request->showProduct) {
                
                $query->whereHas('products.merchantProduct.product', function ($q) use ($request) {
                    $q->where('name', 'like', "%$request->showProduct%");
                })
                ->with([
                    'products.merchantProduct.product' => function ($q) use ($request) {
                        $q->where('name', 'like', "%$request->showProduct%");
                    },
                    'products.variations.merchantProductVariation.productVariation', 'delivery', 'agent', 'dispatch.delivery', 'dispatch.return'
                ]);

            }
        
         */



        return [
            'all' => new RequisitionCollection($query->paginate($perPage)),  
        ];
    }

    // Merchant Product Requisition (MerchantProductRequisitionIndex)
    public function showMerchantProductAllRequisitions($merchantProductId, $perPage = false)
    {       
        if ($perPage) {

            // $merchantProductRequisitions = Requisition::with(['creator', 'updater', 'delivery', 'agent', 'dispatch.delivery', 'dispatch.return', 'products.merchantProduct.product', 'products.merchantProduct.latestStock', 'products.merchantProduct.addresses', 'products.variations.merchantProductVariation.latestStock', 'products.variations.merchantProductVariation.productVariation.variation', 'cancellation'])
            // ->whereHas('products', function ($query) use ($merchantProductId) {
            //     $query->where('merchant_product_id', $merchantProductId);
            // })
            // ->with(['products' => function ($query) use ($merchantProductId) {
            //     $query->where('merchant_product_id', $merchantProductId);
            // }]);

            return [

                'dispatched' => new RequisitionCollection(
                    Requisition::with(['creator', 'updater', 'delivery', 'agent', 'dispatch.delivery', 'dispatch.return', 'products.merchantProduct.product', 'products.merchantProduct.addresses', 'products.variations.merchantProductVariation.stocks', 'products.variations.merchantProductVariation.productVariation.variation'])
                    ->whereHas('products', function ($query) use ($merchantProductId) {
                        $query->where('merchant_product_id', $merchantProductId);
                    })
                    ->with(['products' => function ($query) use ($merchantProductId) {
                        $query->where('merchant_product_id', $merchantProductId);
                    }])
                    ->where('status', 1)->latest()->paginate($perPage)
                ),  

                'pending' => new RequisitionCollection(
                    Requisition::with(['creator', 'updater', 'delivery', 'agent', 'dispatch.delivery', 'dispatch.return', 'products.merchantProduct.product', 'products.merchantProduct.addresses', 'products.variations.merchantProductVariation.stocks', 'products.variations.merchantProductVariation.productVariation.variation'])
                    ->whereHas('products', function ($query) use ($merchantProductId) {
                        $query->where('merchant_product_id', $merchantProductId);
                    })
                    ->with(['products' => function ($query) use ($merchantProductId) {
                        $query->where('merchant_product_id', $merchantProductId);
                    }])
                    ->with(['products.merchantProduct.stocks' => function ($query) {
                        $query->where('available_quantity', '>', 0);
                    }])
                    ->with(['products.variations.merchantProductVariation.stocks' => function ($query) {
                        $query->where('available_quantity', '>', 0);
                    }])
                    ->where('status', 0)->latest()->paginate($perPage)
                ),  
                
                'cancelled' => new RequisitionCollection(
                    Requisition::with(['creator', 'updater', 'delivery', 'agent', 'dispatch.delivery', 'dispatch.return', 'products.merchantProduct.product', 'products.merchantProduct.addresses', 'products.variations.merchantProductVariation.stocks', 'products.variations.merchantProductVariation.productVariation.variation', 'cancellation'])
                    ->whereHas('products', function ($query) use ($merchantProductId) {
                        $query->where('merchant_product_id', $merchantProductId);
                    })
                    ->with(['products' => function ($query) use ($merchantProductId) {
                        $query->where('merchant_product_id', $merchantProductId);
                    }])
                    ->where('status', -1)->latest()->paginate($perPage)
                ),  
            
            ];

        }

        // return MerchantProductRequisitionResource::collection($merchantProductRequisitions->where('status', 0)->latest()->get());

    }

    public function searchMerchantProductAllRequisitions(Request $request, $perPage)
    {
        $request->validate([
            'search' => 'nullable|required_without_all:dateTo,dateFrom|string', 
            'dateTo' => 'nullable|required_without_all:search,dateFrom|date',
            'dateFrom' => 'nullable|required_without_all:search,dateTo|date',
            'merchant_product_id' => 'required|exists:merchant_products,id'
            // 'showPendingRequisitions' => 'nullable|boolean',
            // 'showCancelledRequisitions' => 'nullable|boolean',
            // 'showDispatchedRequisitions' => 'nullable|boolean',
            // 'showProduct' => 'nullable|string', 
        ]);        

        $query = Requisition::with(['creator', 'updater', 'delivery', 'agent', 'dispatch.delivery', 'dispatch.return', 'products.merchantProduct.product', 'products.merchantProduct.addresses', 'products.variations.merchantProductVariation.stocks', 'products.variations.merchantProductVariation.productVariation.variation', 'cancellation'])
        ->whereHas('products', function ($query) use ($request) {
            $query->where('merchant_product_id', $request->merchant_product_id);
        })
        ->with(['products' => function ($query) use ($request) {
            $query->where('merchant_product_id', $request->merchant_product_id);
        }])
        ->with(['products.merchantProduct.stocks' => function ($query) {
            $query->where('available_quantity', '>', 0);
        }])
        ->with(['products.variations.merchantProductVariation.stocks' => function ($query) {
            $query->where('available_quantity', '>', 0);
        }]);

        /* 
            if ($request->showPendingRequisitions) {
                
                $query->where('status', 0);

            }

            if ($request->showCancelledRequisitions) {
                
                $query->where('status', -1);

            }

            if ($request->showDispatchedRequisitions) {
                
                $query->where('status', 1);

            }
        */

        if ($request->search) {
            
            $query->where(function ($query1) use ($request) {
                $query1->where('subject', 'like', "%$request->search%")
                    ->orWhere('description', 'like', "%$request->search%")
                    ->orWhereHas('products.merchantProduct.product', function ($q) use ($request) {
                        $q->where('name', 'like', "%$request->search%");
                });
            });

        }

        if ($request->dateFrom) {
            
            $query->whereDate('created_at', '>=', $request->dateFrom);

        }

        if ($request->dateTo) {
            
            $query->whereDate('created_at', '<=', $request->dateTo);

        }

        /*
            if ($request->showProduct) {
                
                $query->whereHas('products.merchantProduct.product', function ($q) use ($request) {
                    $q->where('name', 'like', "%$request->showProduct%");
                })
                ->with([
                    'products.merchantProduct.product' => function ($q) use ($request) {
                        $q->where('name', 'like', "%$request->showProduct%");
                    },
                    'products.variations.merchantProductVariation.productVariation', 'delivery', 'agent', 'dispatch.delivery', 'dispatch.return'
                ]);

            }
        */

        return [
            'all' => new RequisitionCollection($query->paginate($perPage)),  
        ];
    }

    protected function validateProductSerials($products = [])
    {
        foreach ($products as $requiredProduct) {
            
            if ($requiredProduct->merchant_product->has_serials && ! $requiredProduct->merchant_product->has_variations && (empty($requiredProduct->serials) || $requiredProduct->total_quantity != count($requiredProduct->serials))) {
                
                return response()->json(['errors'=>["productSerial" => "Product serial is required"]], 422);

            }

            else if ($requiredProduct->merchant_product->has_serials && ! $requiredProduct->merchant_product->has_variations && count($requiredProduct->serials)) {
                        
                foreach ($requiredProduct->serials as $requiredProductSerialIndex => $requiredProductSerial) {
                    
                    if (! ProductSerial::where('serial_no', $requiredProductSerial->serial_no)->where('has_requisitions', false)->where('has_dispatched', false)->exists()) {
                        
                        return response()->json(['errors'=>["productSerial" => "Product serial has already requisition"]], 422);

                    }

                }

            }

            else if ($requiredProduct->merchant_product->has_serials && $requiredProduct->merchant_product->has_variations) {
                
                foreach ($requiredProduct->merchant_product->variations as $requiredProductVariation) {
                    
                    if (! empty($requiredProductVariation->required_quantity) && $requiredProductVariation->required_quantity > 0 && (empty($requiredProductVariation->required_serials) || $requiredProductVariation->required_quantity != count($requiredProductVariation->required_serials))) {
                
                        return response()->json(['errors'=>["variationSerial" => "Variation serial is required"]], 422);

                    }

                    else if (! empty($requiredProductVariation->required_quantity) && $requiredProductVariation->required_quantity > 0 && count($requiredProductVariation->required_serials)) {
                        
                        foreach ($requiredProductVariation->required_serials as $requiredProductVariationSerialIndex => $requiredProductVariationSerial) {
                            
                            if (! ProductVariationSerial::where('serial_no', $requiredProductVariationSerial->serial_no)->where('has_requisitions', false)->where('has_dispatched', false)->exists()) {
                                
                                return response()->json(['errors'=>["variationSerial" => "Variation serial has already requisition or dispatched"]], 422);

                            }

                        }

                    }

                }

            }

        }
    }

}
