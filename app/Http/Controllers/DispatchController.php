<?php

namespace App\Http\Controllers;

use App\Models\Dispatch;
use App\Models\Requisition;
use Illuminate\Http\Request;
use App\Models\MerchantProduct;
use Illuminate\Validation\Rule;
use App\Models\RequiredProductSerial;
use App\Jobs\BroadcastRequisitionDispatch;
use App\Models\RequiredProductVariationSerial;
use App\Http\Resources\Web\RequisitionCollection;

class DispatchController extends Controller
{
    public function __construct()
    {
        $this->middleware("permission:view-dispatch-index")->only(['showAllDispatches', 'searchAllDispatches']);
        $this->middleware("permission:recommend-dispatch")->only('makeDispatch');
        // $this->middleware("permission:approve-dispatch")->only('updateDispatch');
    }

    // Requisition (Admin)
    public function showAllRequisitions($perPage = false)
    {       
        if ($perPage) {

            return [

                'pending' => new RequisitionCollection(Requisition::with(['updater', 'products.merchantProduct', 'products.variations.productVariation', 'delivery', 'agent'])->where('status', 0)->paginate($perPage)),  
                'dispatched' => new RequisitionCollection(Requisition::with(['updater', 'products.merchantProduct', 'products.variations.productVariation', 'delivery', 'agent', 'dispatch.delivery', 'dispatch.return'])->where('status', 1)->whereHas('dispatch', function ($query) {
                        $query->where('has_approval', 1);
                    })->paginate($perPage)),  
                'cancelled' => new RequisitionCollection(Requisition::with(['updater', 'products.merchantProduct', 'products.variations.productVariation', 'delivery', 'agent', 'dispatch.delivery', 'dispatch.return'])->where('status', -1)->paginate($perPage)),  
            
            ];

        }

        return RequisitionResource::collection(Requisition::with(['products.merchantProduct', 'products.variations.productVariation', 'delivery', 'agent'])->where('status', 0)->get());

    }

    // Despatch (Admin)
    public function showAllDispatches($perPage = false)
    {       
        if ($perPage) {
            
            return new RequisitionCollection(Requisition::with(['updater', 'products.product', 'products.variations.productVariation', 'delivery', 'agent', 'dispatch.delivery', 'dispatch.return'])->where('status', 1)->paginate($perPage));

        }
    }

