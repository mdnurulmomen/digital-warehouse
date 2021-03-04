<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use Illuminate\Http\Request;
use App\Models\WarehouseOwner;
use Illuminate\Support\Facades\Hash;
use App\Models\WarehouseContainerStatus;
use App\Models\WarehouseContainerShelfStatus;
use App\Http\Resources\Web\WarehouseCollection;
use App\Models\WarehouseContainerShelfUnitStatus;

class WarehouseController extends Controller
{
    public function showAllOwners($perPage=false)
    {
    	if ($perPage) {
            
            return response()->json([

        		'approved' => WarehouseOwner::where('active', 1)->paginate($perPage),
                'pending' => WarehouseOwner::where('active', 0)->paginate($perPage),
        		'trashed' => WarehouseOwner::onlyTrashed()->paginate($perPage),

        	], 200);

        }

        return WarehouseOwner::where('active', 1)->get();
    }

    public function storeNewOwner(Request $request, $perPage)
    {
    	$request->validate([
            'first_name' => 'nullable|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'user_name' => 'required|string|max:100|unique:warehouse_owners,user_name',
            'email' => 'required|string|max:100|unique:warehouse_owners,email',
            'mobile' => 'required|string|max:50|unique:warehouse_owners,mobile',
            'password' => 'required|string|max:255|confirmed',
            'active' => 'required|boolean',
        ]);

        $newUser = new WarehouseOwner();

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

        return $this->showAllOwners($perPage);
    }

