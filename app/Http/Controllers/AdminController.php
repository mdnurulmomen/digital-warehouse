<?php

namespace App\Http\Controllers;

use App\Models\Dispatch;
use App\Models\Requisition;
use Illuminate\Http\Request;
use App\Jobs\BroadcastRequisitionDispatch;
// use App\Http\Resources\Web\DispatchCollection;
use App\Http\Resources\Web\RequisitionResource;
use App\Http\Resources\Web\RequisitionCollection;


class AdminController extends Controller
{
    // Requisition
    public function showAllRequisitions($perPage = false)
    {       
        if ($perPage) {

            return [

                'pending' => new RequisitionCollection(Requisition::with(['products.product', 'products.variations.productVariation', 'delivery', 'agent'])->where('status', 0)->paginate($perPage)),  
                'dispatched' => new RequisitionCollection(Requisition::with(['products.product', 'products.variations.productVariation', 'delivery', 'agent', 'dispatch.delivery', 'dispatch.return'])->where('status', 1)->paginate($perPage)),  
            
            ];

        }

        return RequisitionResource::collection(Requisition::with(['products.product', 'products.variations.productVariation', 'delivery', 'agent'])->where('status', 0)->get());

    }

/*
    public function makeNewRequisition(Request $request, $perPage)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'products' => 'required|array|min:1',
            'products.*.id' => 'required|numeric|exists:products,id',
            'products.*.quantity' => 'required|numeric|min:1',
        ]);

        $currentMerchant = \Auth::user();

        $newRequisition = new Requisition();

        $newRequisition->subject = $request->subject;
        $newRequisition->description = $request->description;
        $newRequisition->merchant_id = $currentMerchant->id;

        $newRequisition->save();

        $newRequisition->required_products = $request->products;

        return $this->showMerchantAllRequisitions($perPage);
    }
*/

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

    // Despatch
    public function showAllDispatches($perPage = false)
    {       
        if ($perPage) {
            
            return new RequisitionCollection(Requisition::with(['products.product', 'products.variations.productVariation', 'delivery', 'agent', 'dispatch.delivery', 'dispatch.return'])->where('status', 1)->paginate($perPage));

        }
    }


    public function makeNewDispatch(Request $request, $perPage)
    {
        $request->validate([
            
            'requisition' => 'required',
            'requisition.id' => 'required|exists:requisitions,id',
            'requisition.products' => 'required|array|min:1',
            'requisition.products.*.product_id' => 'required|exists:products,id',
            'requisition.products.*.quantity' => 'required|numeric|min:1',
            'requisition.products.*.variations' => 'nullable|array|min:1',

            // 'agent' => 'required_if:delivery,0,',
            // 'agent.agent_receipt' => 'required_if:delivery,0,',
            
            'delivery' => 'required_if:requisition.agent,0,',
            // 'delivery.address' => 'required_if:agent,0,',
            'delivery.delivery_price' => 'required_if:requisition.agent,0,',
            'delivery.delivery_receipt' => 'required_if:requisition.agent,0,',

        ]);

        // $currentMerchant = \Auth::user();

        $newDispatch = new Dispatch();
        $newDispatch->requisition_id = $request->requisition['id'];
        $newDispatch->save();

        $newDispatch->dispatch_products = json_decode(json_encode($request->requisition['products']));

        if (!empty($request->delivery) && !empty($request->delivery['delivery_price']) && !empty($request->delivery['delivery_receipt'])) {
            
            $newDispatch->deliver_requisition = json_decode(json_encode($request->delivery));
        }
        else {
            $newDispatch->return()->create([
                'receiving_confirmation' => false,
            ]);
        }
        
        /*
        else if (!empty($request->agent) && !empty($request->agent['agent_receipt']))  {

            $newDispatch->return_requisition = json_decode(json_encode($request->agent));
        }
        */

        $newDispatch->requisition()->update([
            'status' => true
        ]);

        BroadcastRequisitionDispatch::dispatch($newDispatch->requisition);

        return $this->showAllDispatches($perPage);
    }


    public function searchAllDispatches($search, $perPage)
    {
        $query = Requisition::with(['products.product', 'products.variations.productVariation', 'delivery', 'agent', 'dispatch.delivery', 'dispatch.return'])
                                ->where('status', 1)
                                ->where(function ($q) use ($search) {
                                    $q->where('subject', 'like', "%$search%")
                                      ->orWhere('description', 'like', "%$search%");
                                });

        return [
            'all' => new RequisitionCollection($query->paginate($perPage)),
        ];
    }
}
