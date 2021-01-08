<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\Web\WarhouseResource;

class ProfileController extends Controller
{
	public function showAdminProfile()
	{
		return response(Auth::guard('admin')->user(), 200);
	}

	public function updateAdminProfile(Request $request)
	{
		$admin = Auth::guard('admin')->user();

		$request->validate([
            'first_name' => 'nullable|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'user_name' => 'required|string|max:100|unique:admins,user_name,'.$admin->id,
            'email' => 'required|string|max:100|unique:admins,email,'.$admin->id,
            'mobile' => 'required|string|max:50|unique:admins,mobile,'.$admin->id,
        ]);

		$admin->first_name = $request->first_name;
		$admin->last_name = $request->last_name;
		$admin->user_name = $request->user_name;
		$admin->email = $request->email;
		$admin->mobile = $request->mobile;
		$admin->profile_preview = $request->profile_preview['preview'] ?? NULL;

		$admin->save();

		return $this->showAdminProfile();
	}

	public function updateAdminPassword(Request $request)
	{
		$request->validate([
            'current_password' => 'required|string|max:255',
            'password' => 'required|string|max:255|confirmed',
        ]);

        $admin = Auth::guard('admin')->user();

		if (Hash::check($request->current_password, $admin->password)) {
		    
			$admin->update([
	            'password' => Hash::make($request->password)
	        ]);

			return response('Password has been updated', 200);
		}

		return response("Password doesn't match", 401);
	}

	// Merchant
	public function showMerchantProfile()
	{
		return response(Auth::user(), 200);
	}

	public function updateMerchantProfile(Request $request)
	{
		$merchant = Auth::guard('merchant')->user();

		$request->validate([
            'first_name' => 'nullable|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'user_name' => 'required|string|max:100|unique:merchants,user_name,'.$merchant->id,
            'email' => 'required|string|max:100|unique:merchants,email,'.$merchant->id,
            'mobile' => 'required|string|max:50|unique:merchants,mobile,'.$merchant->id,
        ]);

		$merchant->first_name = $request->first_name;
		$merchant->last_name = $request->last_name;
		$merchant->user_name = $request->user_name;
		$merchant->email = $request->email;
		$merchant->mobile = $request->mobile;
		$merchant->profile_preview = $request->profile_preview['preview'] ?? NULL;

		$merchant->save();

		return $this->showMerchantProfile();
	}

	public function updateMerchantPassword(Request $request)
	{
		$request->validate([
            'current_password' => 'required|string|max:255',
            'password' => 'required|string|max:255|confirmed',
        ]);

        $merchant = Auth::user();

		if (Hash::check($request->current_password, $merchant->password)) {
		    
			$merchant->update([
	            'password' => Hash::make($request->password)
	        ]);

			return response('Password has been updated', 200);
		}

		return response("Password doesn't match", 401);
	}

	// Owner
	public function showOwnerProfile()
	{
		return response(Auth::guard('owner')->user(), 200);
	}

	public function updateOwnerProfile(Request $request)
	{
		$owner = Auth::guard('owner')->user();

		$request->validate([
            'first_name' => 'nullable|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'user_name' => 'required|string|max:100|unique:warhouse_owners,user_name,'.$owner->id,
            'email' => 'required|string|max:100|unique:warhouse_owners,email,'.$owner->id,
            'mobile' => 'required|string|max:50|unique:warhouse_owners,mobile,'.$owner->id,
        ]);

		$owner->first_name = $request->first_name;
		$owner->last_name = $request->last_name;
		$owner->user_name = $request->user_name;
		$owner->email = $request->email;
		$owner->mobile = $request->mobile;
		$owner->profile_preview = $request->profile_preview['preview'] ?? NULL;

		$owner->save();

		return $this->showMerchantProfile();
	}

