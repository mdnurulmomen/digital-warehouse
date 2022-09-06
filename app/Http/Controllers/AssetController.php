<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use App\Models\Variation;
use App\Models\Container;
use App\Models\RentPeriod;
use App\Models\StorageType;
use Illuminate\Http\Request;
use App\Models\ContainerType;
use App\Models\VariationType;
use Illuminate\Validation\Rule;
use App\Models\DeliveryCompany;
use App\Models\PackagingPackage;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\Web\VariationCollection;
use App\Http\Resources\Web\VariationTypeResource;

class AssetController extends Controller
{
    public function __construct()
    {
        // Storage-Types
        $this->middleware("permission:view-warehouse-asset-index")->only(['showAllStorageTypes', 'searchAllStorageTypes']);
        $this->middleware("permission:create-warehouse-asset")->only('storeNewStorageType');
        $this->middleware("permission:update-warehouse-asset")->only('updateStorageType');
        $this->middleware("permission:delete-warehouse-asset")->only(['deleteStorageType', 'restoreStorageType']); 

        // Container-Types
        $this->middleware("permission:view-warehouse-asset-index")->only(['showAllContainerTypes', 'searchAllContainerTypes']);
        $this->middleware("permission:create-warehouse-asset")->only('storeNewContainerType');
        $this->middleware("permission:update-warehouse-asset")->only('updateContainerType');
        $this->middleware("permission:delete-warehouse-asset")->only(['deleteContainerType', 'restoreContainerType']); 

        // Containers
        $this->middleware("permission:view-warehouse-asset-index")->only(['showAllContainers', 'searchAllContainers']);
        $this->middleware("permission:create-warehouse-asset")->only('storeNewContainer');
        $this->middleware("permission:update-warehouse-asset")->only('updateContainer');
        $this->middleware("permission:delete-warehouse-asset")->only(['deleteContainer', 'restoreContainer']);

        // Rent-Periods
        $this->middleware("permission:view-warehouse-asset-index")->only(['showAllRentPeriods', 'searchAllRentPeriods']);
        $this->middleware("permission:create-warehouse-asset")->only('storeNewRentPeriod');
        $this->middleware("permission:update-warehouse-asset")->only('updateRentPeriod');
        $this->middleware("permission:delete-warehouse-asset")->only(['deleteRentPeriod', 'restoreRentPeriod']);

        // Variation-Types
        $this->middleware("permission:view-product-asset-index")->only(['showAllVariationTypes', 'searchVariationTypes']);
        $this->middleware("permission:create-product-asset")->only('storeVariationType');
        $this->middleware("permission:update-product-asset")->only('updateVariationType');
        $this->middleware("permission:delete-product-asset")->only(['deleteVariationType', 'restoreVariationType']);

        // Variations
        $this->middleware("permission:view-product-asset-index")->only(['showAllVariations', 'searchAllVariations']);
        $this->middleware("permission:create-product-asset")->only('storeNewVariation');
        $this->middleware("permission:update-product-asset")->only('updateVariation');
        $this->middleware("permission:delete-product-asset")->only(['deleteVariation', 'restoreVariation']);

        // Packaging-Packages
        $this->middleware("permission:view-logistic-index")->only(['showAllPackagingPackages', 'searchAllPackagingPackages']);
        $this->middleware("permission:create-logistic-asset")->only('storeNewPackagingPackage');
        $this->middleware("permission:update-logistic-asset")->only('updatePackagingPackage');
        $this->middleware("permission:delete-logistic-asset")->only(['deletePackagingPackage', 'restorePackagingPackage']);

        // Delivery-Companies
        $this->middleware("permission:view-logistic-asset-index")->only(['showDeliveryAllCompanies', 'searchDeliveryAllCompanies']);
        $this->middleware("permission:create-logistic-asset")->only('storeDeliveryNewCompany');
        $this->middleware("permission:update-logistic-asset")->only('updateDeliveryCompany');
        $this->middleware("permission:delete-logistic-asset")->only(['deleteDeliveryCompany', 'restoreDeliveryCompany']);

        // Vendors
        $this->middleware("permission:view-product-asset-index")->only(['showAllVendors', 'searchAllVendors']);
        $this->middleware("permission:create-product-asset")->only('storeNewVendor');
        $this->middleware("permission:update-product-asset")->only('updateVendor');
        $this->middleware("permission:delete-product-asset")->only(['deleteVendor', 'restoreVendor']); 
    }

