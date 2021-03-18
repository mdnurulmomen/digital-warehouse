<?php

namespace App\Http\Controllers;

use App\Models\Manager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ManagerController extends Controller
{
    public function __construct()
    {
        $currentGuard = 'merchant';

        if (Auth::guard('admin')->check()) {
            $currentGuard = 'admin';
        }
        else if (Auth::guard('manager')->check()) {
            $currentGuard = 'manager';
        }
        else if (Auth::guard('warehouse')->check()) {
            $currentGuard = 'warehouse';
        }
        else if (Auth::guard('owner')->check()) {
            $currentGuard = 'owner';
        }

        $this->middleware("permission:$currentGuard,show-manager-index")->only(['showAllManagers', 'searchAllManagers']);
        $this->middleware("permission:$currentGuard,create-manager")->only('storeNewManager');
        $this->middleware("permission:$currentGuard,update-manager")->only('updateManager');
        $this->middleware("permission:$currentGuard,delete-manager")->only(['deleteManager', 'restoreManager']);
    }

    public function showAllManagers($perPage)
    {
    	return response()->json([

    		'approved' => Manager::where('active', 1)->paginate($perPage),
            'pending' => Manager::where('active', 0)->paginate($perPage),
    		'trashed' => Manager::onlyTrashed()->paginate($perPage),

    	], 200);
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

        $query = Manager::withTrashed();

        foreach($columnsToSearch as $column){
            $query->orWhere($column, 'like', "%$search%");
        }

        $query->orWhereHas('warehouse.warehouse', function($q) use ($search){
            $q->where('name', 'like', "%$search%")
              ->orWhere('code', 'like', "%$search%");
        });

        return response()->json([
            'all' => $query->paginate($perPage),    
        ], 200);
    }
}
