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

                'pending' => new RequisitionCollection(Requisition::with(['products.product', 'products.variations.productVariation', 'delivery', 'agent'])->where('status', 0)->paginate($perPage)),  
                'dispatched' => new RequisitionCollection(Requisition::with(['products.product', 'products.variations.productVariation', 'delivery', 'agent', 'dispatch.delivery', 'dispatch.return'])->where('status', 1)->paginate($perPage)),  
                'cancelled' => new RequisitionCollection(Requisition::with(['products.product', 'products.variations.productVariation', 'delivery', 'agent', 'dispatch.delivery', 'dispatch.return'])->where('status', -1)->paginate($perPage)),  
            
            ];

        }

        return RequisitionResource::collection(Requisition::with(['products.product', 'products.variations.productVariation', 'delivery', 'agent'])->where('status', 0)->get());

    }

    public function cancelRequisition($requisition, $perPage)
    {
        $expectedRequisition = Requisition::findOrFail($requisition);

        $expectedRequisition->update([
            'status' => -1,
        ]);

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
