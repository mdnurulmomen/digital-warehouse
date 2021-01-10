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

    public function showOwnerAllWarehouses($owner, $perPage)
    {
        return [

            'approved' => new WarehouseCollection(Warehouse::where('active', 1)
            											 ->where('warehouse_owner_id', $owner)
            											 ->paginate($perPage)),
            'pending' => new WarehouseCollection(Warehouse::where('active', 0)
            											->where('warehouse_owner_id', $owner)
            											->paginate($perPage)),

        ];
    }

    public function searchOwnerAllWarehouses($owner, $search, $perPage)
    {
        $columnsToSearch = ['user_name', 'email', 'mobile', 'warehouse_deal'];

        $query = Warehouse::where('name', "%$search%");

        foreach($columnsToSearch as $column){
            $query->orWhere($column, 'like', "%$search%");
        }

        $query->orWhereHas('owner', function($q) use ($search){
            $q->where('first_name', 'like', "%$search%")
              ->orWhere('last_name', 'like', "%$search%")
              ->orWhere('user_name', 'like', "%$search%")
              ->orWhere('email', 'like', "%$search%")
              ->orWhere('mobile', 'like', "%$search%");
        });

        $query->where('warehouse_owner_id', $owner);

        return response()->json([
            'all' => new WarehouseCollection($query->paginate($perPage)),    
        ], 200);
    }
}