    public function updateOwner(Request $request, $owner, $perPage)
    {
    	$userToUpdate = WarehouseOwner::findOrFail($owner);

        $request->validate([
            'first_name' => 'nullable|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'user_name' => 'required|string|max:100|unique:warehouse_owners,user_name,'.$userToUpdate->id,
            'email' => 'required|string|max:100|unique:warehouse_owners,email,'.$userToUpdate->id,
            'mobile' => 'required|string|max:50|unique:warehouse_owners,mobile,'.$userToUpdate->id,
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

        return $this->showAllOwners($perPage);
    }

    public function deleteOwner($owner, $perPage)
    {
    	$userToDelete = WarehouseOwner::findOrFail($owner);
        // $userToDelete->warehouses()->delete();
        $userToDelete->delete();

        return $this->showAllOwners($perPage);
    }

    public function restoreOwner($owner, $perPage)
    {
    	$userToRestore = WarehouseOwner::withTrashed()->findOrFail($owner);
        // $userToRestore->warehouses()->restore();
        $userToRestore->restore();

        return $this->showAllOwners($perPage);
    }

    public function searchAllOwners($search, $perPage)
    {
        $columnsToSearch = ['first_name', 'last_name', 'user_name', 'mobile', 'email'];

        $query = WarehouseOwner::withTrashed();

        foreach($columnsToSearch as $column){
            $query->orWhere($column, 'like', "%$search%");
        }

        $query->orWhereHas('warehouses', function($q) use ($search){
            $q->where('name', 'like', "%$search%")
              ->orWhere('code', 'like', "%$search%");
        });

        return response()->json([
            'all' => $query->paginate($perPage),    
        ], 200);
    }

    // Warehouse
    public function showAllWarehouses($perPage)
    {
        return [

            'approved' => new WarehouseCollection(Warehouse::where('active', 1)->paginate($perPage)),
            'pending' => new WarehouseCollection(Warehouse::where('active', 0)->paginate($perPage)),
            'trashed' => new WarehouseCollection(Warehouse::onlyTrashed()->paginate($perPage)),

        ];
    
    /*
        return response()->json([

            'approved' => Warehouse::with(['owner', 'previews', 'feature', 'storages.storageType', 'storages.previews', 'storages.feature'])->where('active', 1)->paginate($perPage),
            'pending' => Warehouse::with(['owner', 'previews', 'feature', 'storages.storageType', 'storages.previews', 'storages.feature'])->where('active', 0)->paginate($perPage),
            'trashed' => Warehouse::onlyTrashed()->with(['owner', 'previews', 'feature', 'storages.storageType', 'storages.previews', 'storages.feature'])->paginate($perPage),

        ], 200);
    */
    }

    public function storeNewWarehouse(Request $request, $perPage)
    {
        $request->validate([
            'name' => 'nullable|string|max:100',
            'code' => 'required|string|max:100|unique:warehouses,code',
            'user_name' => 'required|string|max:100|unique:warehouses,user_name',
            'email' => 'required|string|max:100|unique:warehouses,email',
            'mobile' => 'required|string|max:50|unique:warehouses,mobile',
            'password' => 'required|string|max:255|confirmed',
            'warehouse_deal' => 'required|string',
            'active' => 'boolean',
            'warehouse_owner_id' => 'required|numeric',
            'feature.features' => 'required|string',
            'previews' => 'required|array',

            'storages' => 'required|array',
            'storages.*.feature.features' => 'required|string',
            'storages.*.previews' => 'required|array',
            'storages.*.storage_type' => 'required',

            'containers' => 'required|array',
            'containers.*.container.id' => 'required|exists:containers,id',
            'containers.*.quantity' => 'required|integer|min:0',
            'containers.*.rents' => 'required',
        ]);

        $newWarehouse = new Warehouse();

        $newWarehouse->name = $request->name;
        $newWarehouse->code = $request->code;
        $newWarehouse->user_name = $request->user_name;
        $newWarehouse->email = $request->email;
        $newWarehouse->mobile = $request->mobile;
        $newWarehouse->password = Hash::make($request->password);
        $newWarehouse->lat = $request->lat ?? 1;
        $newWarehouse->lng = $request->lng ?? 2;
        $newWarehouse->warehouse_deal = $request->warehouse_deal;
        $newWarehouse->active = $request->active ?? false;
        $newWarehouse->warehouse_owner_id = $request->warehouse_owner_id;

        $newWarehouse->save();

        if ($request->site_map_preview) {

            $newWarehouse->map_preview = $request->site_map_preview;
            $newWarehouse->save();

        }

        $newWarehouse->feature()->updateOrCreate(
            [ 'warehouse_id' => $newWarehouse->id ],
            [ 'features' => $request->feature['features']]
        );

        if (count($request->previews)) {
            
            $newWarehouse->warehouse_previews = $request->previews;

        }

        if (count($request->storages)) {
            
            $newWarehouse->warehouse_storages = $request->storages;

        }

        if (count($request->containers)) {
            
            $newWarehouse->warehouse_containers = $request->containers;

        }

        return $this->showAllWarehouses($perPage);
    }

    public function updateWarehouse(Request $request, $warehouse, $perPage)
    {
        $warehouseToUpdate = Warehouse::findOrFail($warehouse);

        $request->validate([
            'name' => 'nullable|string|max:100',
            'code' => 'required|string|max:100|unique:warehouses,code,'.$warehouseToUpdate->id,
            'user_name' => 'required|string|max:100|unique:warehouses,user_name,'.$warehouseToUpdate->id,
            'email' => 'required|string|max:100|unique:warehouses,email,'.$warehouseToUpdate->id,
            'mobile' => 'required|string|max:50|unique:warehouses,mobile,'.$warehouseToUpdate->id,
            'password' => 'nullable|string|max:255|confirmed',
            'warehouse_deal' => 'required|string',
            'active' => 'boolean',
            'warehouse_owner_id' => 'required|numeric',
            'feature.features' => 'required|string',
            'previews' => 'required|array',

            'storages' => 'required|array',
            'storages.*.feature.features' => 'required|string',
            'storages.*.previews' => 'required|array',
            'storages.*.storage_type' => 'required',

            'containers' => 'required|array',
            'containers.*.container.id' => 'required|exists:containers,id',
            'containers.*.quantity' => 'required|numeric',
            'containers.*.rents' => 'required',
        ]);

        $warehouseToUpdate->name = $request->name;
        $warehouseToUpdate->code = $request->code;
        $warehouseToUpdate->user_name = $request->user_name;
        $warehouseToUpdate->email = $request->email;
        $warehouseToUpdate->mobile = $request->mobile;

        if ($request->password) {
            
            $warehouseToUpdate->password = Hash::make($request->password);
        
        }

        if ($request->site_map_preview) {

            $warehouseToUpdate->map_preview = $request->site_map_preview;

        }
        
        $warehouseToUpdate->lat = $request->lat ?? 1;
        $warehouseToUpdate->lng = $request->lng ?? 2;
        $warehouseToUpdate->warehouse_deal = $request->warehouse_deal;
        $warehouseToUpdate->active = $request->active ?? false;
        $warehouseToUpdate->warehouse_owner_id = $request->warehouse_owner_id;

        $warehouseToUpdate->save();


        $warehouseToUpdate->feature()->updateOrCreate(
            [ 'warehouse_id' => $warehouseToUpdate->id ],
            [ 'features' => $request->feature['features']]
        );

        if (count($request->previews)) {
            
            $warehouseToUpdate->warehouse_previews = $request->previews;

        }

        if (count($request->storages)) {
            
            $warehouseToUpdate->warehouse_storages = $request->storages;

        }

        if (count($request->containers)) {
            
            $warehouseToUpdate->warehouse_containers = $request->containers;

        }

        return $this->showAllWarehouses($perPage);
    }

    public function deleteWarehouse($owner, $perPage)
    {
        $warehouseToDelete = Warehouse::findOrFail($owner);
        // $warehouseToDelete->warehouses()->delete();
        $warehouseToDelete->delete();

        return $this->showAllWarehouses($perPage);
    }

    public function restoreWarehouse($owner, $perPage)
    {
        $warehouseToRestore = Warehouse::withTrashed()->findOrFail($owner);
        // $warehouseToRestore->warehouses()->restore();
        $warehouseToRestore->restore();

        return $this->showAllWarehouses($perPage);
    }

    public function searchAllWarehouses($search, $perPage)
    {
        $columnsToSearch = ['name', 'user_name', 'email', 'mobile', 'warehouse_deal'];

        $query = Warehouse::withTrashed();

        foreach($columnsToSearch as $column){
            $query->orWhere($column, 'like', "%$search%");
        }

        $query->orWhereHas('owner', function($q) use ($search){
            $q->where('first_name', 'like', "%$search%")
              ->orWhere('last_name', 'like', "%$search%")
              ->orWhere('user_name', 'like', "%$search%")
              ->orWhere('email', 'like', "%$search%")
              ->orWhere('mobile', 'like', "%$search%");
        });

        return response()->json([
            'all' => new WarehouseCollection($query->paginate($perPage)),    
        ], 200);
    }

    // warehouse-contaners
    public function showAllWarehouseContainers($perPage = false) {
        
        // if ($perPage) {
            
            $emptyContainers = WarehouseContainerStatus::where('engaged', 0)->get();


            $emptyShelfContainers = WarehouseContainerStatus::whereHas('containerShelfStatuses',                    function ($query) {
                                        $query->where('engaged', 0);
                                    })
                                    ->with([
                                        'containerShelfStatuses' => 
                                            function ($query) {
                                                $query->where('engaged', 0);
                                            }
                                    ])
                                    ->get();

            $emptyUnitContainers = WarehouseContainerStatus::whereHas('containerShelfStatuses.containerShelfUnitStatuses', 
                                    function ($query) {
                                        $query->where('engaged', 0);
                                    })
                                    ->with([
                                        'containerShelfStatuses' => 
                                            function ($query) {
                                                $query->where('engaged', 0)
                                                      ->orWhere('engaged', 0.5);
                                            },

                                        'containerShelfStatuses.containerShelfUnitStatuses' => 
                                            function ($query) {
                                                $query->where('engaged', 0);
                                            },
                                    ])
                                    ->get();

            return [
                'emptyContainers' => $emptyContainers, 
                'emptyShelfContainers' => $emptyShelfContainers, 
                'emptyUnitContainers' => $emptyUnitContainers, 
            ];

        // }
                                
    }

    // containers of specific warehouse
    public function showWarehouseAllContainers($perPage = false) {
        
        if ($perPage) {
            
            $currentWarehouse = \Auth::guard('warehouse')->user();

            $emptyContainers = WarehouseContainerStatus::with(['containerShelfStatuses'])
                                                ->where('engaged', 0)
                                                ->whereHas('warehouseContainer', function ($query) use ($currentWarehouse) {
                                                    $query->where('warehouse_id', $currentWarehouse->id);
                                                })
                                                ->paginate($perPage);

            $partialContainers = WarehouseContainerStatus::with(['containerShelfStatuses'])
                                                ->where('engaged', 0.5)
                                                ->whereHas('warehouseContainer', function ($query) use ($currentWarehouse) {
                                                    $query->where('warehouse_id', $currentWarehouse->id);
                                                })
                                                ->paginate($perPage);

            $engagedContainers = WarehouseContainerStatus::with(['containerShelfStatuses'])
                                                ->where('engaged', 1)
                                                ->whereHas('warehouseContainer', function ($query) use ($currentWarehouse) {
                                                    $query->where('warehouse_id', $currentWarehouse->id);
                                                })
                                                ->paginate($perPage);

            return [
                'empty' => $emptyContainers, 
                'partial' => $partialContainers, 
                'engaged' => $engagedContainers, 
            ];

        }
                                
    }

    public function searchWarehouseAllContainers($search, $perPage)
    {
        $currentWarehouse = \Auth::guard('warehouse')->user();

        $query = WarehouseContainerStatus::with('containerShelfStatuses')
                            ->where(function ($query) use ($search) {
                                $query->where('name', 'like', "%$search%")
                                      ->orWhereHas('warehouseContainer.container', function ($q) use ($search) {
                                            $q->where('name', 'like', "%$search%");
                                       });
                            })
                            ->whereHas('warehouseContainer', function ($q) use ($currentWarehouse) {
                                $q->where('warehouse_id', $currentWarehouse->id);
                            });

        return response()->json([
            'all' => $query->paginate($perPage),    
        ], 200);
    }

    // shelves of specific container
    public function showContainerAllShelves($containerId, $perPage = false) {
        
        if ($perPage) {
            
            $currentWarehouse = \Auth::guard('warehouse')->user();

            $expectedContainer = WarehouseContainerStatus::findOrFail($containerId);

            if ($expectedContainer && $expectedContainer->warehouseContainer->warehouse_id==$currentWarehouse->id) {
                
                $emptyShelves = WarehouseContainerShelfStatus::with(['containerShelfUnitStatuses'])
                                                ->where('engaged', 0)
                                                ->where('warehouse_container_status_id', $expectedContainer->id)
                                                ->paginate($perPage);

                $partialShelves = WarehouseContainerShelfStatus::with(['containerShelfUnitStatuses'])
                                                ->where('engaged', 0.5)
                                                ->where('warehouse_container_status_id', $expectedContainer->id)
                                                ->paginate($perPage);

                $engagedShelves = WarehouseContainerShelfStatus::with(['containerShelfUnitStatuses'])
                                                ->where('engaged', 1)
                                                ->where('warehouse_container_status_id', $expectedContainer->id)
                                                ->paginate($perPage);

                return [
                    'empty' => $emptyShelves, 
                    'partial' => $partialShelves, 
                    'engaged' => $engagedShelves, 
                ];

            }

            return [
                'empty' => [], 
                'partial' => [], 
                'engaged' => [], 
            ];

        }
                                
    }

    public function searchContainerAllShelves($container, $search, $perPage)
    {
        if ($perPage) {

            $currentWarehouse = \Auth::guard('warehouse')->user();

            $expectedContainer = WarehouseContainerStatus::findOrFail($container);

            if ($expectedContainer && $expectedContainer->warehouseContainer->warehouse_id==$currentWarehouse->id) {

                $query = WarehouseContainerShelfStatus::with(['containerShelfUnitStatuses'])
                                                     ->where('name', 'like', "%$search%")
                                                     ->where('warehouse_container_status_id', $expectedContainer->id);

                return response()->json([
                    'all' => $query->paginate($perPage),    
                ], 200);

            }

            return response()->json([
                'all' => [],    
            ], 200);

        }

    }

    // units of specific shelf
    public function showShelfAllUnits($shelf, $perPage = false) {
        
        if ($perPage) {
            
            $currentWarehouse = \Auth::guard('warehouse')->user();

            $expectedShelf = WarehouseContainerShelfStatus::findOrFail($shelf);

            if ($expectedShelf && $expectedShelf->warehouseContainer->warehouse_id==$currentWarehouse->id) {
                
                $emptyUnits = WarehouseContainerShelfUnitStatus::where('engaged', 0)
                                                ->where('warehouse_container_shelf_status_id', $expectedShelf->id)
                                                ->paginate($perPage);

                $partialUnits = WarehouseContainerShelfUnitStatus::where('engaged', 0.5)
                                                ->where('warehouse_container_shelf_status_id', $expectedShelf->id)
                                                ->paginate($perPage);

                $engagedUnits = WarehouseContainerShelfUnitStatus::where('engaged', 1)
                                                ->where('warehouse_container_shelf_status_id', $expectedShelf->id)
                                                ->paginate($perPage);

                return [
                    'empty' => $emptyUnits, 
                    'partial' => $partialUnits, 
                    'engaged' => $engagedUnits, 
                ];

            }

            return [
                'empty' => [], 
                'partial' => [], 
                'engaged' => [], 
            ];

        }
                                
    }

    public function searchShelfAllUnits($shelf, $search, $perPage)
    {
        if ($perPage) {

            $currentWarehouse = \Auth::guard('warehouse')->user();

            $expectedShelf = WarehouseContainerShelfStatus::findOrFail($shelf);

            if ($expectedShelf && $expectedShelf->warehouseContainer->warehouse_id==$currentWarehouse->id) {

                $query = WarehouseContainerShelfUnitStatus::where('name', 'like', "%$search%")
                                                     ->where('warehouse_container_shelf_status_id', $expectedShelf->id);

                return response()->json([
                    'all' => $query->paginate($perPage),    
                ], 200);

            }

            return response()->json([
                'all' => [],    
            ], 200);

        }

    }
}
