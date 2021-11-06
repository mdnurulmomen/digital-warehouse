<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Manager;
use App\Models\Product;
use App\Models\Merchant;
use App\Models\Warehouse;
use App\Models\Requisition;
// use App\Models\ProductStock;
use Illuminate\Http\Request;
use App\Models\ProductSerial;
use App\Models\MerchantProduct;
use Illuminate\Validation\Rule;
use App\Models\RequisitionAgent;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File; 
use App\Jobs\BroadcastNewRequisition;
use App\Models\ProductVariationSerial;
use App\Http\Resources\Web\MyProductResource;
use App\Http\Resources\Web\MyProductCollection;
use App\Jobs\BroadcastProductReceiveConfirmation;
use App\Http\Resources\Web\MyRequisitionCollection;
use App\Http\Resources\Web\MerchantProductResource;
use App\Http\Resources\Web\MerchantProductCollection;

class MerchantController extends Controller
{
    public function __construct()
    {
        // Merchants
        $this->middleware("permission:view-merchant-index")->only(['showAllMerchants', 'searchAllMerchants']);
        $this->middleware("permission:create-merchant")->only('storeNewMerchant');
        $this->middleware("permission:update-merchant")->only('updateMerchant');
        $this->middleware("permission:delete-merchant")->only(['deleteMerchant', 'restoreMerchant']);

        // Merchant-Products
        $this->middleware("permission:view-merchant-product-index")->only(['showMerchantAllProducts', 'showMerchantAvailableProducts', 'searchMerchantAllProducts']);
        $this->middleware("permission:create-merchant-product")->only('storeMerchantNewProduct');
        $this->middleware("permission:update-merchant-product")->only('updateMerchantProduct');
        $this->middleware("permission:delete-merchant-product")->only('deleteMerchantProduct');  
    }

    public function showAllMerchants($perPage = false)
    {
    	if ($perPage) {
            
            return response()->json([

        		'approved' => Merchant::with(['roles', 'permissions'])->withCount('deals')->where('active', 1)->latest()->paginate($perPage),
                'pending' => Merchant::with(['roles', 'permissions'])->withCount('deals')->where('active', 0)->latest()->paginate($perPage),
        		'trashed' => Merchant::with(['roles', 'permissions'])->withCount('deals')->onlyTrashed()->latest()->paginate($perPage),

        	], 200);

        }

        return Merchant::latest()->get();
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
        $userToDelete->permissions()->detach();
        $userToDelete->roles()->detach();
        $userToDelete->delete();

        return $this->showAllMerchants($perPage);
    }

