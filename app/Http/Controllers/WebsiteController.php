<?php

namespace App\Http\Controllers;

use Storage;
use App\Models\Merchant;
use App\Models\Applicant;
use App\Models\JobApplicant;
use App\Models\ContactQuery;
use Illuminate\Http\Request;
use App\Models\WarehouseOwner;
use App\Models\ApplicantResume;
use Illuminate\Validation\Rule;
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
            'mobile' => 'required|regex:/(01)[0-9]{9}/',
            'subject' => 'required_without:last_name|string|max:255',
            'message' => 'required|string|max:65500'
        ]);

        $newQuery = new ContactQuery();

        $newQuery->first_name = strtolower($request->first_name);
        $newQuery->last_name = strtolower($request->last_name);
        $newQuery->email = strtolower($request->email);
        $newQuery->mobile = $request->mobile;
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
            'company_name' => 'nullable|string|max:100',
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
        $newOwner->company_name = strtolower($request->company_name);
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
            'company_name' => 'nullable|string|max:100',
            'user_name' => 'required|string|max:100|unique:merchants,user_name',
            'email' => 'required|string|max:100|unique:merchants,email',
            'mobile' => 'required|string|max:50|unique:merchants,mobile',
            'password' => 'required|string|max:255|confirmed',
            'warehouse_id' => 'nullable|numeric|exists:warehouses,id',
            'container_type_id' => 'required|numeric|exists:container_types,id',
            'container_id' => 'nullable|numeric|exists:containers,id',
            'quantity' => 'required|numeric|min:1'
        ]);

        $newMerchant = new Merchant();

        $newMerchant->first_name = strtolower($request->first_name);
        $newMerchant->last_name = strtolower($request->last_name);
        $newMerchant->company_name = strtolower($request->company_name);
        $newMerchant->user_name = strtolower($request->user_name);
        $newMerchant->email = strtolower($request->email);
        $newMerchant->mobile = $request->mobile;
        $newMerchant->password = Hash::make($request->password);
        
        $newMerchant->save();

        $newMerchant->requirements()->create([
            'warehouse_id' =>  $request->warehouse_id, 
            'container_type_id' =>  $request->container_type_id, 
            'container_id' =>  $request->container_id, 
            'quantity' =>  $request->quantity, 
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

    public function submitQuotation(Request $request)
    {
        $request->validate([
            'first_name' => 'required_without:last_name|string|max:100',
            'last_name' => 'required_without:first_name|string|max:100',
            'company_name' => 'nullable|string|max:100',
            'email' => 'required|string|max:100',
            'mobile' => 'required|string|max:50',
            // 'user_name' => 'required|string|max:100|unique:merchants,user_name',
            // 'password' => 'required|string|max:255|confirmed',
            'industry' => 'required|string|max:255',
            'warehouse_id' => 'required|numeric|exists:warehouses,id',
            'container_type_id' => 'required|numeric|exists:container_types,id',
            'container_id' => 'nullable|numeric|exists:containers,id',
            'quantity' => 'required|numeric',
            'message' => 'nullable|string|max:6500'
        ]);

        $merchant = Merchant::where('user_name', strtolower(str_replace(" ","", ($request->first_name.$request->last_name))))
                    ->orWhere('email', strtolower($request->email))
                    ->orWhere('mobile', $request->mobile)
                    ->firstOr(function () use ($request) { 
                        return Merchant::create([
                            'first_name' => strtolower($request->first_name),
                            'last_name' => strtolower($request->last_name),
                            'company_name' => strtolower($request->company_name),
                            'user_name' => strtolower(str_replace(" ","", ($request->first_name.$request->last_name))),
                            'email' => strtolower($request->email),
                            'mobile' => $request->mobile,
                            'password' => Hash::make('12345678'),
                        ]);
                    });

        $requirement = $merchant->requirements()->firstOrCreate(
            [
                'warehouse_id' => $request->warehouse_id,
                'industry' => strtolower($request->industry),
                'container_type_id' =>  $request->container_type_id, 
                'container_id' =>  $request->container_id, 
                'quantity' =>  $request->quantity, 
            ],
            [
                'message' => strtolower($request->message),
            ]
        );

        return response()->json([
            'name' => ucfirst($merchant->first_name).' '.ucfirst($merchant->last_name),
        ], 200);
    }

    public function submitJobApplication(Request $request)
    {
        $request->validate([
            'first_name' => 'required_without:last_name|string|max:100',
            'last_name' => 'required_without:first_name|string|max:100',
            'email' => 'required|email|max:100',
            'mobile' => 'required|string|max:50|regex:/(01)[0-9]{9}/',
            'address' => 'nullable|string|max:255',
            'educational_highest_level' => 'nullable|string',
            'resume' => 'required|file|mimes:doc,pdf,docx|max:2048',
            'job_id' => [
                'required', 'numeric', 
                // Rule::exists('jobs', 'id')                     
                // ->where('status', 1),      
            ]
        ]);

        $newApplicant = Applicant::where('email', strtolower($request->email))
        ->where('mobile', $request->mobile)
        ->firstOr(function () use ($request) { 
            return Applicant::create([
                'first_name' => strtolower($request->first_name),
                'last_name' => strtolower($request->last_name),
                'email' => strtolower($request->email),
                'mobile' => $request->mobile,
                'address' => $request->address,
                'educational_highest_level' => $request->educational_highest_level
            ]);
        });

        if(Storage::disk('public')->exists($newApplicant->resume->path)){
            
            Storage::disk('public')->delete($newApplicant->resume->path);

        }

        $file = $request->file('resume');
        
        $path = 'applicants/';
        $name = $newApplicant->id.'.'.$file->extension();
        
        $file->storePubliclyAs($path, $name, 'public');

        ApplicantResume::updateOrCreate(
            [
                'applicant_id' => $newApplicant->id
            ],
            [
                'path' => ($path.$name)
            ]
        );

        JobApplicant::updateOrCreate(
            [ 
                'applicant_id' => $newApplicant->id, 
                'job_id' => $request->job_id 
            ], 
            [
                'created_at' => now(), 
                'updated_at' => now() 
            ]
        );

        return response()->json([
            'name' => ucfirst($newApplicant->first_name).' '.ucfirst($newApplicant->last_name),
        ], 200);
    }
}
