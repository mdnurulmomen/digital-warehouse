<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use Illuminate\Http\Request;
use App\Http\Resources\Web\WarehouseCollection;

class OwnerController extends Controller
{
    // Products
    public function currentOwner()
    {
        if (\Auth::guard('owner')->check()) {
            // The Owner is logged in...
            return response()->json([

                'user' => \Auth::guard('owner')->user(),

            ], 200);
        }

    }

    public function showOwnerAllWarehouses($perPage)
    {
        $currentOwner = \Auth::guard('owner')->user();

        return [

            'approved' => new WarehouseCollection(Warehouse::where('active', 1)
            											 ->where('warehouse_owner_id', $currentOwner->id)
            											 ->paginate($perPage)),
            'pending' => new WarehouseCollection(Warehouse::where('active', 0)
            											->where('warehouse_owner_id', $currentOwner->id)
            											->paginate($perPage)),

        ];
    }

    public function searchOwnerAllWarehouses($search, $perPage)
    {
        $currentOwner = \Auth::guard('owner')->user();

        $query = Warehouse::where(function ($q) use ($search) {
                    $q->where('name', 'like', "%$search%")
                      ->orWhere('user_name', 'like', "%$search%")
                      ->orWhere('email', 'like', "%$search%")
                      ->orWhere('mobile', 'like', "%$search%")
                      ->orWhere('warehouse_deal', 'like', "%$search%");
                    })
                    ->where(function ($q) use ($currentOwner) {
                        $q->where('warehouse_owner_id', $currentOwner->id);
                });        

        return response()->json([
            'all' => new WarehouseCollection($query->paginate($perPage)),    
        ], 200);
    }
}