    public function restoreMerchant($owner, $perPage)
    {
    	$userToRestore = Merchant::withTrashed()->findOrFail($owner);
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

    /*
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
    */

    // Merchant-Products (web.php) 
    public function showMyAllProducts($perPage=false)
    {
        $currentMerchant = \Auth::user();
        
        if ($perPage) {
            
            return [

                'retail' => new MyProductCollection(MerchantProduct::where('merchant_id', $currentMerchant->id)
                                                        ->whereHas('product', function ($query) {
                                                            $query->where('product_category_id', '>', 0);
                                                        })
                                                        ->paginate($perPage)),
                
                'bulk' => new MyProductCollection(MerchantProduct::where('merchant_id', $currentMerchant->id)
                                                        ->whereHas('product', function ($query) {
                                                            $query->whereNull('product_category_id')
                                                                ->orWhere('product_category_id', 0);
                                                        })
                                                       ->paginate($perPage)),
            ];

        }

        
        // return MyProductResource::collection(
        //     Product::where('merchant_id', $currentMerchant->id)
        //         ->where(
        //             ProductStock::select('available_quantity')
        //                 ->whereColumn('product_stocks.product_id', 'products.id')
        //                 ->latest()
        //                 ->take(1), '>', 0
        //         )
        //         ->get()
        // );
        
       
        return MyProductResource::collection(
            MerchantProduct::where('merchant_id', $currentMerchant->id)->whereHas('latestStock', function ($query) {
                $query->where('available_quantity', '>', 0);
            })->latest()->get()
        );

    }

    public function searchMyAllProducts($search, $perPage)
    {
        $currentMerchant = \Auth::user();

        $query = MerchantProduct::where('merchant_id', $currentMerchant->id)
                ->where(function ($query1) use ($search) {
                    $query1->where('sku', 'like', "%$search%")
                    ->orWhere('description', 'like', "%$search%")
                    ->orWhere('warning_quantity', 'like', "%$search%")
                    ->orWhere('selling_price', 'like', "%$search%")
                    ->orWhereHas('manufacturer', function ($query2) use ($search) {
                        $query2->where('name', 'like', "%$search%");
                    })
                    ->orWhereHas('product', function ($query3) use ($search) {
                        $query3->where('name', 'like', "%$search%")
                            ->orWhere('quantity_type', 'like', "%$search%")
                            ->orWhereHas('category', function ($q) use ($search) {
                                $q->where('name', 'like', "%$search%");
                            });
                    });
                });

        return [
            'all' => new MyProductCollection($query->paginate($perPage)),  
        ];
    }
    
    // My-Agents (web.php)
    public function showMyAllAgents()
    {
        $currentMerchant = \Auth::user();

        return RequisitionAgent::whereHas('requisition', function ($query) use ($currentMerchant) {
            $query->where('merchant_id', $currentMerchant->id);
        })->latest('id')->get()->unique('name');
    }

    // My-Requisition (web.php)
    public function showMyAllRequisitions($perPage = false)
    {
        $currentMerchant = \Auth::user();
            
        if ($perPage) {

            return [

                'pending' => new MyRequisitionCollection(Requisition::with(['products.merchantProduct.variations.productVariation', 'delivery', 'agent'])->where('status', 0)->where('merchant_id', $currentMerchant->id)->paginate($perPage)),  
                'dispatched' => new MyRequisitionCollection(Requisition::with(['products.merchantProduct.variations.productVariation', 'delivery', 'agent', 'dispatch.delivery', 'dispatch.return'])->where('status', 1)->where('merchant_id', $currentMerchant->id)->paginate($perPage)),  
                'cancelled' => new MyRequisitionCollection(Requisition::with(['products.merchantProduct.variations.productVariation', 'delivery', 'agent', 'dispatch.delivery', 'dispatch.return'])->where('status', -1)->where('merchant_id', $currentMerchant->id)->paginate($perPage)),  
            
            ];

        }

        return Requisition::with('products.merchantProduct')->where('merchant_id', $currentMerchant->id)->latest()->get();

    }

    public function makeNewRequisition(Request $request, $perPage)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            
            'products' => 'required|array|min:1',
            'products.*.id' => 'required|numeric|exists:merchant_products,id',
            'products.*.total_quantity' => 'required|numeric|min:1',
            'products.*.packaging_service' => 'boolean',
            // 'products.*.package' => 'required_if:products.*.packaging_service,true',
            'products.*.product' => 'required',
            'products.*.product.has_serials' => 'required|boolean',
            'products.*.product.has_variations' => 'required|boolean',

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

        $serialError = $this->validateProductSerials(json_decode(json_encode($request->products)));

        if (! empty($serialError)) {

            return $serialError;

        }

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

        return $this->showMyAllRequisitions($perPage);
    }

