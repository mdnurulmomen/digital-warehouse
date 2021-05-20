<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function __construct()
    {
        // role
        $this->middleware("permission:view-role-index")->only(['showAllRoles', 'searchAllRoles']);
        $this->middleware("permission:create-role")->only('storeNewRole');
        $this->middleware("permission:update-role")->only('updateRole');
        $this->middleware("permission:delete-role")->only('deleteRole');

        // permission
        $this->middleware("permission:view-permission-index")->only(['showAllPermissions']);
    }    

    // roles
    public function showAllRoles($perPage=false)
    {
    	if ($perPage) {
            
            return response()->json([

        		'current' => Role::paginate($perPage),
        		// 'trashed' => Role::onlyTrashed()->paginate($perPage),

        	], 200);

        }

        return Role::all();
    }

    public function storeNewRole(Request $request, $perPage)
    {
    	$request->validate([
            'name' => 'required|string|max:100|unique:roles,name',
            'permissions' => 'required|array|min:1',
            'permissions.*.id' => 'required|exists:permissions,id',
            'permissions.*.name' => 'required|exists:permissions,name',
        ]);

        $newRole = new Role();
        $newRole->name = strtolower($request->name);
        $newRole->save();

        $newRole->role_permissions = json_decode(json_encode($request->permissions));

        return $this->showAllRoles($perPage);
    }

    public function updateRole(Request $request, $role, $perPage)
    {
    	$roleToUpdate = Role::findOrFail($role);

        $request->validate([
            'name' => 'required|string|max:100|unique:roles,name,'.$roleToUpdate->id,
            'permissions' => 'required|array|min:1',
            'permissions.*.id' => 'required|exists:permissions,id',
            'permissions.*.name' => 'required|exists:permissions,name',
        ]);

        $roleToUpdate->name = strtolower($request->name);
        $roleToUpdate->role_permissions = json_decode(json_encode($request->permissions));

        $roleToUpdate->save();

        return $this->showAllRoles($perPage);
    }

    public function deleteRole($role, $perPage)
    {
    	$roleToDelete = Role::findOrFail($role);
        $roleToDelete->permissions()->detach();
        $roleToDelete->delete();

        return $this->showAllRoles($perPage);
    }

/*
    public function restoreRole($role, $perPage)
    {
    	$roleToRestore = Role::withTrashed()->findOrFail($role);
        // $roleToRestore->warehouses()->restore();
        $roleToRestore->restore();

        return $this->showAllRoles($perPage);
    }
*/

    public function searchAllRoles($search, $perPage)
    {
        return response()->json([
            'all' => Role::where('name', 'like', "%$search%")->orWhereHas('permissions', function ($query) use ($search) {
                $query->where('name', 'like', "%search%");
            })->paginate($perPage),    
        ], 200);
    }

    // permissions
    public function showAllPermissions()
    {
        return Permission::all();
    }
}