    // Storage-Types
    public function showAllStorageTypes($perPage=false)
    {
    	if ($perPage) {
            
            return response()->json([

        		'current' => StorageType::latest('id')->paginate($perPage),
        		'trashed' => StorageType::latest('id')->onlyTrashed()->paginate($perPage),

        	], 200);

        }

        return StorageType::latest('id')->get();
    }

    public function storeNewStorageType(Request $request, $perPage)
    {
    	$request->validate([
            'name' => 'required|string|max:100|unique:storage_types,name',
            // 'code' => 'required|string|max:100|unique:storage_types,code',
        ]);

        $newAsset = new StorageType();

        $newAsset->name = strtolower($request->name);
        // $newAsset->code = strtolower($request->code);

        $newAsset->save();

        return $this->showAllStorageTypes($perPage);
    }

    public function updateStorageType(Request $request, $owner, $perPage)
    {
    	$assetToUpdate = StorageType::findOrFail($owner);

        $request->validate([
            'name' => 'required|string|max:100|unique:storage_types,name,'.$assetToUpdate->id,
            // 'code' => 'required|string|max:100|unique:storage_types,code,'.$assetToUpdate->id,
        ]);

        $assetToUpdate->name = strtolower($request->name);
        // $assetToUpdate->code = strtolower($request->code);

        $assetToUpdate->save();

        return $this->showAllStorageTypes($perPage);
    }

    public function deleteStorageType($asset, $perPage)
    {
    	$assetToDelete = StorageType::findOrFail($asset);
        
        if ($assetToDelete->containers->count()) {
            
            return response()->json(['errors'=>["engaged" => ucfirst($assetToDelete->name)." is in use at ".$assetToDelete->containers->count()." containers"]], 422);

        }
        else if ($assetToDelete->warehouses->count()) {
            
            return response()->json(['errors'=>["engaged" => ucfirst($assetToDelete->name)." is in use at ".$assetToDelete->warehouses->count().' warehouses']], 422);

        }
        
        // $userToDelete->warehouses()->delete();
        $assetToDelete->delete();

        return $this->showAllStorageTypes($perPage);
    }

    public function restoreStorageType($asset, $perPage)
    {
    	$userToRestore = StorageType::withTrashed()->findOrFail($asset);
        // $userToRestore->warehouses()->restore();
        $userToRestore->restore();

        return $this->showAllStorageTypes($perPage);
    }

    public function searchAllStorageTypes($search, $perPage)
    {
        $columnsToSearch = ['name'];

        $query = StorageType::withTrashed();

        foreach($columnsToSearch as $column){
            $query->orWhere($column, 'like', "%$search%");
        }

        return response()->json([
            'all' => $query->paginate($perPage),    
        ], 200);
    }

    // Container-Types
    public function showAllContainerTypes($perPage=false)
    {
        if ($perPage) {
            
            return response()->json([

                'current' => ContainerType::latest('id')->paginate($perPage),
                'trashed' => ContainerType::latest('id')->onlyTrashed()->paginate($perPage),

            ], 200);

        }

        return ContainerType::latest('id')->get();
    }

