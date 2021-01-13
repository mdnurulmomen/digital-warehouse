<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Merchant;
use App\Models\Requisition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\Web\ProductResource;
use App\Http\Resources\Web\ProductCollection;

class MerchantController extends Controller
{
    public function showAllMerchants($perPage = false)
    {
    	if ($perPage) {
            
            return response()->json([

        		'approved' => Merchant::where('active', 1)->paginate($perPage),
                'pending' => Merchant::where('active', 0)->paginate($perPage),
        		'trashed' => Merchant::onlyTrashed()->paginate($perPage),

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

        $query = Merchant::withTrashed();

        foreach($columnsToSearch as $column){
            $query->orWhere($column, 'like', "%$search%");
        }

        return response()->json([
            'all' => $query->paginate($perPage),    
        ], 200);
    }

    // Products
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
            
            return response()->json([

                'retail' => new ProductCollection(Product::where('product_category_id', '>', 0)
                                                         ->where('merchant_id', $currentMerchant->id)
                                                         ->paginate($perPage)),
                'bulk' => new ProductCollection(Product::where(function ($query) {
                                                            $query->whereNull('product_category_id')
                                                                  ->orWhere('product_category_id', 0);
                                                        })
                                                        ->where(function ($query) use ($currentMerchant) {
                                                            $query->where('merchant_id', $currentMerchant->id);
                                                        })
                                                       ->paginate($perPage)),

            ], 200);

        }

        return ProductResource::collection(Product::where('merchant_id', $currentMerchant->id)->get());

    }

    public function searchMerchantAllProducts($search, $perPage)
    {
        $currentMerchant = \Auth::user();

        $query = $query = Product::where(function ($query) use ($search) {
                    $query->where('name', 'like', "%$search%")
                            ->orWhere('sku', 'like', "%$search%")
                            ->orWhere('price', 'like', "%$search%")
                            ->orWhere('initial_quantity', 'like', "%$search%")
                            ->orWhere('available_quantity', 'like', "%$search%")
                            ->orWhere('quantity_type', 'like', "%$search%")
                            ->orWhereHas('category', function ($q) use ($search) {
                                $q->where('name', 'like', "%$search%");
                            });
                })
                ->where(function ($query) use ($currentMerchant) {
                    $query->where('merchant_id', $currentMerchant->id);
                });

        return response()->json([
            'all' => new ProductCollection($query->paginate($perPage)),  
        ], 200);
    }

    // Requisition
    public function showMerchantAllRequisitions($perPage = false)
    {
        $currentMerchant = \Auth::user();
            
        if ($perPage) {

            return response()->json([

                'pending' => Requisition::with('products.product')->where('status', 0)->where('merchant_id', $currentMerchant->id)->paginate($perPage),  
                'dispatched' => Requisition::with('products.product')->where('status', 1)->where('merchant_id', $currentMerchant->id)->paginate($perPage),  
            
            ], 200);

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
            'products.*.quantity' => 'required|numeric|min:1',
        ]);

        $currentMerchant = \Auth::user();

        $newRequisition = new Requisition();

        $newRequisition->subject = $request->subject;
        $newRequisition->description = $request->description;
        $newRequisition->merchant_id = $currentMerchant->id;

        $newRequisition->save();

        $newRequisition->required_products = $request->products;

        return $this->showMerchantAllRequisitions($perPage);
    }

    public function searchMerchantAllRequisitions($search, $perPage)
    {
        $currentMerchant = \Auth::user();

        $query = $query = Requisition::with('products.product')
                                ->where(function ($query) use ($search) {
                                    $query->where('subject', 'like', "%$search%")
                                            ->orWhere('description', 'like', "%$search%")
                                            ->orWhereHas('products.product', function ($q) use ($search) {
                                                $q->where('name', 'like', "%$search%");
                                            });
                                })
                                ->where(function ($query) use ($currentMerchant) {
                                    $query->where('merchant_id', $currentMerchant->id);
                                });

        return response()->json([
            'all' => $query->paginate($perPage),  
        ], 200);
    }

}
