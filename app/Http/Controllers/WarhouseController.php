<?php

namespace App\Http\Controllers;

use App\Models\Warhouse;
use Illuminate\Http\Request;
use App\Models\WarhouseOwner;
use Illuminate\Support\Facades\Hash;
use App\Models\WarhouseContainerStatus;
use App\Models\WarhouseContainerShelfStatus;
use App\Http\Resources\Web\WarhouseCollection;
use App\Models\WarhouseContainerShelfUnitStatus;

class WarhouseController extends Controller
{
    public function showAllOwners($perPage=false)
    {
    	if ($perPage) {
            
            return response()->json([

        		'approved' => WarhouseOwner::where('active', 1)->paginate($perPage),
                'pending' => WarhouseOwner::where('active', 0)->paginate($perPage),
        		'trashed' => WarhouseOwner::onlyTrashed()->paginate($perPage),

        	], 200);

        }

        return WarhouseOwner::where('active', 1)->get();
    }

    public function storeNewOwner(Request $request, $perPage)
    {
    	$request->validate([
            'first_name' => 'nullable|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'user_name' => 'required|string|max:100|unique:warhouse_owners,user_name',
            'email' => 'required|string|max:100|unique:warhouse_owners,email',
            'mobile' => 'required|string|max:50|unique:warhouse_owners,mobile',
            'password' => 'required|string|max:255|confirmed',
            'active' => 'required|boolean',
        ]);

        $newUser = new WarhouseOwner();

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
    	$userToUpdate = WarhouseOwner::findOrFail($owner);

        $request->validate([
            'first_name' => 'nullable|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'user_name' => 'required|string|max:100|unique:warhouse_owners,user_name,'.$userToUpdate->id,
            'email' => 'required|string|max:100|unique:warhouse_owners,email,'.$userToUpdate->id,
            'mobile' => 'required|string|max:50|unique:warhouse_owners,mobile,'.$userToUpdate->id,
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
    	$userToDelete = WarhouseOwner::findOrFail($owner);
        // $userToDelete->warhouses()->delete();
        $userToDelete->delete();

        return $this->showAllOwners($perPage);
    }

    public function restoreOwner($owner, $perPage)
    {
    	$userToRestore = WarhouseOwner::withTrashed()->findOrFail($owner);
        // $userToRestore->warhouses()->restore();
        $userToRestore->restore();

        return $this->showAllOwners($perPage);
    }

    public function searchAllOwners($search, $perPage)
    {
        $columnsToSearch = ['first_name', 'last_name', 'user_name', 'mobile', 'email'];

        $query = WarhouseOwner::withTrashed();

        foreach($columnsToSearch as $column){
            $query->orWhere($column, 'like', "%$search%");
        }

        $query->orWhereHas('warhouses', function($q) use ($search){
            $q->where('name', 'like', "%$search%")
              ->orWhere('code', 'like', "%$search%");
        });

        return response()->json([
            'all' => $query->paginate($perPage),    
        ], 200);
    }

    // Warhouse
    public function showAllWarhouses($perPage)
    {
        return [

            'approved' => new WarhouseCollection(Warhouse::where('active', 1)->paginate($perPage)),
            'pending' => new WarhouseCollection(Warhouse::where('active', 0)->paginate($perPage)),
            'trashed' => new WarhouseCollection(Warhouse::onlyTrashed()->paginate($perPage)),

        ];
    
    /*
        return response()->json([

            'approved' => Warhouse::with(['owner', 'previews', 'feature', 'storages.storageType', 'storages.previews', 'storages.feature'])->where('active', 1)->paginate($perPage),
            'pending' => Warhouse::with(['owner', 'previews', 'feature', 'storages.storageType', 'storages.previews', 'storages.feature'])->where('active', 0)->paginate($perPage),
            'trashed' => Warhouse::onlyTrashed()->with(['owner', 'previews', 'feature', 'storages.storageType', 'storages.previews', 'storages.feature'])->paginate($perPage),

        ], 200);
    */
    }

    public function storeNewWarhouse(Request $request, $perPage)
    {
        $request->validate([
            'name' => 'nullable|string|max:100',
            'code' => 'required|string|max:100|unique:warhouses,code',
            'user_name' => 'required|string|max:100|unique:warhouses,user_name',
            'email' => 'required|string|max:100|unique:warhouses,email',
            'mobile' => 'required|string|max:50|unique:warhouses,mobile',
            'password' => 'required|string|max:255|confirmed',
            'warhouse_deal' => 'required|string',
            'active' => 'boolean',
            'warhouse_owner_id' => 'required|numeric',
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

        $newWarhouse = new Warhouse();

        $newWarhouse->name = $request->name;
        $newWarhouse->code = $request->code;
        $newWarhouse->user_name = $request->user_name;
        $newWarhouse->email = $request->email;
        $newWarhouse->mobile = $request->mobile;
        $newWarhouse->password = Hash::make($request->password);
        $newWarhouse->lat = $request->lat ?? 1;
        $newWarhouse->lng = $request->lng ?? 2;
        $newWarhouse->warhouse_deal = $request->warhouse_deal;
        $newWarhouse->active = $request->active ?? false;
        $newWarhouse->warhouse_owner_id = $request->warhouse_owner_id;

        $newWarhouse->save();

        if ($request->site_map_preview) {

            $newWarhouse->map_preview = $request->site_map_preview;
            $newWarhouse->save();

        }

        $newWarhouse->feature()->updateOrCreate(
            [ 'warhouse_id' => $newWarhouse->id ],
            [ 'features' => $request->feature['features']]
        );

        if (count($request->previews)) {
            
            $newWarhouse->warhouse_previews = $request->previews;

        }

        if (count($request->storages)) {
            
            $newWarhouse->warhouse_storages = $request->storages;

        }

        if (count($request->containers)) {
            
            $newWarhouse->warhouse_containers = $request->containers;

        }

        return $this->showAllWarhouses($perPage);
    }

    public function updateWarhouse(Request $request, $warhouse, $perPage)
    {
        $warhouseToUpdate = Warhouse::findOrFail($warhouse);

        $request->validate([
            'name' => 'nullable|string|max:100',
            'code' => 'required|string|max:100|unique:warhouses,code,'.$warhouseToUpdate->id,
            'user_name' => 'required|string|max:100|unique:warhouses,user_name,'.$warhouseToUpdate->id,
            'email' => 'required|string|max:100|unique:warhouses,email,'.$warhouseToUpdate->id,
            'mobile' => 'required|string|max:50|unique:warhouses,mobile,'.$warhouseToUpdate->id,
            'password' => 'nullable|string|max:255|confirmed',
            'warhouse_deal' => 'required|string',
            'active' => 'boolean',
            'warhouse_owner_id' => 'required|numeric',
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

        $warhouseToUpdate->name = $request->name;
        $warhouseToUpdate->code = $request->code;
        $warhouseToUpdate->user_name = $request->user_name;
        $warhouseToUpdate->email = $request->email;
        $warhouseToUpdate->mobile = $request->mobile;

        if ($request->password) {
            
            $warhouseToUpdate->password = Hash::make($request->password);
        
        }

        if ($request->site_map_preview) {

            $warhouseToUpdate->map_preview = $request->site_map_preview;

        }
        
        $warhouseToUpdate->lat = $request->lat ?? 1;
        $warhouseToUpdate->lng = $request->lng ?? 2;
        $warhouseToUpdate->warhouse_deal = $request->warhouse_deal;
        $warhouseToUpdate->active = $request->active ?? false;
        $warhouseToUpdate->warhouse_owner_id = $request->warhouse_owner_id;

        $warhouseToUpdate->save();


        $warhouseToUpdate->feature()->updateOrCreate(
            [ 'warhouse_id' => $warhouseToUpdate->id ],
            [ 'features' => $request->feature['features']]
        );

        if (count($request->previews)) {
            
            $warhouseToUpdate->warhouse_previews = $request->previews;

        }

        if (count($request->storages)) {
            
            $warhouseToUpdate->warhouse_storages = $request->storages;

        }

        if (count($request->containers)) {
            
            $warhouseToUpdate->warhouse_containers = $request->containers;

        }

        return $this->showAllWarhouses($perPage);
    }

    public function deleteWarhouse($owner, $perPage)
    {
        $warhouseToDelete = Warhouse::findOrFail($owner);
        // $warhouseToDelete->warhouses()->delete();
        $warhouseToDelete->delete();

        return $this->showAllWarhouses($perPage);
    }

    public function restoreWarhouse($owner, $perPage)
    {
        $warhouseToRestore = Warhouse::withTrashed()->findOrFail($owner);
        // $warhouseToRestore->warhouses()->restore();
        $warhouseToRestore->restore();

        return $this->showAllWarhouses($perPage);
    }

    public function searchAllWarhouses($search, $perPage)
    {
        $columnsToSearch = ['name', 'user_name', 'email', 'mobile', 'warhouse_deal'];

        $query = Warhouse::withTrashed();

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
            'all' => new WarhouseCollection($query->paginate($perPage)),    
        ], 200);
    }

    // warehouse-contaners
    public function showAllWarhouseContainers($perPage = false) {
        
        if ($perPage) {
            
            $emptyContainers = WarhouseContainerStatus::where('engaged', 0)->get();


            $emptyShelfContainers = WarhouseContainerStatus::whereHas('containerShelfStatuses', function ($query) {
                                                                $query->where('engaged', 0);
                                                            })
                                                            ->with([
                                                                'containerShelfStatuses' => 
                                                                    function ($query) {
                                                                        $query->where('engaged', 0);
                                                                    }
                                                            ])
                                                            ->get();

            $emptyUnitContainers = WarhouseContainerStatus::whereHas('containerShelfStatuses.containerShelfUnitStatuses', 
                                                                function ($query) {
                                                                $query->where('engaged', 0);
                                                            })
                                                            ->with([
                                                                'containerShelfStatuses.containerShelfUnitStatuses' => 
                                                                    function ($query) {
                                                                        $query->where('engaged', 0);
                                                                    }
                                                            ])
                                                            ->get();

            return [
                'emptyContainers' => $emptyContainers, 
                'emptyShelfContainers' => $emptyShelfContainers, 
                'emptyUnitContainers' => $emptyUnitContainers, 
            ];

        }
                                
    }

    // containers of specific warehouse
    public function showWarhouseAllContainers($perPage = false) {
        
        if ($perPage) {
            
            $currentWarhouse = \Auth::guard('warhouse')->user();

            $emptyContainers = WarhouseContainerStatus::with(['containerShelfStatuses'])
                                                ->where('engaged', 0)
                                                ->whereHas('warhouseContainer', function ($query) use ($currentWarhouse) {
                                                    $query->where('warhouse_id', $currentWarhouse->id);
                                                })
                                                ->paginate($perPage);

            $partialContainers = WarhouseContainerStatus::with(['containerShelfStatuses'])
                                                ->where('engaged', 0.5)
                                                ->whereHas('warhouseContainer', function ($query) use ($currentWarhouse) {
                                                    $query->where('warhouse_id', $currentWarhouse->id);
                                                })
                                                ->paginate($perPage);

            $engagedContainers = WarhouseContainerStatus::with(['containerShelfStatuses'])
                                                ->where('engaged', 1)
                                                ->whereHas('warhouseContainer', function ($query) use ($currentWarhouse) {
                                                    $query->where('warhouse_id', $currentWarhouse->id);
                                                })
                                                ->paginate($perPage);

            return [
                'empty' => $emptyContainers, 
                'partial' => $partialContainers, 
                'engaged' => $engagedContainers, 
            ];

        }
                                
    }

    public function searchWarhouseAllContainers($search, $perPage)
    {
        $currentWarhouse = \Auth::guard('warhouse')->user();

        $query = WarhouseContainerStatus::with(['containerShelfStatuses'])
                                        ->where('name', 'like', "%$search%")
                                        ->orWhereHas('warhouseContainer.container', function ($query) use ($search) {
                                                $query->where('name', 'like', "%$search%");
                                            })
                                        ->whereHas('warhouseContainer', function ($query) use ($currentWarhouse) {
                                                $query->where('warhouse_id', $currentWarhouse->id);
                                            });

        return response()->json([
            'all' => $query->paginate($perPage),    
        ], 200);
    }

    // shelves of specific container
    public function showContainerAllShelves($containerId, $perPage = false) {
        
        if ($perPage) {
            
            $currentWarhouse = \Auth::guard('warhouse')->user();

            $expectedContainer = WarhouseContainerStatus::findOrFail($containerId);

            if ($expectedContainer && $expectedContainer->warhouseContainer->warhouse_id==$currentWarhouse->id) {
                
                $emptyShelves = WarhouseContainerShelfStatus::with(['containerShelfUnitStatuses'])
                                                ->where('engaged', 0)
                                                ->where('warhouse_container_status_id', $expectedContainer->id)
                                                ->paginate($perPage);

                $partialShelves = WarhouseContainerShelfStatus::with(['containerShelfUnitStatuses'])
                                                ->where('engaged', 0.5)
                                                ->where('warhouse_container_status_id', $expectedContainer->id)
                                                ->paginate($perPage);

                $engagedShelves = WarhouseContainerShelfStatus::with(['containerShelfUnitStatuses'])
                                                ->where('engaged', 1)
                                                ->where('warhouse_container_status_id', $expectedContainer->id)
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

            $currentWarhouse = \Auth::guard('warhouse')->user();

            $expectedContainer = WarhouseContainerStatus::findOrFail($container);

            if ($expectedContainer && $expectedContainer->warhouseContainer->warhouse_id==$currentWarhouse->id) {

                $query = WarhouseContainerShelfStatus::with(['containerShelfUnitStatuses'])
                                                     ->where('name', 'like', "%$search%")
                                                     ->where('warhouse_container_status_id', $expectedContainer->id);

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
            
            $currentWarhouse = \Auth::guard('warhouse')->user();

            $expectedShelf = WarhouseContainerShelfStatus::findOrFail($shelf);

            if ($expectedShelf && $expectedShelf->warhouseContainer->warhouse_id==$currentWarhouse->id) {
                
                $emptyUnits = WarhouseContainerShelfUnitStatus::where('engaged', 0)
                                                ->where('warhouse_container_shelf_status_id', $expectedShelf->id)
                                                ->paginate($perPage);

                $partialUnits = WarhouseContainerShelfUnitStatus::where('engaged', 0.5)
                                                ->where('warhouse_container_shelf_status_id', $expectedShelf->id)
                                                ->paginate($perPage);

                $engagedUnits = WarhouseContainerShelfUnitStatus::where('engaged', 1)
                                                ->where('warhouse_container_shelf_status_id', $expectedShelf->id)
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

            $currentWarhouse = \Auth::guard('warhouse')->user();

            $expectedShelf = WarhouseContainerShelfStatus::findOrFail($shelf);

            if ($expectedShelf && $expectedShelf->warhouseContainer->warhouse_id==$currentWarhouse->id) {

                $query = WarhouseContainerShelfUnitStatus::where('name', 'like', "%$search%")
                                                     ->where('warhouse_container_shelf_status_id', $expectedShelf->id);

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
