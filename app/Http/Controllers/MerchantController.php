<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Merchant;
use App\Models\Requisition;
use App\Models\ProductStock;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\RequisitionAgent;
use Illuminate\Support\Facades\Hash;
use App\Jobs\BroadcastNewRequisition;
use App\Http\Resources\Web\MyProductResource;
use App\Http\Resources\Web\MyProductCollection;
use App\Jobs\BroadcastProductReceiveConfirmation;
use App\Http\Resources\Web\MyRequisitionCollection;

class MerchantController extends Controller
{
    public function __construct()
    {
        $this->middleware("permission:view-merchant-index")->only(['showAllMerchants', 'searchAllMerchants']);
        $this->middleware("permission:create-merchant")->only('storeNewMerchant');
        $this->middleware("permission:update-merchant")->only('updateMerchant');
        $this->middleware("permission:delete-merchant")->only(['deleteMerchant', 'restoreMerchant']);
    }

    public function showAllMerchants($perPage = false)
    {
    	if ($perPage) {
            
            return response()->json([

        		'approved' => Merchant::with(['roles', 'permissions'])->where('active', 1)->paginate($perPage),
                'pending' => Merchant::with(['roles', 'permissions'])->where('active', 0)->paginate($perPage),
        		'trashed' => Merchant::with(['roles', 'permissions'])->onlyTrashed()->paginate($perPage),

        	], 200);

        }

        return Merchant::all();
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

        $newUser->user_permissions = json_decode(json_encode($request->permissions));
        $newUser->user_roles = json_decode(json_encode($request->roles));

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

        $userToUpdate->user_permissions = json_decode(json_encode($request->permissions));
        $userToUpdate->user_roles = json_decode(json_encode($request->roles));

        $userToUpdate->save();

        return $this->showAllMerchants($perPage);
    }

    public function deleteMerchant($owner, $perPage)
    {
    	$userToDelete = Merchant::findOrFail($owner);
        // $userToDelete->warehouses()->delete();
        $userToDelete->delete();

        return $this->showAllMerchants($perPage);
    }

    public function restoreMerchant($owner, $perPage)
    {
    	$userToRestore = Merchant::withTrashed()->findOrFail($owner);
        // $userToRestore->warehouses()->restore();
        $userToRestore->restore();

        return $this->showAllMerchants($perPage);
    }

    public function searchAllMerchants($search, $perPage)
    {
        $columnsToSearch = ['first_name', 'last_name', 'user_name', 'mobile', 'email'];

        $query = Merchant::with(['roles', 'permissions'])->withTrashed();

        foreach($columnsToSearch as $column){
            $query->orWhere($column, 'like', "%$search%");
        }

        return response()->json([
            'all' => $query->paginate($perPage),    
        ], 200);
    }

    // Current User
    public function currentMerchant()
    {
        if (\Auth::check()) {
            // The merchant is logged in...
            return response()->json([

                'user' => \Auth::user(),

            ], 200);
        }
    }

    public function showMerchantAllProducts($perPage=false)
    {
        $currentMerchant = \Auth::user();
        
        if ($perPage) {
            
            return [

                'retail' => new MyProductCollection(Product::where('product_category_id', '>', 0)
                                                         ->where('merchant_id', $currentMerchant->id)
                                                         ->paginate($perPage)),
                'bulk' => new MyProductCollection(Product::where(function ($query) {
                                                            $query->whereNull('product_category_id')
                                                                  ->orWhere('product_category_id', 0);
                                                        })
                                                        ->where(function ($query) use ($currentMerchant) {
                                                            $query->where('merchant_id', $currentMerchant->id);
                                                        })
                                                       ->paginate($perPage)),
            ];

        }

        /*
            return MyProductResource::collection(
                Product::where('merchant_id', $currentMerchant->id)
                    ->where(
                        ProductStock::select('available_quantity')
                            ->whereColumn('product_stocks.product_id', 'products.id')
                            ->latest()
                            ->take(1), '>', 0
                    )
                    ->get()
            );
        */
       
        return MyProductResource::collection(
            Product::where('merchant_id', $currentMerchant->id)->whereHas('latestStock', function ($query) {
                $query->where('available_quantity', '>', 0);
            })->get()
        );

    }

    public function searchMerchantAllProducts($search, $perPage)
    {
        $currentMerchant = \Auth::user();

        $query = $query = Product::where(function ($query) use ($search) {
                    $query->where('name', 'like', "%$search%")
                            ->orWhere('sku', 'like', "%$search%")
                            ->orWhere('price', 'like', "%$search%")
                            ->orWhere('quantity_type', 'like', "%$search%")
                            ->orWhereHas('category', function ($q) use ($search) {
                                $q->where('name', 'like', "%$search%");
                            });
                })
                ->where(function ($query) use ($currentMerchant) {
                    $query->where('merchant_id', $currentMerchant->id);
                });

        return [
            'all' => new MyProductCollection($query->paginate($perPage)),  
        ];
    }

    public function showMerchantAllAgents()
    {
        $currentMerchant = \Auth::user();

        return RequisitionAgent::whereHas('requisition', function ($query) use ($currentMerchant) {
            $query->where('merchant_id', $currentMerchant->id);
        })->get()->unique('name');
    }

