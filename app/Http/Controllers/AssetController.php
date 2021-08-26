<?php

namespace App\Http\Controllers;

use App\Models\Variation;
use App\Models\Container;
use App\Models\RentPeriod;
use App\Models\StorageType;
use Illuminate\Http\Request;
use App\Models\VariationType;
use App\Models\PackagingPackage;
use Illuminate\Support\Facades\Auth;

class AssetController extends Controller
{
    public function __construct()
    {
        $this->middleware("permission:view-asset-index")->only(['showAllStorageTypes', 'searchAllStorageTypes', 'showAllContainers', 'searchAllContainers', 'showAllRentPeriods', 'searchAllRentPeriods', 'showAllVariationTypes', 'searchVariationTypes', 'showAllVariations', 'searchAllVariations']);

        $this->middleware("permission:create-asset")->only(['storeNewStorageType', 'storeNewContainer', 'storeNewRentPeriod', 'storeVariationType', 'storeNewVariation', 'storeNewPackagingPackage']);

        $this->middleware("permission:update-asset")->only(['updateStorageType', 'updateContainer', 'updateRentPeriod', 'updateVariationType', 'updateVariation', 'updatePackagingPackage']);
        
        $this->middleware("permission:delete-asset")->only(['deleteStorageType', 'restoreStorageType', 'deleteContainer', 'restoreContainer', 'deleteRentPeriod', 'restoreRentPeriod', 'deleteVariationType', 'restoreVariationType', 'deleteVariation', 'restoreVariation', 'deletePackagingPackage', 'restorePackagingPackage']);
    }

    // storage types
    public function showAllStorageTypes($perPage=false)
    {
    	if ($perPage) {
            
            return response()->json([

        		'current' => StorageType::paginate($perPage),
        		'trashed' => StorageType::onlyTrashed()->paginate($perPage),

        	], 200);

        }

        return StorageType::all();
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

    // Containers
    public function showAllContainers($perPage=false)
    {
        if ($perPage) {
            return response()->json([

                'current' => Container::with(['shelf.unit'])->paginate($perPage),
                'trashed' => Container::with(['shelf.unit'])->onlyTrashed()->paginate($perPage),

            ], 200);
        }

        return Container::with(['shelf.unit'])->get();
    }

    public function storeNewContainer(Request $request, $perPage)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:containers,name',
            // 'code' => 'required|string|max:100|unique:containers,code',
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

        $newContainer->name = strtolower($request->name);
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
            'name' => 'required|string|max:100|unique:containers,name,'.$containerToUpdate->id,
            // 'code' => 'required|string|max:100|unique:containers,code',
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

        $containerToUpdate->update([
            'name' => strtolower($request->name),
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
        $columnsToSearch = ['name', 'length', 'width', 'height'];

        $query = Container::with(['shelf.unit'])->withTrashed();

        foreach($columnsToSearch as $column){
            $query->orWhere($column, 'like', "%$search%");
        }

        return response()->json([
            'all' => $query->paginate($perPage),    
        ], 200);
    }

    
    // Rent Periods
    public function showAllRentPeriods($perPage=false)
    {
        if ($perPage) {
            
            return response()->json([

                'current' => RentPeriod::paginate($perPage),
                'trashed' => RentPeriod::onlyTrashed()->paginate($perPage),

            ], 200);

        }

        return RentPeriod::all();
    }

    public function storeNewRentPeriod(Request $request, $perPage)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:rent_periods,name|in:daily,weekly,monthly,yearly',
            // 'code' => 'required|string|max:100|unique:storage_types,code',
        ]);

        $newAsset = new RentPeriod();

        $newAsset->name = strtolower($request->name);
        // $newAsset->code = $request->code;

        $newAsset->save();

        return $this->showAllRentPeriods($perPage);
    }

    public function updateRentPeriod(Request $request, $owner, $perPage)
    {
        $assetToUpdate = RentPeriod::findOrFail($owner);

        $request->validate([
            'name' => 'required|string|max:100|in:daily,weekly,monthly,yearly|unique:rent_periods,name,'.$assetToUpdate->id,
            // 'code' => 'required|string|max:100|unique:storage_types,code,'.$assetToUpdate->id,
        ]);

        $assetToUpdate->name = strtolower($request->name);
        // $assetToUpdate->code = $request->code;

        $assetToUpdate->save();

        return $this->showAllRentPeriods($perPage);
    }

    public function deleteRentPeriod($asset, $perPage)
    {
        $assetToDelete = RentPeriod::findOrFail($asset);
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
        $columnsToSearch = ['name'];

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

                'current' => VariationType::paginate($perPage),
                'trashed' => VariationType::onlyTrashed()->paginate($perPage),

            ], 200);

        }

        return VariationType::with('variations')->get();
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

                'current' => Variation::paginate($perPage),
                'trashed' => Variation::onlyTrashed()->paginate($perPage),

            ], 200);

        }

        return Variation::all();
    }

    public function storeNewVariation(Request $request, $perPage)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:variations,name',
            'variation_type_id' => 'required|numeric|exists:variation_types,id',
        ]);

        $newAsset = new Variation();

        $newAsset->name = strtolower($request->name);
        $newAsset->variation_type_id = $request->variation_type_id;

        $newAsset->save();

        return $this->showAllVariations($perPage);
    }

    public function updateVariation(Request $request, $asset, $perPage)
    {
        $assetToUpdate = Variation::findOrFail($asset);

        $request->validate([
            'name' => 'required|string|max:100|unique:variations,name,'.$assetToUpdate->id,
            'variation_type_id' => 'required|numeric|exists:variation_types,id',
        ]);

        $assetToUpdate->name = strtolower($request->name);
        $assetToUpdate->variation_type_id = $request->variation_type_id;

        $assetToUpdate->save();

        return $this->showAllVariations($perPage);
    }

    public function deleteVariation($asset, $perPage)
    {
        $assetToDelete = Variation::findOrFail($asset);
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

        $query = Variation::withTrashed();

        foreach($columnsToSearch as $column){
            $query->orWhere($column, 'like', "%$search%");
        }

        return response()->json([
            'all' => $query->paginate($perPage),    
        ], 200);
    }

    // packaging packages
    public function showAllPackagingPackages($perPage=false)
    {
        if ($perPage) {
            
            return response()->json([

                'current' => PackagingPackage::paginate($perPage),
                'trashed' => PackagingPackage::onlyTrashed()->paginate($perPage),

            ], 200);

        }

        return PackagingPackage::all();
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
    
}