	public function updateOwnerPassword(Request $request)
	{
		$request->validate([
            'current_password' => 'required|string|max:255',
            'password' => 'required|string|max:255|confirmed',
        ]);

        $owner = Auth::guard('owner')->user();

		if (Hash::check($request->current_password, $owner->password)) {
		    
			$owner->update([
	            'password' => Hash::make($request->password)
	        ]);

			return response('Password has been updated', 200);
		}

		return response("Password doesn't match", 401);
	}

	// Warhouse
	public function showWarhouseProfile()
	{
		// return response(Auth::guard('warhouse')->user(), 200);
		return new WarhouseResource(Auth::guard('warhouse')->user());
	}

	public function updateWarhouseProfile(Request $request)
	{
		$warhouse = Auth::guard('warhouse')->user();

		$request->validate([
            'name' => 'nullable|string|max:100',
            'code' => 'required|string|max:100|unique:warhouses,code,'.$warhouse->id,
            'user_name' => 'required|string|max:100|unique:warhouses,user_name,'.$warhouse->id,
            'email' => 'required|string|max:100|unique:warhouses,email,'.$warhouse->id,
            'mobile' => 'required|string|max:50|unique:warhouses,mobile,'.$warhouse->id,
        ]);

		$warhouse->name = $request->name;
		$warhouse->code = $request->code;
		$warhouse->user_name = $request->user_name;
		$warhouse->email = $request->email;
		$warhouse->mobile = $request->mobile;
		$warhouse->map_preview = $request->site_map_preview ?? NULL;

		$warhouse->save();

		return $this->showWarhouseProfile();
	}

	public function updateWarhouseDeal(Request $request)
	{
		$warhouse = Auth::guard('warhouse')->user();

		$request->validate([
           	'warhouse_owner_id' => 'required|numeric|exists:warhouse_owners,id',
        	'warhouse_deal' => 'required|string|max:255', 
        ]);

		$warhouse->warhouse_owner_id = $request->warhouse_owner_id;
		$warhouse->warhouse_deal = $request->warhouse_deal;

		$warhouse->save();

		return $this->showWarhouseProfile();
	}

	public function updateWarhouseFeaturesAndPreviews(Request $request)
	{
		$warhouse = Auth::guard('warhouse')->user();

		$request->validate([
            'feature.features' => 'required|string',
            'previews' => 'required|array',
        ]);

        $warhouse->feature()->updateOrCreate(
            [ 'warhouse_id' => $warhouse->id ],
            [ 'features' => $request->feature['features']]
        );

        if (count($request->previews)) {
            
            $warhouse->warhouse_previews = $request->previews;

        }

		// $warhouse->save();

		return $this->showWarhouseProfile();
	}

	public function updateWarhouseStorages(Request $request)
	{
		$warhouse = Auth::guard('warhouse')->user();

		$request->validate([
            'storages' => 'required|array',
            'storages.*.feature.features' => 'required|string',
            'storages.*.previews' => 'required|array',
            'storages.*.storage_type' => 'required',
        ]);

        if (count($request->storages)) {
            
            $warhouse->warhouse_storages = $request->storages;

        }

		// $warhouse->save();

		return $this->showWarhouseProfile();
	}

	public function updateWarhouseContainers(Request $request)
	{
		$warhouse = Auth::guard('warhouse')->user();

		$request->validate([
            'containers' => 'required|array',
            'containers.*.container.id' => 'required|exists:containers,id',
            'containers.*.quantity' => 'required|integer|min:0',
            'containers.*.rents' => 'required',
        ]);

        if (count($request->containers)) {
            
            $warhouse->warhouse_containers = $request->containers;

        }

		return $this->showWarhouseProfile();
	}

	public function updateWarhousePassword(Request $request)
	{
		$request->validate([
            'current_password' => 'required|string|max:255',
            'password' => 'required|string|max:255|confirmed',
        ]);

        $warhouse = Auth::guard('warhouse')->user();

		if (Hash::check($request->current_password, $warhouse->password)) {
		    
			$warhouse->update([
	            'password' => Hash::make($request->password)
	        ]);

			return response('Password has been updated', 200);
		}

		return response("Password doesn't match", 401);
	}

}