    // Requisition
    public function showMerchantAllRequisitions($perPage = false)
    {
        $currentMerchant = \Auth::user();
            
        if ($perPage) {

            return [

                'pending' => new MyRequisitionCollection(Requisition::with(['products.product', 'products.variations.productVariation', 'delivery', 'agent'])->where('status', 0)->where('merchant_id', $currentMerchant->id)->paginate($perPage)),  
                'dispatched' => new MyRequisitionCollection(Requisition::with(['products.product', 'products.variations.productVariation', 'delivery', 'agent', 'dispatch.delivery', 'dispatch.return'])->where('status', 1)->where('merchant_id', $currentMerchant->id)->paginate($perPage)),  
                'cancelled' => new MyRequisitionCollection(Requisition::with(['products.product', 'products.variations.productVariation', 'delivery', 'agent', 'dispatch.delivery', 'dispatch.return'])->where('status', -1)->where('merchant_id', $currentMerchant->id)->paginate($perPage)),  
            
            ];

        }

        return Requisition::with('products.product')->where('merchant_id', $currentMerchant->id)->get();

    }

    public function makeNewRequisition(Request $request, $perPage)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'products' => 'required|array|min:1',
            'products.*.id' => 'required|numeric|exists:products,id',
            'products.*.total_quantity' => 'required|numeric|min:1',
            'products.*.product' => 'required',

            'products.*.serials' => 'exclude_if:products.*.product.has_variations,true|required_if:products.*.product.has_serials,true|array',
            'products.*.serials.*' => [
                    Rule::exists('product_serials', 'serial_no')->where(function ($query) {
                        return $query->where('has_requisitions', false)->where('has_dispatched', false);
                    }),
            ],

            'products.*.product.variations' => 'required_if:products.*.product.has_variations,true|array',
            'products.*.product.variations.*.required_serials' => 'required_with:products.*.product.variations.*.serials|array',
            'products.*.product.variations.*.required_serials.*' => [
                    Rule::exists('product_variation_serials', 'serial_no')->where(function ($query) {
                        return $query->where('has_requisitions', false)->where('has_dispatched', false);
                    }),
            ],

            'delivery_service' => 'boolean',

            'agent' => 'required_if:delivery_service,false,',
            'agent.name' => 'required_if:delivery_service,false,',
            'agent.mobile' => 'required_if:delivery_service,false,',
            'agent.code' => 'required_if:delivery_service,false,|string',
            // 'agent.presence_confirmation' => 'nullable|boolean',
            'delivery' => 'required_if:delivery_service,true',
            'delivery.address' => 'required_if:delivery_service,true',
        ]);

        $currentMerchant = \Auth::user();

        $newRequisition = new Requisition();

        $newRequisition->subject = strtolower($request->subject);
        $newRequisition->description = strtolower($request->description);
        $newRequisition->merchant_id = $currentMerchant->id;

        $newRequisition->save();

        $newRequisition->required_products = json_decode(json_encode($request->products));

        if ($request->delivery_service) {
            
            $newRequisition->delivery()->create([
                'address' => $request->delivery['address'],
            ]);
        
        }

        else {

           $newRequisition->agent()->create([
                'name' => $request->agent['name'],
                'mobile' => $request->agent['mobile'],
                'code' => $request->agent['code'],
                // 'presence_confirmation' => $request->agent['presence_confirmation'] ?? false,
            ]); 

        }

        BroadcastNewRequisition::dispatch($newRequisition);

        return $this->showMerchantAllRequisitions($perPage);
    }

    public function searchMerchantAllRequisitions($search, $perPage)
    {
        $currentMerchant = \Auth::user();

        $query = Requisition::with(['products.product', 'products.variations.productVariation', 'delivery', 'agent', 'dispatch.delivery', 'dispatch.return'])->where(function ($query) use ($search) {
                                    $query->where('subject', 'like', "%$search%")
                                            ->orWhere('description', 'like', "%$search%")
                                            ->orWhereHas('products.product', function ($q) use ($search) {
                                                $q->where('name', 'like', "%$search%");
                                            });
                                })
                                ->where(function ($query) use ($currentMerchant) {
                                    $query->where('merchant_id', $currentMerchant->id);
                                });

        return [
            'all' => new MyRequisitionCollection($query->paginate($perPage)),  
        ];
    }

    public function receiveDispatchedProducts(Request $request, $perPage)
    {
        $request->validate([
            'id' => 'required|numeric|exists:requisitions,id|exists:dispatches,requisition_id',
        ]);

        $dispatchedRequisition = Requisition::findOrFail($request->id);

        if ($dispatchedRequisition->dispatch && $dispatchedRequisition->agent) {
            
            $dispatchedRequisition->dispatch->return()->update([
                'receiving_confirmation' => true,
            ]);

        }
        else if ($dispatchedRequisition->dispatch && $dispatchedRequisition->delivery) {
            
            $dispatchedRequisition->dispatch->delivery()->update([
                'receiving_confirmation' => true,
            ]);

        }

        BroadcastProductReceiveConfirmation::dispatch($dispatchedRequisition);

        return $this->showMerchantAllRequisitions($perPage);
    }

}
