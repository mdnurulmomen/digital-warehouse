<?php

namespace App\Http\Controllers;

use App\Models\Requisition;
use Illuminate\Http\Request;
use App\Http\Resources\Web\RequisitionCollection;


class AdminController extends Controller
{
    // Requisition
    public function showAllRequisitions($perPage = false)
    {       
        if ($perPage) {

            return [

                'pending' => new RequisitionCollection(Requisition::with(['products.product', 'products.variations.productVariation', 'delivery', 'agent'])->where('status', 0)->paginate($perPage)),  
                'dispatched' => new RequisitionCollection(Requisition::with(['products.product', 'products.variations.productVariation', 'delivery', 'agent'])->where('status', 1)->paginate($perPage)),  
            
            ];

        }

        return Requisition::with('products.product')->get();

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
        $query = $query = Requisition::with(['products.product', 'products.variations.productVariation', 'delivery', 'agent'])
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
