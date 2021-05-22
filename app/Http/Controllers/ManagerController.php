<?php

namespace App\Http\Controllers;

use App\Models\Manager;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\Web\WarehouseCollection;

class ManagerController extends Controller
{
    public function __construct()
    {
        $this->middleware("permission:view-manager-index")->only(['showAllManagers', 'searchAllManagers']);
        $this->middleware("permission:create-manager")->only('storeNewManager');
        $this->middleware("permission:update-manager")->only('updateManager');
        $this->middleware("permission:delete-manager")->only(['deleteManager', 'restoreManager']);
    }

    public function showAllManagers($perPage = false)
    {
    	if ($perPage) {
            
            return response()->json([

        		'approved' => Manager::with(['warehouses', 'roles', 'permissions'])->where('active', 1)->paginate($perPage),
                'pending' => Manager::with(['warehouses', 'roles', 'permissions'])->where('active', 0)->paginate($perPage),
        		'trashed' => Manager::with(['warehouses', 'roles', 'permissions'])->onlyTrashed()->paginate($perPage),

        	], 200);

        }

        return Manager::where('active', 1)->get();
    }

    public function storeNewManager(Request $request, $perPage)
    {
    	$request->validate([
            'first_name' => 'nullable|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'user_name' => 'required|string|max:100|unique:managers,user_name',
            'email' => 'required|string|max:100|unique:managers,email',
            'mobile' => 'required|string|max:50|unique:managers,mobile',
            'password' => 'required|string|max:255|confirmed',
            'active' => 'required|boolean',
        ]);

        $newUser = new Manager();

        $newUser->first_name = strtolower($request->first_name);
        $newUser->last_name = strtolower($request->last_name);
        $newUser->user_name = strtolower($request->user_name);
        $newUser->email = strtolower($request->email);
        $newUser->mobile = $request->mobile;
        $newUser->password = Hash::make($request->password);
        $newUser->active = $request->active;

        $newUser->save();

        if (array_key_exists('preview', $request->profile_preview)) {
            $newUser->profile_preview = $request->profile_preview['preview'];
            $newUser->save();
        }

        $newUser->user_permissions = json_decode(json_encode($request->permissions));
        $newUser->user_roles = json_decode(json_encode($request->roles));

        return $this->showAllManagers($perPage);
    }

    public function updateManager(Request $request, $manager, $perPage)
    {
    	$userToUpdate = Manager::findOrFail($manager);

        $request->validate([
            'first_name' => 'nullable|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'user_name' => 'required|string|max:100|unique:managers,user_name,'.$userToUpdate->id,
            'email' => 'required|string|max:100|unique:managers,email,'.$userToUpdate->id,
            'mobile' => 'required|string|max:50|unique:managers,mobile,'.$userToUpdate->id,
            'password' => 'nullable|string|max:255|confirmed',
            'active' => 'required|boolean',
        ]);

        $userToUpdate->first_name = strtolower($request->first_name);
        $userToUpdate->last_name = strtolower($request->last_name);
        $userToUpdate->user_name = strtolower($request->user_name);
        $userToUpdate->email = strtolower($request->email);
        $userToUpdate->mobile = $request->mobile;
        $userToUpdate->profile_preview = $request->profile_preview['preview'] ?? NULL;
        $userToUpdate->active = $request->active;

        if ($request->password) {
            $userToUpdate->password = Hash::make($request->password);
        }

        $userToUpdate->user_permissions = json_decode(json_encode($request->permissions));
        $userToUpdate->user_roles = json_decode(json_encode($request->roles));

        $userToUpdate->save();

        return $this->showAllManagers($perPage);
    }

    public function deleteManager($manager, $perPage)
    {
    	$userToDelete = Manager::findOrFail($manager);
        // $userToDelete->warehouse()->delete();
        $userToDelete->delete();

        return $this->showAllManagers($perPage);
    }

    public function restoreManager($manager, $perPage)
    {
        $userToRestore = Manager::withTrashed()->findOrFail($manager);
        // $userToRestore->warehouse()->restore();
        $userToRestore->restore();

        return $this->showAllManagers($perPage);
    }

    public function searchAllManagers($search, $perPage)
    {
        $columnsToSearch = ['first_name', 'last_name', 'user_name', 'mobile', 'email'];

        $query = Manager::with(['warehouses', 'roles', 'permissions'])->withTrashed();

        foreach($columnsToSearch as $column){
            $query->orWhere($column, 'like', "%$search%");
        }

        $query->orWhereHas('warehouses.warehouse', function($q) use ($search){
            $q->where('name', 'like', "%$search%")
              ->orWhere('user_name', 'like', "%$search%")
              ->orWhere('email', 'like', "%$search%")
              ->orWhere('mobile', 'like', "%$search%");
        });

        return response()->json([
            'all' => $query->paginate($perPage),    
        ], 200);
    }

    // Warehouse
    public function showManagerAllWarehouses($perPage)
    {
        $currentManager = \Auth::guard('manager')->user();

        return [

            'approved' => new WarehouseCollection(Warehouse::with(['previews', 'owner', 'feature', 'storages', 'containers', 'roles', 'permissions'])->where('active', 1)->whereHas('managers', function ($query) use ($currentManager) {
                $query->where('manager_id', $currentManager->id);
            })->paginate($perPage)),
            
            'pending' => new WarehouseCollection(Warehouse::with(['previews', 'owner', 'feature', 'storages', 'containers', 'roles', 'permissions'])->where('active', 0)->whereHas('managers', function ($query) use ($currentManager) {
                $query->where('manager_id', $currentManager->id);
            })->paginate($perPage)),
            
            'trashed' => new WarehouseCollection(Warehouse::with(['previews', 'owner', 'feature', 'storages', 'containers', 'roles', 'permissions'])->onlyTrashed()->whereHas('managers', function ($query) use ($currentManager) {
                $query->where('manager_id', $currentManager->id);
            })->paginate($perPage)),

        ];
    
    }

    public function searchManagerAllWarehouses($search, $perPage)
    {
        $currentManager = \Auth::guard('manager')->user();

        $query = Warehouse::with(['previews', 'owner', 'feature', 'storages', 'containers', 'roles', 'permissions']);

        $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%$search%")
              ->orWhere('user_name', 'like', "%$search%")
              ->orWhere('email', 'like', "%$search%")
              ->orWhere('mobile', 'like', "%$search%")
              ->orWhere('warehouse_deal', 'like', "%$search%")
              ->orWhereHas('owner', function($q2) use ($search) {
                $q2->where('first_name', 'like', "%$search%")
                  ->orWhere('last_name', 'like', "%$search%")
                  ->orWhere('user_name', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%")
                  ->orWhere('mobile', 'like', "%$search%");
                });
        });

        $query->whereHas('managers', function($q) use ($currentManager) {
            $q->where('manager_id', $currentManager->id);
        });

        return response()->json([
            'all' => new WarehouseCollection($query->paginate($perPage)),    
        ], 200);
    }
    
}