    public function searchMyAllRequisitions($search, $perPage)
    {
        $currentMerchant = \Auth::user();

        $query = Requisition::with(['products.merchantProduct.product', 'products.merchantProduct.variations.productVariation', 'delivery', 'agent', 'dispatch.delivery', 'dispatch.return'])->where(function ($query) use ($search) {
                                    $query->where('subject', 'like', "%$search%")
                                            ->orWhere('description', 'like', "%$search%")
                                            ->orWhereHas('products.merchantProduct.product', function ($q) use ($search) {
                                                $q->where('name', 'like', "%$search%");
                                            })
                                            ->orWhereHasMorph(
                                                'updater',
                                                [Admin::class, Manager::class, Warehouse::class],
                                                function ($query) use ($search) {
                                                    $query->where('user_name', 'like', "%$search%");
                                                }
                                            );
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

        return $this->showMyAllRequisitions($perPage);
    }

    // Merchant-Products for Admin (stocks) only
    public function showMerchantAllProducts($merchant)
    {
        $expectedMerchant = Merchant::findOrFail($merchant);
       
        return MerchantProductResource::collection(
            MerchantProduct::where('merchant_id', $expectedMerchant->id)
            ->with(['variations'])
            ->latest()
            ->get()
        );
    }

    // Merchant-Products for Admin && Merchant 
    public function showMerchantAvailableProducts($merchant, $perPage=false)
    {
        $expectedMerchant = Merchant::findOrFail($merchant);
        
        if ($perPage) {
            
            return [

                'retail' => new MerchantProductCollection(MerchantProduct::where('merchant_id', $expectedMerchant->id)
                                                        ->whereHas('product', function ($query) {
                                                            $query->where('product_category_id', '>', 0);
                                                        })
                                                        ->with(['merchant', 'variations', 'addresses', 'serials', 'latestStock', 'nonDispatchedRequests', 'dispatchedRequests'])
                                                        ->paginate($perPage)),
                
                'bulk' => new MerchantProductCollection(MerchantProduct::where('merchant_id', $expectedMerchant->id)
                                                        ->whereHas('product', function ($query) {
                                                            $query->whereNull('product_category_id')
                                                                ->orWhere('product_category_id', 0);
                                                        })
                                                        ->with(['merchant', 'variations', 'addresses', 'serials', 'latestStock', 'nonDispatchedRequests', 'dispatchedRequests'])
                                                       ->paginate($perPage)),
            ];

        }
       
        return MerchantProductResource::collection(
            MerchantProduct::where('merchant_id', $expectedMerchant->id)->whereHas('latestStock', function ($query) {
                $query->where('available_quantity', '>', 0);
            })
            ->with(['merchant', 'variations', 'serials', 'latestStock', 'nonDispatchedRequests', 'dispatchedRequests'])
            ->latest()
            ->get()
        );
    }

    public function storeMerchantNewProduct(Request $request, $perPage)
    {
        $product = Product::findOrFail($request->product_id);

        $request->validate([
            // 'product' => 'required',
            // 'product.id' => 'required|numeric|exists:products,id',
            // 'merchant' => 'required',
            // 'merchant.id' => 'required|numeric|exists:merchants,id',
            'product_id' => [
                'required', 'numeric', 'exists:products,id', 
                Rule::unique('merchant_products', 'product_id')->where(function ($query) use($request) {
                    return $query->where('merchant_id', $request->merchant_id)->where('manufacturer_id', $request->manufacturer_id);
                }),
            ],
            'merchant_id' => [
                'required', 'numeric', 'exists:merchants,id', 
                Rule::unique('merchant_products', 'merchant_id')->where(function ($query) use($request) {
                    return $query->where('product_id', $request->product_id)->where('manufacturer_id', $request->manufacturer_id);
                }),
            ],
            'sku' => [
                'required', 'string', 
                Rule::unique('merchant_products', 'sku')->where(function ($query) use($request) {
                    return $query->where('product_id', $request->product_id)->where('merchant_id', $request->merchant_id)->where('manufacturer_id', $request->manufacturer_id);
                }),
            ],
            'manufacturer_id' => 'nullable|numeric|exists:product_manufacturers,id',
            // 'selling_price' => 'required|numeric',
            'selling_price' => [ 'nullable', 'numeric', 
                Rule::requiredIf(function () use ($product) {
                    return $product->product_category_id != NULL;
                }),
            ],
            'discount' => 'nullable|numeric|between:0,100',
            'description' => 'nullable|string|max:255',
            'warning_quantity' => 'nullable|numeric',
            'variations' => [
                'array', 
                Rule::requiredIf(function () use ($product) {
                    return $product->has_variations;
                })
            ],
            'variations.*.variation' => 'required_with:variations',
            'variations.*.variation.id' => [
                'required_with:variations', 
                Rule::exists('product_variations', 'id')->where('product_id', $product->id), 
            ],
            'variations.*.selling_price' => 'required_with:variations|numeric',
            'variations.*.sku' => 'string',
        ],
        [
            'product_id.required' => 'Product is required',
            'product_id.unique' => 'Product already exists',
            'product_id.*' => 'Product is invalid',

            'merchant_id.required' => 'Merchant is required',
            'merchant_id.unique' => 'Merchant already exists',
            'merchant_id.*' => 'Merchant is invalid', 

            'sku.required' => 'SKU is required',
            'sku.unique' => 'SKU already exists',
            'sku.*' => 'SKU is invalid', 

            'manufacturer_id' => 'Manufacturer is invalid', 
            'selling_price' => 'Product selling price is required',
            'warning_quantity' => 'Warning quantity should be numeric',
            'variations.*.variation' => 'Variation name is required',
            'variations.*.variation.id.*' => 'Invalid variations, please reload',
            'variations.*.selling_price' => 'Variation selling price is required',
            'variations.*.sku' => 'Invalid variation SKU',
        ]);

        $currentUser = \Auth::guard('admin')->user() ?? \Auth::guard('manager')->user() ?? \Auth::guard('warehouse')->user() ?? \Auth::guard('owner')->user() ?? \Auth::user();

        if (empty($currentUser)) {

            return response()->json(['errors'=>["noUser" => "Current user missing, please reload the page"]], 422);
            
        }

        $productNewMerchant = MerchantProduct::create([

            'sku' => strtoupper($request->sku) ?? $this->generateProductSKU($request->merchant_id, $product->product_category_id, $product->id, $request->manufacturer_id), 
            // 'merchant_product_preview' => $request->preview, 
            'description' => strtolower($request->description), 
            'manufacturer_id' => $request->manufacturer_id, 
            'warning_quantity' => $request->warning_quantity ?? 0,
            'selling_price' => $product->product_category_id ? $request->selling_price : NULL,
            'discount' => $product->product_category_id ? $request->discount : NULL,
            'product_id' => $request->product_id,
            'merchant_id' => $request->merchant_id,
            'created_at' => now()

        ]);

        $productNewMerchant->merchant_product_preview = $request->preview;
        $productNewMerchant->save();

        if ($product->has_variations) {
            
            $productNewMerchant->merchant_product_variations = json_decode(json_encode($request->variations));

        }

        return $this->showMerchantAvailableProducts($request->merchant_id, $perPage);
    }

    public function updateMerchantProduct(Request $request, $productMerchant, $perPage)
    {
        $product = Product::findOrFail($request->product_id);

        $request->validate([
            // 'product' => 'required',
            // 'product.id' => 'required|numeric|exists:products,id',
            // 'merchant' => 'required',
            // 'merchant.id' => 'required|numeric|exists:merchants,id',
            'product_id' => [
                'required', 'numeric', 'exists:products,id', 
                Rule::unique('merchant_products', 'product_id')->where(function ($query) use($request) {
                    return $query->where('merchant_id', $request->merchant_id)->where('manufacturer_id', $request->manufacturer_id);
                })->ignore($productMerchant),
            ],
            'merchant_id' => [
                'required', 'numeric', 'exists:merchants,id', 
                Rule::unique('merchant_products', 'merchant_id')->where(function ($query) use($request) {
                    return $query->where('product_id', $request->product_id)->where('manufacturer_id', $request->manufacturer_id);
                })->ignore($productMerchant),
            ],
            'sku' => [
                'required', 'string', 
                Rule::unique('merchant_products', 'sku')->where(function ($query) use($request) {
                    return $query->where('product_id', $request->product_id)->where('merchant_id', $request->merchant_id)->where('manufacturer_id', $request->manufacturer_id);
                })->ignore($productMerchant),
            ], 
            'manufacturer_id' => 'nullable|numeric|exists:product_manufacturers,id',
            // 'selling_price' => 'required|numeric',
            'selling_price' => [ 'nullable', 'numeric', 
                Rule::requiredIf(function () use ($product) {
                    return $product->product_category_id != NULL;
                }),
            ],
            'discount' => 'nullable|numeric|between:0,100',
            'description' => 'nullable|string|max:255',
            'warning_quantity' => 'nullable|numeric',
            'variations' => [
                'array', 
                Rule::requiredIf(function () use ($product) {
                    return $product->has_variations;
                })
            ],
            'variations.*.variation.id' => 'required_without:variations.*.product_variation_id',
            'variations.*.product_variation_id' => [
                'required_without:variations.*.variation.id',
                Rule::exists('product_variations', 'id')->where('product_id', $product->id), 
            ],
            'variations.*.selling_price' => 'required_with:variations|numeric',
            'variations.*.sku' => 'string',
        ],
        [
            'product_id.required' => 'Product is required',
            'product_id.unique' => 'Product already exists',
            'product_id.*' => 'Product is invalid',

            'merchant_id.required' => 'Merchant is required',
            'merchant_id.unique' => 'Merchant already exists',
            'merchant_id.*' => 'Merchant is invalid', 

            'sku.required' => 'SKU is required',
            'sku.unique' => 'SKU already exists',
            'sku.*' => 'SKU is invalid', 

            'manufacturer_id' => 'Manufacturer is invalid', 
            'selling_price' => 'Product selling price is required',
            'warning_quantity' => 'Warning quantity should be numeric',
            'variations.*.variation' => 'Variation name is required',
            'variations.*.variation.id.*' => 'Invalid variations, please reload',
            'variations.*.selling_price' => 'Variation selling price is required',
            'variations.*.sku' => 'Invalid variation SKU',
        ]);

        $currentUser = \Auth::guard('admin')->user() ?? \Auth::guard('manager')->user() ?? \Auth::guard('warehouse')->user() ?? \Auth::guard('owner')->user() ?? \Auth::user();

        if (empty($currentUser)) {

            return response()->json(['errors'=>["noUser" => "Current user missing, please reload the page"]], 422);
            
        }

        $productMerchantToUpdate = MerchantProduct::findOrFail($productMerchant);

        $productMerchantToUpdate->update([

            'sku' => strtoupper($request->sku) ?? $this->generateProductSKU($request->merchant_id, $product->product_category_id, $product->id, $request->manufacturer_id), 
            'manufacturer_id' => $request->manufacturer_id, 
            // 'merchant_product_preview' => $request->preview, 
            'description' => strtolower($request->description), 
            'warning_quantity' => $request->warning_quantity ?? 0,
            'selling_price' => $product->product_category_id ? $request->selling_price : NULL,
            'discount' => $product->product_category_id ? $request->discount : NULL,
            'product_id' => $request->product_id,
            'merchant_id' => $request->merchant_id,
            'created_at' => now()

        ]);

        $productMerchantToUpdate->merchant_product_preview = $request->preview;
        $productMerchantToUpdate->save();

        if ($product->has_variations) {
            
            $productMerchantToUpdate->merchant_product_variations = json_decode(json_encode($request->variations));

        }

        return $this->showMerchantAvailableProducts($request->merchant_id, $perPage);
    }

    public function deleteMerchantProduct($productMerchant, $perPage)
    {
        $merchantProductToDelete = MerchantProduct::findOrFail($productMerchant);
        $merchant = $merchantProductToDelete->merchant_id;

        // if any related stock / requisition exists
        if ($merchantProductToDelete->product_immutability) {
            
           return response()->json(['errors'=>["undeletableMerchant" => "Merchant has stock or requisition"]], 422); 

        }
            
        // $merchantProductToDelete->stocks()->delete();
        // $merchantProductToDelete->requests()->delete();
        // $merchantProductToDelete->deleteOldAddresses();
        
        File::delete($merchantProductToDelete->preview);

        foreach ($merchantProductToDelete->variations as $merchantProductVariationKey => $merchantProductVariationToDelete) {
            
            File::delete($merchantProductVariationToDelete->preview);
            
        }

        $merchantProductToDelete->variations()->delete();
        $merchantProductToDelete->delete();

        return $this->showMerchantAvailableProducts($merchant, $perPage);
    }

    public function searchMerchantAllProducts($merchant, $search, $perPage)
    {
        $expectedMerchant = Merchant::findOrFail($merchant);

        $query = MerchantProduct::where('merchant_id', $expectedMerchant->id)
                ->with(['merchant', 'variations', 'addresses', 'serials', 'latestStock', 'nonDispatchedRequests', 'dispatchedRequests'])
                ->where(function ($query1) use ($search) {
                    $query1->where('sku', 'like', "%$search%")
                    ->orWhere('description', 'like', "%$search%")
                    ->orWhere('warning_quantity', 'like', "%$search%")
                    ->orWhere('selling_price', 'like', "%$search%")
                    ->orWhereHas('manufacturer', function ($query2) use ($search) {
                        $query2->where('name', 'like', "%$search%");
                    })
                    ->orWhereHas('product', function ($query3) use ($search) {
                        $query3->where('name', 'like', "%$search%")
                            ->orWhere('quantity_type', 'like', "%$search%")
                            ->orWhereHas('category', function ($q) use ($search) {
                                $q->where('name', 'like', "%$search%");
                            });
                    });
                });

        return [
            'all' => new MerchantProductCollection($query->paginate($perPage)),  
        ];
    }

    protected function validateProductSerials($products = [])
    {
        foreach ($products as $requiredProduct) {
            
            if ($requiredProduct->product->has_serials && ! $requiredProduct->product->has_variations && (empty($requiredProduct->serials) || $requiredProduct->total_quantity != count($requiredProduct->serials))) {
                
                return response()->json(['errors'=>["productSerial" => "Product serial is required"]], 422);

            }

            else if ($requiredProduct->product->has_serials && ! $requiredProduct->product->has_variations && count($requiredProduct->serials)) {
                        
                foreach ($requiredProduct->serials as $requiredProductSerialIndex => $requiredProductSerial) {
                    
                    if (! ProductSerial::where('serial_no', $requiredProductSerial)->where('has_requisitions', false)->where('has_dispatched', false)->exists()) {
                        
                        return response()->json(['errors'=>["productSerial" => "Product serial has already requisition"]], 422);

                    }

                }

            }

            else if ($requiredProduct->product->has_serials && $requiredProduct->product->has_variations) {
                
                foreach ($requiredProduct->product->variations as $requiredProductVariation) {
                    
                    if (empty($requiredProductVariation->required_serials) || $requiredProductVariation->required_quantity != count($requiredProductVariation->required_serials)) {
                
                        return response()->json(['errors'=>["variationSerial" => "Variation serial is required"]], 422);

                    }

                    else if (count($requiredProductVariation->required_serials)) {
                        
                        foreach ($requiredProductVariation->required_serials as $requiredProductVariationSerialIndex => $requiredProductVariationSerial) {
                            
                            if (! ProductVariationSerial::where('serial_no', $requiredProductVariationSerial)->where('has_requisitions', false)->where('has_dispatched', false)->exists()) {
                                
                                return response()->json(['errors'=>["variationSerial" => "Variation serial has already requisition"]], 422);

                            }

                        }

                    }

                }

            }

        }
    }

}
