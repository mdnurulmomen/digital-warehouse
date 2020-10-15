<?php

namespace App\Http\Controllers;

use App\Models\Merchant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class MerchantController extends Controller
{
    public function showAllMerchants($perPage)
    {
    	return response()->json([

    		'approved' => Merchant::where('active', 1)->paginate($perPage),
            'pending' => Merchant::where('active', 0)->paginate($perPage),
    		'trashed' => Merchant::onlyTrashed()->paginate($perPage),

    	], 200);
    }

    public function storeNewMerchant(Request $request, $perPage)
    {
    	$request->validate([
            'first_name' => 'nullable|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'user_name' => 'required|string|max:100|unique:merchants,user_name',
            'email' => 'required|string|max:100|unique:merchants,email',
            'mobile' => 'required|string|max:50|unique:merchants,mobile',
            'password' => 'required|string|max:255|confirmed',
            'active' => 'required|boolean',
        ]);

        $newUser = new Merchant();

        $newUser->first_name = $request->first_name;
        $newUser->last_name = $request->last_name;
        $newUser->user_name = $request->user_name;
        $newUser->email = $request->email;
        $newUser->mobile = $request->mobile;
        $newUser->password = Hash::make($request->password);
        $newUser->active = $request->active;

        $newUser->save();

        if (array_key_exists('preview', $request->profile_preview)) {
            $newUser->profile_preview = $request->profile_preview['preview'];
            $newUser->save();
        }

        return $this->showAllMerchants($perPage);
    }

    public function updateMerchant(Request $request, $owner, $perPage)
    {
    	$userToUpdate = Merchant::findOrFail($owner);

        $request->validate([
            'first_name' => 'nullable|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'user_name' => 'required|string|max:100|unique:merchants,user_name,'.$userToUpdate->id,
            'email' => 'required|string|max:100|unique:merchants,email,'.$userToUpdate->id,
            'mobile' => 'required|string|max:50|unique:merchants,mobile,'.$userToUpdate->id,
            'password' => 'nullable|string|max:255|confirmed',
            'active' => 'required|boolean',
        ]);

        $userToUpdate->first_name = $request->first_name;
        $userToUpdate->last_name = $request->last_name;
        $userToUpdate->user_name = $request->user_name;
        $userToUpdate->email = $request->email;
        $userToUpdate->mobile = $request->mobile;
        $userToUpdate->profile_preview = $request->profile_preview['preview'] ?? NULL;
        $userToUpdate->active = $request->active;

        if ($request->password) {
            $userToUpdate->password = Hash::make($request->password);
        }

        $userToUpdate->save();

        return $this->showAllMerchants($perPage);
    }

    public function deleteMerchant($owner, $perPage)
    {
    	$userToDelete = Merchant::findOrFail($owner);
        // $userToDelete->warhouses()->delete();
        $userToDelete->delete();

        return $this->showAllMerchants($perPage);
    }

    public function restoreMerchant($owner, $perPage)
    {
    	$userToRestore = Merchant::withTrashed()->findOrFail($owner);
        // $userToRestore->warhouses()->restore();
        $userToRestore->restore();

        return $this->showAllMerchants($perPage);
    }

    public function searchAllMerchants($search, $perPage)
    {
        $columnsToSearch = ['first_name', 'last_name', 'user_name', 'mobile', 'email'];

        $query = Merchant::withTrashed();

        foreach($columnsToSearch as $column){
            $query->orWhere($column, 'like', "%$search%");
        }

        return response()->json([
            'all' => $query->paginate($perPage),    
        ], 200);
    }
}