    public function makeDispatch(Request $request, $perPage)
    {
        $expectedRequisition = Requisition::findOrFail($request->requisition['id']);

        $request->validate([
            
            'requisition' => 'required',

            'requisition.id' => [
                'required', 'exists:requisitions,id'
                /*
                    Rule::exists('requisitions', 'id')->where(function ($query) {
                        return $query->where('status', 0);
                    }),
                */
            ],

            'requisition.products' => 'required|array|min:1',
            'requisition.products.*.merchant_product_id' => 'required|exists:merchant_products,id',

            'requisition.products.*.quantity' => 'required|numeric|min:1',
            'requisition.products.*.available_quantity' => 'required|numeric|min:1',

            'requisition.products.*.packaging_service' => 'required|boolean',
            'requisition.products.*.dispatched_package' => 'required_if:requisition.products.*.packaging_service,1',
            'requisition.products.*.dispatched_package.id' => 'required_if:requisition.products.*.packaging_service,1|exists:packaging_packages,id',
            'requisition.products.*.dispatched_package_quantity' => 'required_if:requisition.products.*.packaging_service,1|numeric',

            // validate laterly
            'requisition.products.*.serials' => 'exclude_if:requisition.products.*.has_variations,1|required_if:requisition.products.*.has_serials,1|array|min:1',

            // 'agent' => 'required_if:delivery,0,',
            // 'agent.agent_receipt' => 'required_if:delivery,0,',
            
            'requisition.products.*.variations' => 'required_if:requisition.products.*.has_variations,1|array|min:1',

            'requisition.products.*.variations.*.quantity' => 'required_with:requisition.products.*.variations|numeric|min:1',
            'requisition.products.*.variations.*.available_quantity' => 'required_with:requisition.products.*.variations|numeric|min:1',

            'requisition.products.*.variations.*.serials' => 'required_if:requisition.products.*.variations.*.has_serials,1|array|min:1',

            'delivery' => 'required_if:requisition.agent,0,',
            // 'delivery.address' => 'required_if:agent,0,',
            'delivery.delivery_price' => 'required_if:requisition.agent,0,',
            'delivery.delivery_receipt' => 'required_if:requisition.agent,0,',

            'agent' => 'required_if:requisition.delivery,0,',
            'agent.collection_point' => [ 
                // 'exists:warehouses,id', 
                'string', 
                Rule::requiredIf($expectedRequisition->agent && $expectedRequisition->status != -1) 
            ],
        
        ],
        
        [
            'requisition.id.exists' => 'Dispatched requisition, please reload !',
        ]
        );

        $currentUser = \Auth::guard('admin')->user() ?? \Auth::guard('manager')->user() ?? \Auth::guard('warehouse')->user() ?? \Auth::guard('owner')->user() ?? Auth::user();

        if (empty($currentUser)) {

            return response()->json(['errors'=>["noUser" => "Current user missing, please reload the page"]], 422);
            
        }

        if ($expectedRequisition->status==-1) {

            return response()->json(['errors'=>["approvedOrCancelled" => "Cancelled requisition, please refresh"]], 422);

        }
        else if ($expectedRequisition->status == 0) {        // to dispatch
           
            $error = $this->validateRequiredProducts(json_decode(json_encode($request->requisition['products'])));

            if (! empty($error)) {
                
                return $error;

            }

            $userHasApprovingPermission = $currentUser->hasPermissionTo('approve-dispatch');

            $requisitionDispatch = new Dispatch();
            // $requisitionDispatch->released_at = now();
            $requisitionDispatch->has_approval = $userHasApprovingPermission ? 1 : 0;
            $requisitionDispatch->updater_type = $userHasApprovingPermission ? get_class($currentUser) : NULL;
            $requisitionDispatch->updater_id = $userHasApprovingPermission ? $currentUser->id : NULL;
            $requisitionDispatch->updated_at = $userHasApprovingPermission ? now() : NULL;
            $requisitionDispatch->requisition_id = $request->requisition['id'];
            $requisitionDispatch->save();

            $requisitionDispatch->requisition()->update([
                'status' => true,
                'updater_type' => get_class($currentUser),
                'updater_id' => $currentUser->id,
            ]);

            if ($requisitionDispatch->has_approval == 1) {
           
                $requisitionDispatch->dispatch_products = json_decode(json_encode($request->requisition['products']));

            }

        }
        else if ($expectedRequisition->status == 1) {   // to approve
            
            $requisitionDispatch = $expectedRequisition->dispatch;

            if ($requisitionDispatch->has_approval) { // 1 / -1
               
                return response()->json(['errors'=>["approvedOrCancelled" => "Approved/Cancelled dispatch, please refresh"]], 422);

            }
       
            $requisitionDispatch->has_approval = 1;
            $requisitionDispatch->updater_type = get_class($currentUser);
            $requisitionDispatch->updater_id = $currentUser->id;
            $requisitionDispatch->updated_at = now();
            $requisitionDispatch->save();

            $requisitionDispatch->dispatch_products = json_decode(json_encode($request->requisition['products']));

        }

        if (!empty($request->delivery) && !empty($request->delivery['delivery_price']) && !empty($request->delivery['delivery_receipt'])) {
                
            $requisitionDispatch->deliver_requisition = json_decode(json_encode($request->delivery));

        }
        else {

            $requisitionDispatch->return()->updateOrCreate(
                [ 'dispatch_id' => $requisitionDispatch->id ],
                [
                    'collection_point' => $request->agent['collection_point'], 
                    'receiving_confirmation' => false, 
                ]
            );

        }
        
        if ($requisitionDispatch->has_approval == 1) {
            
            BroadcastRequisitionDispatch::dispatch($expectedRequisition);

        }

        // return $this->showAllDispatches($perPage);
        return $this->showAllRequisitions($perPage);
    }

