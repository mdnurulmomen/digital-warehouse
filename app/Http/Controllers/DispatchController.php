<?php

namespace App\Http\Controllers;

use App\Models\Dispatch;
use App\Models\Requisition;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Jobs\BroadcastRequisitionDispatch;
use App\Http\Resources\Web\RequisitionCollection;

class DispatchController extends Controller
{
    public function __construct()
    {
        $this->middleware("permission:view-dispatch-index")->only(['showAllDispatches', 'searchAllDispatches']);
        $this->middleware("permission:make-dispatch")->only('makeNewDispatch');
    }

    // Despatch (Admin)
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

            'requisition.id' => [
                'required',
                Rule::exists('requisitions', 'id')->where(function ($query) {
                    return $query->where('status', 0);
                }),
            ],

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

        ],
        [
            'requisition.id.exists' => 'Dispatched Requisition, Please Refresh !',
        ]
        );

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
