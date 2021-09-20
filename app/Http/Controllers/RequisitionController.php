<?php

namespace App\Http\Controllers;

use App\Models\Requisition;
use Illuminate\Http\Request;
use App\Http\Resources\Web\RequisitionResource;
use App\Http\Resources\Web\RequisitionCollection;

class RequisitionController extends Controller
{
    public function __construct()
    {
        $this->middleware("permission:view-requisition-index")->only(['showAllRequisitions', 'searchAllRequisitions']);
        $this->middleware("permission:update-requisition")->only('cancelRequisition');
    }

    // Requisition (Admin RequisitionIndex)
    public function showAllRequisitions($perPage = false)
    {       
        if ($perPage) {

            return [

                'pending' => new RequisitionCollection(Requisition::with(['updater', 'products.merchantProduct', 'products.merchantProduct.variations.productVariation', 'delivery', 'agent'])->where('status', 0)->paginate($perPage)),  
                'dispatched' => new RequisitionCollection(Requisition::with(['updater', 'products.merchantProduct', 'products.merchantProduct.variations.productVariation', 'delivery', 'agent', 'dispatch.delivery', 'dispatch.return', 'cancellation'])->where('status', 1)->paginate($perPage)),  
                'cancelled' => new RequisitionCollection(Requisition::with(['updater', 'products.merchantProduct', 'products.merchantProduct.variations.productVariation', 'delivery', 'agent', 'cancellation'])->where('status', -1)->paginate($perPage)),  
            
            ];

        }

        return RequisitionResource::collection(Requisition::with(['products.merchantProduct', 'products.merchantProduct.variations.productVariation', 'delivery', 'agent'])->where('status', 0)->get());

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
            'search' => 'required|string', 
            'dateTo' => 'nullable|date',
            'dateFrom' => 'nullable|date',
            // 'showPendingRequisitions' => 'nullable|boolean',
            // 'showCancelledRequisitions' => 'nullable|boolean',
            // 'showDispatchedRequisitions' => 'nullable|boolean',
            // 'showProduct' => 'nullable|string', 
        ]);        

        $query = Requisition::with(['products.merchantProduct.product', 'products.variations.merchantProductVariation.productVariation', 'delivery', 'agent', 'dispatch.delivery', 'dispatch.return'])
                ->where(function ($query1) use ($request) {
                    $query1->where('subject', 'like', "%$request->search%")
                            ->orWhere('description', 'like', "%$request->search%")
                            ->orWhereHas('products.merchantProduct.product', function ($q) use ($request) {
                                $q->where('name', 'like', "%$request->search%");
                            });
                });

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


        if ($request->dateFrom) {
            
            $query->where('created_at', '>', $request->dateFrom);

        }

        if ($request->dateTo) {
            
            $query->where('created_at', '<', $request->dateTo);

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
}