    /*
        public function updateDispatch(Request $request, $dispatch, $perPage)
        {
            $request->validate([
                'approved' => 'required|boolean',

                'requisition' => 'required',

                'requisition.id' => [
                    'required',
                    Rule::exists('requisitions', 'id')->where(function ($query) {
                        return $query->where('status', 1);
                    }),
                ],

                'requisition.products' => 'required|array|min:1',
                'requisition.products.*.product_id' => 'required|exists:products,id',

                'requisition.products.*.quantity' => 'required|numeric|min:1',
                'requisition.products.*.available_quantity' => 'required|numeric|min:1',

                // validate laterly
                'requisition.products.*.serials' => 'exclude_if:requisition.products.*.has_variations,1|required_if:requisition.products.*.has_serials,1|array|min:1',

                // 'agent' => 'required_if:delivery,0,',
                // 'agent.agent_receipt' => 'required_if:delivery,0,',
                
                'requisition.products.*.variations' => 'required_if:requisition.products.*.has_variations,1|array|min:1',

                'requisition.products.*.variations.*.quantity' => 'required_with:requisition.products.*.variations|numeric|min:1',
                // 'requisition.products.*.variations.*.available_quantity' => 'required|numeric|min:1',

                'requisition.products.*.variations.*.serials' => 'required_if:requisition.products.*.variations.*.has_serials,1|array|min:1',

                'delivery' => 'required_if:requisition.agent,0,',
                // 'delivery.address' => 'required_if:agent,0,',
                'delivery.delivery_price' => 'required_if:requisition.agent,0,',
                'delivery.delivery_receipt' => 'required_if:requisition.agent,0,',
            ],
            [
                'requisition.id.exists' => 'Unreviewed requisition, please reload !',
            ]
            );

            $currentUser = \Auth::guard('admin')->user() ?? \Auth::guard('manager')->user() ?? \Auth::guard('warehouse')->user() ?? \Auth::guard('owner')->user() ?? Auth::user();

            if (empty($currentUser)) {

                return response()->json(['errors'=>["noUser" => "Current user missing, please reload the page"]], 422);
                
            }

            $dispatchToUpdate = Dispatch::findOrFail($dispatch);

            if ($dispatchToUpdate->has_approval) { // 1 / -1
               
                return response()->json(['errors'=>["approvedOrCancelled" => "Already approved or cancelled dispatch"]], 422);

            }
       
            $dispatchToUpdate->has_approval = $request->approved ? 1 : -1;
            $dispatchToUpdate->updater_type = get_class($currentUser);
            $dispatchToUpdate->updater_id = $currentUser->id;
            $dispatchToUpdate->updated_at = now();
            $dispatchToUpdate->save();

            if ($dispatchToUpdate->has_approval==1) {
            
                $dispatchToUpdate->dispatch_products = json_decode(json_encode($request->requisition['products']));

                if (!empty($request->delivery) && !empty($request->delivery['delivery_price']) && !empty($request->delivery['delivery_receipt'])) {
                    
                    $dispatchToUpdate->deliver_requisition = json_decode(json_encode($request->delivery));

                }
                else {

                    $dispatchToUpdate->return()->updateOrCreate(
                        [ 'dispatch_id' => $dispatchToUpdate->id ],
                        [
                            'receiving_confirmation' => false,
                        ]
                    );

                }
            
            }
            else if($dispatchToUpdate->has_approval==-1) {  // if cancelled

                $dispatchToUpdate->delivery()->delete();
                $dispatchToUpdate->return()->delete();
                $dispatchToUpdate->cancel_product_requisition = json_decode(json_encode($request->requisition['products']));

            }

            BroadcastRequisitionDispatch::dispatch($dispatchToUpdate->requisition);

            // return $this->showAllDispatches($perPage);
            return $this->showAllRequisitions($perPage);
        }
    */

