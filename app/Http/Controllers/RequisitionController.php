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

    // Requisition (Admin)
    public function showAllRequisitions($perPage = false)
    {       
        if ($perPage) {

            return [

                'pending' => new RequisitionCollection(Requisition::with(['updater', 'products.merchantProduct', 'products.variations.productVariation', 'delivery', 'agent'])->where('status', 0)->paginate($perPage)),  
                'dispatched' => new RequisitionCollection(Requisition::with(['updater', 'products.merchantProduct', 'products.variations.productVariation', 'delivery', 'agent', 'dispatch.delivery', 'dispatch.return', 'cancellation'])->where('status', 1)->paginate($perPage)),  
                'cancelled' => new RequisitionCollection(Requisition::with(['updater', 'products.merchantProduct', 'products.variations.productVariation', 'delivery', 'agent', 'cancellation'])->where('status', -1)->paginate($perPage)),  
            
            ];

        }

        return RequisitionResource::collection(Requisition::with(['products.merchantProduct', 'products.variations.productVariation', 'delivery', 'agent'])->where('status', 0)->get());

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

    public function searchAllRequisitions($search, $perPage)
    {
        $query = $query = Requisition::with(['products.product', 'products.variations.productVariation', 'delivery', 'agent', 'dispatch.delivery', 'dispatch.return'])
                                ->where(function ($query) use ($search) {
                                    $query->where('subject', 'like', "%$search%")
                                            ->orWhere('description', 'like', "%$search%")
                                            ->orWhereHas('products.product', function ($q) use ($search) {
                                                $q->where('name', 'like', "%$search%");
                                            });
                                });

        return [
            'all' => new RequisitionCollection($query->paginate($perPage)),  
        ];
    }
}
