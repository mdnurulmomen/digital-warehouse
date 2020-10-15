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
}
