<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\Web\WarehouseResource;

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
            'user_name' => 'required|string|max:100|unique:warehouse_owners,user_name,'.$owner->id,
            'email' => 'required|string|max:100|unique:warehouse_owners,email,'.$owner->id,
            'mobile' => 'required|string|max:50|unique:warehouse_owners,mobile,'.$owner->id,
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

	// Warehouse
	public function showWarehouseProfile()
	{
		// return response(Auth::guard('warehouse')->user(), 200);
		return new WarehouseResource(Auth::guard('warehouse')->user());
	}

	public function updateWarehouseProfile(Request $request)
	{
		$warehouse = Auth::guard('warehouse')->user();

		$request->validate([
            'name' => 'nullable|string|max:100',
            'code' => 'required|string|max:100|unique:warehouses,code,'.$warehouse->id,
            'user_name' => 'required|string|max:100|unique:warehouses,user_name,'.$warehouse->id,
            'email' => 'required|string|max:100|unique:warehouses,email,'.$warehouse->id,
            'mobile' => 'required|string|max:50|unique:warehouses,mobile,'.$warehouse->id,
        ]);

		$warehouse->name = $request->name;
		$warehouse->code = $request->code;
		$warehouse->user_name = $request->user_name;
		$warehouse->email = $request->email;
		$warehouse->mobile = $request->mobile;
		$warehouse->map_preview = $request->site_map_preview ?? NULL;

		$warehouse->save();

		return $this->showWarehouseProfile();
	}

	public function updateWarehouseDeal(Request $request)
	{
		$warehouse = Auth::guard('warehouse')->user();

		$request->validate([
           	'warehouse_owner_id' => 'required|numeric|exists:warehouse_owners,id',
        	'warehouse_deal' => 'required|string|max:255', 
        ]);

		$warehouse->warehouse_owner_id = $request->warehouse_owner_id;
		$warehouse->warehouse_deal = $request->warehouse_deal;

		$warehouse->save();

		return $this->showWarehouseProfile();
	}

	public function updateWarehouseFeaturesAndPreviews(Request $request)
	{
		$warehouse = Auth::guard('warehouse')->user();

		$request->validate([
            'feature.features' => 'required|string',
            'previews' => 'required|array',
        ]);

        $warehouse->feature()->updateOrCreate(
            [ 'warehouse_id' => $warehouse->id ],
            [ 'features' => $request->feature['features']]
        );

        if (count($request->previews)) {
            
            $warehouse->warehouse_previews = $request->previews;

        }

		// $warehouse->save();

		return $this->showWarehouseProfile();
	}

	public function updateWarehouseStorages(Request $request)
	{
		$warehouse = Auth::guard('warehouse')->user();

		$request->validate([
            'storages' => 'required|array',
            'storages.*.feature.features' => 'required|string',
            'storages.*.previews' => 'required|array',
            'storages.*.storage_type' => 'required',
        ]);

        if (count($request->storages)) {
            
            $warehouse->warehouse_storages = $request->storages;

        }

		// $warehouse->save();

		return $this->showWarehouseProfile();
	}

	public function updateWarehouseContainers(Request $request)
	{
		$warehouse = Auth::guard('warehouse')->user();

		$request->validate([
            'containers' => 'required|array',
            'containers.*.container.id' => 'required|exists:containers,id',
            'containers.*.quantity' => 'required|integer|min:0',
            'containers.*.rents' => 'required',
        ]);

        if (count($request->containers)) {
            
            $warehouse->warehouse_containers = $request->containers;

        }

		return $this->showWarehouseProfile();
	}

	public function updateWarehousePassword(Request $request)
	{
		$request->validate([
            'current_password' => 'required|string|max:255',
            'password' => 'required|string|max:255|confirmed',
        ]);

        $warehouse = Auth::guard('warehouse')->user();

		if (Hash::check($request->current_password, $warehouse->password)) {
		    
			$warehouse->update([
	            'password' => Hash::make($request->password)
	        ]);

			return response('Password has been updated', 200);
		}

		return response("Password doesn't match", 401);
	}

	// Manager
	public function showManagerProfile()
	{
		return response(Auth::guard('manager')->user(), 200);
	}

	public function updateManagerProfile(Request $request)
	{
		$manager = Auth::guard('manager')->user();

		$request->validate([
            'first_name' => 'nullable|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'user_name' => 'required|string|max:100|unique:managers,user_name,'.$manager->id,
            'email' => 'required|string|max:100|unique:managers,email,'.$manager->id,
            'mobile' => 'required|string|max:50|unique:managers,mobile,'.$manager->id,
        ]);

		$manager->first_name = $request->first_name;
		$manager->last_name = $request->last_name;
		$manager->user_name = $request->user_name;
		$manager->email = $request->email;
		$manager->mobile = $request->mobile;
		$manager->profile_preview = $request->profile_preview['preview'] ?? NULL;

		$manager->save();

		return $this->showMerchantProfile();
	}

	public function updateManagerPassword(Request $request)
	{
		$request->validate([
            'current_password' => 'required|string|max:255',
            'password' => 'required|string|max:255|confirmed',
        ]);

        $manager = Auth::guard('manager')->user();

		if (Hash::check($request->current_password, $manager->password)) {
			$manager->update([
	            'password' => Hash::make($request->password)
	        ]);

			return response('Password has been updated', 200);
		}

		return response("Password doesn't match", 401);
	}

}