    public function searchAllDispatches($search, $perPage)
    {
        $query = Requisition::with(['products.product', 'products.variations.productVariation', 'delivery', 'agent', 'dispatch.delivery', 'dispatch.return'])
                                ->where('status', 1)
                                ->where(function ($q) use ($search) {
                                    $q->where('subject', 'like', "%$search%")
                                      ->orWhere('description', 'like', "%$search%")
                                      ->orWhereHas('updater', function ($query) {
                                            $query->where('user_name', 'like', "%$search%");
                                        });
                                });

        return [
            'all' => new RequisitionCollection($query->paginate($perPage)),
        ];
    }

    protected function validateRequiredProducts($products = [])
    {
        foreach ($products as $productKey => $productToDispatch) {

            $expectedMerchantProduct = MerchantProduct::find($productToDispatch->merchant_product_id);

            $productAvailableQuantity = $expectedMerchantProduct->latestStock->available_quantity ?? 0;

            if ($productAvailableQuantity < $productToDispatch->quantity) {
                
                return response()->json(['errors'=>["productSerialError" => ucfirst($expectedMerchantProduct->product->name)." quantity is more than available"]], 422);

            }

            else if ($expectedMerchantProduct->product->has_serials && ! $expectedMerchantProduct->product->has_variations && count($productToDispatch->serials) != $productToDispatch->quantity) {
                
                return response()->json(['errors'=>["productSerialError" => ucfirst($expectedMerchantProduct->product->name)." serial is more or less than required"]], 422);

            }

            else if ($expectedMerchantProduct->product->has_serials && ! $expectedMerchantProduct->product->has_variations && count($productToDispatch->serials) == $productToDispatch->quantity) {
                                     
                foreach ($productToDispatch->serials as $productSerialToDispatch) {
                    
                    $productSerial = RequiredProductSerial::where('id', $productSerialToDispatch->id)->whereHas('serial', function($q){
                        $q->where('has_dispatched', false);
                    })->exists();

                    if (! $productSerial) {
                        
                        return response()->json(['errors'=>["productSerialError" => ucfirst($expectedMerchantProduct->product->name)." serial is already dispatched or not found"]], 422);

                    }

                }

            }

            if ($expectedMerchantProduct->product->has_variations && $productToDispatch->has_variations) {
                
                foreach ($productToDispatch->variations as $variationToDispatch) {
            
                    $merchantProductExpectedVariation = $expectedMerchantProduct->variations()->where('id', $variationToDispatch->merchant_product_variation_id)->first();

                    $variationAvailableQuantity = $merchantProductExpectedVariation->latestStock->available_quantity ?? 0;

                    if ($variationToDispatch->quantity > $variationAvailableQuantity) {
                        
                        return response()->json(['errors'=>["variationSerialError" => ucfirst($variationToDispatch->variation_name)." quantity is more than available"]], 422);

                    }

                    else if ($variationToDispatch->has_serials && count($variationToDispatch->serials) != $variationToDispatch->quantity) {
                        
                        return response()->json(['errors'=>["variationSerialError" => ucfirst($variationToDispatch->variation_name)." serial is more or less than required"]], 422);

                    }
                    else if ($variationToDispatch->has_serials && count($variationToDispatch->serials)==$variationToDispatch->quantity) {
                         
                        foreach ($variationToDispatch->serials as $variationSerialToDispatch) {

                            $productVariationSerial = RequiredProductVariationSerial::where('id', $variationSerialToDispatch->id)->whereHas('serial', function($q){
                                $q->where('has_dispatched', false);
                            })->exists();

                            if (! $productVariationSerial) {
                                
                                return response()->json(['errors'=>["variationSerialError" => ucfirst($variationToDispatch->variation_name)." serial is already dispatched or not found"]], 422);

                            }

                        }

                    } 

                }

            }

        }
    }

}
