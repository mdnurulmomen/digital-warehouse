<?php

namespace App\Http\Controllers;

use App\Models\Merchant;
use App\Models\ContactQuery;
use Illuminate\Http\Request;
use App\Models\WarehouseOwner;
use Illuminate\Support\Facades\Hash;

class WebsiteController extends Controller
{
    public function index()
    {
    	return view('index');
    }

    public function storeNewMessage(Request $request)
    {
        $request->validate([
            'first_name' => 'required_without:last_name|string|max:50',
            'last_name' => 'required_without:first_name|string|max:50',
            'email' => 'required|email|max:50',
            'contact_no' => 'required|regex:/(01)[0-9]{9}/',
            'subject' => 'required_without:last_name|string|max:255',
            'message' => 'required|string|max:65500'
        ]);

        $newQuery = new ContactQuery();

        $newQuery->first_name = strtolower($request->first_name);
        $newQuery->last_name = strtolower($request->last_name);
        $newQuery->email = strtolower($request->email);
        $newQuery->contact_no = $request->contact_no;
        $newQuery->subject = strtolower($request->subject);
        $newQuery->message = strtolower($request->message);

        $newQuery->save();

        return response()->json([
		    'name' => ucfirst($newQuery->first_name).' '.ucfirst($newQuery->last_name),
		], 200);
    }

    public function storeOwnerRegistrationRequest(Request $request)
    {
        $request->validate([
            'first_name' => 'required_without:last_name|string|max:100',
            'last_name' => 'required_without:first_name|string|max:100',
            'user_name' => 'required|string|max:100|unique:warehouse_owners,user_name',
            'email' => 'required|string|max:100|unique:warehouse_owners,email',
            'mobile' => 'required|string|max:50|unique:warehouse_owners,mobile',
            'password' => 'required|string|max:255|confirmed',
            'number_rentable_warehouses' => 'required|numeric|min:1',
            'available_size' => 'required|string|max:255'
        ]);

        $newOwner = new WarehouseOwner();

        $newOwner->first_name = strtolower($request->first_name);
        $newOwner->last_name = strtolower($request->last_name);
        $newOwner->user_name = strtolower($request->user_name);
        $newOwner->email = strtolower($request->email);
        $newOwner->mobile = $request->mobile;
        $newOwner->password = Hash::make($request->password);
        
        $newOwner->save();

        $newOwner->rentableWarehouse()->create([
            'number_rentable_warehouses' => $request->number_rentable_warehouses,
            'available_size' => $request->available_size
        ]);

        /*
        if (array_key_exists('preview', $request->profile_preview)) {
            $newUser->profile_preview = $request->profile_preview['preview'];
            $newUser->save();
        }
        */

        return response()->json([
            'name' => ucfirst($newOwner->first_name).' '.ucfirst($newOwner->last_name),
        ], 200);
    }

    public function storeMerchantRegistrationRequest(Request $request)
    {
        $request->validate([
            'first_name' => 'required_without:last_name|string|max:100',
            'last_name' => 'required_without:first_name|string|max:100',
            'user_name' => 'required|string|max:100|unique:merchants,user_name',
            'email' => 'required|string|max:100|unique:merchants,email',
            'mobile' => 'required|string|max:50|unique:merchants,mobile',
            'password' => 'required|string|max:255|confirmed',
            'company' => 'nullable|string|max:255',
            'required_size' => 'required|string|max:255'
        ]);

        $newMerchant = new Merchant();

        $newMerchant->first_name = strtolower($request->first_name);
        $newMerchant->last_name = strtolower($request->last_name);
        $newMerchant->user_name = strtolower($request->user_name);
        $newMerchant->email = strtolower($request->email);
        $newMerchant->mobile = $request->mobile;
        $newMerchant->password = Hash::make($request->password);
        
        $newMerchant->save();

        $newMerchant->spaceRequired()->create([
            'company' => $request->company,
            'required_size' => $request->required_size
        ]);

        /*
        if (array_key_exists('preview', $request->profile_preview)) {
            $newUser->profile_preview = $request->profile_preview['preview'];
            $newUser->save();
        }
        */

        return response()->json([
            'name' => ucfirst($newMerchant->first_name).' '.ucfirst($newMerchant->last_name),
        ], 200);
    }
}
