<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WarhouseOwner;
use Illuminate\Support\Facades\Hash;

class WarhouseController extends Controller
{
    public function showAllOwners($perPage)
    {
    	return response()->json([

    		'approved' => WarhouseOwner::where('active', 1)->paginate($perPage),
            'pending' => WarhouseOwner::where('active', 0)->paginate($perPage),
    		'trashed' => WarhouseOwner::onlyTrashed()->paginate($perPage),

    	], 200);
    }

    public function storeNewOwner(Request $request, $perPage)
    {
    	$request->validate([
            'first_name' => 'nullable|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'user_name' => 'required|string|max:100|unique:warhouse_owners,user_name',
            'email' => 'required|string|max:100|unique:warhouse_owners,email',
            'mobile' => 'required|string|max:50|unique:warhouse_owners,mobile',
            'password' => 'required|string|max:255|confirmed',
            'active' => 'required|boolean',
        ]);

        $newUser = new WarhouseOwner();

        $newUser->first_name = $request->first_name;
        $newUser->last_name = $request->last_name;
        $newUser->user_name = $request->user_name;
        $newUser->email = $request->email;
        $newUser->mobile = $request->mobile;
        $newUser->password = Hash::make($request->password);
        $newUser->active = $request->active;

        $newUser->save();

        $newProfilePicture = json_decode(json_encode($request->profile_preview));
        
        if ($newProfilePicture->preview) {

            $newUser->profile_preview = $newProfilePicture->preview;
            $newUser->save();
            
        }

        return $this->showAllOwners($perPage);
    }

    public function updateOwner(Request $request, $owner, $perPage)
    {
    	$userToUpdate = WarhouseOwner::findOrFail($owner);

        $request->validate([
            'first_name' => 'nullable|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'user_name' => 'required|string|max:100|unique:warhouse_owners,user_name,'.$userToUpdate->id,
            'email' => 'required|string|max:100|unique:warhouse_owners,email,'.$userToUpdate->id,
            'mobile' => 'required|string|max:50|unique:warhouse_owners,mobile,'.$userToUpdate->id,
            'password' => 'nullable|string|max:255|confirmed',
            'active' => 'required|boolean',
        ]);

        $userToUpdate->first_name = $request->first_name;
        $userToUpdate->last_name = $request->last_name;
        $userToUpdate->user_name = $request->user_name;
        $userToUpdate->email = $request->email;
        $userToUpdate->mobile = $request->mobile;
        $userToUpdate->profile_preview = json_decode(json_encode($request->profile_preview))->preview;
        $userToUpdate->active = $request->active;

        if ($request->password) {
            $userToUpdate->password = Hash::make($request->password);
        }

        $userToUpdate->save();

        return $this->showAllOwners($perPage);
    }

    public function deleteOwner($owner, $perPage)
    {
    	$userToDelete = WarhouseOwner::findOrFail($owner);
        // $userToDelete->warhouses()->delete();
        $userToDelete->delete();

        return $this->showAllOwners($perPage);
    }

    public function restoreOwner($owner, $perPage)
    {
    	$userToRestore = WarhouseOwner::withTrashed()->findOrFail($owner);
        // $userToRestore->warhouses()->restore();
        $userToRestore->restore();

        return $this->showAllOwners($perPage);
    }

    public function searchAllOwners($search, $perPage)
    {
        $columnsToSearch = ['first_name', 'last_name', 'user_name', 'mobile', 'email'];

        $query = WarhouseOwner::withTrashed();

        foreach($columnsToSearch as $column){
            $query->orWhere($column, 'like', "%$search%");
        }

        $query->orWhereHas('warhouses', function($q) use ($search){
            $q->where('name', 'like', "%$search%")
              ->orWhere('warhouse_code', 'like', "%$search%");
        });

        return response()->json([
            'all' => $query->paginate($perPage),    
        ], 200);
    }
}