    public function storeNewContainerType(Request $request, $perPage)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:container_types,name',
            // 'code' => 'required|string|max:100|unique:storage_types,code',
        ]);

        $newAsset = new ContainerType();

        $newAsset->name = strtolower($request->name);
        // $newAsset->code = strtolower($request->code);

        $newAsset->save();

        return $this->showAllContainerTypes($perPage);
    }

    public function updateContainerType(Request $request, $owner, $perPage)
    {
        $assetToUpdate = ContainerType::findOrFail($owner);

        $request->validate([
            'name' => 'required|string|max:100|unique:container_types,name,'.$assetToUpdate->id,
            // 'code' => 'required|string|max:100|unique:storage_types,code,'.$assetToUpdate->id,
        ]);

        $assetToUpdate->name = strtolower($request->name);
        // $assetToUpdate->code = strtolower($request->code);

        $assetToUpdate->save();

        return $this->showAllContainerTypes($perPage);
    }

    public function deleteContainerType($asset, $perPage)
    {
        $assetToDelete = ContainerType::findOrFail($asset);
        
        if ($assetToDelete->containers->count()) {
            
            return response()->json(['errors'=>["engaged" => ucfirst($assetToDelete->name)." is in use at ".$assetToDelete->containers->count()." containers"]], 422);

        }
        
        // $userToDelete->warehouses()->delete();
        $assetToDelete->delete();

        return $this->showAllContainerTypes($perPage);
    }

    public function restoreContainerType($asset, $perPage)
    {
        $userToRestore = ContainerType::withTrashed()->findOrFail($asset);
        // $userToRestore->warehouses()->restore();
        $userToRestore->restore();

        return $this->showAllContainerTypes($perPage);
    }

    public function searchAllContainerTypes($search, $perPage)
    {
        $columnsToSearch = ['name'];

        $query = ContainerType::withTrashed();

        foreach($columnsToSearch as $column){
            $query->orWhere($column, 'like', "%$search%");
        }

        return response()->json([
            'all' => $query->paginate($perPage),    
        ], 200);
    }

    // Containers
    public function showAllContainers($perPage=false)
    {
        if ($perPage) {
            return response()->json([

                'current' => Container::with(['shelf.unit', 'storageType', 'containerType'])->withCount('warehouses')->latest('id')->paginate($perPage),

                'trashed' => Container::with(['shelf.unit', 'storageType', 'containerType'])->onlyTrashed()->withCount('warehouses')->latest('id')->paginate($perPage),

            ], 200);
        }

        return Container::with(['shelf.unit'])->latest('id')->get();
    }

    public function storeNewContainer(Request $request, $perPage)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:containers,name',
            'code' => 'required|string|alpha_dash|max:100|unique:containers,code',
            'storage_type_id' => 'required|integer|exists:storage_types,id', 
            'container_type_id' => 'required|integer|exists:container_types,id', 
            'length' => 'required|numeric',
            'width' => 'required|numeric',
            'height' => 'required|numeric',
            // 'storing_price' => 'required|numeric',
            // 'selling_price' => 'required|numeric',
            // 'has_shelve' => 'required|boolean',
            'shelf.quantity' => 'required_if:has_shelve,1|numeric',
            // 'shelf.storing_price' => 'required_if:has_shelve,1|numeric',
            // 'shelf.selling_price' => 'required_if:has_shelve,1|numeric',
            // 'shelf.has_units' => 'required_if:has_shelve,1|boolean',
            'shelf.unit.quantity' => 'required_if:shelf.has_units,1|numeric',
            // 'shelf.unit.storing_price' => 'required_if:shelf.has_units,1|numeric',
            // 'shelf.unit.selling_price' => 'required_if:shelf.has_units,1|numeric',
        ]);

        $newContainer = new Container();

        $newContainer->storage_type_id = $request->storage_type_id;
        $newContainer->container_type_id = $request->container_type_id;
        $newContainer->name = strtolower($request->name);
        $newContainer->code = strtolower($request->code);
        $newContainer->length = $request->length;
        $newContainer->width = $request->width;
        $newContainer->height = $request->height;
        // $newContainer->storing_price = $request->storing_price;
        // $newContainer->selling_price = $request->selling_price;
        $newContainer->has_shelve = $request->has_shelve ?? false;

        $newContainer->save();

        if ($newContainer->has_shelve) {
            
            $request->shelf = json_decode(json_encode($request->shelf));

            $newContainerShelve = $newContainer->shelf()->create([
               'quantity' => $request->shelf->quantity,
               // 'storing_price' => $request->shelf->storing_price,
               // 'selling_price' => $request->shelf->selling_price,
               'has_units' => $request->shelf->has_units ?? false,
            ]);

            if ($newContainerShelve->has_units) {
                
                $newContainerShelve->unit()->create([
                   'quantity' => $request->shelf->unit->quantity,
                   // 'storing_price' => $request->shelf->unit->storing_price,
                   // 'selling_price' => $request->shelf->unit->selling_price,
                ]);

            }

        }

        return $this->showAllContainers($perPage);
    }

    public function updateContainer(Request $request, $owner, $perPage)
    {
        $containerToUpdate = Container::findOrFail($owner);

        $request->validate([
            'storage_type_id' => 'required|integer|exists:storage_types,id', 
            'container_type_id' => 'required|integer|exists:container_types,id', 
            'name' => 'required|string|max:100|unique:containers,name,'.$containerToUpdate->id,
            'code' => 'required|string|alpha_dash|max:100|unique:containers,code,'.$containerToUpdate->id,
            'length' => 'required|numeric',
            'width' => 'required|numeric',
            'height' => 'required|numeric',
            // 'storing_price' => 'required|numeric',
            // 'selling_price' => 'required|numeric',
            // 'has_shelve' => 'required|boolean',
            'shelf.quantity' => 'required_if:has_shelve,1|integer',
            // 'shelf.storing_price' => 'required_if:has_shelve,1|numeric',
            // 'shelf.selling_price' => 'required_if:has_shelve,1|numeric',
            // 'shelf.has_units' => 'required_if:has_shelve,1|boolean',
            'shelf.unit.quantity' => 'required_if:shelf.has_units,1|integer',
            // 'shelf.unit.storing_price' => 'required_if:shelf.has_units,1|numeric',
            // 'shelf.unit.selling_price' => 'required_if:shelf.has_units,1|numeric',
        ]);

        $containerToUpdate->update([
            'storage_type_id' => $request->storage_type_id,
            'container_type_id' => $request->container_type_id,
            'name' => strtolower($request->name),
            'code' => strtolower($request->code),
            'length' => $request->length,
            'width' => $request->width,
            'height' => $request->height,
            // 'storing_price' => $request->storing_price,
            // 'selling_price' => $request->selling_price,
            // 'has_shelve' => $request->has_shelve,
        ]);

    /*
        if ($containerToUpdate->has_shelve) {
            
            $request->shelf = json_decode(json_encode($request->shelf));

            $containerToUpdate->shelf()->update([
               // 'quantity' => $request->shelf->quantity ?? false,
               // 'has_units' => $request->shelf->has_units,
               // 'storing_price' => $request->shelf->storing_price,
               // 'selling_price' => $request->shelf->selling_price,
            ]);

            if ($containerToUpdate->shelf->has_units) {
                
                $containerToUpdate->unit()->update([
                   // 'container_shelf_units.quantity' => $request->shelf->unit->quantity ?? false,
                   // 'container_shelf_units.storing_price' => $request->shelf->unit->storing_price,
                   // 'container_shelf_units.selling_price' => $request->shelf->unit->selling_price,
                ]);

            }

        }
    */

        return $this->showAllContainers($perPage);
    }

    public function deleteContainer($asset, $perPage)
    {
        $containerToDelete = Container::findOrFail($asset);
        
        if ($containerToDelete->warehouses->count()) {
            
            return response()->json(['errors'=>["engaged" => ucfirst($containerToDelete->name)." is in use at ".$containerToDelete->warehouses->count()." warehouses"]], 422);

        }

        // $userToDelete->warehouses()->delete();
        $containerToDelete->delete();

        return $this->showAllContainers($perPage);
    }

    public function restoreContainer($asset, $perPage)
    {
        $containerToRestore = Container::withTrashed()->findOrFail($asset);
        // $userToRestore->warehouses()->restore();
        $containerToRestore->restore();

        return $this->showAllContainers($perPage);
    }

    public function searchAllContainers($search, $perPage)
    {
        $columnsToSearch = ['name', 'code', 'length', 'width', 'height'];

        $query = Container::with(['shelf.unit'])->withTrashed();

        foreach($columnsToSearch as $column){
            $query->orWhere($column, 'like', "%$search%");
        }

        return response()->json([
            'all' => $query->paginate($perPage),    
        ], 200);
    }

    
    // Rent-Periods
    public function showAllRentPeriods($perPage=false)
    {
        if ($perPage) {
            
            return response()->json([

                'current' => RentPeriod::latest('id')->withCount('spaces')->paginate($perPage),
                'trashed' => RentPeriod::latest('id')->onlyTrashed()->withCount('spaces')->paginate($perPage),

            ], 200);

        }

        return RentPeriod::latest('id')->get();
    }

    public function storeNewRentPeriod(Request $request, $perPage)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:rent_periods,name',
            'number_days' => 'required|integer|unique:rent_periods,number_days|max:65535',
        ]);

        $newAsset = new RentPeriod();

        $newAsset->name = strtolower($request->name);
        $newAsset->number_days = $request->number_days;

        $newAsset->save();

        return $this->showAllRentPeriods($perPage);
    }

    public function updateRentPeriod(Request $request, $owner, $perPage)
    {
        $assetToUpdate = RentPeriod::findOrFail($owner);

        $request->validate([
            'name' => 'required|string|max:100|unique:rent_periods,name,'.$assetToUpdate->id,
            'number_days' => 'required|integer|max:65535|unique:rent_periods,number_days,'.$assetToUpdate->id,
        ]);

        $assetToUpdate->name = strtolower($request->name);
        $assetToUpdate->number_days = $request->number_days;

        $assetToUpdate->save();

        return $this->showAllRentPeriods($perPage);
    }

    public function deleteRentPeriod($asset, $perPage)
    {
        $assetToDelete = RentPeriod::findOrFail($asset);
        
        if ($assetToDelete->spaces->count()) {
            
            return response()->json(['errors'=>["engaged" => ucfirst($assetToDelete->name)." is in use at ".$assetToDelete->spaces->count()." spaces"]], 422);

        }

        // $userToDelete->warehouses()->delete();
        
        $assetToDelete->delete();

        return $this->showAllRentPeriods($perPage);
    }

    public function restoreRentPeriod($asset, $perPage)
    {
        $assetToRestore = RentPeriod::withTrashed()->findOrFail($asset);
        // $userToRestore->warehouses()->restore();
        $assetToRestore->restore();

        return $this->showAllRentPeriods($perPage);
    }

    public function searchAllRentPeriods($search, $perPage)
    {
        $columnsToSearch = ['name', 'number_days'];

        $query = RentPeriod::withTrashed();

        foreach($columnsToSearch as $column){
            $query->orWhere($column, 'like', "%$search%");
        }

        return response()->json([
            'all' => $query->paginate($perPage),    
        ], 200);
    }


    // Variation Types
    public function showAllVariationTypes($perPage=false)
    {
        if ($perPage) {
            
            return response()->json([

                'current' => VariationType::latest('id')->paginate($perPage),
                'trashed' => VariationType::onlyTrashed()->latest('id')->paginate($perPage),

            ], 200);

        }

        return VariationTypeResource::collection(
            VariationType::with([
                'variations' => function ($query) {
                    $query->whereNull('variation_parent_id');
                }, 
            ])->latest('id')->get()
        );
    }

    public function storeVariationType(Request $request, $perPage)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:variation_types,name',
            // 'code' => 'required|string|max:100|unique:storage_types,code',
        ]);

        $newAsset = new VariationType();

        $newAsset->name = strtolower($request->name);
        // $newAsset->code = $request->code;

        $newAsset->save();

        return $this->showAllVariationTypes($perPage);
    }

    public function updateVariationType(Request $request, $asset, $perPage)
    {
        $assetToUpdate = VariationType::findOrFail($asset);

        $request->validate([
            'name' => 'required|string|max:100|unique:variation_types,name,'.$assetToUpdate->id,
            // 'code' => 'required|string|max:100|unique:storage_types,code,'.$assetToUpdate->id,
        ]);

        $assetToUpdate->name = strtolower($request->name);
        // $assetToUpdate->code = $request->code;

        $assetToUpdate->save();

        return $this->showAllVariationTypes($perPage);
    }

    public function deleteVariationType($asset, $perPage)
    {
        $assetToDelete = VariationType::findOrFail($asset);
        
        if ($assetToDelete->variations->count()) {
            
            return response()->json(['errors'=>["engaged" => ucfirst($assetToDelete->name)." is in use at ".$assetToDelete->variations->count()." variations"]], 422);

        }

        // $userToDelete->warehouses()->delete();
        $assetToDelete->delete();

        return $this->showAllVariationTypes($perPage);
    }

    public function restoreVariationType($asset, $perPage)
    {
        $assetToRestore = VariationType::withTrashed()->findOrFail($asset);
        // $userToRestore->warehouses()->restore();
        $assetToRestore->restore();

        return $this->showAllVariationTypes($perPage);
    }

    public function searchVariationTypes($search, $perPage)
    {
        $columnsToSearch = ['name'];

        $query = VariationType::withTrashed();

        foreach($columnsToSearch as $column){
            $query->orWhere($column, 'like', "%$search%");
        }

        return response()->json([
            'all' => $query->paginate($perPage),    
        ], 200);
    }


    // Variations
    public function showAllVariations($perPage=false)
    {
        if ($perPage) {
            
            return response()->json([

                'current' => new VariationCollection(Variation::with([ 'type', 'parent' ])->latest('id')->paginate($perPage)),

                'trashed' => new VariationCollection(Variation::with([ 'type', 'parent' ])->onlyTrashed()->latest('id')->paginate($perPage)),

            ], 200);

        }

        return Variation::latest('id')->get();
    }

    public function storeNewVariation(Request $request, $perPage)
    {
        $request->validate([
            'name'  => [
                'required', 'string', 'max:100',
                Rule::unique('variations', 'name')->where(function ($query) use ($request) {
                    return $query
                        ->whereName($request->name)
                        ->where('variation_type_id', $request->variation_type_id)
                        ->where('variation_parent_id', $request->variation_parent_id);
                }),
            ],
            'variation_type_id' => 'required|integer|exists:variation_types,id',
            'variation_parent_id' => 'nullable|integer|exists:variations,id',
        ]);

        $newAsset = new Variation();

        $newAsset->name = strtolower($request->name);
        $newAsset->variation_type_id = $request->variation_type_id;
        $newAsset->variation_parent_id = $request->variation_parent_id;

        $newAsset->save();

        return $this->showAllVariations($perPage);
    }

    public function updateVariation(Request $request, $asset, $perPage)
    {
        $assetToUpdate = Variation::findOrFail($asset);

        $request->validate([
            'name'  => [
                'required', 'string', 'max:100', 
                Rule::unique('variations', 'name')->where(function ($query) use ($request, $assetToUpdate) {
                    return $query
                        ->whereName($request->name)
                        ->where('variation_type_id', $request->variation_type_id)
                        ->where('variation_parent_id', $request->variation_parent_id)
                        ->whereNotIn('id', [ $assetToUpdate->id ]);
                }),
            ],
            'variation_type_id' => 'required|integer|exists:variation_types,id',
            // 'variation_parent_id' => 'nullable|integer|exists:variations,id',
            'variation_parent_id' => [
                'nullable', 'integer', 'exists:variations,id', 
                Rule::notIn([ $asset ]),
            ],
        ]);

        if ($request->variation_parent_id && $assetToUpdate->id < $request->variation_parent_id && Variation::findOrFail($request->variation_parent_id)->parent()->exists()) {
            return response()->json(['errors'=>["invalidParent" => "Invalid parent variation"]], 422);
        }

        $assetToUpdate->name = strtolower($request->name);
        $assetToUpdate->variation_type_id = $request->variation_type_id;
        $assetToUpdate->variation_parent_id = $request->variation_parent_id;

        $assetToUpdate->save();

        return $this->showAllVariations($perPage);
    }

    public function deleteVariation($asset, $perPage)
    {
        $assetToDelete = Variation::findOrFail($asset);
        
        if ($assetToDelete->childs->count()) {
            
            return response()->json(['errors'=>["engaged" => ucfirst($assetToDelete->name)." is in use at ".$assetToDelete->childs->count()." childs"]], 422);

        }

        // $userToDelete->warehouses()->delete();
        $assetToDelete->delete();

        return $this->showAllVariations($perPage);
    }

    public function restoreVariation($asset, $perPage)
    {
        $assetToRestore = Variation::withTrashed()->findOrFail($asset);
        // $userToRestore->warehouses()->restore();
        $assetToRestore->restore();

        return $this->showAllVariations($perPage);
    }

    public function searchAllVariations($search, $perPage)
    {
        $columnsToSearch = ['name'];

        $query = Variation::with(['type.variations', 'parent'])->withTrashed();

        foreach($columnsToSearch as $column){
            $query->orWhere($column, 'like', "%$search%");
        }

        return response()->json([
            'all' => $query->paginate($perPage),    
        ], 200);
    }

    // Packaging-Packages
    public function showAllPackagingPackages($perPage=false)
    {
        if ($perPage) {
            
            return response()->json([

                'current' => PackagingPackage::paginate($perPage),
                'trashed' => PackagingPackage::onlyTrashed()->paginate($perPage),

            ], 200);

        }

        return PackagingPackage::latest('id')->get();
    }

    public function storeNewPackagingPackage(Request $request, $perPage)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:packaging_packages,name',
            'price' => 'required|numeric',
            'description' => 'nullable|string|max:255',
        ]);

        $newAsset = new PackagingPackage();

        $newAsset->name = strtolower($request->name);
        $newAsset->price = $request->price;
        $newAsset->description = strtolower($request->description);

        $newAsset->save();

        $newAsset->package_preview = $request->preview;
        $newAsset->save();

        return $this->showAllPackagingPackages($perPage);
    }

    public function updatePackagingPackage(Request $request, $owner, $perPage)
    {
        $assetToUpdate = PackagingPackage::findOrFail($owner);

        $request->validate([
            'name' => 'required|string|max:100|unique:packaging_packages,name,'.$assetToUpdate->id,
            'price' => 'required|numeric',
            'description' => 'nullable|string|max:255',
        ]);

        $assetToUpdate->name = strtolower($request->name);
        $assetToUpdate->price = $request->price;
        $assetToUpdate->package_preview = $request->preview;
        $assetToUpdate->description = strtolower($request->description);

        $assetToUpdate->save();

        return $this->showAllPackagingPackages($perPage);
    }

    public function deletePackagingPackage($package, $perPage)
    {
        $assetToDelete = PackagingPackage::findOrFail($package);
        // $userToDelete->warehouses()->delete();
        $assetToDelete->delete();

        return $this->showAllPackagingPackages($perPage);
    }

    public function restorePackagingPackage($package, $perPage)
    {
        $userToRestore = PackagingPackage::withTrashed()->findOrFail($package);
        // $userToRestore->warehouses()->restore();
        $userToRestore->restore();

        return $this->showAllPackagingPackages($perPage);
    }

    public function searchAllPackagingPackages($search, $perPage)
    {
        $columnsToSearch = ['name', 'price', 'description'];

        $query = PackagingPackage::withTrashed();

        foreach($columnsToSearch as $column){
            $query->orWhere($column, 'like', "%$search%");
        }

        return response()->json([
            'all' => $query->paginate($perPage),    
        ], 200);
    }

    // Delivery-Companies
    public function showDeliveryAllCompanies($perPage=false)
    {
        if ($perPage) {
            
            return response()->json([

                'current' => DeliveryCompany::paginate($perPage),
                'trashed' => DeliveryCompany::onlyTrashed()->paginate($perPage),

            ], 200);

        }

        return DeliveryCompany::latest('id')->get();
    }

    public function storeDeliveryNewCompany(Request $request, $perPage)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:delivery_companies,name',
            'commission' => 'required|numeric',
            'service_time' => 'nullable|string|max:255',
        ]);

        $newAsset = new DeliveryCompany();

        $newAsset->name = strtolower($request->name);
        $newAsset->commission = $request->commission;
        $newAsset->service_time = $request->service_time;

        $newAsset->save();

        return $this->showDeliveryAllCompanies($perPage);
    }

    public function updateDeliveryCompany(Request $request, $owner, $perPage)
    {
        $assetToUpdate = DeliveryCompany::findOrFail($owner);

        $request->validate([
            'name' => 'required|string|max:100|unique:delivery_companies,name,'.$assetToUpdate->id,
            'commission' => 'required|numeric',
            'service_time' => 'nullable|string|max:255',
        ]);

        $assetToUpdate->name = strtolower($request->name);
        $assetToUpdate->commission = $request->commission;
        $assetToUpdate->service_time = $request->service_time;

        $assetToUpdate->save();

        return $this->showDeliveryAllCompanies($perPage);
    }

    public function deleteDeliveryCompany($package, $perPage)
    {
        $assetToDelete = DeliveryCompany::findOrFail($package);
        // $userToDelete->warehouses()->delete();
        $assetToDelete->delete();

        return $this->showDeliveryAllCompanies($perPage);
    }

    public function restoreDeliveryCompany($package, $perPage)
    {
        $userToRestore = DeliveryCompany::withTrashed()->findOrFail($package);
        // $userToRestore->warehouses()->restore();
        $userToRestore->restore();

        return $this->showDeliveryAllCompanies($perPage);
    }

    public function searchDeliveryAllCompanies($search, $perPage)
    {
        $columnsToSearch = ['name', 'commission', 'service_time'];

        $query = DeliveryCompany::withTrashed();

        foreach($columnsToSearch as $column){
            $query->orWhere($column, 'like', "%$search%");
        }

        return response()->json([
            'all' => $query->paginate($perPage),    
        ], 200);
    }

    // Vendors
    public function showAllVendors($perPage=false)
    {
        if ($perPage) {
            
            return response()->json([

                'current' => Vendor::paginate($perPage),
                'trashed' => Vendor::onlyTrashed()->paginate($perPage),

            ], 200);

        }

        return Vendor::all();
    }

    public function storeNewVendor(Request $request, $perPage)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:vendors,name',
        ]);

        $newAsset = new Vendor();

        $newAsset->name = strtolower($request->name);

        $newAsset->save();

        return $this->showAllVendors($perPage);
    }

    public function updateVendor(Request $request, $asset, $perPage)
    {
        $assetToUpdate = Vendor::findOrFail($asset);

        $request->validate([
            'name' => 'required|string|max:100|unique:vendors,name,'.$assetToUpdate->id,
        ]);

        $assetToUpdate->name = strtolower($request->name);

        $assetToUpdate->save();

        return $this->showAllVendors($perPage);
    }

    public function deleteVendor($asset, $perPage)
    {
        $assetToDelete = Vendor::findOrFail($asset);

        if ($assetToDelete->stocks->count()) {
            
            return response()->json(['errors'=>["engaged" => ucfirst($assetToDelete->name)." is in use at ".$assetToDelete->merchantProducts->count()." products"]], 422);

        }

        $assetToDelete->delete();

        return $this->showAllVendors($perPage);
    }

    public function restoreVendor($asset, $perPage)
    {
        $assetToRestore = Vendor::withTrashed()->findOrFail($asset);
        
        $assetToRestore->restore();

        return $this->showAllVendors($perPage);
    }

    public function searchAllVendors($search, $perPage)
    {
        $query = Vendor::withTrashed()->where('name', 'like', "%$search%");

        return response()->json([
            'all' => $query->paginate($perPage),    
        ], 200);
    }
    
}
