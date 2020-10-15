<?php

namespace App\Http\Controllers;

use App\Models\StorageType;
use Illuminate\Http\Request;

class AssetController extends Controller
{
    public function showAllStorageTypes($perPage)
    {
    	return response()->json([

    		'current' => StorageType::paginate($perPage),
    		'trashed' => StorageType::onlyTrashed()->paginate($perPage),

    	], 200);
    }

    public function storeNewStorageType(Request $request, $perPage)
    {
    	$request->validate([
            'name' => 'required|string|max:100|unique:storage_types,name',
            'code' => 'required|string|max:100|unique:storage_types,code',
        ]);

        $newAsset = new StorageType();

        $newAsset->name = $request->name;
        $newAsset->code = $request->code;

        $newAsset->save();

        return $this->showAllStorageTypes($perPage);
    }

    public function updateStorageType(Request $request, $owner, $perPage)
    {
    	$assetToUpdate = StorageType::findOrFail($owner);

        $request->validate([
            'name' => 'required|string|max:100|unique:storage_types,name,'.$assetToUpdate->id,
            'code' => 'required|string|max:100|unique:storage_types,code,'.$assetToUpdate->id,
        ]);

        $assetToUpdate->name = $request->name;
        $assetToUpdate->code = $request->code;

        $assetToUpdate->save();

        return $this->showAllStorageTypes($perPage);
    }

    public function deleteStorageType($asset, $perPage)
    {
    	$assetToDelete = StorageType::findOrFail($asset);
        // $userToDelete->warhouses()->delete();
        $assetToDelete->delete();

        return $this->showAllStorageTypes($perPage);
    }

    public function restoreStorageType($asset, $perPage)
    {
    	$userToRestore = StorageType::withTrashed()->findOrFail($asset);
        // $userToRestore->warhouses()->restore();
        $userToRestore->restore();

        return $this->showAllStorageTypes($perPage);
    }

    public function searchAllStorageTypes($search, $perPage)
    {
        $columnsToSearch = ['name', 'code'];

        $query = StorageType::withTrashed();

        foreach($columnsToSearch as $column){
            $query->orWhere($column, 'like', "%$search%");
        }

        return response()->json([
            'all' => $query->paginate($perPage),    
        ], 200);
    }
}
