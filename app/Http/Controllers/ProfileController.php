<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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
}
